<?php

namespace WooCommerceCustobar\DataType;

defined( 'ABSPATH' ) || exit;

/**
 * Class Custobar_Data_Type
 *
 * Base class for all custobar datatypes.
 *
 * @package WooCommerceCustobar\DataType
 */
abstract class Custobar_Data_Type {


	protected static $defaultKeys = array();

	public function __construct() {
		static::$defaultKeys = static::get_default_keys();
	}

	public function get_assigned_properties() {
		 $dataSourceFields = $this->dataSource->get_fields();
		$fieldsMap         = static::get_fields_map();
		$properties        = array();

		foreach ( $fieldsMap as $custobarKey => $sourceKey ) {
			if ( ! in_array( $custobarKey, static::$defaultKeys, true ) ) {
				continue;
			}

			if ( ! array_key_exists( $sourceKey, $dataSourceFields ) ) {
				continue;
			}

			$methodOrFn = $dataSourceFields[ $sourceKey ];

			if ( ! is_callable( $methodOrFn ) && ! is_string( $methodOrFn ) ) {
				continue;
			}

			if ( is_string( $methodOrFn ) && method_exists( $this->dataSource, $methodOrFn ) ) {
				$methodOrFn = array( $this->dataSource, $methodOrFn );
			}

			$value = call_user_func( $methodOrFn, $this );

			if ( is_null( $value ) ) {
				continue;
			}

			$properties[ $custobarKey ] = $value;
		}

		return $properties;
	}

	abstract static function get_fields_map();

	protected static function get_default_keys() {
		$reflection = new \ReflectionClass( get_called_class() );
		return array_values( $reflection->getConstants() );
	}
}
