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
      'method' => 'POST',
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

      wc_get_logger()->warning("Custobar data upload failed. code: $response_code", array(
        'source'        => 'woocommerce-custobar'
      ));
    }

    // return response
    return $responseData;

  }

  public static function addHooks() {
    add_action( 'wp_ajax_custobar_export', __CLASS__ . '::jxExport' );
    add_action( 'wp_ajax_custobar_api_test', __CLASS__ . '::apiTest' );
  }

  public static function jxExport() {

    // environment checks
    $plugin = new Plugin();
    if ($plugin::isWooCommerceActived() && $plugin::hasAllSettingsDefined()) {

      $recordType = sanitize_text_field( $_POST['recordType'] );
      $resetOffset = !empty($_POST['reset']);

      switch( $recordType ) {
        case 'customer':
          if ($resetOffset) {
            // Pass false as total to trigger total count update
            CustomerSync::trackerSave(0, false);
          }
          $apiResponse = CustomerSync::batchUpdate();
          $apiResponse->stats = self::fetchSyncStatCustomers();
          break;
        case 'sale':
          if ($resetOffset) {
            // Pass false as total to trigger total count update
            SaleSync::trackerSave(0, false);
          }
          $apiResponse = SaleSync::batchUpdate();
          $apiResponse->stats = self::fetchSyncStatSales();
          break;
        case 'product':
          if ($resetOffset) {
            // Pass false as totals to trigger total count update
            ProductSync::trackerSave(0, 0, false, false);
          }
          $apiResponse = ProductSync::batchUpdate();
          $apiResponse->stats = self::fetchSyncStatProducts();
          break;
      }

      $apiResponse->recordType = $recordType;

    } else {
      $response = array(
        'code' => 420
      );
      print json_encode( $response );
      wp_die();
    }

    if( $apiResponse ) {
      print json_encode( $apiResponse );
    } else {
      $response = array(
        'code' => 440
      );
      print json_encode( $response );
    }

    wp_die();

  }

  public static function fetchSyncStatProducts() {

    $stat = new \stdClass;
    $tracker = ProductSync::trackerFetch();

    // get total product count
    if (isset($tracker['total'], $tracker['variant_total']) && is_int($tracker['total']) && is_int($tracker['variant_total'])) {
      $stat->total = $tracker['total'];
      $stat->variant_total = $tracker['variant_total'];
    } else {

      $product_count = 0;
      foreach(wp_count_posts( 'product' ) as $state=>$count) {
        $product_count += $count;
      }

      $stat->total = $product_count;

      $variant_count = 0;
      foreach(wp_count_posts( 'product_variation' ) as $state=>$count) {
        $variant_count += $count;
      }

      $stat->variant_total = $variant_count;
      ProductSync::trackerSave(null, null, $stat->total, $stat->variant_total);
    }

    $stat->synced = $tracker['offset'];
    if( $stat->total > 0 ) {
      $stat->synced_percent = number_format(($stat->synced / $stat->total) * 100) . '%';
    } else {
      $stat->synced_percent = '-';
    }

    $stat->variant_synced = $tracker['variant_offset'];
    if( $stat->variant_synced > 0 ) {
      $stat->synced_percent .= ' / ' . number_format(($stat->variant_synced / $stat->variant_total) * 100) . '%';
    } else {
      $stat->synced_percent .= ' / -';
    }

    if (is_int($tracker['updated']) && $tracker['updated']) {
      $stat->last_updated = date(wc_date_format(), $tracker['updated']) . ' ' . date(wc_time_format(), $tracker['updated']);
    } else {
      $stat->last_updated = '';
    }

    return $stat;

  }

  public static function fetchSyncStatSales() {

    $stat = new \stdClass;
    $tracker = SaleSync::trackerFetch();

    // Cache total count
    if (isset($tracker['total']) && is_int($tracker['total'])) {
      $stat->total = $tracker['total'];
    } else {
      $order_count = 0;
      foreach(wp_count_posts( 'shop_order' ) as $state=>$count) {
        $order_count += $count;
      }

      $stat->total = $order_count;
      SaleSync::trackerSave(null, $stat->total);
    }

    $stat->synced = $tracker['offset'];

    if( $stat->total > 0 ) {
      $stat->synced_percent = number_format(($stat->synced / $stat->total) * 100) . '%';
    } else {
      $stat->synced_percent = '-';
    }

    if (is_int($tracker['updated']) && $tracker['updated']) {
      $stat->last_updated = date(wc_date_format(), $tracker['updated']) . ' ' . date(wc_time_format(), $tracker['updated']);
    } else {
      $stat->last_updated = '';
    }

    return $stat;

  }

  public static function fetchSyncStatCustomers() {

    $stat = new \stdClass;
    $tracker = CustomerSync::trackerFetch();

    // Cache total count
    if (isset($tracker['total']) && is_int($tracker['total'])) {
      $stat->total = $tracker['total'];
    } else {
      $query = new \WP_User_Query(
        array(
          'role'   => 'customer',
          'fields' => 'ID'
        )
      );
      
      $stat->total = $query->get_total();
      CustomerSync::trackerSave(null, $stat->total);
    }

    $stat->synced = $tracker['offset'];
    if( $stat->total > 0 ) {
      $stat->synced_percent = number_format(($stat->synced / $stat->total) * 100) . '%';
    } else {
      $stat->synced_percent = '-';
    }

    if (is_int($tracker['updated']) && $tracker['updated']) {
      $stat->last_updated = date(wc_date_format(), $tracker['updated']) . ' ' . date(wc_time_format(), $tracker['updated']);
    } else {
      $stat->last_updated = '';
    }

    return $stat;

  }

  public static function apiTest() {

    $apiToken = \WC_Admin_Settings::get_option( 'custobar_api_setting_token', false );
    $companyDomain = \WC_Admin_Settings::get_option( 'custobar_api_setting_company', false );
    $url = sprintf('https://%s.custobar.com/api', $companyDomain) . '/data/customers/';

    $response = wp_remote_request($url, array(
      'method' => 'GET',
      'headers' => array(
        'Content-Type'  => 'application/json',
        'Authorization' => 'Token ' . $apiToken
      )
    ));

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

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
