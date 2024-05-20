<?php

namespace WooCommerceCustobar\DataType;

use WC_Customer;
use WooCommerceCustobar\DataSource\Abstract_Data_Source;
use WooCommerceCustobar\DataSource\Customer;


defined( 'ABSPATH' ) || exit;

/**
 * Class Custobar_Data_Type
 *
 * Base class for all custobar datatypes.
 *
 * @package WooCommerceCustobar\DataType
 */
abstract class Custobar_Data_Type {



	protected static $default_keys = array();


	public function __construct() {
		 static::$default_keys = array_keys( static::get_fields_map() );
	}

	public function get_assigned_properties( $param = null ) {
		$data_source_fields = $this->data_source->get_fields();
		$fields_map         = static::get_fields_map();
		$meta_data_fields   = $param ? array_map(function ($meta) {
			return $meta->key;
		}, $param->get_meta_data()) : [];
		$properties         = array();

		foreach ( $fields_map as $custobar_key => $source_key ) {
			// Custom_data_source
			if ( is_array( $data_source_fields[ $source_key ] ) && 'get_custom_data_source' === $data_source_fields[ $source_key ][0] && $param ) {

				$method_or_fn           = $data_source_fields[ $source_key ];
					$custom_data_source = Abstract_Data_Source::get_custom_data_source( $method_or_fn[1] );
					$value              = call_user_func( $custom_data_source, $param );

				if ( is_null( $value ) ) {
					continue;
				}
				$properties[ $custobar_key ] = $value;
				continue;
			}
			// Custom_data_source ends

			if ($param && in_array($source_key, $meta_data_fields)) {
				$properties[ $custobar_key ] = $param->get_meta($source_key);
				continue;
			}

			if ( ! in_array( $custobar_key, static::$default_keys, true ) ) {
				continue;
			}

			if ( ! array_key_exists( $source_key, $data_source_fields ) ) {
				continue;
			}
			$method_or_fn = $data_source_fields[ $source_key ];

			if ( ! is_callable( $method_or_fn ) && ! is_string( $method_or_fn ) ) {
				continue;
			}

			if ( is_string( $method_or_fn ) && method_exists( $this->data_source, $method_or_fn ) ) {
				$method_or_fn = array( $this->data_source, $method_or_fn );
				$value        = call_user_func( $method_or_fn, $this );
			}
			if ( is_null( $value ) ) {
				continue;
			}
			$properties[ $custobar_key ] = $value;
		}
		return $properties;
	}

	abstract public static function get_fields_map();

	protected static function get_default_keys() {
		$reflection = new \ReflectionClass( get_called_class() );
		return array_values( $reflection->getConstants() );
	}
}
