<?php

namespace WooCommerceCustobar;

use WooCommerceCustobar\DataSource\Sale;
use WooCommerceCustobar\DataSource\Product;
use WooCommerceCustobar\DataSource\Customer;
use WooCommerceCustobar\DataType\CustobarSale as CBSale;
use WooCommerceCustobar\DataType\CustobarProduct as CBProduct;
use WooCommerceCustobar\DataType\CustobarCustomer as CBCustomer;

defined('ABSPATH') or exit;

class FieldsMap
{

    /**
     * Get custobar to woocommer fields map
     *
     * @param string $fieldGroup
     * @return array
     */
    public static function getDefaults($fieldGroup = 'all')
    {
        $groups = array(
            /**
             * Customer fields map
             *
             * custobar => woocommerce
             */
            'custobar_customer_fields' => array(
                CBCustomer::EXTERNAL_ID    => Customer::USER_ID,
                CBCustomer::FIRST_NAME     => Customer::BILLING_FIRST_NAME,
                CBCustomer::LAST_NAME      => Customer::BILLING_LAST_NAME,
                CBCustomer::PHONE_NUMBER   => Customer::BILLING_PHONE,
                CBCustomer::COMPANY        => Customer::COMPANY,
                CBCustomer::STREET_ADDRESS => Customer::STREET_ADDRESS,
                CBCustomer::CITY           => Customer::CITY,
                CBCustomer::ZIP_CODE       => Customer::ZIP_CODE,
                CBCustomer::STATE          => Customer::STATE,
                CBCustomer::COUNTRY        => Customer::COUNTRY,
                CBCustomer::DATE_JOINED    => Customer::DATE_JOINED,
                CBCustomer::CAN_SMS        => Customer::CAN_SMS,
                CBCustomer::CAN_EMAIL      => Customer::CAN_EMAIL,
                CBCustomer::NIN            => null,
                CBCustomer::CAN_POST       => null,
                CBCustomer::LANGUAGE       => null,
                CBCustomer::LAST_LOGIN     => null,
                CBCustomer::VAT_NUMBER     => null,
                CBCustomer::CAN_PROFILE    => null,
                CBCustomer::CANONICAL_ID   => null,
                CBCustomer::MAILING_LISTS  => null,
            ),

            /**
             * Prduct fields map
             *
             * custobar => woocommerce
             */
            'custobar_product_fields' => array(
                CBProduct::EXTERNAL_ID                  => Product::PRODUCT_ID,
                CBProduct::TITLE                        => Product::TITLE,
                CBProduct::DESCRIPTION                  => Product::DESCRIPTION,
                CBProduct::IMAGE                        => Product::IMAGE,
                CBProduct::TYPE                         => Product::TYPE,
                CBProduct::WEIGHT                       => Product::WEIGHT,
                CBProduct::UNIT                         => Product::UNIT,
                CBProduct::PRICE                        => Product::PRICE,
                CBProduct::SALE_PRICE                   => Product::SALE_PRICE,
                CBProduct::CATEGORY                     => Product::CATEGORY,
                CBProduct::CATEGORY_ID                  => Product::CATEGORY_IDS,
                CBProduct::DATE                         => Product::DATE,
                CBProduct::URL                          => Product::URL,
                CBProduct::VISIBLE                      => Product::VISIBLE,
                CBProduct::BRAND                        => null,
                CBProduct::VENDOR                       => null,
                CBProduct::LANGUAGE                     => null,
                CBProduct::EXCLUDE_FROM_RECOMMENDATIONS => null,
            ),

            /**
             * Sale/order fields map
             *
             * custobar => woocommerce
             */
            'custobar_sale_fields' => array(
                CBSale::EXTERNAL_ID         => Sale::ORDER_ID,
                CBSale::SALE_EXTERNAL_ID    => Sale::ORDER_NUMBER,
                CBSale::SALE_DATE           => Sale::ORDER_DATE,
                CBSale::TOTAL               => Sale::TOTAL,
                CBSale::SALE_TOTAL          => Sale::ORDER_TOTAL,
                CBSale::SALE_CUSTOMER_ID    => Sale::CUSTOMER_ID,
                CBSale::SALE_PHONE_NUMBER   => Sale::CUSTOMER_PHONE,
                CBSale::SALE_EMAIL          => Sale::CUSTOMER_EMAIL,
                CBSale::PRODUCT_ID          => Sale::PRODUCT_ID,
                CBSale::QUANTITY            => Sale::QUANTITY,
                CBSale::UNIT_PRICE          => Sale::PRICE,
                CBSale::SALE_DISCOUNT       => Sale::TOTAL_DISCOUNT,
                CBSale::SALE_SHIPPING       => Sale::SALE_SHIPPING,
                CBSale::SALE_PAYMENT_METHOD => Sale::PAYMENT_METHOD_TITLE,
                CBSale::DISCOUNT            => null,
                CBSale::SALE_STATE          => null,
                CBSale::SALE_SHOP_ID        => null,
            ),
        );

        /**
         * @param array $group
         * @param string $fieldGroup
         *
         */
        $groups = apply_filters('woocommerce_custobar_get_fields_map', $groups, $fieldGroup);

        return isset($groups[$fieldGroup]) ? $groups[$fieldGroup] : $groups;
    }

    /**
     * Prepare fields output for restore default butotn
     *
     * @return array
     */
    public static function getFieldsMapForFront()
    {
        $groups = self::getDefaults();
        $out = array();

        foreach ($groups as $key => $group)
        {
            foreach ($group as $ckey => $wkey)
            {
                if (is_null($wkey))
                {
                    $wkey = 'null';
                }

                if (isset($out[$key]))
                {
                    $out[$key] .= "{$ckey}: {$wkey}\n";
                } else {
                    $out[$key] = "{$ckey}: {$wkey}\n";
                }
            }
        }

        return $out;
    }

    /**
     * Get user defined product fields map
     *
     * @return array
     */
    public static function getProductFields()
    {
        return self::getSavedFields('custobar_product_fields');
    }

    /**
     * Get user defined sale fields map
     *
     * @return array
     */
    public static function getSaleFields()
    {
        return self::getSavedFields('custobar_sale_fields');
    }

    /**
     * Get user defined customer fields map
     *
     * @return array
     */
    public static function getCustomerFields()
    {
        return self::getSavedFields('custobar_customer_fields');
    }

    /**
     * Get processed fields map
     *
     * @param string $fieldsId
     * @return array
     */
    protected static function getSavedFields( $fieldsId )
    {
        $fieldsStr = get_option($fieldsId, '');
        $fieldsArr = explode(PHP_EOL, $fieldsStr);

        $out = array_reduce($fieldsArr, function($carry, $field) {
            $arr = explode(':', $field);

            // Make sure there is actually two keys
            if (count($arr) < 2)
            {
                return $carry;
            }

            // Trim white spaces around keys
            $loadKey = trim($arr[0]);
            $sourceKey = trim($arr[1]);

            // Set actual null for stirng null
            $carry[$loadKey] = $sourceKey !== 'null' ? $sourceKey : null;

            return $carry;
        }, []);

        // Remove all falsy fields e.g. null, false, empty string
        $out = array_filter($out);

        wc_get_logger()->info('getSavedFields, $out: ' . print_r($out,1), array(
          'source'        => 'woocommerce-custobar'
        ));

        return $out;
    }
}
