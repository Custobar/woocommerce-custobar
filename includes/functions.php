<?php

namespace WooCommerceCustobar;

defined( 'ABSPATH' ) || exit;

/**
 * functions.php
 */

/**
 * Get plugin main class instance.
 *
 * @return Plugin
 */
function plugin() {
	global $plugin;

	if ( ! $plugin instanceof Plugin ) {
		$plugin = new Plugin();
	}

	return $plugin;
}

/**
 * Get the plugin directory.
 *
 * @return string
 */
function get_plugin_dir() {
	return dirname( __DIR__ );
}

/**
 * Load the plugin text domain for this plugin.
 *
 * @return void
 */
function load_localizations() {
	add_action(
		'plugins_loaded',
		function () {
			$domain = 'woocommerce-custobar';
			$locale = apply_filters( 'plugin_locale', is_admin() ? get_user_locale() : get_locale(), $domain );
			$mofile = $domain . '-' . $locale . '.mo';
			load_textdomain( $domain, get_plugin_dir() . '/languages/' . $mofile );
		}
	);
}
