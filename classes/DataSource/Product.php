<?php

namespace WooCommerceCustobar\DataSource;

use WooCommerceCustobar\DataType\Utilities;

defined( 'ABSPATH' ) or exit;

class Product extends AbstractDataSource {


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

	public static $sourceKey = 'product';

	public function __construct( \WC_Product $product ) {
		parent::__construct();

		$this->product = $product;
	}

	public function getProductId() {
		return (string) $this->product->get_id();
	}

	public function getPrice() {
		return Utilities::getPriceInCents( $this->product->get_regular_price() );
	}

	public function getSalePrice() {
		return Utilities::getPriceInCents( $this->product->get_sale_price() );
	}

	public function getTitle() {
		return $this->product->get_name();
	}

	public function getImage() {
		$image_id = $this->product->get_image_id();
		return ( $image = wp_get_attachment_image_url( $image_id, 'woocommerce_single' ) ) ? $image : null;
	}

	public function getType() {
		return $this->product->get_type();
	}

	public function getCategory() {
		return $this->getCategories( $this->product->get_id() );
	}

	public function getCategoryIds() {
		return ( $this->product->get_category_ids() ) ? array_map( 'strval', $this->product->get_category_ids() ) : null;
	}

	public function getDescription() {
		return $this->product->get_description();
	}

	public function getDate() {
		return Utilities::formatDateTime( $this->product->get_date_modified() );
	}

	public function getTags() {
		$terms = get_the_terms( $this->product->get_id(), 'product_tag' );

		return ! empty( $terms ) ? wp_list_pluck( $terms, 'name' ) : null;
	}

	public function getUrl() {
		return $this->product->get_permalink();
	}

	public function getVisible() {
		return $this->product->is_visible();
	}

	public function getWeight() {
		( $this->product->get_weight() ) ? $this->product->get_weight() : null;
	}

	public function getCategories( $productId ) {
		$terms = get_the_terms( $productId, 'product_cat' );

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
