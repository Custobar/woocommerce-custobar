<?php

/**
 * Plugin name: Custobar for WooCommerce
 * Description: Syncs your WooCommerce data with Custobar.
 * Author: Custobar
 * Text Domain: woocommerce-custobar
 * Version: 2.8.2
 * Domain Path: /languages
 * WC requires at least: 5.0
 * Requires PHP 7.4+
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WOOCOMMERCE_CUSTOBAR_PATH' ) ) {
	define( 'WOOCOMMERCE_CUSTOBAR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WOOCOMMERCE_CUSTOBAR_URL' ) ) {
	define( 'WOOCOMMERCE_CUSTOBAR_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'WOOCOMMERCE_CUSTOBAR_VERSION' ) ) {
	define( 'WOOCOMMERCE_CUSTOBAR_VERSION', '2.6.0' );
}

require_once WOOCOMMERCE_CUSTOBAR_PATH . '/includes/functions.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/includes/checkout.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/includes/my-account.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/class-plugin.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/class-data-upload.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/data-types/abstract-custobar-data-type.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/data-types/class-custobar-customer.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/data-types/class-custobar-event.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/data-types/class-custobar-product.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/data-types/class-custobar-sale.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/data-types/class-custobar-shop.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/data-types/class-utilities.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/sync-classes/abstract-data-sync.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/sync-classes/class-customer-sync.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/sync-classes/class-product-sync.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/sync-classes/class-sale-sync.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/class-template.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/class-fields-map.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/data-sources/abstract-data-source.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/data-sources/class-product.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/data-sources/class-customer.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/data-sources/class-sale.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/data-sources/class-custobar-data-source.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/rest-api/class-rest-marketing-permissions.php';

// Add settings page
add_filter(
	'woocommerce_get_settings_pages',
	function ( $settings ) {
		require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/class-settings.php';
		$settings[] = new \WooCommerceCustobar\WC_Settings_Custobar();
		return $settings;
	}
);

register_activation_hook( __FILE__, array( 'WooCommerceCustobar\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WooCommerceCustobar\Plugin', 'deactivate' ) );

// Initialize plugin after WooCommerce is loaded
add_action( 'plugins_loaded', array( 'WooCommerceCustobar\Plugin', 'initialize' ) );

// Initialize Rest API endpoint
add_action( 'init', array( new \WooCommerceCustobar\RestAPI\REST_Marketing_Permissions(), 'init' ) );

// Load translations
add_action( 'init', 'WooCommerceCustobar\load_textdomain' );


/**
 * Add custobar tracking script.
 */
function custobar_tracking_script() {
	$script = get_option( 'custobar_api_tracking_script' );
	if ( isset( $script ) ) {
		// phpcs:ignore
		echo( '<script>' . $script . '</script>' );
	}
}

add_action( 'wp_head', 'custobar_tracking_script' );
