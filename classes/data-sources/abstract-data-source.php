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

	/**
	 * Get all default keys from all extended classes
	 */
	protected static function get_all_default_keys() {
		$all_default_keys = array();

		$reflection = new \ReflectionClass( static::class );
		$base_class = self::get_base_class( $reflection );

		$child_classes = self::get_extended_classes( $base_class );

		foreach ( $child_classes as $child_class ) {
			$child_reflection = new \ReflectionClass( $child_class );

			if ( $child_reflection->hasMethod( 'get_default_keys' ) ) {
				$all_default_keys = array_merge( $all_default_keys, $child_reflection->getMethod( 'get_default_keys' )->invoke( null ) );
			}
		}

		return $all_default_keys;
	}

	private static function get_base_class( \ReflectionClass $reflection ): string {
		$parent = $reflection->getParentClass();
		if ( false === $parent ) {
			return $reflection->getName();
		} else {
			return self::get_base_class( $parent );
		}
	}

	private static function get_extended_classes( string $class ): array {
		$child_classes = array();
		$all_classes   = get_declared_classes();

		foreach ( $all_classes as $class_name ) {
			$class_reflection = new \ReflectionClass( $class_name );
			if ( $class_reflection->isSubclassOf( $class ) && ! $class_reflection->isAbstract() ) {
				$child_classes[] = $class_name;
				$child_classes   = array_merge( $child_classes, self::get_extended_classes( $class_name ) );
			}
		}

		return $child_classes;
	}

	public function get_fields() {
		$keys          = $this->default_keys;
		$custom_keys   = array_keys( self::$custom_data_sources );
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
		$all_default_keys = self::get_all_default_keys();
		$all_custom_keys  = array_keys( self::$custom_data_sources );

		if ( in_array( $name, $all_default_keys ) || in_array( $name, $all_custom_keys ) ) {
			throw new \Exception( 'Error: Custobar data source name "' . $name . '" is already in use.' );
		} else {
			self::$custom_data_sources[ $name ] = $callback;
		}
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
