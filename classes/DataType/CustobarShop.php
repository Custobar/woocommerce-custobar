<?php

namespace WooCommerceCustobar\DataType;

defined('ABSPATH') or exit;

/**
 * Class CustobarShop
 *
 * @package WooCommerceCustobar\DataType
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
