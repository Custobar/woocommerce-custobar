<?php

namespace WooCommerceCustobar\DataSource;

defined('ABSPATH') or exit;

abstract class AbstractDataSource
{
    protected $defaultKeys = array();

    protected $fields = array();

    public static $sourceKey = 'common';

    public function __construct()
    {
        $this->defaultKeys = static::getDefaultKeys();
    }

    protected static function getDefaultKeys()
    {
        $reflection = new \ReflectionClass(get_called_class());
        return array_values($reflection->getConstants());
    }

    public function getFields()
    {
        $fields = array_reduce($this->defaultKeys, function($carry, $key) {
            $method = static::getMethodByKey($key);

            if (!method_exists($this, $method))
            {
                return $carry;
            }

            $carry[$key] = $method;

            return $carry;
        }, array());

        return apply_filters('woocommerce_custobar_get_'. static::$sourceKey . '_fields', $fields, $this);
    }

    protected static function getMethodByKey($key)
    {
        $key = str_replace('_', ' ', $key);
        $key = ucwords($key);
        $key = str_replace(' ', '', $key);
        return "get{$key}";
    }
}
