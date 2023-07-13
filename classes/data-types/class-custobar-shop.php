<?php

namespace WooCommerceCustobar\DataType;

defined( 'ABSPATH' ) || exit;

/**
 * Class Custobar_Shop
 *
 * @package WooCommerceCustobar\DataType
 */
class Custobar_Shop extends Custobar_Data_Type {


	const EXTERNAL_ID  = 'external_id';
	const NAME         = 'name';
	const EMAIL        = 'email';
	const SHOP_TYPE    = 'shop_type';
	const PHONE_NUMBER = 'phone_number';

	protected $external_id;
	protected $name;
	protected $email;
	protected $shop_type;
	protected $phone_number;

	public static function get_fields_map() {
		return array();
	}

	public function get_assigned_properties() {
		return $this->get_assigned_properties_base();
	}
}
