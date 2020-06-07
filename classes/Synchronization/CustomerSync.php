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

      $response = new \stdClass;
      $tracker = self::trackerFetch();
      $offset = $tracker['offset'];

      $limit = 500;

      /*
       * Fetch users
       */


      $admin_users = new \WP_User_Query(
        array(
          'role'   => 'administrator',
          'fields' => 'ID',
        )
      );

      $manager_users = new \WP_User_Query(
        array(
          'role'   => 'shop_manager',
          'fields' => 'ID',
        )
      );

      $query = new \WP_User_Query(
        array(
          'exclude' => array_merge( $admin_users->get_results(), $manager_users->get_results() ),
          'fields'  => 'ID',
          'number'  => $limit,
          'offset'  => $offset
        )
      );

      $users = $query->get_results();

      if( empty( $users )) {
        $response->code = 220;
        return $response;
      }

      // loop over orders to find unique customers
      // customer data organized into $data
      $customers = [];
      foreach ($users as $user_id) {
        $customer = new \WC_Customer( $user_id );
        $customers[] = self::formatSingleItem($customer);
      }

      // no data
      if( empty( $customers )) {
        $response->code = 221;
        return $response;
      }

      $count = count($customers);

      // track the export
      self::trackerSave( $offset + $count );

      // do upload to custobar API
      $apiResponse = self::uploadDataTypeData($customers);

      // return response
      $response->code = $apiResponse->code;
      $response->body = $apiResponse->body;
      $response->tracker = self::trackerFetch();
      $response->count = $count;
      return $response;

    }

    public static function trackerFetch() {
      $trackerKey = 'custobar_export_customer';
      $tracker = get_option($trackerKey);
      if( !is_array( $tracker )) {
        $tracker = [];
      }
      if( !isset($tracker['offset']) ) {
        $tracker['offset'] = 0;
      }
      if( !isset($tracker['updated']) ) {
        $tracker['updated'] = false;
      }
      return $tracker;
    }

    public static function trackerSave( $offset ) {
      $tracker = self::trackerFetch();
      $tracker['offset'] = $offset;
      $tracker['updated'] = time();
      update_option('custobar_export_customer', $tracker);
    }

    protected static function formatSingleItem($user) {
      $custobar_customer = new CustobarCustomer($user);
      $properties = $custobar_customer->getAssignedProperties();
      return apply_filters('woocommerce_custobar_customer_properties', $properties, $user);
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
