<?php

namespace Sofokus\WooCommerceCustobar\DataType;

defined('ABSPATH') or exit;

/**
 * Class CustobarShop
 *
 * @package Sofokus\WooCommerceCustobar\DataType
 */
class CustobarShop extends AbstractCustobarDataType
{
    protected $external_id;
    protected $name;
    protected $email;
    protected $shop_type;
    protected $phone_number;

    public function __construct()
    {
    }
}
