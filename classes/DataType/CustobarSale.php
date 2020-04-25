<?php

namespace WooCommerceCustobar\DataType;

defined('ABSPATH') or exit;

/**
 * Class CustobarSale
 *
 * Check field descriptions here: https://www.custobar.com/api/docs/sales/
 *
 * @package WooCommerceCustobar\DataType
 */
class CustobarSale extends AbstractCustobarDataType
{
    CONST SALE_EXTERNAL_ID = 'sale_external_id';
    CONST SALE_DATE = 'sale_date';
    CONST SALE_CUSTOMER_ID = 'sale_customer_id';
    CONST SALE_DISCOUNT = 'sale_discount';
    CONST SALE_PAYMENT_METHOD = 'sale_payment_method';
    CONST SALE_SHIPPING = 'sale_shipping';
    CONST SALE_SHOP_ID = 'sale_shop_id';
    CONST SALE_STATE = 'sale_state';
    CONST SALE_TOTAL = 'sale_total';
    CONST EXTERNAL_ID = 'external_id';
    CONST PRODUCT_ID = 'product_id';
    CONST QUANTITY = 'quantity';
    CONST UNIT_PRICE = 'unit_price';
    CONST DISCOUNT = 'discount';
    CONST TOTAL = 'total';
    CONST SALE_PHONE_NUMBER = 'sale_phone_number';
    CONST SALE_EMAIL = 'sale_email';

    protected $sale_external_id;
    protected $sale_date;
    protected $sale_customer_id;
    protected $sale_discount;
    protected $sale_payment_method;
    protected $sale_shipping;
    protected $sale_shop_id;
    protected $sale_state;
    protected $sale_total;
    protected $external_id;
    protected $product_id;
    protected $quantity;
    protected $unit_price;
    protected $discount;
    protected $total;

    /**
     * Maps WC_Order and WC_Order_Item_Product objects' properties to match
     * the ones used in Custobar.
     *
     * @param WC_Order              $order
     * @param WC_Order_Item_Product $order_item
     */
    public function __construct($order, $order_item)
    {
        $this->sale_external_id    = $order->get_order_number();
        $this->sale_date           = Utilities::formatDateTime($order->get_date_created());
        $this->sale_customer_id    = ($order->get_user_id()) ? (string)$order->get_user_id() : null;
        $this->sale_phone_number   = $order->get_billing_phone();
        $this->sale_email          = $order->get_billing_email();
        $this->sale_discount       = Utilities::getPriceInCents($order->get_total_discount());
        $this->sale_payment_method = $order->get_payment_method_title();
        $this->sale_shipping       = Utilities::getPriceInCents($order->get_shipping_total());
        $this->sale_shop_id        = null;
        $this->sale_state          = null;
        $this->sale_total          = Utilities::getPriceInCents($order->get_total());
        $this->external_id         = $order_item->get_id();
        $this->product_id          = $order_item->get_product_id();
        $this->quantity            = $order_item->get_quantity();
        $this->unit_price          = Utilities::getPriceInCents($order_item->get_total() / $order_item->get_quantity());
        $this->discount            = null;
        $this->total               = Utilities::getPriceInCents($order_item->get_total());
    }
}
