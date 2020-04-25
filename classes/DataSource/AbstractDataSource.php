<?php

namespace WooCommerceCustobar\DataSource;

defined('ABSPATH') or exit;

abstract class AbstractDataSource
{

    protected static $allowedKeys = array();

    public static function getValidKeys()
    {
        if (empty(static::$allowedKeys)) {
            $reflection = new \ReflectionClass(get_called_class());
            static::$allowedKeys = array_values($reflection->getConstants());
        }

        return static::$allowedKeys;
    }

    public static function isValidKey($key)
    {
        return in_array($key, static::getValidKeys(), true);
    }
}
