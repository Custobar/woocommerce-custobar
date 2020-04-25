<?php

namespace WooCommerceCustobar;

use WooCommerceCustobar\DataType\CustobarCustomer as Customer;
use WooCommerceCustobar\DataType\CustobarProduct as Product;
use WooCommerceCustobar\DataType\CustobarSale as Sale;

defined('ABSPATH') or exit;

class FieldsMap
{
    
    /**
     * Get custobar to woocommer fields map
     *
     * @param string $fieldGroup
     * @return array
     */
    public static function getDefaults($fieldGroup = 'product')
    {
        $groups = array(
            /**
             * Customer fields map
             * 
             * custobar => woocommerce
             */
            'customer' => array(
                Customer::EXTERNAL_ID    => 'user_id',
                Customer::FIRST_NAME     => 'billing_first_name',
                Customer::LAST_NAME      => 'billing_last_name',
                Customer::PHONE_NUMBER   => 'billing_phone',
                Customer::COMPANY        => 'company',
                Customer::STREET_ADDRESS => 'street_address',
                Customer::CITY           => 'city',
                Customer::ZIP_CODE       => 'zip_code',
                Customer::STATE          => 'state',
                Customer::COUNTRY        => 'country',
                Customer::DATE_JOINED    => 'date_joined',
                Customer::CAN_SMS        => 'can_sms',
                Customer::CAN_EMAIL      => 'can_email',
                Customer::NIN            => null,
                Customer::CAN_POST       => null,
                Customer::LANGUAGE       => null,
                Customer::LAST_LOGIN     => null,
                Customer::VAT_NUMBER     => null,
                Customer::CAN_PROFILE    => null,
                Customer::CANONICAL_ID   => null,
                Customer::MAILING_LISTS  => null,
            ),

            /**
             * Prduct fields map
             * 
             * custobar => woocommerce
             */
            'product' => array(
                Product::EXTERNAL_ID                  => 'product_id',
                Product::TITLE                        => 'title',
                Product::DESCRIPTION                  => 'description',
                Product::IMAGE                        => 'image',
                Product::TYPE                         => 'type',
                Product::WEIGHT                       => 'weight',
                Product::UNIT                         => 'unit',
                Product::PRICE                        => 'price',
                Product::SALE_PRICE                   => 'sale_price',
                Product::CATEGORY                     => 'category',
                Product::CATEGORY_ID                  => 'category_ids',
                Product::DATE                         => 'date',
                Product::URL                          => 'url',
                Product::VISIBLE                      => 'visible',
                Product::BRAND                        => null,
                Product::VENDOR                       => null,
                Product::LANGUAGE                     => null,
                Product::EXCLUDE_FROM_RECOMMENDATIONS => null,
            ),

            /**
             * Sale/order fields map
             * 
             * custobar => woocommerce
             */
            'sale' => array(
                Sale::EXTERNAL_ID         => 'order_id',
                Sale::SALE_EXTERNAL_ID    => 'order_number',
                Sale::SALE_DATE           => 'order_date',
                Sale::TOTAL               => 'total',
                Sale::SALE_TOTAL          => 'total',
                Sale::SALE_CUSTOMER_ID    => 'customer_id',
                Sale::SALE_PHONE_NUMBER   => 'customer_phone',
                Sale::SALE_EMAIL          => 'customer_email',
                Sale::PRODUCT_ID          => 'product_id',
                Sale::QUANTITY            => 'quantity',
                Sale::UNIT_PRICE          => 'price',
                Sale::SALE_DISCOUNT       => 'total_discount',
                Sale::SALE_SHIPPING       => 'sale_shipping',
                Sale::SALE_PAYMENT_METHOD => 'payment_method_title',
                Sale::DISCOUNT            => null,
                Sale::SALE_STATE          => null,
                Sale::SALE_SHOP_ID        => null,
            ),
        );

        return isset($groups[$fieldGroup]) ? $groups[$fieldGroup] : $groups;
    }

    /**
     * Prepare fields output for restore defualt butotn
     *
     * @return array
     */
    public static function getFieldsMapFroFront()
    {
        $groups = self::getDefaults( 'all' );
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
}
