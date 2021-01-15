<?php

namespace WooCommerceCustobar\DataSource;

use WooCommerceCustobar\DataType\Utilities;

defined( 'ABSPATH' ) || exit;

class Sale extends Abstract_Data_Source {


	const ORDER_ID             = 'order_id';
	const ORDER_NUMBER         = 'order_number';
	const ORDER_DATE           = 'order_date';
	const TOTAL                = 'total';
	const ORDER_TOTAL          = 'order_total';
	const CUSTOMER_ID          = 'customer_id';
	const CUSTOMER_PHONE       = 'customer_phone';
	const CUSTOMER_EMAIL       = 'customer_email';
	const PRODUCT_ID           = 'product_id';
	const QUANTITY             = 'quantity';
	const PRICE                = 'price';
	const TOTAL_DISCOUNT       = 'total_discount';
	const SALE_SHIPPING        = 'sale_shipping';
	const PAYMENT_METHOD_TITLE = 'payment_method_title';
	const STATUS               = 'status';

	public static $sourceKey = 'sale';

	public function __construct( $order, $order_item ) {
		parent::__construct();

		$this->order      = $order;
		$this->order_item = $order_item;
	}

	public function get_order_id() {
		return (string) $this->order_item->get_id();
	}

	public function get_order_number() {
		return $this->order->get_order_number();
	}

	public function get_order_date() {
		return Utilities::format_datetime( $this->order->get_date_created() );
	}

	public function get_total() {
		return Utilities::get_price_in_cents( $this->order_item->get_total() );
	}

	public function get_order_total() {
		 return Utilities::get_price_in_cents( $this->order->get_total() );
	}

	public function get_customer_id() {
		 return ( $this->order->get_user_id() ) ? (string) $this->order->get_user_id() : null;
	}

	public function get_customer_phone() {
		return $this->order->get_billing_phone();
	}

	public function get_customer_email() {
		return $this->order->get_billing_email();
	}

	public function get_product_id() {
		$variation_id = $this->order_item->get_variation_id();

		if ( isset( $variation_id ) && $variation_id ) {
			return $variation_id;
		} else {
			return $this->order_item->get_product_id();
		}
	}

	public function get_quantity() {
		return $this->order_item->get_quantity();
	}

	public function get_price() {
		return Utilities::get_price_in_cents( $this->order_item->get_total() / $this->order_item->get_quantity() );
	}

	public function get_total_discount() {
		return Utilities::get_price_in_cents( $this->order->get_total_discount() );
	}

	public function get_sale_shipping() {
		return Utilities::get_price_in_cents( $this->order->get_shipping_total() );
	}

	public function get_status() {
		return strtoupper( $this->order->get_status() );
	}

	public function get_payment_method_title() {
		return $this->order->get_payment_method_title();
	}
}
