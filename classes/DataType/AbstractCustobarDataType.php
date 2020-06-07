<?php

namespace WooCommerceCustobar\DataType;

defined('ABSPATH') or exit;

/**
 * Class AbstractCustobarDataType
 *
 * Base class for all custobar datatypes.
 *
 * @package WooCommerceCustobar\DataType
 */
abstract class AbstractCustobarDataType
{
    protected static $defaultKeys = array();

    public function __construct()
    {
        static::$defaultKeys = static::getDefaultKeys();
    }

    public function getAssignedProperties()
    {
        $dataSourceFields = $this->dataSource->getFields();
        $fieldsMap = static::getFieldsMap();
        $properties = array();

        foreach ($fieldsMap as $custobarKey => $sourceKey)
        {
            if (!in_array($custobarKey, static::$defaultKeys, true))
            {
                continue;
            }

            if (!array_key_exists($sourceKey, $dataSourceFields))
            {
                continue;
            }

            $methodOrFn = $dataSourceFields[$sourceKey];

            if (!is_callable($methodOrFn) && !is_string($methodOrFn)) {
                continue;
            }

            if (is_string($methodOrFn) && method_exists($this->dataSource, $methodOrFn))
            {
                $methodOrFn = array($this->dataSource, $methodOrFn);
            }

            $value = call_user_func($methodOrFn, $this);

            if (is_null($value))
            {
                continue;
            }

            $properties[$custobarKey] = $value;
        }

        return $properties;
    }

    abstract static function getFieldsMap();

    protected static function getDefaultKeys()
    {
        $reflection = new \ReflectionClass(get_called_class());
        return array_values($reflection->getConstants());
    }
}
