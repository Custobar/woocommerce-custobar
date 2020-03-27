<?php

namespace Sofokus\WooCommerceCustobar\DataType;

defined('ABSPATH') or exit;

/**
 * Class AbstractCustobarDataType
 *
 * Base class for all custobar datatypes.
 *
 * @package Sofokus\WooCommerceCustobar\DataType
 */
abstract class AbstractCustobarDataType
{
    public function getAssignedProperties()
    {
        $not_null_values = array();
        foreach ($this as $key => $value) {
            if (!is_null($value)) {
                $not_null_values[$key] = $value;
            }
        }
        return $not_null_values;
    }
}
