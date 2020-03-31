<?php

/**
 * Plugin name: woocommerce-custobar
 * Description: Syncs relevant WooCommerce data to Custobar CRM.
 * Author: Sofokus
 * Text Domain: woocommerce-custobar
 * Domain Path: /languages
 * WC requires at least: 3.0
 */

define( 'WOOCOMMERCE_CUSTOBAR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOOCOMMERCE_CUSTOBAR_URL', plugin_dir_url( __FILE__ ) );
define( 'WOOCOMMERCE_CUSTOBAR_VERSION', '1.0.0' );

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/includes/functions.php');

\Sofokus\WooCommerceCustobar\load_localizations();

register_activation_hook(__FILE__, [\Sofokus\WooCommerceCustobar\Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [\Sofokus\WooCommerceCustobar\Plugin::class, 'deactivate']);

\Sofokus\WooCommerceCustobar\plugin()->initialize();


/*
$customerArray = [
  'customers' => [
    [
      "external_id" => "3619490226",
      "first_name" => "James",
      "last_name" => "Carroll",
      "email": "james.carroll@example.org",
      "phone_number": "+447757138957"
    ]
  ]
];
$customers = json_encode( $customerArray );
$response = wp_remote_request('https://woocomtest.custobar.com/api/customers/upload/', array(
  'method' => 'PUT',
  'headers' => array(
    'Content-Type'  => 'application/json',
    'Authorization' => 'Basic ' . base64_encode(WOOCOMMERCE_CUSTOBAR_USERNAME . ':' . WOOCOMMERCE_CUSTOBAR_PASSWORD)
  ),
  'body' => $customers
));

print '<pre>';
var_dump( $response );
print '</pre>';
*/
