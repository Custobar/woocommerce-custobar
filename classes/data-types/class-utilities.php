<?php

namespace WooCommerceCustobar\DataType;

defined( 'ABSPATH' ) || exit;

/**
 * Class Utilities
 *
 * @package WooCommerceCustobar\DataType
 */
class Utilities {

	/**
	 * Returns the price in cents and rounds to total cents.
	 *
	 * @param  string $price
	 *
	 * @return int
	 */
	public static function get_price_in_cents( $price ) {
		return (int) round( (float) $price * 100 );
	}

	/**
	 * Returns the time in the format required by Custobar API.
	 *
	 * @param  WC_DateTime|DateTime $datetime
	 *
	 * @return string
	 */
	public static function format_datetime( $datetime ) {
		if ( ! $datetime || ! is_a( $datetime, 'DateTime' ) ) {
			return null;
		}

		// ISO8601 formatted datetime
		return $datetime->setTimezone( new \DateTimeZone( 'UTC' ) )->format( 'c' );
	}

	/**
	 * Get single data item from Custobar
	 *
	 * @param string $data_type
	 * @param int    $external_id
	 * @return array|boolean|WP_Error If successfull return Custobar data as array, false if no results or WP_Error on failure
	 */
	public static function get_data( $data_type, $external_id ) {
		$api_token      = \WC_Admin_Settings::get_option( 'custobar_api_setting_token', false );
		$company_domain = \WC_Admin_Settings::get_option( 'custobar_api_setting_company', false );
		$url            = sprintf( 'https://%s.custobar.com/api', $company_domain ) . '/data/' . $data_type . '/?external_id=' . $external_id;

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
		$response_body = wp_remote_retrieve_body( $response );

		if ( 200 === $response_code ) {
			//Handle successfull request
			$response_body_array = json_decode( $response_body, true );
			// Since we are using the external key, we are expecting one result from the correct data type. Just making sure.
			if ( isset( $response_body_array[ $data_type ] ) && 1 === count( $response_body_array[ $data_type ] ) ) {
				return $response_body_array[ $data_type ][0];
			} else {
				return false;
			}
		} else {
			// Return WP_Error on error
			$error_response = json_decode( $response_body, true );
			$error_reason   = $error_response['error']['reason'] ?? '';
			return new \WP_Error( $response_code, $error_reason );
		}
	}

	public function get_assigned_properties() {
		return $this->get_assigned_properties_base();
	}
}
