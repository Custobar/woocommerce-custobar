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

require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/config.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/includes/functions.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/includes/post_type_log.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/includes/wp-async-task.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/Plugin.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataUpload.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/API.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/AsyncTasks/CustobarAsyncTask.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataType/AbstractCustobarDataType.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataType/CustobarCustomer.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataType/CustobarEvent.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataType/CustobarProduct.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataType/CustobarSale.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataType/CustobarShop.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataType/Utilities.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/Synchronization/AbstractDataSync.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/Synchronization/CustomerSync.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/Synchronization/ProductSync.php');
require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/Synchronization/SaleSync.php');

\WooCommerceCustobar\load_localizations();

register_activation_hook(__FILE__, [\WooCommerceCustobar\Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [\WooCommerceCustobar\Plugin::class, 'deactivate']);

\WooCommerceCustobar\plugin()->initialize();
