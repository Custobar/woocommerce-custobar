<?php

namespace WooCommerceCustobar\DataType;

defined( 'ABSPATH' ) or exit;

/**
 * Class CustobarShop
 *
 * @package WooCommerceCustobar\DataType
 */
class CustobarShop extends AbstractCustobarDataType {

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

	public static function getFieldsMap() {
		return array();
	}
}
