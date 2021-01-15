<?php

namespace WooCommerceCustobar\Synchronization;

defined( 'ABSPATH' ) || exit;

use WooCommerceCustobar\DataType\Custobar_Sale;
use WooCommerceCustobar\DataType\Utilities;

/**
 * Class Sale_Sync
 *
 * @package WooCommerceCustobar\Synchronization
 */
class Sale_Sync extends Data_Sync {


	protected static $endpoint = '/sales/upload/';

	public static function add_hooks() {
		// Schedule actions
		add_action( 'woocommerce_new_order', array( __CLASS__, 'schedule_single_update' ), 10, 1 );
		add_action( 'woocommerce_update_order', array( __CLASS__, 'schedule_single_update' ), 10, 1 );
		add_action( 'woocommerce_subscription_status_updated', array( __CLASS__, 'schedule_subscription_status_updated' ), 10, 3 );
		add_action( 'woocommerce_subscription_renewal_payment_complete', array( __CLASS__, 'schedule_subscription_renewal_payment_complete' ), 10, 2 );

		// Hook into scheduled actions
		add_action( 'woocommerce_custobar_salesync_single_update', array( __CLASS__, 'single_update' ), 10, 1 );

		add_filter( 'woocommerce_custobar_sale_properties', array( __CLASS__, 'add_subscription_fields' ), 10, 3 );
	}

	public static function schedule_single_update( $order_id ) {
		wc_get_logger()->info(
			'schedule_single_update called with $order_id: ' . $order_id,
			array( 'source' => 'custobar' )
		);

		$hook  = 'woocommerce_custobar_salesync_single_update';
		$args  = array( 'order_id' => $order_id );
		$group = 'custobar';

		// We need only one action scheduled
		if ( ! as_next_scheduled_action( $hook, $args, $group ) ) {
			as_enqueue_async_action( $hook, $args, $group );
		}
	}

	public static function schedule_subscription_renewal_payment_complete( $subscription, $order ) {
		wc_get_logger()->info(
			'schedule_subscription_renewal_payment_complete called with $subscription: ' . $subscription->get_id() . ' $order: ' . $order->get_id(),
			array( 'source' => 'custobar' )
		);

		$hook  = 'woocommerce_custobar_salesync_single_update';
		$args  = array( 'order_id' => $order->get_id() );
		$group = 'custobar';

		// We need only one action scheduled
		if ( ! as_next_scheduled_action( $hook, $args, $group ) ) {
			as_enqueue_async_action( $hook, $args, $group );
		}
	}

	public static function schedule_subscription_status_updated( $subscription, $new_status, $old_status ) {
		wc_get_logger()->info(
			'schedule_subscription_status_updated called with $subscription: ' . $subscription->get_id() . " new_status: $new_status, old_status: $old_status",
			array( 'source' => 'custobar' )
		);

		$hook  = 'woocommerce_custobar_salesync_single_update';
		$args  = array( 'order_id' => $subscription->get_parent_id() );
		$group = 'custobar';

		// We need only one action scheduled
		if ( ! as_next_scheduled_action( $hook, $args, $group ) ) {
			as_enqueue_async_action( $hook, $args, $group );
		}
	}

	public static function single_update( $order_id ) {
		wc_get_logger()->info(
			'salesync single_update called with $order_id: ' . $order_id,
			array( 'source' => 'custobar' )
		);

		$order = wc_get_order( $order_id );

		// Would sometimes be triggered twice without the class check, because
		// Subscriptions plugin also creates additional order instance.
		if ( $order && ( get_class( $order ) === 'WC_Order' || get_class( $order ) === 'Automattic\WooCommerce\Admin\Overrides\Order' ) ) {
			$data = array();
			foreach ( $order->get_items() as $order_item ) {
				$data[] = self::format_single_item(
					array(
						'order'      => $order,
						'order_item' => $order_item,
					)
				);
			}
			self::upload_data_type_data( $data );
		}
	}

