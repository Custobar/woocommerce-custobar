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
        $order = \wc_get_order($args[0]);
        if ($order && get_class($order) === 'WC_Order') {
            $properties = self::formatSingleItem($order);
            self::uploadDataTypeData($properties, true);
        }
    }

    public static function batchUpdate() {

      /*
       * Fetch orders
       * Add check for is_processed
       * Meta data custobar_processed = 1 means we can skip it
       */
      $orders = \wc_get_orders(array(
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
      ));

      // loop over orders to find unique customers
      // customer data organized into $data
      $data = [];
      foreach ($orders as $order) {
        if (!self::customerAlreadyAdded($data, $order)) {
          $data[] = self::formatSingleItem($order);
        }
      }

      // do upload to custobar API
      $response = self::uploadDataTypeData($data);

      var_dump( $response );

    }

    protected static function customerAlreadyAdded($already_looped_data, $order)
    {
        $identifier_keys = array(
            'external_id'  => $order->get_user_id(),
            'phone_number' => $order->get_billing_phone(),
            'email'        => $order->get_billing_email()
        );
        foreach ($already_looped_data as $item) {
            foreach ($identifier_keys as $key => $value) {
                if (isset($item[$key]) && $item[$key] && $item[$key] == $identifier_keys[$key]) {
                    return true;
                }
            }
        }
        return false;
    }

    protected static function formatSingleItem($order)
    {
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
