<?php

namespace WooCommerceCustobar;

defined('ABSPATH') or exit;

use WooCommerceCustobar\Synchronization\ProductSync;
use WooCommerceCustobar\Synchronization\CustomerSync;
use WooCommerceCustobar\Synchronization\SaleSync;

/**
 * Class DataUpload
 *
 * @package WooCommerceCustobar
 */
class DataUpload {

  public static function uploadCustobarData($endpoint, $data) {

    $responseData = new \stdClass;

    $body = json_encode($data);
    $apiToken = \WC_Admin_Settings::get_option( 'custobar_api_setting_token', false );
    $companyDomain = \WC_Admin_Settings::get_option( 'custobar_api_setting_company', false );
    $url = sprintf('https://%s.custobar.com/api', $companyDomain) . $endpoint;

    $response = wp_remote_request($url, array(
      'method' => 'PUT',
      'headers' => array(
        'Content-Type'  => 'application/json',
        'Authorization' => 'Token ' . $apiToken
      ),
      'body' => $body
    ));

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    // form response data
    $responseData->code = $response_code;
    $responseData->body = $response_body;

    // do wc logging
    if (!in_array($response_code, array(200, 201)) || is_wp_error($response_body)) {
      wc_get_logger()->warning('Custobar data upload failed', array(
        'source'        => 'woocommerce-custobar',
        'response_code' => $response_code,
        'response_body' => $response_body,
      ));
    } else {
      wc_get_logger()->info('Sent request to Custobar API', array(
        'source'        => 'woocommerce-custobar',
        'response_code' => $response_code,
        'response_body' => $response_body,
      ));
    }

    // return response
    return $responseData;

  }

  public static function addHooks() {
    add_action( 'wp_ajax_custobar_export', __CLASS__ . '::jxExport' );
  }

  public static function jxExport() {

    // change to public functions (self::)

    $plugin = new Plugin();

    if ($plugin::isWooCommerceActived() && $plugin::hasAllSettingsDefined()) {
      $response = CustomerSync::batchUpdate();
      // ProductSync::batchUpdate();
      // SaleSync::batchUpdate();
    }

    if( $response_code == 200 ) {
      $message = "Successful test, your site is connected to Custobar.";
    } else {
      $message = "Sorry the test failed, please check your API token and domain and try again. If the problems persists please contact Custobar support.";
    }

    $response = array(
      'url'     => $url,
      'code'    => $response_code,
      'body'    => $response_body,
      'message' => $message
    );
    print json_encode( $response );

    wp_die();

  }

}
