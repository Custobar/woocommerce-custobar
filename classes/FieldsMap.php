<?php

namespace WooCommerceCustobar;

defined('ABSPATH') or exit;

class FieldsMap {
	
	/**
	 * Get custobar to woocommer fields map
	 *
	 * @param string $fieldGroup
	 * @return array
	 */
	public static function getDefaults( $fieldGroup = 'product' ) {
		$fieldsGroups = array(
			/**
			 * Customer fields map
			 * 
			 * custobar => woocommerce
			 */
			'customer' => array(
				'nin'            => null,
				'external_id'    => 'user_id',
				'phone_number'   => 'billing_phone',
				'canonical_id'   => null,
				'first_name'     => 'billing_first_name',
				'last_name'      => 'billing_last_name',
				'can_email'      => 'can_email',
				'can_post'       => null,
				'can_profile'    => null,
				'can_sms'        => 'can_sms',
				'date_joined'    => 'date_joined',
				'mailing_lists'  => null,
				'street_address' => 'street_address',
				'zip_code'       => 'zip_code',
				'state'          => 'state',
				'city'           => 'city',
				'country'        => 'country',
				'language'       => 'null',
				'company'        => 'company',
				'vat_number'     => null,
				'last_login'     => null,
			),

			/**
             * Prduct fields map
             * 
             * custobar => woocommerce
             */
			'product' => array(
				'external_id'                  => 'product_id',
				'price'                        => 'price',
				'type'                         => 'type',
				'category'                     => 'category',
				'category_id'                  => 'category_ids',
				'vendor'                       => null,
				'brand'                        => null,
				'title'                        => 'title',
				'image'                        => 'image',
				'date'                         => 'date',
				'url'                          => 'url',
				'sale_price'                   => 'sale_price',
				'description'                  => 'description',
				'language'                     => null,
				'visible'                      => 'visible',
				'exclude_from_recommendations' => null,
				'unit'                         => 'unit',
				'weight'                       => 'weight',
			),

			/**
             * Sale/order fields map
             * 
             * custobar => woocommerce
             */
			'sale' => array(
				'sale_external_id'    => 'order_number',
				'sale_date'           => 'order_date',
				'sale_customer_id'    => 'customer_id',
				'sale_phone_number'   => 'customer_phone',
				'sale_email'          => 'customer_email',
				'sale_discount'       => 'total_discount',
				'sale_payment_method' => 'payment_method_title',
				'sale_shipping'       => 'sale_shipping',
				'sale_shop_id'        => null,
				'sale_state'          => null,
				'sale_total'          => 'total',
				'external_id'         => 'order_id',
				'product_id'          => 'product_id',
				'quantity'            => 'quantity',
				'unit_price'          => 'price',
				'discount'            => null,
				'total'               => 'total',
			),
		);

		return isset( $fieldsGroups[$fieldGroup] ) ? $fieldsGroups[$fieldGroup] : $fieldsGroups['product'];
	}
}
