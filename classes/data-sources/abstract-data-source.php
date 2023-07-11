<?php

namespace WooCommerceCustobar\DataSource;

defined( 'ABSPATH' ) || exit;

abstract class Abstract_Data_Source {


	protected $default_keys = array();

	protected $fields = array();

	public static $source_key = 'common';

	protected static $custom_data_sources = array();



	public function __construct() {
		 $this->default_keys = static::get_default_keys();
	}

	protected static function get_default_keys() {
		$reflection = new \ReflectionClass( get_called_class() );
		return array_values( $reflection->getConstants() );
	}

	public function get_custom_keys() {
		 return array_keys( self::$custom_data_sources );
	}

	public function get_fields() {
		$keys          = $this->default_keys;
		$custom_keys   = static::get_custom_keys();
		$custom_fields = array();

		$fields = array_reduce(
			$keys,
			function ( $carry, $key ) {
				$method = "get_{$key}";

				if ( ! method_exists( $this, $method ) ) {
					return $carry;
				}

				$carry[ $key ] = $method;

				return $carry;
			},
			array()
		);

		if ( $custom_keys ) {
			$custom_fields = array_reduce(
				$custom_keys,
				function ( $carry, $key ) {
					$carry[ $key ] = array( 'get_custom_data_source', $key );
					return $carry;
				},
				array()
			);
		}

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

		if ( $custom_fields ) {
			$fields = array_merge( $fields, $custom_fields );
		}
		$fields = apply_filters( 'woocommerce_custobar_get_' . static::$source_key . '_fields', $fields, $this );

		return $fields;
	}

	public static function create_custom_data_source( string $name, callable $callback ) {
		self::$custom_data_sources[ $name ] = $callback;
	}
	public static function get_custom_data_source( $name ) {
		if ( array_key_exists( $name, self::$custom_data_sources ) ) {
			$custom_data_source = self::$custom_data_sources[ $name ];
			$callback           = $custom_data_source;
			if ( is_callable( $callback ) ) {
				return $custom_data_source;
			}
		}
		return null;
	}
}
