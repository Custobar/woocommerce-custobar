<?php

namespace WooCommerceCustobar;

defined('ABSPATH') or exit;

use WooCommerceCustobar\Synchronization\ProductSync;
use WooCommerceCustobar\Synchronization\CustomerSync;
use WooCommerceCustobar\Synchronization\SaleSync;

/**
 * Class Plugin
 *
 * @package WooCommerceCustobar
 */
class Plugin
{
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
    public function initialize()
    {

        if ($this->initialized) {
            return;
        }

        $this->initialized = true;

        if (self::isWooCommerceActived() && self::hasAllSettingsDefined()) {

          // Data type hooks
          ProductSync::addHooks();
          CustomerSync::addHooks();
          SaleSync::addHooks();

          // Add other
          add_action('woocommerce_after_checkout_registration_form', [__CLASS__, 'askPermissionForMarketing']);
          add_action('woocommerce_checkout_update_order_meta', [__CLASS__, 'savePermissionForMarketing']);

        }
    }

    /**
     * Adds a checkbox field to the checkout asking for permissions for
     * marketing.
     */
    public static function askPermissionForMarketing($checkout)
    {
        woocommerce_form_field('marketing_permission', array(
            'type'  => 'checkbox',
            'class' => array('input-checkbox'),
            'label' => apply_filters(
                'woocommerce_custobar_marketing_permission_text',
                __('I would like to receive marketing messages', 'woocommerce-custobar')
            ),
        ), $checkout->get_value('marketing_permission'));
    }

    public static function savePermissionForMarketing($order_id)
    {
        if (isset($_POST['marketing_permission']) && $_POST['marketing_permission']) {
            update_post_meta($order_id, '_woocommerce_custobar_can_email', esc_attr($_POST['marketing_permission']));
            update_post_meta($order_id, '_woocommerce_custobar_can_sms', esc_attr($_POST['marketing_permission']));
        }
    }

    /**
     * Checks if WooCommerce is active.
     *
     * @return boolean
     */
    protected static function isWooCommerceActived()
    {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            return true;
        }
        return false;
    }

    /**
     * Check that all necessary settings have been set in the wp-config file.
     *
     * @return boolean
     */
    protected static function hasAllSettingsDefined()
    {
        if (defined('WOOCOMMERCE_CUSTOBAR_USERNAME') &&
            defined('WOOCOMMERCE_CUSTOBAR_PASSWORD') &&
            defined('WOOCOMMERCE_CUSTOBAR_API_PREFIX')) {
            return true;
        }
        return false;
    }

    /**
     * Uploads initial data of all defined data types to Custobar.
     *
     * @return void
     */
    protected static function runBatchUploadForAllDataTypes()
    {

        if (self::isWooCommerceActived() && self::hasAllSettingsDefined()) {
            CustomerSync::batchUpdate();
            ProductSync::batchUpdate();
            SaleSync::batchUpdate();
        }
    }

    /**
     * Plugin activation.
     *
     * @return void
     */
    public static function activate() {
      self::runBatchUploadForAllDataTypes();
    }

    /**
     * Plugin deactivation.
     *
     * @return void
     */
    public static function deactivate()
    {
    }
}
