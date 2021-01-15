<?php

namespace WooCommerceCustobar;

defined( 'ABSPATH' ) || exit;

use WooCommerceCustobar\Synchronization\Product_Sync;
use WooCommerceCustobar\Synchronization\Customer_Sync;
use WooCommerceCustobar\Synchronization\Sale_Sync;

/**
 * Class Plugin
 *
 * @package WooCommerceCustobar
 */
class Plugin {

	/**
	 * Has this instance been initialized?
	 *
	 * @access protected
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * Initialize this instance.
	 *
	 * Note: the WP `init` hook has presumably not run yet when calling this method,
	 * so hook to it in case something doesn't seem to work as expected.
	 *
	 * @return void
	 */
	public function initialize() {

		if ( $this->initialized ) {
			return;
		}

		$this->initialized = true;

		if ( self::is_woocommerce_activated() && self::has_all_settings_defined() ) {

			// Data type hooks
			Product_Sync::add_hooks();
			Customer_Sync::add_hooks();
			Sale_Sync::add_hooks();
			Data_Upload::add_hooks();

			// Add other

			// add_action('woocommerce_after_checkout_registration_form', [__CLASS__, 'ask_permission_for_marketing']);
			// add_action('woocommerce_checkout_update_order_meta', [__CLASS__, 'save_permission_for_marketing']);
		}

	}

	/**
	 * Adds a checkbox field to the checkout asking for permissions for
	 * marketing.
	 */
	public static function ask_permission_for_marketing( $checkout ) {
		woocommerce_form_field(
			'marketing_permission',
			array(
				'type'  => 'checkbox',
				'class' => array( 'input-checkbox' ),
				'label' => apply_filters(
					'woocommerce_custobar_marketing_permission_text',
					__( 'I would like to receive marketing messages', 'woocommerce-custobar' )
				),
			),
			$checkout->get_value( 'marketing_permission' )
		);
	}

	public static function save_permission_for_marketing( $order_id ) {
		if ( isset( $_POST['marketing_permission'] ) && $_POST['marketing_permission'] ) {
			update_post_meta( $order_id, '_woocommerce_custobar_can_email', esc_attr( $_POST['marketing_permission'] ) );
			update_post_meta( $order_id, '_woocommerce_custobar_can_sms', esc_attr( $_POST['marketing_permission'] ) );
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
