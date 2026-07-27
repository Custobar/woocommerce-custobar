<?php

namespace WooCommerceCustobar;

defined( 'ABSPATH' ) || exit;

/**
 * Class Data_Upload
 *
 * @package WooCommerceCustobar
 */
class Data_Upload {


	/**
	 * Do the request to Custobar API
	 *
	 * @param string $endpoint
	 * @param array $data
	 * @return mixed response object or WP_Error
	 */
	public static function upload_custobar_data( $endpoint, $data ) {

		$data_type = key( $data );
		update_option( 'woocommerce_custobar_export_' . $data_type . '_status', 'in_progress' );

		$body = wp_json_encode( $data );

		$api_token      = \WC_Admin_Settings::get_option( 'custobar_api_setting_token', false );
		$company_domain = \WC_Admin_Settings::get_option( 'custobar_api_setting_company', false );
		$url            = sprintf( 'https://%s.custobar.com/api', $company_domain ) . $endpoint;

		$response = wp_remote_request(
			$url,
			array(
				'method'  => 'POST',
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Token ' . $api_token,
				),
				'body'    => $body,
			)
		);

		// Failed request, do logging and return error object
		if ( is_wp_error( $response ) ) {
			$message = $response->get_error_message();

			wc_get_logger()->warning(
				"Custobar data upload failed: $message",
				array( 'source' => 'custobar' )
			);
			return $response;
		}

		// Create a simpler response object
		$response_data       = new \stdClass();
		$response_data->code = wp_remote_retrieve_response_code( $response );
		$response_data->body = wp_remote_retrieve_body( $response );

		// Unexpected response code, do logging
		if ( ! in_array( $response_data->code, array( 200, 201 ) ) ) {

			wc_get_logger()->warning(
				"Custobar data upload: Unexpected response code: {$response_data->code}",
				array( 'source' => 'custobar' )
			);

		} else {
			if ( get_option( 'custobar_wc_api_extended_logging' ) ) {
				wc_get_logger()->notice(
					'Successful request to endpoint: ' . $endpoint . "\nRequest body:\n" . wp_json_encode( json_decode( $body ), JSON_PRETTY_PRINT ) . "\nResponse body:\n" . json_encode( json_decode( $response_data->body ), JSON_PRETTY_PRINT ),
					array( 'source' => 'custobar-requests' )
				);
			}
		}
		return $response_data;
	}

	public static function add_hooks() {
		add_action( 'wp_ajax_custobar_api_test', __CLASS__ . '::api_test' );
	}

	public static function api_test() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'Forbidden', 403 );
		}

		check_ajax_referer( 'custobar_admin' );

		$api_token      = \WC_Admin_Settings::get_option( 'custobar_api_setting_token', false );
		$company_domain = \WC_Admin_Settings::get_option( 'custobar_api_setting_company', false );
		$url            = sprintf( 'https://%s.custobar.com/api', $company_domain ) . '/data/customers/';

		$response = wp_remote_request(
			$url,
			array(
				'method'  => 'GET',
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Token ' . $api_token,
				),
			)
		);

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 200 === $response_code ) {
			$message = 'Successful test, your site is connected to Custobar.';
		} else {
			$message = 'Sorry the test failed, please check your API token and domain and try again. If the problems persists please contact Custobar support.';
		}

		$response = array(
			'url'     => $url,
			'code'    => $response_code,
			'message' => $message,
		);

		wp_send_json_success( $response );
	}
}
