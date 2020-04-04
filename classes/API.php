<?php

namespace WooCommerceCustobar;

defined('ABSPATH') or exit;

use WooCommerceCustobar\DataUpload;

/**
 * Class API
 *
 * @package WooCommerceCustobar
 */
class API
{
    public static function createCustobarCustomer($fields)
    {
        $endpoint = '/customers/upload/';

        $data = array(
            'customers' => array()
        );
        $data['customers'][] = $fields;  // TODO: validate that given in a proper form and with proper field values

        DataUpload::uploadCustobarData($endpoint, $data);
    }
}
