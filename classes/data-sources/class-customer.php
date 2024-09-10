<?php

namespace WooCommerceCustobar\DataSource;

use WooCommerceCustobar\DataType\Utilities;

defined( 'ABSPATH' ) || exit;

class Customer extends Abstract_Data_Source {


	const ID             = 'id';
	const FIRST_NAME     = 'first_name';
	const LAST_NAME      = 'last_name';
	const EMAIL          = 'email';
	const PHONE          = 'phone';
	const COMPANY        = 'company';
	const STREET_ADDRESS = 'street_address';
	const CITY           = 'city';
	const ZIP_CODE       = 'postcode';
	const STATE          = 'state';
	const COUNTRY        = 'country';
	const DATE_JOINED    = 'date_joined';
	const CAN_SMS        = 'can_sms';
	const CAN_EMAIL      = 'can_email';

	public static $source_key = 'customer';

	/**
	 * Maps the customer properties found in the WC_Customer object to match
	 * the ones used in Custobar.
	 *
	 * @param \WC_Customer $customer
	 */
	public function __construct( $customer ) {
		parent::__construct();

		$this->customer = $customer;
	}

	public function get_id() {
		return $this->customer->get_id() ? (string) $this->customer->get_id() : $this->customer->get_email();
	}

	public function get_first_name() {
		return $this->customer->get_first_name();
	}

	public function get_last_name() {
		return $this->customer->get_last_name();
	}

	public function get_email() {
		return $this->customer->get_email();
	}

	public function get_phone() {
		return $this->customer->get_billing_phone();
	}

	public function get_company() {
		return $this->customer->get_billing_company();
	}

	public function get_city() {
		return $this->customer->get_billing_city();
	}

	public function get_postcode() {
		return $this->customer->get_billing_postcode();
	}

	public function get_state() {
		return $this->customer->get_billing_state();
	}

	public function get_country() {
		return $this->customer->get_billing_country();
	}

	public function get_can_email() {
		if ( ! metadata_exists( 'user', $this->customer->get_id(), '_woocommerce_custobar_can_email' ) ) {
			return null;
		}
		$can_email = get_user_meta( $this->customer->get_id(), '_woocommerce_custobar_can_email', true );
		if ( $can_email ) {
			return true;
		}
		return false;
	}

	public function get_can_sms() {
		if ( ! metadata_exists( 'user', $this->customer->get_id(), '_woocommerce_custobar_can_sms' ) ) {
			return null;
		}
		$can_sms = get_user_meta( $this->customer->get_id(), '_woocommerce_custobar_can_sms', true );
		if ( $can_sms ) {
			return true;
		}
		return false;
	}

	public function get_street_address() {
		$address           = $this->customer->get_billing_address_1();
		$billing_address_2 = $this->customer->get_billing_address_2();
		if ( $billing_address_2 ) {
			$address .= '\n' . $billing_address_2;
		}
		return $address;
	}

	/**
	 * Get the date user registered
	 *
	 * @return \DateTime
	 */
	public function get_date_joined() {
		$userdata = get_userdata( $this->customer->get_id() );
		if ( $userdata ) {
			$date = $userdata->user_registered;
			return Utilities::format_datetime( new \DateTime( $date ) );
		} else {
			$date_info = $this->customer->get_date_created();
			$datetime = new \DateTime();
			$datetime->setTimestamp($date_info->getTimestamp());
			return Utilities::format_datetime( $datetime );
		}
	}
}
