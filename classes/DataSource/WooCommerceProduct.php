<?php

namespace WooCommerceCustobar\DataSource;

use WooCommerceCustobar\DataType\Utilities;

defined('ABSPATH') or exit;

class WooCommerceProduct extends AbstractDataSource
{

    CONST PRODUCT_ID = 'product_id';
    CONST TITLE = 'title';
    CONST DESCRIPTION = 'description';
    CONST IMAGE = 'image';
    CONST TYPE = 'type';
    CONST WEIGHT = 'weight';
    CONST UNIT = 'unit';
    CONST PRICE = 'price';
    CONST SALE_PRICE = 'sale_price';
    CONST CATEGORY = 'category';
    CONST CATEGORY_IDS = 'category_ids';
    CONST DATE = 'date';
    CONST URL = 'url';
    CONST VISIBLE = 'visible';

    /**
     * Instance of WC_Product
     *
     * @var WC_Product
     */
    protected $product;

    public function __construct(\WC_Product $product)
    {
        $this->product = $product;
    }

    public function getProductId()
    {
        return (string) $this->product->get_id();
    }

    public function getPrice()
    {
        return Utilities::getPriceInCents($this->product->get_regular_price());
    }

    public function getSalePrice()
    {
        return Utilities::getPriceInCents($this->product->get_sale_price());
    }

    public function getTitle()
    {
        return $this->product->get_name();
    }

    public function getImage()
    {
        return ($image=get_the_post_thumbnail_url($this->product->get_id(), 'shop_thumbnail')) ? $image : null;
    }

    public function getType()
    {
        return $this->product->get_type();
    }

    public function getCategory()
    {
        return $this->getCategories($this->product->get_id());
    }

    public function getCategoryIds()
    {
        return ($this->product->get_category_ids()) ? array_map('strval', $this->product->get_category_ids()) : null;
    }

    public function getDescription()
    {
        return $this->product->get_description();
    }

    public function getDate()
    {
        return Utilities::formatDateTime($this->product->get_date_modified());
    }

    public function getUrl()
    {
        return $this->product->get_permalink();
    }
    
    public function visible()
    {
        return $this->product->is_visible();
    }

    public function getWeight()
    {
        ($this->product->get_weight()) ? $this->product->get_weight() : null;
    }

    protected function getCategories($productId)
    {
        $terms = get_the_terms($productId, 'product_cat');

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
