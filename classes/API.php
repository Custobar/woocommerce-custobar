<?php

namespace Sofokus\WooCommerceCustobar;

defined('ABSPATH') or exit;

use Sofokus\WooCommerceCustobar\DataUpload;

/**
 * Class API
 *
 * @package Sofokus\WooCommerceCustobar
 */
class API
{
    public static function createCustobarCustomer($fields)
    {
        $url = sprintf('https://%s.custobar.com/api/customers/upload/', WOOCOMMERCE_CUSTOBAR_API_PREFIX);

        $data = array(
            'customers' => array()
        );
        $data['customers'][] = $fields;  // TODO: validate that given in a proper form and with proper field values

        DataUpload::uploadCustobarData($url, $data);
    }
}
