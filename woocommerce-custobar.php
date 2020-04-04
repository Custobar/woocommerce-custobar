<?php

/**
 * Plugin name: WooCommerce Custobar
 * Description: Syncs your WooCommerce data with Custobar CRM.
 * Author: Custobar CRM
 * Text Domain: woocommerce-custobar
 * Domain Path: /languages
 * WC requires at least: 3.0
 */

define( 'WOOCOMMERCE_CUSTOBAR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOOCOMMERCE_CUSTOBAR_URL', plugin_dir_url( __FILE__ ) );
define( 'WOOCOMMERCE_CUSTOBAR_VERSION', '1.0.0' );

require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/includes/loader.php');

\WooCommerceCustobar\Settings::init();
\WooCommerceCustobar\load_localizations();

register_activation_hook(__FILE__, [\WooCommerceCustobar\Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [\WooCommerceCustobar\Plugin::class, 'deactivate']);

\WooCommerceCustobar\plugin()->initialize();

/*
 * API test call
 */
function testCall() {

  $endpoint = '/customers/upload/';
  $data = array(
    'customers' => array(
      array(
        "first_name" => "Sengridita",
        "last_name" => "Gue",
        "email" => "seng.gue@gmail.com",
        "phone_number" => "+9382718374",
      )
    )
  );
  \WooCommerceCustobar\DataUpload::uploadCustobarData($endpoint, $data);

}

add_action('init', 'testCall', 50000);
