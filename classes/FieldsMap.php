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
                Customer::NIN            => null,
                Customer::EXTERNAL_ID    => 'user_id',
                Customer::PHONE_NUMBER   => 'billing_phone',
                Customer::CANONICAL_ID   => null,
                Customer::FIRST_NAME     => 'billing_first_name',
                Customer::LAST_NAME      => 'billing_last_name',
                Customer::CAN_EMAIL      => 'can_email',
                Customer::CAN_POST       => null,
                Customer::CAN_PROFILE    => null,
                Customer::CAN_SMS        => 'can_sms',
                Customer::DATE_JOINED    => 'date_joined',
                Customer::MAILING_LISTS  => null,
                Customer::STREET_ADDRESS => 'street_address',
                Customer::ZIP_CODE       => 'zip_code',
                Customer::STATE          => 'state',
                Customer::CITY           => 'city',
                Customer::COUNTRY        => 'country',
                Customer::LANGUAGE       => 'null',
                Customer::COMPANY        => 'company',
                Customer::VAT_NUMBER     => null,
                Customer::LAST_LOGIN     => null,
            ),

            /**
             * Prduct fields map
             * 
             * custobar => woocommerce
             */
            'product' => array(
                Product::EXTERNAL_ID                  => 'product_id',
                Product::PRICE                        => 'price',
                Product::TYPE                         => 'type',
                Product::CATEGORY                     => 'category',
                Product::CATEGORY_ID                  => 'category_ids',
                Product::VENDOR                       => null,
                Product::BRAND                        => null,
                Product::TITLE                        => 'title',
                Product::IMAGE                        => 'image',
                Product::DATE                         => 'date',
                Product::URL                          => 'url',
                Product::SALE_PRICE                   => 'sale_price',
                Product::DESCRIPTION                  => 'description',
                Product::LANGUAGE                     => null,
                Product::VISIBLE                      => 'visible',
                Product::UNIT                         => 'unit',
                Product::WEIGHT                       => 'weight',
                Product::EXCLUDE_FROM_RECOMMENDATIONS => null,
            ),

            /**
             * Sale/order fields map
             * 
             * custobar => woocommerce
             */
            'sale' => array(
                Sale::SALE_EXTERNAL_ID    => 'order_number',
                Sale::SALE_DATE           => 'order_date',
                Sale::SALE_CUSTOMER_ID    => 'customer_id',
                Sale::SALE_PHONE_NUMBER   => 'customer_phone',
                Sale::SALE_EMAIL          => 'customer_email',
                Sale::SALE_DISCOUNT       => 'total_discount',
                Sale::SALE_PAYMENT_METHOD => 'payment_method_title',
                Sale::SALE_SHIPPING       => 'sale_shipping',
                Sale::SALE_SHOP_ID        => null,
                Sale::SALE_STATE          => null,
                Sale::SALE_TOTAL          => 'total',
                Sale::EXTERNAL_ID         => 'order_id',
                Sale::PRODUCT_ID          => 'product_id',
                Sale::QUANTITY            => 'quantity',
                Sale::UNIT_PRICE          => 'price',
                Sale::DISCOUNT            => null,
                Sale::TOTAL               => 'total',
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
