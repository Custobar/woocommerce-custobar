<?php

namespace WooCommerceCustobar;

defined('ABSPATH') or exit;

use WC_Settings_Page;
use WC_Admin_Settings;

/**
 * Class Settings
 *
 * @package WooCommerceCustobar
 */
class WC_Settings_Custobar extends WC_Settings_Page {

  /**
   * Bootstraps the class and hooks required actions & filters.
   *
   */
  public function __construct() {

    add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ));

    $this->id = 'custobar';
    $this->label = __( 'Custobar', 'woocommerce-custobar' );

    parent::__construct();

  }

  public function add_settings_page( $pages ) {
		return parent::add_settings_page( $pages );
	}

  public function get_sections() {

    return array(
			''        => __( 'Data Syncronization', 'woocommerce-custobar' ),
			'fields' => __( 'Field Settings', 'woocommerce-custobar' ),
			'api' => __( 'API Settings', 'woocommerce-custobar' ),
		);

  }

  /**
   * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
   *
   * @uses woocommerce_update_options()
   * @uses self::get_settings_api()
   */
  public function save() {
    woocommerce_update_options( $this->get_settings_api() );
    woocommerce_update_options( $this->get_settings_fields() );
  }


  /**
   * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
   *
   * @return array Array of settings for @see woocommerce_admin_fields() function.
   */
  public function get_settings_api() {

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

    return $settings;

  }

  /**
   * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
   *
   * @return array Array of settings for @see woocommerce_admin_fields() function.
   */
  public function get_settings_fields() {

    $settings = array(
      'custobar_customer_fields' => array(
        'name'     => __( 'Customer Field Map', 'woocommerce-custobar' ),
        'type'     => 'textarea',
        'desc'     => '',
        'id'       => 'custobar_customer_fields'
      ),
      'section_end' => array(
        'type' => 'sectionend',
        'id' => 'custobar_section_end'
      )
    );

    return $settings;

  }

  public function output() {

    global $current_section, $hide_save_button;

    print '<div id="custobar-settings">';

    if ( '' === $current_section ) {

      $hide_save_button = true;

      $dataUpload = new DataUpload();
      $template = new Template();

      $productStat = $dataUpload->fetchSyncStatProducts();
      $saleStat = $dataUpload->fetchSyncStatSales();
      $customerStat = $dataUpload->fetchSyncStatCustomers();

      $template = new Template();
      $template->name = 'sync-report';
      $template->data = [
        'productStat' => $productStat,
        'saleStat' => $saleStat,
        'customerStat' => $customerStat
      ];
      print $template->get();

    } elseif ( 'api' === $current_section ) {

      $template = new Template();
      $template->name = 'api-test';
      $template->data = [];
      print $template->get();

      WC_Admin_Settings::output_fields( $this->get_settings_api() );


    } else {

      WC_Admin_Settings::output_fields( $this->get_settings_fields() );

    }

    print '</div>'; // close settings wrap

  }

  public function scripts() {

    wp_enqueue_script(
      'custobar-admin-js',
      WOOCOMMERCE_CUSTOBAR_URL . 'assets/custobar.admin.js',
      array( 'jquery' ),
      '1.0.0',
      true
    );

    wp_enqueue_style(
      'custobar-admin-style',
      WOOCOMMERCE_CUSTOBAR_URL . 'assets/custobar.admin.css',
      array(),
      '1.0.0'
    );

  }

}
