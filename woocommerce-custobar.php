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

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/includes/functions.php');

\Sofokus\WooCommerceCustobar\load_localizations();

register_activation_hook(__FILE__, [\Sofokus\WooCommerceCustobar\Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [\Sofokus\WooCommerceCustobar\Plugin::class, 'deactivate']);

\Sofokus\WooCommerceCustobar\plugin()->initialize();
