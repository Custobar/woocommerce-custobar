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


	protected static $endpoint = '/customers/upload/';
	protected static $child    = __CLASS__;

	public static function add_hooks() {
		// Schedule actions
		add_action( 'user_register', array( __CLASS__, 'schedule_single_update' ), 10, 1 );
		add_action( 'profile_update', array( __CLASS__, 'schedule_single_update' ), 10, 1 );
		add_action( 'woocommerce_new_customer', array( __CLASS__, 'schedule_single_update', 10, 1 ) );
		add_action( 'woocommerce_update_customer', array( __CLASS__, 'schedule_single_update' ), 10, 1 );

		// Hook into scheduled actions
		// Call parent method to consider request limit
		add_action( 'woocommerce_custobar_customer_sync', array( __CLASS__, 'throttle_single_update' ), 10, 1 );
	}

	public static function schedule_single_update( $user_id, $force = false ) {
		// Allow 3rd parties to decide if customer should be synced
		if ( ! apply_filters( 'woocommerce_custobar_customer_should_sync', true, $user_id ) ) {
			return;
		}

		$hook  = 'woocommerce_custobar_customer_sync';
		$args  = array( 'user_id' => $user_id );
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
	 * @param int $user_id
	 * @return void
	 */
	public static function single_update( $user_id ) {

		wc_get_logger()->info(
			'#' . $user_id . ' CUSTOMER SYNC, UPLOADING TO CUSTOBAR',
			array( 'source' => 'custobar' )
		);

		$customer = new \WC_Customer( $user_id );

		if ( in_array( $customer->get_role(), self::get_allowed_roles() ) ) {

			$properties     = self::format_single_item( $customer );
			$initial_export = false;

			// Have initial marketing permissions been exported?
			if ( ! get_user_meta( $user_id, '_custobar_permissions_export', true ) ) {
				// Push initial marketing permissions with customer object
				$initial_export = true;

				if ( 'yes' === get_option( 'custobar_initial_can_email' ) ) {
					$properties['can_email'] = true;
				}

				if ( 'yes' === get_option( 'custobar_initial_can_sms' ) ) {
					$properties['can_sms'] = true;
				}
			}

			$response = self::upload_data_type_data( $properties, true );

			if ( ! is_wp_error( $response ) && in_array( $response->code, array( 200, 201 ) ) && $initial_export ) {
				// Initial export done
				update_user_meta( $user_id, '_custobar_permissions_export', gmdate( 'c' ) );
			}

			return $response;

		} else {

			wc_get_logger()->warning(
				'#' . $user_id . ' tried to sync customer, but user role was not allowed',
				array( 'source' => 'custobar' )
			);

		}

		return false;
	}

	/**
	 * Batch update customers, launched from admin
	 *
	 * @return $response
	 */
	public static function batch_update() {
		$response = new \stdClass();
		$tracker  = self::tracker_fetch();
		$offset   = $tracker['offset'];
		$limit    = 500;

		/*
		* Fetch users
		*/
		$query = new \WP_User_Query(
			array(
				'role__in' => self::get_allowed_roles(),
				'fields'   => 'ID',
				'orderby'  => 'ID',
				'order'    => 'ASC',
				'number'   => $limit,
				'offset'   => $offset,
			)
		);

		$users = $query->get_results();

		if ( empty( $users ) ) {
			$response->code = 220;
			return $response;
		}

		// Override marketing permissions?
		$can_email = ! empty( $_POST['can_email'] );
		$can_sms   = ! empty( $_POST['can_sms'] );

		// Loop over orders to find unique customers
		// Customer data organized into $data
		$customers = array();
		foreach ( $users as $user_id ) {
			$customer   = new \WC_Customer( $user_id );
			$properties = self::format_single_item( $customer );

			// Check settings for exporting marketing permissions
			if ( $can_email ) {
				$properties['can_email'] = true;
			}

			if ( $can_sms ) {
				$properties['can_sms'] = true;
			}

			$customers[] = $properties;
		}

		// No data
		if ( empty( $customers ) ) {
			$response->code = 221;
			return $response;
		}

		$count = count( $customers );

		// Track the export
		self::tracker_save( $offset + $count );

		// Do upload to Custobar API
		$api_response = self::upload_data_type_data( $customers );

		if ( is_wp_error( $api_response ) ) {
			// Request was invalid
			$response->code = 444;
			$response->body = $api_response->get_error_message();
			return $response;
		}

		if ( in_array( $api_response->code, array( 200, 201 ) ) ) {
			if ( $can_email || $can_sms ) {
				// We have exported marketing permissions for these users
				foreach ( $users as $user_id ) {
					update_user_meta( $user_id, '_custobar_permissions_export', gmdate( 'c' ) );
				}
			}
		}

		// return response
		$response->code    = $api_response->code;
		$response->body    = $api_response->body;
		$response->tracker = self::tracker_fetch();
		$response->count   = $count;
		return $response;
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

	public static function tracker_fetch() {
		$tracker = get_option( 'custobar_export_customer' );
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
		update_option( 'custobar_export_customer', $tracker );
	}

	/**
	 * Format single item
	 *
	 * @param \WC_Customer $customer
	 * @return array filtered properties
	 */
	protected static function format_single_item( $customer ) {
		$custobar_customer = new Custobar_Customer( $customer );
		$properties        = $custobar_customer->get_assigned_properties();
		return apply_filters( 'woocommerce_custobar_customer_properties', $properties, $customer );
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
}
