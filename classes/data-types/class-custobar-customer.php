<?php

namespace WooCommerceCustobar\DataType;

use WooCommerceCustobar\Fields_Map;
use WooCommerceCustobar\DataSource\Customer;

defined( 'ABSPATH' ) || exit;

/**
 * Class Custobar_Customer
 *
 * Check field descriptions here: https://www.custobar.com/api/docs/customers/
 *
 * @package WooCommerceCustobar\DataType
 */
class Custobar_Customer extends Custobar_Data_Type {


	const EXTERNAL_ID    = 'external_id';
	const EMAIL          = 'email';
	const PHONE_NUMBER   = 'phone_number';
	const CANONICAL_ID   = 'canonical_id';
	const FIRST_NAME     = 'first_name';
	const LAST_NAME      = 'last_name';
	const CAN_EMAIL      = 'can_email';
	const CAN_POST       = 'can_post';
	const CAN_PROFILE    = 'can_profile';
	const CAN_SMS        = 'can_sms';
	const DATE_JOINED    = 'date_joined';
	const MAILING_LISTS  = 'mailing_lists';
	const STREET_ADDRESS = 'street_address';
	const ZIP_CODE       = 'zip_code';
	const STATE          = 'state';
	const CITY           = 'city';
	const COUNTRY        = 'country';
	const LANGUAGE       = 'language';
	const COMPANY        = 'company';
	const VAT_NUMBER     = 'vat_number';
	const LAST_LOGIN     = 'last_login';

	/**
	 * Maps the customer properties found in the WC_Order object to match
	 * the ones used in Custobar.
	 *
	 * @param \WC_Order $order
	 */
	public function __construct( $order ) {
		parent::__construct();

		$this->dataSource = new Customer( $order );
	}

	public static function get_fields_map() {
		return Fields_Map::get_customer_fields();
	}
}
