<?php

namespace WooCommerceCustobar\DataType;

use WooCommerceCustobar\FieldsMap;
use WooCommerceCustobar\DataSource\Product;

defined('ABSPATH') or exit;

/**
 * Class CustobarProduct
 *
 * Check field descriptions here: https://www.custobar.com/api/docs/products/
 *
 * @package WooCommerceCustobar\DataType
 */
class CustobarProduct extends AbstractCustobarDataType
{
    CONST EXTERNAL_ID = 'external_id';
    CONST PRICE = 'price';
    CONST TYPE = 'type';
    CONST CATEGORY = 'category';
    CONST CATEGORY_ID = 'category_id';
    CONST VENDOR = 'vendor';
    CONST BRAND = 'brand';
    CONST TITLE = 'title';
    CONST IMAGE = 'image';
    CONST DATE = 'date';
    CONST URL = 'url';
    CONST SALE_PRICE = 'sale_price';
    CONST DESCRIPTION = 'description';
    CONST LANGUAGE = 'language';
    CONST VISIBLE = 'visible';
    CONST EXCLUDE_FROM_RECOMMENDATIONS = 'exclude_from_recommendations';
    CONST UNIT = 'unit';
    CONST WEIGHT = 'weight';

    /**
     * Maps WC_Product object's properties to match the ones used in Custobar.
     *
     * @param \WC_Product $product
     */
    public function __construct($product)
    {
        parent::__construct();
        
        $this->dataSource = new Product($product);
    }

    public static function getFieldsMap() {
        return FieldsMap::getProductFields();
    }
}