	public static function batch_update() {
		$response = new \stdClass();
		$tracker  = self::tracker_fetch();
		$offset   = $tracker['offset'];

		// Get orders by offset and limit
		$args = array(
			'type'    => 'shop_order', // skip shop_order_refund
			'limit'   => 350,
			'offset'  => $offset,
			'orderby' => 'ID',
			'order'   => 'ASC',
		);

		// Allow 3rd parties to modify args
		$args = apply_filters( 'woocommerce_custobar_batch_update_orders_args', $args );

		$orders = wc_get_orders( $args );

		$order_rows = array();

		foreach ( $orders as $order ) {

			foreach ( $order->get_items() as $order_item ) {

				$order_rows[] = self::format_single_item(
					array(
						'order'      => $order,
						'order_item' => $order_item,
					)
				);

			}
		}

		// No rows to export
		if ( empty( $order_rows ) ) {
			$response->code = 220;
			return $response;
		}

		$count = count( $orders );

		$api_response = self::upload_data_type_data( $order_rows );

		self::tracker_save( $offset + $count );

		$response->code    = $api_response->code;
		$response->body    = $api_response->body;
		$response->tracker = self::tracker_fetch();
		$response->count   = $count;
		return $response;

	}

	public static function tracker_fetch() {
		$tracker = get_option( 'custobar_export_sale' );
		if ( ! is_array( $tracker ) ) {
			$tracker = array();
		}
		if ( ! isset( $tracker['offset'] ) ) {
			$tracker['offset'] = 0;
		}
		if ( ! isset( $tracker['updated'] ) ) {
			$tracker['updated'] = false;
		}
		return $tracker;
	}

	public static function tracker_save( $offset, $total = null ) {
		$tracker = self::tracker_fetch();
		if ( isset( $offset ) ) {
			$tracker['offset']  = $offset;
			$tracker['updated'] = time();
		}
		if ( isset( $total ) ) {
			$tracker['total'] = $total;
		}
		update_option( 'custobar_export_sale', $tracker );
	}

	/**
	 * Format single item
	 * Single argument required according to inherited abstract
	 *
	 * @param array $args containing order and order_item
	 * @return array
	 */
	protected static function format_single_item( $args ) {
		$order         = $args['order'];
		$order_item    = $args['order_item'];
		$custobar_sale = new Custobar_Sale( $order, $order_item );
		$properties    = $custobar_sale->get_assigned_properties();

		return apply_filters( 'woocommerce_custobar_sale_properties', $properties, $order, $order_item );
	}

	protected static function upload_data_type_data( $data ) {
		$formatted_data = array(
			'sales' => $data,
		);
		return self::upload_custobar_data( $formatted_data );
	}

	/**
	 * Modify basic properties by adding WooCommerce Subscriptions related
	 * fields.
	 */
	public static function add_subscription_fields( $properties, $order, $order_item ) {
		if ( function_exists( 'wcs_order_contains_subscription' ) && wcs_order_contains_subscription( $order ) ) {
			$product_id = $order_item->get_product_id();
			$prefix     = apply_filters( 'woocommerce_custobar_company_field_prefix', get_option( 'custobar_api_setting_company' ) );

			foreach ( wcs_get_subscriptions_for_order( $order ) as $subscription ) {
				foreach ( $subscription->get_items() as $line_item ) {
					if ( $line_item->get_product_id() === $product_id ) {
						$properties[ $prefix . '__subscription_status' ] = $subscription->get_status();
						if ( $subscription->get_date( 'date_created' ) ) {
							$properties[ $prefix . '__subscription_date_created' ] = Utilities::format_datetime( new \DateTime( $subscription->get_date( 'date_created' ) ) );
						}
						if ( $subscription->get_date( 'trial_end' ) ) {
							$properties[ $prefix . '__subscription_trial_end' ] = Utilities::format_datetime( $subscription->get_date( 'trial_end' ) );
						}
						if ( $subscription->get_date( 'next_payment' ) ) {
							$properties[ $prefix . '__subscription_next_payment' ] = Utilities::format_datetime( $subscription->get_date( 'next_payment' ) );
						}
						if ( $subscription->get_date( 'last_order_date_paid' ) ) {
							$properties[ $prefix . '__subscription_last_order_date_paid' ] = Utilities::format_datetime( new \DateTime( $subscription->get_date( 'last_order_date_paid' ) ) );
						}
						if ( $subscription->get_date( 'cancelled' ) ) {
							$properties[ $prefix . '__subscription_cancelled' ] = Utilities::format_datetime( $subscription->get_date( 'cancelled' ) );
						}
						if ( $subscription->get_date( 'end' ) ) {
							$properties[ $prefix . '__subscription_end' ] = Utilities::format_datetime( new \DateTime( $subscription->get_date( 'end' ) ) );
						}
					}
				}
			}
		}
		return $properties;
	}
}
