<?php

/**
 * Plugin name: WooCommerce Custobar
 * Description: Syncs your WooCommerce data with Custobar CRM.
 * Author: Custobar CRM
 * Text Domain: woocommerce-custobar
 * Version: 1.3.0
 * Domain Path: /languages
 * WC requires at least: 3.0
 */

define( 'WOOCOMMERCE_CUSTOBAR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOOCOMMERCE_CUSTOBAR_URL', plugin_dir_url( __FILE__ ) );
define( 'WOOCOMMERCE_CUSTOBAR_VERSION', '1.3.0' );

require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/includes/loader.php');

register_activation_hook(__FILE__, [\WooCommerceCustobar\Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [\WooCommerceCustobar\Plugin::class, 'deactivate']);
