<?php

namespace WooCommerceCustobar\Synchronization;

defined( 'ABSPATH' ) || exit;

use WooCommerceCustobar\DataType\Custobar_Customer;

/**
 * Class Customer_Sync
 *
 * @package WooCommerceCustobar\Synchronization
 */
class Customer_Sync extends Data_Sync {


	protected static $endpoint  = '/customers/upload/';
	protected static $child     = __CLASS__;
	protected static $data_type = 'customer';

	public static function add_hooks() {
		// Schedule actions
		add_action( 'user_register', array( __CLASS__, 'schedule_single_update' ), 10, 1 );
		add_action( 'profile_update', array( __CLASS__, 'schedule_single_update' ), 10, 1 );
		add_action( 'woocommerce_new_customer', array( __CLASS__, 'schedule_single_update', 10, 1 ) );
		add_action( 'woocommerce_update_customer', array( __CLASS__, 'schedule_single_update' ), 10, 1 );

		// Hook into scheduled actions
		// Call parent method to consider request limit
		add_action( 'woocommerce_custobar_customer_sync', array( __CLASS__, 'throttle_single_update' ), 10, 1 );

		// Hook export related actions
		add_action( 'woocommerce_custobar_customer_export', array( __CLASS__, 'export_batch' ), 10, 1 );
	}


