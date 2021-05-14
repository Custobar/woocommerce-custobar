<?php

namespace WooCommerceCustobar;

defined( 'ABSPATH' ) || exit;

use WooCommerceCustobar\Synchronization\Product_Sync;
use WooCommerceCustobar\Synchronization\Customer_Sync;
use WooCommerceCustobar\Synchronization\Sale_Sync;
use WooCommerceCustobar\Synchronization\Data_Sync;

/**
 * Class Plugin
 *
 * @package WooCommerceCustobar
 */
class Plugin {

	/**
	 * Initialize this instance.
	 *
	 * Note: the WP `init` hook has presumably not run yet when calling this method,
	 * so hook to it in case something doesn't seem to work as expected.
	 *
	 * @return void
	 */
	public static function initialize() {

		if ( self::is_woocommerce_activated() && self::has_all_settings_defined() ) {

			// Data type hooks
			Product_Sync::add_hooks();
			Customer_Sync::add_hooks();
			Sale_Sync::add_hooks();
			Data_Sync::add_hooks();
			Data_Upload::add_hooks();
		}
	}

	/**
	 * Checks if WooCommerce is active.
	 *
	 * @return boolean
	 */
	public static function is_woocommerce_activated() {
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check that all necessary settings have been set in the wp-config file.
	 *
	 * @return boolean
	 */
	public static function has_all_settings_defined() {
		// removed all checks here temporarily - add check for API connection
		return true;
	}

	/**
	 * Uploads initial data of all defined data types to Custobar.
	 *
	 * @return void
	 */
	protected static function run_batch_upload_for_all_data_types() {
		if ( self::is_woocommerce_activated() && self::has_all_settings_defined() ) {
			Customer_Sync::batch_update();
			Product_Sync::batch_update();
			Sale_Sync::batch_update();
		}
	}

	/**
	 * Plugin activation.
	 *
	 * @return void
	 */
	public static function activate() {

		// setup default field maps
		$field_maps = \WooCommerceCustobar\Fields_Map::get_fields_map_for_front();
		update_option( 'custobar_product_fields', $field_maps['custobar_product_fields'] );
		update_option( 'custobar_customer_fields', $field_maps['custobar_customer_fields'] );
		update_option( 'custobar_sale_fields', $field_maps['custobar_sale_fields'] );

	}

	/**
	 * Plugin deactivation.
	 *
	 * @return void
	 */
	public static function deactivate() {

	}
}
