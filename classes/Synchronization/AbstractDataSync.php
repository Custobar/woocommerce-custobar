<?php

namespace WooCommerceCustobar\Synchronization;

defined('ABSPATH') or exit;

use WooCommerceCustobar\DataUpload;

/**
 * Class AbstractDataSync
 *
 * @package WooCommerceCustobar\Synchronization
 */
abstract class AbstractDataSync
{
    abstract public static function singleUpdate($item_id);
    abstract public static function batchUpdate();
    abstract protected static function formatSingleItem($item);
    abstract protected static function uploadDataTypeData($data);

    protected static function uploadCustobarData($data)
    {
      $endpoint = static::$endpoint;
      DataUpload::uploadCustobarData($endpoint, $data);
    }
}
