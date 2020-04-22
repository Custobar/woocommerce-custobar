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

    public static function singleUpdate($args)
    {
        $product = wc_get_product($args[0]);
        $properties = self::formatSingleItem($product);
        self::uploadDataTypeData($properties, true);
    }

    public static function batchUpdate() {

      $limit = 500;
      $tracker = self::trackerFetch();
      $trackerData = $tracker['data'];

      $products = [];
      $productIds = [];
      foreach (wc_get_products(array('limit' => -1)) as $product) {

        // skip already processed orders
        if( in_array( $product->get_id(), $trackerData)) {
          continue;
        }

        $products[] = self::formatSingleItem($product);

        $productIds[] = $product->get_id();
        if( count($products) >= $limit ) {
          break;
        }

      }

      // no products
      if( empty( $productIds )) {
        return false;
      }

      self::trackerSave( $productIds );
      $apiResponse = self::uploadDataTypeData($products);

      // return response
      $response = new \stdClass;
      $response->code = $apiResponse->code;
      $response->body = $apiResponse->body;
      $response->tracker = self::trackerFetch();
      $response->count = count( $productIds );
      return $response;

    }

    public static function trackerFetch() {
      $trackerKey = 'custobar_export_product';
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