	public static function export_batch( $offset ) {
			global $wpdb;
			$response = new \stdClass();
			$limit    = 100;

			$order_ids = array_map(function ($row) {
				return $row->id;
			},
			$wpdb->get_results("
				SELECT MAX(p.id) as id FROM {$wpdb->prefix}posts p
				INNER JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id
				WHERE p.post_type = 'shop_order'
				AND pm.meta_key = '_billing_email'
				GROUP BY pm.meta_value
				ORDER BY p.ID DESC LIMIT {$limit} OFFSET {$offset}
			"));

			// Get orders by offset and limit
			$args = array(
				'post__in'       => $order_ids,
				'type'     => 'shop_order', // skip shop_order_refund
				'orderby'  => 'ID',
				'order'    => 'DESC',
				'paginate' => true,
			);

			// Allow 3rd parties to modify args
			$args = apply_filters( 'woocommerce_custobar_batch_update_orders_args', $args );

			$results = wc_get_orders( $args );

			$orders = $results->orders;

			// Create an array to store the newest order for each billing email
			$newest_orders_by_email = array();

			foreach ( $orders as $order ) {
				$billing_email = $order->get_billing_email();

				// Check if this billing email already exists in the array
				if ( isset( $newest_orders_by_email[ $billing_email ] ) ) {
					$existing_order_date = $newest_orders_by_email[ $billing_email ]->get_date_created()->getTimestamp();
					$current_order_date  = $order->get_date_created()->getTimestamp();

					// Compare the dates to keep only the newest order
					if ( $current_order_date > $existing_order_date ) {
						// Replace the existing order with the newer one
						$newest_orders_by_email[ $billing_email ] = $order;
					}
				} else {
					// If billing email is not already in the array, add the order
					$newest_orders_by_email[ $billing_email ] = $order;
				}
			}

			// Todo: send these as arguments
			$can_email = get_option( 'custobar_export_force_can_email' );
			$can_sms   = get_option( 'custobar_export_force_can_sms' );

			$customers = array();

			foreach ( $newest_orders_by_email as $order ) {
				$order_id = $order->get_id();

				// Get customer by order id
				$customer = self::get_customer_by_order_id( $order_id );

				if ( $customer ) {
					$properties = self::format_single_item( $customer );

					// Check settings for exporting marketing permissions
					if ( 'yes' === $can_email ) {
						$properties['can_email'] = true;
					}

					if ( 'yes' === $can_sms ) {
						$properties['can_sms'] = true;
					}

					$customers[] = $properties;
				}
			}

			$processed_count = count( $customers );
			$total_count     = (int)$wpdb->get_var("
				SELECT COUNT(*) FROM (SELECT MAX(p.id) FROM wp_posts p
				INNER JOIN wp_postmeta pm ON p.ID = pm.post_id
				WHERE p.post_type = 'shop_order'
				AND pm.meta_key = '_billing_email'
				GROUP BY pm.meta_value) q
			");

			// Upload data
			$api_response = self::upload_data_type_data( $customers );

			// Handle response and possibly schedule next round
			self::handle_export_response( 'customer', $offset, $limit, $processed_count, $total_count, $api_response );
	}

	public static function schedule_single_update( $id, $force = false ) {
		$customer = new \WC_Customer( $id );
		$user_id  = 'No WP user';

		//	Check if WP user
		if ( $customer->get_username() ) {
			$user_id = $id;
			// Allow 3rd parties to decide if customer should be synced
			if ( ! apply_filters( 'woocommerce_custobar_customer_should_sync', true, $user_id ) ) {
				return;
			}
			$args = array( 'user_id' => $user_id );
		} else {
			$order_id = $id;
			$args     = array( 'order_id' => $order_id );
		}

		$hook  = 'woocommerce_custobar_customer_sync';
		$group = 'custobar';

		// Force schedule
		// For example reschedule when action still in progress
		if ( $force ) {
			as_schedule_single_action( time(), $hook, $args, $group );

			wc_get_logger()->info(
				'#' . $user_id . ' NEW/UPDATE CUSTOMER, SYNC SCHEDULED (FORCE)',
				array( 'source' => 'custobar' )
			);
		}

		// We need only one action scheduled
		if ( ! as_next_scheduled_action( $hook, $args, $group ) ) {
			as_schedule_single_action( time(), $hook, $args, $group );

			wc_get_logger()->info(
				'#' . $user_id . ' NEW/UPDATE CUSTOMER, SYNC SCHEDULED',
				array( 'source' => 'custobar' )
			);
		}
	}

	/**
	 * Customer sync
	 *
	 * @param int|null $item_id The ID of the user, order or null if not provided.
	 * @return void
	 */

	public static function single_update( $item_id ) {
		$order_id = null;

		//	Check if WP user
		if ( ( new \WC_Customer( $item_id ) )->get_username() ) {
			$user_id = $item_id;
		} else {
			$order_id = $item_id;
		}

		// Non registered user
		if ( $order_id ) {
			$customer = self::get_customer_by_order_id( $order_id );
			if ( ! $customer ) {
				return false;
			}

			wc_get_logger()->info(
				'Order id ' . $order_id . ' CUSTOMER SYNC, UPLOADING TO CUSTOBAR',
				array( 'source' => 'custobar' )
			);

			$properties     = self::format_single_item( $customer );
			$initial_export = false;

			$response = self::upload_data_type_data( $properties, true );

			return $response;

			// Registered user
		} else {
			wc_get_logger()->info(
				'#' . $item_id . ' CUSTOMER SYNC, UPLOADING TO CUSTOBAR',
				array( 'source' => 'custobar' )
			);
			$customer = new \WC_Customer( $user_id );

			if ( in_array( $customer->get_role(), self::get_allowed_roles() ) ) {
				$properties     = self::format_single_item( $customer );
				$initial_export = false;

				$response = self::upload_data_type_data( $properties, true );

				return $response;

			} else {

				wc_get_logger()->warning(
					'#' . $user_id . ' tried to sync customer, but user role was not allowed',
					array( 'source' => 'custobar' )
				);

			}
		}
	}

	public static function get_customer_by_order_id( $order_id ) {
		$order = wc_get_order( $order_id );

		$billing_first_name = $order->get_billing_first_name();
		$billing_last_name  = $order->get_billing_last_name();
		$billing_email      = $order->get_billing_email();
		$billing_phone      = $order->get_billing_phone();
		$billing_company    = $order->get_billing_company();
		$billing_city       = $order->get_billing_city();
		$billing_postcode   = $order->get_billing_postcode();
		$billing_state      = $order->get_billing_state();
		$billing_country    = $order->get_billing_country();
		$meta_data          = $order->get_meta_data();
		$billing_address    = self::get_street_address( $order );

		// Check if customer exists
		if ( get_user_by( 'email', $billing_email ) ) {
			return false;
		}
		$customer = new \WC_Customer();

		$customer->set_first_name( $billing_first_name );
		$customer->set_last_name( $billing_last_name );
		$customer->set_email( $billing_email );
		$customer->set_billing_phone( $billing_phone );
		$customer->set_billing_company( $billing_company );
		$customer->set_billing_city( $billing_city );
		$customer->set_billing_postcode( $billing_postcode );
		$customer->set_billing_state( $billing_state );
		$customer->set_billing_country( $billing_country );
		$customer->set_billing_address( $billing_address );

		foreach ($meta_data as $data) {
			$customer->update_meta_data($data->key, $data->value);
		}

		return $customer;
	}

	public static function get_street_address( $order ) {
		$address           = $order->get_billing_address_1();
		$billing_address_2 = $order->get_billing_address_2();
		if ( $billing_address_2 ) {
			$address .= '\n' . $billing_address_2;
		}
		return $address;
	}

	/**
	 * Get allowed user roles that will be synced
	 * By default only users with the role 'customer' are synced
	 *
	 * @return array Allowed user roles
	 */
	public static function get_allowed_roles() {
		// Allow 3rd parties filter roles to be synced
		$roles = apply_filters( 'woocommerce_custobar_customer_sync_roles', array( 'customer' ) );
		return array_filter( array_unique( $roles ) );
	}

	/**
	 * Format single item
	 *
	 * @param \WC_Customer $wccustomer
	 * @return array filtered properties
	 */
	protected static function format_single_item( $wccustomer ) {
		$custobar_customer = new Custobar_Customer( $wccustomer );
		$properties        = $custobar_customer->get_assigned_properties( $wccustomer );
		return apply_filters( 'woocommerce_custobar_customer_properties', $properties, $wccustomer );
	}

	protected static function upload_data_type_data( $data, $single = false ) {
		$formatted_data = array(
			'customers' => array(),
		);

		if ( $single ) {
			$formatted_data['customers'][] = $data;
		} else {
			$formatted_data['customers'] = $data;
		}

		return self::upload_custobar_data( $formatted_data );
	}
	protected static function get_data_type_from_subclass() {
		return static::$data_type;
	}
}
