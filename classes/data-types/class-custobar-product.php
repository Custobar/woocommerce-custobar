<?php

namespace WooCommerceCustobar\DataType;

use WooCommerceCustobar\Fields_Map;
use WooCommerceCustobar\DataSource\Product;

defined( 'ABSPATH' ) || exit;

/**
 * Class Custobar_Product
 *
 * Check field descriptions here: https://www.custobar.com/api/docs/products/
 *
 * @package WooCommerceCustobar\DataType
 */
class Custobar_Product extends Custobar_Data_Type {


	const EXTERNAL_ID                  = 'external_id';
	const PRICE                        = 'price';
	const TYPE                         = 'type';
	const CATEGORY                     = 'category';
	const CATEGORY_ID                  = 'category_id';
	const VENDOR                       = 'vendor';
	const BRAND                        = 'brand';
	const TITLE                        = 'title';
	const IMAGE                        = 'image';
	const DATE                         = 'date';
	const URL                          = 'url';
	const SALE_PRICE                   = 'sale_price';
	const DESCRIPTION                  = 'description';
	const LANGUAGE                     = 'language';
	const VISIBLE                      = 'visible';
	const EXCLUDE_FROM_RECOMMENDATIONS = 'exclude_from_recommendations';
	const TAGS                         = 'tags';
	const UNIT                         = 'unit';
	const WEIGHT                       = 'weight';

	/**
	 * Maps WC_Product object's properties to match the ones used in Custobar.
	 *
	 * @param \WC_Product $product
	 */
	public function __construct( $product ) {
		parent::__construct();

		$this->data_source = new Product( $product );
	}

	public static function get_fields_map() {
		return Fields_Map::get_product_fields();
	}

	public function get_assigned_properties() {
		$wcproduct = new \WC_Product( $this->data_source->get_product_id() );
		return $this->get_assigned_properties_base( $wcproduct );
	}
}
