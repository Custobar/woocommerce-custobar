<?php

namespace WooCommerceCustobar\DataType;

use WooCommerceCustobar\Fields_Map;
use WooCommerceCustobar\DataSource\Sale;

defined( 'ABSPATH' ) or exit;

/**
 * Class Custobar_Sale
 *
 * Check field descriptions here: https://www.custobar.com/api/docs/sales/
 *
 * @package WooCommerceCustobar\DataType
 */
class Custobar_Sale extends Custobar_Data_Type
{

	const SALE_EXTERNAL_ID    = 'sale_external_id';
	const SALE_DATE           = 'sale_date';
	const SALE_CUSTOMER_ID    = 'sale_customer_id';
	const SALE_DISCOUNT       = 'sale_discount';
	const SALE_PAYMENT_METHOD = 'sale_payment_method';
	const SALE_SHIPPING       = 'sale_shipping';
	const SALE_SHOP_ID        = 'sale_shop_id';
	const SALE_STATE          = 'sale_state';
	const SALE_TOTAL          = 'sale_total';
	const EXTERNAL_ID         = 'external_id';
	const PRODUCT_ID          = 'product_id';
	const QUANTITY            = 'quantity';
	const UNIT_PRICE          = 'unit_price';
	const DISCOUNT            = 'discount';
	const TOTAL               = 'total';
	const SALE_PHONE_NUMBER   = 'sale_phone_number';
	const SALE_EMAIL          = 'sale_email';

	/**
	 * Maps WC_Order and WC_Order_Item_Product objects' properties to match
	 * the ones used in Custobar.
	 *
	 * @param \WC_Order              $order
	 * @param \WC_Order_Item_Product $order_item
	 */
	public function __construct( $order, $order_item ) {
		parent::__construct();

		$this->dataSource = new Sale( $order, $order_item );
	}

	public static function getFieldsMap() {
		return Fields_Map::getSaleFields();
	}
}
