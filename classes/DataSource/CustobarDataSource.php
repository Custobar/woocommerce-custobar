<?php

namespace WooCommerceCustobar\DataSource;

defined('ABSPATH') or exit;

class CustobarDataSource {

  const CUSTOBAR_INTEGRATION_KEY = 'custobar_integration';
  const CUSTOBAR_SALES_DATA_SOURCE_KEY = 'custobar_data_source_sale';
  const CUSTOBAR_CUSTOMERS_DATA_SOURCE_KEY = 'custobar_data_source_customer';
  const CUSTOBAR_PRODUCTS_DATA_SOURCE_KEY = 'custobar_data_source_product';

  public function getIntegrationId() {
    return get_option( CustobarDataSource::CUSTOBAR_INTEGRATION_KEY, false );
  }

  public function getSaleDataSourceId() {
    return get_option( CustobarDataSource::CUSTOBAR_SALES_DATA_SOURCE_KEY, false );
  }

  public function getCustomerDataSourceId() {
    return get_option( CustobarDataSource::CUSTOBAR_CUSTOMERS_DATA_SOURCE_KEY, false );
  }

  public function getProductDataSourceId() {
    return get_option( CustobarDataSource::CUSTOBAR_PRODUCTS_DATA_SOURCE_KEY, false );
  }

  public function createIntegration() {

    $data = [
      'name' => 'WooCommerce'
    ];
    $body = json_encode($data);
    $endpoint = '/integrations/';
    $apiToken = get_option( 'custobar_api_setting_token' );
    $companyDomain = get_option( 'custobar_api_setting_company' );
    $url = sprintf('https://%s.custobar.com/api', $companyDomain) . $endpoint;

    $response = wp_remote_request($url, array(
      'method' => 'POST',
      'headers' => array(
        'Content-Type'  => 'application/json',
        'Authorization' => 'Token ' . $apiToken
      ),
      'body' => $body
    ));

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    $response = json_decode( $response_body );

    if( $response_code == 200 ) {
      update_option( CustobarDataSource::CUSTOBAR_INTEGRATION_KEY, $response->integration->id );
    }

  }

  public function createDataSource( $name, $type ) {

    $data = [
      'name' => $name,
      'integration' => $this->getIntegrationId(),
      'datatype' => $type
    ];

    $body = json_encode($data);
    $endpoint = '/datasources/';
    $apiToken = get_option( 'custobar_api_setting_token' );
    $companyDomain = get_option( 'custobar_api_setting_company' );
    $url = sprintf('https://%s.custobar.com/api', $companyDomain) . $endpoint;

    $response = wp_remote_request($url, array(
      'method' => 'POST',
      'headers' => array(
        'Content-Type'  => 'application/json',
        'Authorization' => 'Token ' . $apiToken
      ),
      'body' => $body
    ));

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    $response = json_decode( $response_body );
    if( $response_code == 200 ) {
      switch( $type ) {
        case 'products':
          update_option( CustobarDataSource::CUSTOBAR_PRODUCTS_DATA_SOURCE_KEY, $response->datasource->id );
          break;
        case 'sales':
          update_option( CustobarDataSource::CUSTOBAR_SALES_DATA_SOURCE_KEY, $response->datasource->id );
          break;
        case 'customers':
          update_option( CustobarDataSource::CUSTOBAR_CUSTOMERS_DATA_SOURCE_KEY, $response->datasource->id );
          break;
      }
    }

    return $response->datasource->id;

  }



}
