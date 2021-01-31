<?php

namespace WooCommerceCustobar;

defined( 'ABSPATH' ) || exit;

/**
 * functions.php
 */

/**
 * Get the plugin directory.
 *
 * @return string
 */
function get_plugin_dir() {
	return dirname( __DIR__ );
}

/**
 * Load plugin’s translated strings.
 */
function load_textdomain() {
	load_plugin_textdomain( 'woocommerce-custobar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
