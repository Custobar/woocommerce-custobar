<?php

namespace WooCommerceCustobar\DataType;

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

    protected $external_id;
    protected $price;
    protected $type;
    protected $category;
    protected $category_id;
    protected $vendor;
    protected $brand;
    protected $title;
    protected $image;
    protected $date;
    protected $url;
    protected $sale_price;
    protected $description;
    protected $language;
    protected $visible;
    protected $exclude_from_recommendations;
    protected $unit;
    protected $weight;

    /**
     * Maps WC_Product object's properties to match the ones used in Custobar.
     *
     * @param WC_Product $product
     */
    public function __construct($product)
    {
        $this->external_id                  = (string)$product->get_id();
        $this->price                        = Utilities::getPriceInCents($product->get_regular_price());
        $this->type                         = $product->get_type();
        $this->category                     = $this->getCategories($product->get_id());
        $this->category_id                  = ($product->get_category_ids()) ? array_map('strval', $product->get_category_ids()) : null;
        $this->vendor                       = null;
        $this->brand                        = null;
        $this->title                        = $product->get_name();
        $this->image                        = ($image=get_the_post_thumbnail_url($product->get_id(), 'shop_thumbnail')) ? $image : null;
        $this->date                         = Utilities::formatDateTime($product->get_date_modified());
        $this->url                          = $product->get_permalink();
        $this->sale_price                   = Utilities::getPriceInCents($product->get_sale_price());
        $this->description                  = $product->get_description();
        $this->language                     = null;
        $this->visible                      = $product->is_visible();
        $this->exclude_from_recommendations = null;
        $this->unit                         = null;
        $this->weight                       = ($product->get_weight()) ? $product->get_weight() : null;
    }

    protected function getCategories($product_id)
    {
        $terms = get_the_terms($product_id, 'product_cat');

        if (is_wp_error($terms) || empty($terms)) {
            return null;
        }
        $categories = array();

        foreach ($terms as $term) {
            $categories[] = $term->name;
        }
        return $categories;
    }
}
