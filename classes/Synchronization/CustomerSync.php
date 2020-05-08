<?php

namespace WooCommerceCustobar\Synchronization;

defined('ABSPATH') or exit;

use WooCommerceCustobar\DataType\CustobarCustomer;
use WooCommerceCustobar\AsyncTasks\CustobarAsyncTask;

/**
 * Class CustomerSync
 *
 * @package WooCommerceCustobar\Synchronization
 */
class CustomerSync extends AbstractDataSync
{
    protected static $endpoint = '/customers/upload/';

    public static function addHooks()
    {
        add_action('wp_async_woocommerce_new_order', [__CLASS__, 'singleUpdate']);
        add_action('wp_async_woocommerce_update_order', [__CLASS__, 'singleUpdate']);
        add_action('plugins_loaded', function () {
            new CustobarAsyncTask('woocommerce_new_order');
            new CustobarAsyncTask('woocommerce_update_order');
        });
    }

    public static function singleUpdate($args)
    {

      wc_get_logger()->info('CustomerSync single update called with $args: ' . print_r($args,1), array(
        'source'        => 'woocommerce-custobar'
      ));

        $order = \wc_get_order($args[0]);

        wc_get_logger()->info('CustomerSync single update before if check, class name: '.get_class($order), array(
          'source'        => 'woocommerce-custobar'
        ));

        if ($order && (get_class($order) === 'WC_Order' || get_class($order) === 'Automattic\WooCommerce\Admin\Overrides\Order' )) {

          wc_get_logger()->info('CustomerSync passed if check', array(
            'source'        => 'woocommerce-custobar'
          ));

            $properties = self::formatSingleItem($order);

            $uid = $properties['external_id'];
            self::trackerSave(
              [ $uid ]
            );

            wc_get_logger()->info('CustomerSync before uploadDataTypeData, $properties: ' . print_r($properties,1), array(
              'source'        => 'woocommerce-custobar'
            ));

            self::uploadDataTypeData($properties, true);

        }
    }

    public static function batchUpdate() {

      $tracker = self::trackerFetch();
      $limit = 250;

      /*
       * Fetch orders
       */
      $orders = \wc_get_orders(array(
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'ASC',
      ));
      if( empty( $orders )) {
        return false;
      }

      // loop over orders to find unique customers
      // customer data organized into $data
      $data = [];
      foreach ($orders as $order) {
        if (!self::customerAlreadyAdded($data, $order, $tracker['data'])) {
          $data[] = self::formatSingleItem($order);

          // enforce single batch limit
          if( count( $data ) >= $limit ) {
            break;
          }

        }
      }

      // no data
      if( empty( $data )) {
        return false;
      }

      $customerIds = [];
      foreach( $data as $customerData ) {
        $uid = $customerData['external_id'];
        $customerIds[] = $uid;
      }

      // track the export
      self::trackerSave( $customerIds );

      // do upload to custobar API
      $apiResponse = self::uploadDataTypeData($data);

      // return response
      $response = new \stdClass;
      $response->code = $apiResponse->code;
      $response->body = $apiResponse->body;
      $response->tracker = self::trackerFetch();
      $response->count = count( $customerIds );
      return $response;

    }

    public static function trackerFetch() {
      $trackerKey = 'custobar_export_customer';
      $tracker = get_option($trackerKey);
      if( !is_array( $tracker )) {
        $tracker = [];
      }
      if( !isset($tracker['data']) ) {
        $tracker['data'] = [];
      }
      if( !isset($tracker['updated']) ) {
        $tracker['updated'] = false;
      }
      return $tracker;
    }

    public static function trackerSave( $objectIds ) {
      $tracker = self::trackerFetch();
      $trackerData = $tracker['data'];
      $trackerData = array_merge($trackerData, $objectIds);
      $trackerData = array_unique($trackerData);
      $tracker['data'] = $trackerData;
      $tracker['updated'] = time();
      update_option('custobar_export_customer', $tracker);
    }

    public static function customerAlreadyAdded( $already_looped_data, $order, $tracker ) {

      // check for already exported
      $uid = $order->get_user_id();
      if( in_array( $uid, $tracker )) {
        return true;
      }

      // check for already in this batch
      $identifier_keys = array(
        'external_id'  => $order->get_user_id(),
        'phone_number' => $order->get_billing_phone(),
        'email'        => $order->get_billing_email()
      );

      foreach ( $already_looped_data as $item ) {
        foreach ($identifier_keys as $key => $value) {
          if (isset($item[$key]) && $item[$key] && $item[$key] == $identifier_keys[$key]) {
            return true;
          }
        }
      }

      return false;

    }

    protected static function formatSingleItem($order) {
      $custobar_customer = new CustobarCustomer($order);
      $properties = $custobar_customer->getAssignedProperties();
      return apply_filters('woocommerce_custobar_customer_properties', $properties, $order);
    }

    protected static function uploadDataTypeData($data, $single = false) {

      $formatted_data = array(
          'customers' => array()
      );
      if ($single) {
          $formatted_data['customers'][] = $data;
      } else {
          $formatted_data['customers'] = $data;
      }

      return self::uploadCustobarData($formatted_data);

    }
}
