<?php

namespace WooCommerceCustobar\DataType;

defined( 'ABSPATH' ) or exit;

/**
 * Class CustobarEvent
 *
 * @package WooCommerceCustobar\DataType
 */
class CustobarEvent extends AbstractCustobarDataType {


	const TYPE          = 'type';
	const DATE          = 'date';
	const CUSTOMER_ID   = 'customer_id';
	const PRODUCT_ID    = 'product_id';
	const MAILING_LISTS = 'mailing_lists';

	protected $type;
	protected $date;
	protected $customer_id;
	protected $product_id;
	protected $mailing_lists;

	public function __construct() {
	}

	public static function getFieldsMap() {
		return array();
	}
}
