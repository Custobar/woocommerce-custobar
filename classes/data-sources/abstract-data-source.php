<?php

namespace WooCommerceCustobar\DataSource;

defined( 'ABSPATH' ) or exit;

abstract class Abstract_Data_Source
{

	protected $defaultKeys = array();

	protected $fields = array();

	public static $sourceKey = 'common';

	public function __construct() {
		$this->defaultKeys = static::getDefaultKeys();
	}

	protected static function getDefaultKeys() {
		$reflection = new \ReflectionClass( get_called_class() );
		return array_values( $reflection->getConstants() );
	}

	public function getFields() {
		$fields = array_reduce(
			$this->defaultKeys,
			function( $carry, $key ) {
				$method = static::getMethodByKey( $key );

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
		 * static::$sourceKey = product | sale | customer
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
		$fields = apply_filters( 'woocommerce_custobar_get_' . static::$sourceKey . '_fields', $fields, $this );

		return $fields;
	}

	protected static function getMethodByKey( $key ) {
		$key = str_replace( '_', ' ', $key );
		$key = ucwords( $key );
		$key = str_replace( ' ', '', $key );
		return "get{$key}";
	}
}
