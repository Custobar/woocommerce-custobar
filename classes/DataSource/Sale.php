<?php

namespace WooCommerceCustobar\DataSource;

use WooCommerceCustobar\DataType\Utilities;

defined('ABSPATH') or exit;

class Sale extends AbstractDataSource
{
    const ORDER_ID = 'order_id';
    const ORDER_NUMBER = 'order_number';
    const ORDER_DATE = 'order_date';
    const TOTAL = 'total';
    const ORDER_TOTAL = 'order_total';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_PHONE = 'customer_phone';
    const CUSTOMER_EMAIL = 'customer_email';
    const PRODUCT_ID = 'product_id';
    const QUANTITY = 'quantity';
    const PRICE = 'price';
    const TOTAL_DISCOUNT = 'total_discount';
    const SALE_SHIPPING = 'sale_shipping';
    const PAYMENT_METHOD_TITLE = 'payment_method_title';
    const STATUS = 'status';

    public static $sourceKey = 'sale';

    public function __construct($order, $order_item)
    {
        parent::__construct();

        $this->order = $order;
        $this->order_item = $order_item;
    }

    public function getOrderId()
    {
        return $this->order_item->get_id();
    }

    public function getOrderNumber()
    {
        return $this->order->get_order_number();
    }

    public function getOrderDate()
    {
        return Utilities::formatDateTime($this->order->get_date_created());
    }

    public function getTotal()
    {
        return Utilities::getPriceInCents($this->order_item->get_total());
    }

    public function getOrderTotal()
    {
        return Utilities::getPriceInCents($this->order->get_total());
    }

    public function getCustomerId()
    {
        return ($this->order->get_user_id()) ? (string)$this->order->get_user_id() : null;
    }

    public function getCustomerPhone()
    {
        return $this->order->get_billing_phone();
    }

    public function getCustomerEmail()
    {
        return $this->order->get_billing_email();
    }

    public function getProductId()
    {
        return $this->order_item->get_product_id();
    }

    public function getQuantity()
    {
        return $this->order_item->get_quantity();
    }

    public function getPrice()
    {
        return Utilities::getPriceInCents($this->order_item->get_total() / $this->order_item->get_quantity());
    }

    public function getTotalDiscount()
    {
        return Utilities::getPriceInCents($this->order->get_total_discount());
    }

    public function getSaleShipping()
    {
        return Utilities::getPriceInCents($this->order->get_shipping_total());
    }

    public function getStatus()
    {
        return strtoupper($this->order->get_status());
    }

    public function getPaymentMethodTitle()
    {
        return $this->order->get_payment_method_title();
    }
}
