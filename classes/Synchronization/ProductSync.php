<?php

namespace WooCommerceCustobar\Synchronization;

defined('ABSPATH') or exit;

use WooCommerceCustobar\DataType\CustobarProduct;
use WooCommerceCustobar\AsyncTasks\CustobarAsyncTask;

/**
 * Class ProductSync
 *
 * @package WooCommerceCustobar\Synchronization
 */
class ProductSync extends AbstractDataSync {

  protected static $endpoint = '/products/upload/';

  public static function addHooks()
    {
        add_action('wp_async_woocommerce_new_product', [__CLASS__, 'singleUpdate']);
        add_action('wp_async_woocommerce_update_product', [__CLASS__, 'singleUpdate']);
        add_action('plugins_loaded', function () {
            new CustobarAsyncTask('woocommerce_new_product');
            new CustobarAsyncTask('woocommerce_update_product');
        });
    }

    public static function singleUpdate($args) {

      wc_get_logger()->info('ProductSync single update called with $args: ' . print_r($args, 1), array(
        'source'        => 'woocommerce-custobar'
      ));

      $product = wc_get_product($args[0]);
      $properties = self::formatSingleItem($product);
      self::uploadDataTypeData($properties, true);

    }

    public static function batchUpdate() {

      $response = new \stdClass;
      $limit = 500;
      $tracker = self::trackerFetch();
      $offset = $tracker['offset'];
      $productList = [];

      $products = wc_get_products([
        'limit'   => $limit,
        'offset'  => $offset,
        'orderby' => 'ID',
        'order'   => 'ASC'
      ]);

      foreach ( $products as $product ) {

        $productList[] = self::formatSingleItem($product);

      }

      $count = count( $productList );

      // no products
      if( empty( $productList )) {
        $response->code = 220;
        return $response;
      }

      $apiResponse = self::uploadDataTypeData($productList);

      self::trackerSave( $offset + $count );

      // return response
      $response->code = $apiResponse->code;
      $response->body = $apiResponse->body;
      $response->tracker = self::trackerFetch();
      $response->count = $count;
      return $response;

    }

    public static function trackerFetch() {
      $trackerKey = 'custobar_export_product';
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
      update_option('custobar_export_product', $tracker);
    }

    protected static function formatSingleItem($product)
    {
        $custobar_product = new CustobarProduct($product);
        $properties = $custobar_product->getAssignedProperties();
        return apply_filters('woocommerce_custobar_product_properties', $properties, $product);
    }

    protected static function uploadDataTypeData($data, $single = false)
    {
        $formatted_data = array(
            'products' => array()
        );
        if ($single) {
            $formatted_data['products'][] = $data;
        } else {
            $formatted_data['products'] = $data;
        }
        return self::uploadCustobarData($formatted_data);
    }
}
