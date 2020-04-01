<?php

namespace WooCommerceCustobar\DataType;

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
    protected $nin;
    protected $external_id;
    protected $phone_number;
    protected $canonical_id;
    protected $first_name;
    protected $can_email;
    protected $can_post;
    protected $can_profile;
    protected $can_sms;
    protected $date_joined;
    protected $mailing_lists;
    protected $street_address;
    protected $zip_code;
    protected $state;
    protected $city;
    protected $country;
    protected $language;
    protected $company;
    protected $vat_number;
    protected $last_login;

    /**
     * Maps the customer properties found in the WC_Order object to match
     * the ones used in Custobar.
     *
     * @param WC_Order $order
     */
    public function __construct($order)
    {
        $this->nin            = null;
        $this->external_id    = ($order->get_user_id()) ? (string)$order->get_user_id() : null;
        $this->phone_number   = $order->get_billing_phone();
        $this->email          = $order->get_billing_email();
        $this->canonical_id   = null;
        $this->first_name     = $order->get_billing_first_name();
        $this->last_name      = $order->get_billing_last_name();
        $this->can_email      = $this->getCanEmail($order);
        $this->can_post       = null;
        $this->can_profile    = null;
        $this->can_sms        = $this->getCanSms($order);
        $this->date_joined    = $this->getDateJoined($order->get_user_id());
        $this->mailing_lists  = null;
        $this->street_address = $this->getStreetAddress($order);
        $this->zip_code       = $order->get_billing_postcode();
        $this->state          = $order->get_billing_state();
        $this->city           = $order->get_billing_city();
        $this->country        = $order->get_billing_country();
        $this->language       = null;
        $this->company        = $order->get_billing_company();
        $this->vat_number     = null;
        $this->last_login     = null;
    }

    protected function getCanEmail($order)
    {
        $can_email = get_post_meta($order->get_id(), '_woocommerce_custobar_can_email', true);
        if ($can_email) {
            return true;
        }

        // Never return false as it would override the current value in
        // Custobar. We don't offer functionality to remove the permission.
        return null;
    }

    protected function getCanSms($order)
    {
        $can_sms = get_post_meta($order->get_id(), '_woocommerce_custobar_can_sms', true);
        if ($can_sms) {
            return true;
        }

        // Never return false as it would override the current value in
        // Custobar. We don't offer functionality to remove the permission.
        return null;
    }

    protected function getStreetAddress($order)
    {
        $address = $order->get_billing_address_1();
        if ($billing_address_2 = $order->get_billing_address_2()) {
            $address .= ' ' . $billing_address_2;
        }
        return $address;
    }

    protected function getDateJoined($user_id)
    {
        if (!$user_id) {
            return null;
        }
        $user_data = get_userdata($user_id);
        $registered_time = $user_data->user_registered;
        $registered_time = new \DateTime($registered_time);
        $formatted_time = Utilities::formatDateTime($registered_time);
        return $formatted_time;
    }
}
