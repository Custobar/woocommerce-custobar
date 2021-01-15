<?php

namespace WooCommerceCustobar\DataType;

defined( 'ABSPATH' ) or exit;

/**
 * Class Custobar_Event
 *
 * @package WooCommerceCustobar\DataType
 */
class Custobar_Event extends Custobar_Data_Type
{


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

	public static function getFieldsMap() {
		return array();
	}
}
