<?php

namespace WooCommerceCustobar;

defined('ABSPATH') or exit;

/**
 * Class Settings
 *
 * @package WooCommerceCustobar
 */
class Settings {

  /**
   * Bootstraps the class and hooks required actions & filters.
   *
   */
  public static function init() {
    add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
    add_action( 'woocommerce_settings_tabs_custobar_settings', __CLASS__ . '::settings_tab' );
    add_action( 'woocommerce_update_options_custobar_settings', __CLASS__ . '::update_settings' );
  }


  /**
   * Add a new settings tab to the WooCommerce settings tabs array.
   *
   * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
   * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
   */
  public static function add_settings_tab( $settings_tabs ) {
    $settings_tabs['custobar_settings'] = __( 'Custobar', 'woocommerce-custobar' );
    return $settings_tabs;
  }


  /**
   * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
   *
   * @uses woocommerce_admin_fields()
   * @uses self::get_settings()
   */
  public static function settings_tab() {
    woocommerce_admin_fields( self::get_settings() );
  }

  /**
   * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
   *
   * @uses woocommerce_update_options()
   * @uses self::get_settings()
   */
  public static function update_settings() {
    woocommerce_update_options( self::get_settings() );
  }


  /**
   * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
   *
   * @return array Array of settings for @see woocommerce_admin_fields() function.
   */
  public static function get_settings() {

    $settings = array(
      'custobar_api_settings' => array(
        'name'     => __( 'Custobar API Settings', 'woocommerce-custobar' ),
        'type'     => 'title',
        'desc'     => '',
        'id'       => 'custobar_api_settings'
      ),
      'custobar_api_token' => array(
        'name' => __( 'API Token', 'woocommerce-custobar' ),
        'type' => 'password',
        'desc' => __( 'Enter your Custobar API token.', 'woocommerce-custobar' ),
        'id'   => 'custobar_api_setting_token'
      ),
      'custobar_api_company' => array(
        'name' => __( 'Company Domain', 'woocommerce-custobar' ),
        'type' => 'text',
        'desc' => __( 'Enter the unique domain prefix for your Custobar account, for example if your Custobar account is at acme123.custobar.com then enter only acme123.', 'woocommerce-custobar' ),
        'id'   => 'custobar_api_setting_company'
      ),
      'section_end' => array(
        'type' => 'sectionend',
        'id' => 'custobar_section_end'
      )
    );

    return apply_filters( 'wc_settings_tab_demo_settings', $settings );
  }

}
