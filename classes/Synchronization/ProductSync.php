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
      $variantOffset = $tracker['variant_offset'];
      $productList = [];
      $variantList = [];

      if ($variantOffset == 0) {

        $products = wc_get_products([
          'limit'   => $limit,
          'offset'  => $offset,
          'orderby' => 'ID',
          'order'   => 'ASC'
        ]);

        foreach ( $products as $product ) {
          $productList[] = self::formatSingleItem($product);
        }

      }

      $count = count( $productList );
      $offset += $count;

      # Fetch variants
      if ($count < $limit) {

        $variants = wc_get_products([
          'type' => 'variation',
          'limit'   => $limit,
          'offset'  => $variantOffset,
          'orderby' => 'ID',
          'order'   => 'ASC'
        ]);

        foreach ( $variants as $variant ) {

          $variantList[] = self::formatSingleVariant($variant);

        }

        $count = count( $variantList );
        $variantOffset += $count;

        $productList = array_merge($productList, $variantList);
      }

      // no products
      if( empty( $productList )) {
        $response->code = 220;
        return $response;
      }

      $apiResponse = self::uploadDataTypeData($productList);

      self::trackerSave( $offset, $variantOffset );

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
      if( !isset($tracker['variant_offset']) ) {
        $tracker['variant_offset'] = 0;
      }
      if( !isset($tracker['updated']) ) {
        $tracker['updated'] = false;
      }
      return $tracker;
    }

    public static function trackerSave( $offset, $variantOffset, $total=null, $variantTotal=null ) {
      $tracker = self::trackerFetch();
      if (isset($offset) && isset($variantOffset)) {
        $tracker['offset'] = $offset;
        $tracker['variant_offset'] = $variantOffset;
        $tracker['updated'] = time();
      }
      if (isset($total) && isset($variantTotal)) {
        $tracker['total'] = $total;
        $tracker['variant_total'] = $variantTotal;
      }
      update_option('custobar_export_product', $tracker);
    }

    protected static function formatSingleItem($product)
    {
        $custobar_product = new CustobarProduct($product);
        $properties = $custobar_product->getAssignedProperties();
        return apply_filters('woocommerce_custobar_product_properties', $properties, $product);
    }

    protected static function formatSingleVariant($variant)
    {
        $custobar_product = new CustobarProduct($variant);
        $properties = $custobar_product->getAssignedProperties();
        $properties['main_product_ids'] = [$variant->get_parent_id()];
        return apply_filters('woocommerce_custobar_product_properties', $properties, $variant);
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
