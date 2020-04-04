<?php

namespace WooCommerceCustobar;

defined('ABSPATH') or exit;

/**
 * Class DataUpload
 *
 * @package WooCommerceCustobar
 */
class DataUpload
{
    public static function uploadCustobarData($endpoint, $data)
    {

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
    }
}
