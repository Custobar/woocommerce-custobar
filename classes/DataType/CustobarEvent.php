<?php

namespace WooCommerceCustobar\DataType;

defined('ABSPATH') or exit;

/**
 * Class CustobarEvent
 *
 * @package WooCommerceCustobar\DataType
 */
class CustobarEvent extends AbstractCustobarDataType
{
    protected $type;
    protected $date;
    protected $customer_id;
    protected $product_id;
    protected $mailing_lists;

    public function __construct()
    {
    }
}
