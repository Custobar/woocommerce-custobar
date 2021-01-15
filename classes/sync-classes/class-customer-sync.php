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

	public static function add_hooks() {
		// Schedule actions
		add_action( 'user_register', array( __CLASS__, 'schedule_single_update' ), 10, 1 );
		add_action( 'profile_update', array( __CLASS__, 'schedule_single_update' ), 10, 1 );
		add_action( 'woocommerce_new_customer', array( __CLASS__, 'schedule_single_update', 10, 1 ) );
		add_action( 'woocommerce_created_customer', array( __CLASS__, 'schedule_single_update' ), 10, 1 );
		add_action( 'woocommerce_update_customer', array( __CLASS__, 'schedule_single_update' ), 10, 1 );

		// Hook into scheduled actions
		add_action( 'woocommerce_custobar_customersync_single_update', array( __CLASS__, 'single_update' ), 10, 1 );
	}

	public static function schedule_single_update( $user_id ) {
		wc_get_logger()->info(
			'Customer_Sync schedule_single_update called with $user_id: ' . $user_id,
			array(
				'source' => 'custobar',
			)
		);

		$hook  = 'woocommerce_custobar_customersync_single_update';
		$args  = array( 'user_id' => $user_id );
		$group = 'custobar';

		// We need only one action scheduled
		if ( ! as_next_scheduled_action( $hook, $args, $group ) ) {
			as_enqueue_async_action( $hook, $args, $group );
		}
	}

	public static function single_update( $user_id ) {

		wc_get_logger()->info(
			'Customer_Sync single update called with $user_id: ' . $user_id,
			array(
				'source' => 'custobar',
			)
		);

		$customer = new \WC_Customer( $user_id );

		if ( in_array( $customer->get_role(), self::get_allowed_roles(), true ) ) {
			$properties = self::format_single_item( $customer );
			self::upload_data_type_data( $properties, true );
		}
	}

	public static function batch_update() {
		$response = new \stdClass();
		$tracker  = self::tracker_fetch();
		$offset   = $tracker['offset'];

		$limit = 500;

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

		// loop over orders to find unique customers
		// customer data organized into $data
		$customers = array();
		foreach ( $users as $user_id ) {
			$customer    = new \WC_Customer( $user_id );
			$customers[] = self::format_single_item( $customer );
		}

		// no data
		if ( empty( $customers ) ) {
			$response->code = 221;
			return $response;
		}

		$count = count( $customers );

		// track the export
		self::tracker_save( $offset + $count );

		// do upload to custobar API
		$api_response = self::upload_data_type_data( $customers );

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
		$roles = apply_filters( 'woocommerce_custobar_customersync_roles', array( 'customer' ) );
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

	protected static function format_single_item( $user ) {
		$custobar_customer = new Custobar_Customer( $user );
		$properties        = $custobar_customer->get_assigned_properties();
		return apply_filters( 'woocommerce_custobar_customer_properties', $properties, $user );
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
