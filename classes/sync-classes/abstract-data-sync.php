<?php

namespace WooCommerceCustobar\Synchronization;

defined( 'ABSPATH' ) || exit;

use WooCommerceCustobar\Data_Upload;

/**
 * Class Data_Sync
 *
 * @package WooCommerceCustobar\Synchronization
 */
abstract class Data_Sync {


	abstract public static function single_update( $item_id );
	abstract public static function batch_update();
	abstract protected static function format_single_item( $item );
	abstract protected static function upload_data_type_data( $data );

	/**
	 * Handle single update timing
	 * consider Custobar's default request limit of 180 per minute,
	 * and go to sleep if processing too fast.
	 *
	 * @return void
	 */
	public static function call_single_update( $id ) {
		// Timing
		$start_time = hrtime( true );

		// The child class that called this method
		$child = static::$child;

		// Do the update
		$child::single_update( $id );

		// The request rate limit and concurrent batches
		$requests_per_minute = apply_filters( 'woocommerce_custobar_requests_per_minute', 180 );
		$concurrent_batches = apply_filters( 'action_scheduler_queue_runner_concurrent_batches', 1 );

		// hrtime returns nanoseconds. That's pretty hardcore. Since we're a relaxed bunch, let's convert to microseconds instead.
		$time_elapsed_in_microseconds = round( ( hrtime( true ) - $start_time ) / 1000 );
		$time_to_sleep_in_microseconds = ( 60 / $requests_per_minute * $concurrent_batches * 1000000 ) - $time_elapsed_in_microseconds;
		if ( $time_to_sleep_in_microseconds > 0 ) {
			// Go to sleep
			usleep( $time_to_sleep_in_microseconds );
		}

		wc_get_logger()->info(
			"$child updating... concurrent batches: $concurrent_batches, TIME TO SLEEP: " . $time_to_sleep_in_microseconds/1000000 . 's',
			array( 'source' => 'custobar' )
		);
	}

	protected static function upload_custobar_data( $data ) {

		$endpoint = static::$endpoint;

		$cds            = new \WooCommerceCustobar\DataSource\Custobar_Data_Source();
		$integration_id = $cds->get_integration_id();
		if ( ! $integration_id ) {
			$integration_id = $cds->create_integration();
		}

		if ( $integration_id ) {

			switch ( $endpoint ) {
				case '/customers/upload/':
					$data_source_id = $cds->get_customer_data_source_id();
					if ( ! $data_source_id ) {
						$data_source_id = $cds->create_data_source( 'WooCommerce customers', 'customers' );
					}
					break;
				case '/products/upload/':
					$data_source_id = $cds->get_product_data_source_id();
					if ( ! $data_source_id ) {
						$data_source_id = $cds->create_data_source( 'WooCommerce products', 'products' );
					}
					break;
				case '/sales/upload/':
					$data_source_id = $cds->get_sale_data_source_id();
					if ( ! $data_source_id ) {
						$data_source_id = $cds->create_data_source( 'WooCommerce sales', 'sales' );
					}
					break;
			}

			if ( $data_source_id ) {
				$endpoint = '/datasources/' . $data_source_id . '/import/';
			}
		}

		return Data_Upload::upload_custobar_data( $endpoint, $data );

	}
}
