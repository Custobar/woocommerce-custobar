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

    CONST TYPE = 'type';
    CONST DATE = 'date';
    CONST CUSTOMER_ID = 'customer_id';
    CONST PRODUCT_ID = 'product_id';
    CONST MAILING_LISTS = 'mailing_lists';

    protected $type;
    protected $date;
    protected $customer_id;
    protected $product_id;
    protected $mailing_lists;

    public function __construct()
    {
    }
}
