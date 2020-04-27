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
    CONST EXTERNAL_ID = 'external_id';
    CONST NAME = 'name';
    CONST EMAIL = 'email';
    CONST SHOP_TYPE = 'shop_type';
    CONST PHONE_NUMBER = 'phone_number';

    protected $external_id;
    protected $name;
    protected $email;
    protected $shop_type;
    protected $phone_number;

    public function __construct()
    {
    }

    public static function getFieldsMap()
    {
        return array();
    }
}
