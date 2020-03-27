<?php

namespace Sofokus\WooCommerceCustobar\Synchronization;

defined('ABSPATH') or exit;

use Sofokus\WooCommerceCustobar\DataUpload;

/**
 * Class AbstractDataSync
 *
 * @package Sofokus\WooCommerceCustobar\Synchronization
 */
abstract class AbstractDataSync
{
    abstract public static function singleUpdate($item_id);
    abstract public static function batchUpdate();
    abstract protected static function formatSingleItem($item);
    abstract protected static function uploadDataTypeData($data);

    protected static function uploadCustobarData($data)
    {
        $url = sprintf(static::$endpoint, WOOCOMMERCE_CUSTOBAR_API_PREFIX);
        DataUpload::uploadCustobarData($url, $data);
    }
}
