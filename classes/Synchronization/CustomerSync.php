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
        add_action('wp_async_user_register', [__CLASS__, 'singleUpdate']);
        add_action('wp_async_profile_update', [__CLASS__, 'singleUpdate']);
        add_action('wp_async_woocommerce_new_customer', [__CLASS__, 'singleUpdate']);
        add_action('wp_async_woocommerce_created_customer', [__CLASS__, 'singleUpdate']);
        add_action('wp_async_woocommerce_update_customer', [__CLASS__, 'singleUpdate']);
        add_action('plugins_loaded', function () {
            new CustobarAsyncTask('user_register');
            new CustobarAsyncTask('profile_update');
            new CustobarAsyncTask('woocommerce_new_customer');
            new CustobarAsyncTask('woocommerce_created_customer');
            new CustobarAsyncTask('woocommerce_update_customer');
        });
    }

    public static function singleUpdate($args)
    {

      wc_get_logger()->info('CustomerSync single update called with $args: ' . print_r($args[0],1), array(
        'source'        => 'woocommerce-custobar'
      ));

      $customer = new \WC_Customer( $args[0] );

      # Update only customers
      if ( $customer->get_role() == 'customer') {
        $properties = self::formatSingleItem($customer);
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

      $query = new \WP_User_Query(
        array(
          'role'    => 'customer',
          'fields'  => 'ID',
          'orderby' => 'ID',
          'order'   => 'ASC',
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
      $tracker = get_option('custobar_export_customer');
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

    public static function trackerSave( $offset, $total=null ) {
      $tracker = self::trackerFetch();
      if (isset($offset)) {
        $tracker['offset'] = $offset;
        $tracker['updated'] = time();
      }
      if (isset($total)) {
        $tracker['total'] = $total;        
      }
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
