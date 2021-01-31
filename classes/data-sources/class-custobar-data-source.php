<?php

namespace WooCommerceCustobar\DataSource;

defined( 'ABSPATH' ) || exit;

class Custobar_Data_Source {


	const CUSTOBAR_INTEGRATION_KEY           = 'custobar_integration';
	const CUSTOBAR_SALES_DATA_SOURCE_KEY     = 'custobar_data_source_sale';
	const CUSTOBAR_CUSTOMERS_DATA_SOURCE_KEY = 'custobar_data_source_customer';
	const CUSTOBAR_PRODUCTS_DATA_SOURCE_KEY  = 'custobar_data_source_product';

	public function get_integration_id() {
		return get_option( self::CUSTOBAR_INTEGRATION_KEY, false );
	}

	public function get_sale_data_source_id() {
		return get_option( self::CUSTOBAR_SALES_DATA_SOURCE_KEY, false );
	}

	public function get_customer_data_source_id() {
		return get_option( self::CUSTOBAR_CUSTOMERS_DATA_SOURCE_KEY, false );
	}

	public function get_product_data_source_id() {
		return get_option( self::CUSTOBAR_PRODUCTS_DATA_SOURCE_KEY, false );
	}

	public function create_integration() {
		$data           = array( 'name' => 'WooCommerce' );
		$body           = wp_json_encode( $data );
		$endpoint       = '/integrations/';
		$api_token      = get_option( 'custobar_api_setting_token' );
		$company_domain = get_option( 'custobar_api_setting_company' );
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

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		$response = json_decode( $response_body );

		if ( 200 === $response_code ) {
			update_option( self::CUSTOBAR_INTEGRATION_KEY, $response->integration->id );
			return $response->integration->id;
		}

		return false;

	}

	public function create_data_source( $name, $type ) {
		$data = array(
			'name'        => $name,
			'integration' => $this->get_integration_id(),
			'datatype'    => $type,
		);

		$body           = wp_json_encode( $data );
		$endpoint       = '/datasources/';
		$api_token      = get_option( 'custobar_api_setting_token' );
		$company_domain = get_option( 'custobar_api_setting_company' );
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

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		$response = json_decode( $response_body );
		if ( 200 === $response_code ) {
			switch ( $type ) {
				case 'products':
					update_option( self::CUSTOBAR_PRODUCTS_DATA_SOURCE_KEY, $response->datasource->id );
					break;
				case 'sales':
					update_option( self::CUSTOBAR_SALES_DATA_SOURCE_KEY, $response->datasource->id );
					break;
				case 'customers':
					update_option( self::CUSTOBAR_CUSTOMERS_DATA_SOURCE_KEY, $response->datasource->id );
					break;
			}
		}

		return $response->datasource->id;
	}
}
