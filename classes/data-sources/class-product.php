<?php

namespace WooCommerceCustobar\DataSource;

use WooCommerceCustobar\DataType\Utilities;

defined( 'ABSPATH' ) || exit;

class Product extends Abstract_Data_Source {

	const PRODUCT_ID   = 'product_id';
	const TITLE        = 'title';
	const DESCRIPTION  = 'description';
	const IMAGE        = 'image';
	const TYPE         = 'type';
	const WEIGHT       = 'weight';
	const UNIT         = 'unit';
	const PRICE        = 'price';
	const SALE_PRICE   = 'sale_price';
	const CATEGORY     = 'category';
	const CATEGORY_IDS = 'category_ids';
	const DATE         = 'date';
	const TAGS         = 'tags';
	const URL          = 'url';
	const VISIBLE      = 'visible';

	public static $source_key = 'product';

	public function __construct( \WC_Product $product ) {
		parent::__construct();

		$this->product = $product;
	}

	public function get_product_id() {
		return (string) $this->product->get_id();
	}

	public function get_price() {
		return Utilities::get_price_in_cents( $this->product->get_regular_price() );
	}

	public function get_sale_price() {
		return Utilities::get_price_in_cents( $this->product->get_sale_price() );
	}

	public function get_title() {
		return $this->product->get_name();
	}

	public function get_image() {
		$image_id = $this->product->get_image_id();
		$image    = wp_get_attachment_image_url( $image_id, 'woocommerce_single' );
		return $image ?: null;
	}

	public function get_type() {
		return $this->product->get_type();
	}

	public function get_category() {
		return $this->get_categories( $this->product->get_id() );
	}

	public function get_category_ids() {
		return ( $this->product->get_category_ids() ) ? array_map( 'strval', $this->product->get_category_ids() ) : null;
	}

	public function get_description() {
		return $this->product->get_description();
	}

	public function get_date() {
		return Utilities::format_datetime( $this->product->get_date_modified() );
	}

	public function get_tags() {
		$terms = get_the_terms( $this->product->get_id(), 'product_tag' );

		return ! empty( $terms ) ? wp_list_pluck( $terms, 'name' ) : null;
	}

	public function get_url() {
		return $this->product->get_permalink();
	}

	public function get_visible() {
		return $this->product->is_visible();
	}

	public function get_weight() {
		return $this->product->get_weight() ?: null;
	}

	public function get_categories( $product_id ) {
		$terms = get_the_terms( $product_id, 'product_cat' );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return null;
		}
		$categories = array();

		foreach ( $terms as $term ) {
			$categories[] = $term->name;
		}
		return $categories;
	}
}
