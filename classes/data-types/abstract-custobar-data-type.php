<?php

namespace WooCommerceCustobar\DataType;

defined('ABSPATH') || exit;

/**
 * Class Custobar_Data_Type
 *
 * Base class for all custobar datatypes.
 *
 * @package WooCommerceCustobar\DataType
 */
abstract class Custobar_Data_Type
{


	protected static $default_keys = array();

	public function __construct()
	{
		static::$default_keys = array_keys(static::get_fields_map());
	}

	public function get_assigned_properties()
	{
		$data_source_fields = $this->data_source->get_fields();
		$fields_map = static::get_fields_map();
		$properties = array();

		foreach ($fields_map as $custobar_key => $source_key) {
			if (!in_array($custobar_key, static::$default_keys, true)) {
				continue;
			}

			if (!array_key_exists($source_key, $data_source_fields)) {
				continue;
			}

			$method_or_fn = $data_source_fields[$source_key];

			if (!is_callable($method_or_fn) && !is_string($method_or_fn)) {
				continue;
			}

			if (is_string($method_or_fn) && method_exists($this->data_source, $method_or_fn)) {
				$method_or_fn = array($this->data_source, $method_or_fn);
			}

			$value = call_user_func($method_or_fn, $this);

			if (is_null($value)) {
				continue;
			}

			$properties[$custobar_key] = $value;
		}


		return $properties;
	}

	abstract public static function get_fields_map();

	protected static function get_default_keys()
	{
		$reflection = new \ReflectionClass(get_called_class());
		return array_values($reflection->getConstants());
	}
}