<?php

namespace WooCommerceCustobar\DataType;

use WooCommerceCustobar\FieldsMap;
use WooCommerceCustobar\DataSource\Customer;

defined('ABSPATH') or exit;

/**
 * Class CustobarCustomer
 *
 * Check field descriptions here: https://www.custobar.com/api/docs/customers/
 *
 * @package WooCommerceCustobar\DataType
 */
class CustobarCustomer extends AbstractCustobarDataType
{
    const EXTERNAL_ID = 'external_id';
    const EMAIL = 'email';
    CONST PHONE_NUMBER = 'phone_number';
    CONST CANONICAL_ID = 'canonical_id';
    CONST FIRST_NAME = 'first_name';
    CONST LAST_NAME = 'last_name';
    CONST CAN_EMAIL = 'can_email';
    CONST CAN_POST = 'can_post';
    CONST CAN_PROFILE = 'can_profile';
    CONST CAN_SMS = 'can_sms';
    CONST DATE_JOINED = 'date_joined';
    CONST MAILING_LISTS = 'mailing_lists';
    CONST STREET_ADDRESS = 'street_address';
    CONST ZIP_CODE = 'zip_code';
    CONST STATE = 'state';
    CONST CITY = 'city';
    CONST COUNTRY = 'country';
    CONST LANGUAGE = 'language';
    CONST COMPANY = 'company';
    CONST VAT_NUMBER = 'vat_number';
    CONST LAST_LOGIN = 'last_login';

    /**
     * Maps the customer properties found in the WC_Order object to match
     * the ones used in Custobar.
     *
     * @param \WC_Order $order
     */
    public function __construct($order)
    {
        parent::__construct();

        $this->dataSource = new Customer($order);
    }

    public static function getFieldsMap() {
        return FieldsMap::getCustomerFields();
    }
}
