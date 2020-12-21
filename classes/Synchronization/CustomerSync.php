<?php

namespace WooCommerceCustobar\Synchronization;

defined( 'ABSPATH' ) or exit;

use WooCommerceCustobar\DataType\CustobarCustomer;

/**
 * Class CustomerSync
 *
 * @package WooCommerceCustobar\Synchronization
 */
class CustomerSync extends AbstractDataSync {

	protected static $endpoint = '/customers/upload/';

	public static function addHooks() {
		// Schedule actions
		add_action( 'user_register', array( __CLASS__, 'schedule_single_update' ), 10, 1 );
		add_action( 'profile_update', array( __CLASS__, 'schedule_single_update' ), 10, 1 );
		add_action( 'woocommerce_new_customer', array( __CLASS__, 'schedule_single_update', 10, 1 ) );
		add_action( 'woocommerce_created_customer', array( __CLASS__, 'schedule_single_update' ), 10, 1 );
		add_action( 'woocommerce_update_customer', array( __CLASS__, 'schedule_single_update' ), 10, 1 );

		// Hook into scheduled actions
		add_action('woocommerce_custobar_customersync_single_update', array( __CLASS__, 'singleUpdate' ), 10, 1);
	}

	public static function schedule_single_update($user_id) {
		wc_get_logger()->info(
			'CustomerSync schedule_single_update called with $user_id: '.$user_id,
			array(
				'source' => 'custobar',
			)
		);

		$hook = 'woocommerce_custobar_customersync_single_update';
		$args = array('user_id' => $user_id);
		$group = 'custobar';

		// We need only one action scheduled
		if (!as_next_scheduled_action( $hook, $args, $group )) {
			as_enqueue_async_action( $hook, $args, $group );
		}
	}

	public static function singleUpdate( $user_id ) {

		wc_get_logger()->info(
			'CustomerSync single update called with $user_id: ' . $user_id,
			array(
				'source' => 'custobar',
			)
		);

		$customer = new \WC_Customer( $user_id );

		// Update only customers
		if ( $customer->get_role() == 'customer' ) {
			$properties = self::formatSingleItem( $customer );
			self::uploadDataTypeData( $properties, true );
		}
	}

	public static function batchUpdate() {

		$response = new \stdClass();
		$tracker  = self::trackerFetch();
		$offset   = $tracker['offset'];

		$limit = 500;

		/*
		* Fetch users
		*/

		$query = new \WP_User_Query(
			array(
				'role'    => 'customer',
				'fields'  => 'ID',
				'orderby' => 'ID',
				'order'   => 'ASC',
				'number'  => $limit,
				'offset'  => $offset,
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
			$customers[] = self::formatSingleItem( $customer );
		}

		// no data
		if ( empty( $customers ) ) {
			$response->code = 221;
			return $response;
		}

		$count = count( $customers );

		// track the export
		self::trackerSave( $offset + $count );

		// do upload to custobar API
		$apiResponse = self::uploadDataTypeData( $customers );

		// return response
		$response->code    = $apiResponse->code;
		$response->body    = $apiResponse->body;
		$response->tracker = self::trackerFetch();
		$response->count   = $count;
		return $response;

	}

	public static function trackerFetch() {
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

	public static function trackerSave( $offset, $total = null ) {
		$tracker = self::trackerFetch();
		if ( isset( $offset ) ) {
			$tracker['offset']  = $offset;
			$tracker['updated'] = time();
		}
		if ( isset( $total ) ) {
			$tracker['total'] = $total;
		}
		update_option( 'custobar_export_customer', $tracker );
	}

	protected static function formatSingleItem( $user ) {
		$custobar_customer = new CustobarCustomer( $user );
		$properties        = $custobar_customer->getAssignedProperties();
		return apply_filters( 'woocommerce_custobar_customer_properties', $properties, $user );
	}

	protected static function uploadDataTypeData( $data, $single = false ) {

		$formatted_data = array(
			'customers' => array(),
		);
		if ( $single ) {
			$formatted_data['customers'][] = $data;
		} else {
			$formatted_data['customers'] = $data;
		}

		return self::uploadCustobarData( $formatted_data );

	}
}
