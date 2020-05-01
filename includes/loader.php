<?php

add_action('plugins_loaded', 'custobarLoadClasses');

function custobarLoadClasses() {

  require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/includes/functions.php');
  require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/includes/wp-async-task.php');
  require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/Plugin.php');
  require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataUpload.php');
  require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/API.php');

  add_filter( 'woocommerce_get_settings_pages', function() {

    require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/Settings.php');
    return new \WooCommerceCustobar\WC_Settings_Custobar();

  });

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
  require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/Synchronization/SyncTracker.php');
  require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/Template.php');
  require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/FieldsMap.php');
  require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataSource/AbstractDataSource.php');
  require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataSource/Product.php');
  require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataSource/Customer.php');
  require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataSource/Sale.php');
  require_once(WOOCOMMERCE_CUSTOBAR_PATH . '/classes/DataSource/CustobarDataSource.php');

  \WooCommerceCustobar\load_localizations();
  \WooCommerceCustobar\plugin()->initialize();

}
