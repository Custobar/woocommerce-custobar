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
class ProductSync extends AbstractDataSync
{
    protected static $endpoint = 'https://%s.custobar.com/api/products/upload/';

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

    public static function batchUpdate()
    {
        $products = [];
        foreach (wc_get_products(array('limit' => -1)) as $product) {
            $products[] = self::formatSingleItem($product);
        }
        self::uploadDataTypeData($products);
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
        self::uploadCustobarData($formatted_data);
    }
}
