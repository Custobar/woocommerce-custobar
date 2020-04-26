<?php

namespace WooCommerceCustobar\DataSource;

defined('ABSPATH') or exit;

abstract class AbstractDataSource
{
    protected static $allowedKeys = array();

    protected static function getValidKeys()
    {
        if (empty(static::$allowedKeys)) {
            $reflection = new \ReflectionClass(get_called_class());
            static::$allowedKeys = array_values($reflection->getConstants());
        }

        return static::$allowedKeys;
    }

    protected static function isValidKey($key)
    {
        return in_array($key, static::getValidKeys(), true);
    }

    public function getValue($key)
    {
        $method = static::getMethodByKey($key);

        if (!static::isValidKey($key) || !method_exists($this, $method))
        {
            return null;
        }

        return $this->{$method}();
    }

    protected static function getMethodByKey($key)
    {
        $key = str_replace('_', ' ', $key);
        $key = ucwords($key);
        $key = str_replace(' ', '', $key);
        return "get{$key}";
    }
}
