<?php

namespace WooCommerceCustobar\DataSource;

use WooCommerceCustobar\DataType\Utilities;

defined('ABSPATH') or exit;

class Customer extends AbstractDataSource
{
    const USER_ID = 'user_id';
    const BILLING_FIRST_NAME = 'billing_first_name';
    const BILLING_LAST_NAME = 'billing_last_name';
    const BILLING_PHONE = 'billing_phone';
    const COMPANY = 'company';
    const STREET_ADDRESS = 'street_address';
    const CITY = 'city';
    const ZIP_CODE = 'zip_code';
    const STATE = 'state';
    const COUNTRY = 'country';
    const DATE_JOINED = 'date_joined';
    const CAN_SMS = 'can_sms';
    const CAN_EMAIL = 'can_email';

    public static $sourceKey = 'customer';

    /**
     * Maps the customer properties found in the WC_Order object to match
     * the ones used in Custobar.
     *
     * @param \WC_Order $order
     */
    public function __construct($order)
    {
        parent::__construct();

        $this->order = $order;
    }

    public function getUserId()
    {
        return ($this->order->get_user_id()) ? (string)$this->order->get_user_id() : null;
    }

    public function getBillingFirstName()
    {
        return $this->order->get_billing_first_name();
    }

    public function getBillingLastName()
    {
        return $this->order->get_billing_last_name();
    }

    public function getBillingPhone()
    {
        return $this->order->get_billing_phone();
    }

    public function getCompany()
    {
        return $this->order->get_billing_company();
    }

    public function getCity()
    {
        return $this->order->get_billing_city();
    }

    public function getZipCode()
    {
        return $this->order->get_billing_postcode();
    }

    public function getState()
    {
        return $this->order->get_billing_state();
    }

    public function getCountry()
    {
        return $this->order->get_billing_country();
    }

    public function getCanEmail()
    {
        $can_email = get_post_meta($this->order->get_id(), '_woocommerce_custobar_can_email', true);
        if ($can_email) {
            return true;
        }

        // Never return false as it would override the current value in
        // Custobar. We don't offer functionality to remove the permission.
        return null;
    }

    public function getCanSms()
    {
        $can_sms = get_post_meta($this->order->get_id(), '_woocommerce_custobar_can_sms', true);
        if ($can_sms) {
            return true;
        }

        // Never return false as it would override the current value in
        // Custobar. We don't offer functionality to remove the permission.
        return null;
    }

    public function getStreetAddress()
    {
        $address = $this->order->get_billing_address_1();
        if ($billing_address_2 = $this->order->get_billing_address_2()) {
            $address .= ' ' . $billing_address_2;
        }
        return $address;
    }

    public function getDateJoined()
    {
        $user_data = get_userdata($this->order->get_user_id());
        $registered_time = $user_data->user_registered;
        $registered_time = new \DateTime($registered_time);
        $formatted_time = Utilities::formatDateTime($registered_time);
        return $formatted_time;
    }
}
