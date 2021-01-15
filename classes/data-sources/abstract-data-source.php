<?php

namespace WooCommerceCustobar\DataSource;

defined( 'ABSPATH' ) || exit;

abstract class Abstract_Data_Source {

	protected $default_keys = array();

	protected $fields = array();

	public static $source_key = 'common';

	public function __construct() {
		$this->default_keys = static::get_default_keys();
	}

	protected static function get_default_keys() {
		$reflection = new \ReflectionClass( get_called_class() );
		return array_values( $reflection->getConstants() );
	}

	public function get_fields() {
		$fields = array_reduce(
			$this->default_keys,
			function( $carry, $key ) {
				$method = static::get_method_by_key( $key );

				if ( ! method_exists( $this, $method ) ) {
					return $carry;
				}

				$carry[ $key ] = $method;

				return $carry;
			},
			array()
		);

		/**
		 * @param array $fields
		 * array(key => callback)
		 * key = predefined field key or user/dev defined key
		 * static::$source_key = product | sale | customer
		 *
		 * To customize the product fields
		 * add_filter('woocommerce_custobar_get_product_fields', function($fields, $CustobarProductInstance) {
		 *      // lets say our new key is - awesome_product_id and we want to add a callback
		 *      $fields['awesome_product_id'] = function() {
		 *      };
		 *
		 *      // lets say our old key is - product_id and we want to override the callback
		 *      $fields['product_id'] = function() {
		 *      };
		 *      return $fields;
		 * }, 10, 2);
		 */
		$fields = apply_filters( 'woocommerce_custobar_get_' . static::$source_key . '_fields', $fields, $this );

		return $fields;
	}

	protected static function get_method_by_key( $key ) {
		$key = str_replace( '_', ' ', $key );
		$key = ucwords( $key );
		$key = str_replace( ' ', '', $key );
		return "get{$key}";
	}
}
