<?php

defined( 'ABSPATH' ) || exit;

require_once WOOCOMMERCE_CUSTOBAR_PATH . '/includes/functions.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/class-plugin.php';
require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/class-data-upload.php';

add_filter(
	'woocommerce_get_settings_pages',
	function ( $settings ) {
		require_once WOOCOMMERCE_CUSTOBAR_PATH . '/classes/class-settings.php';
		$settings[] = new \WooCommerceCustobar\WC_Settings_Custobar();
		return $settings;
	}
);

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

\WooCommerceCustobar\load_localizations();
\WooCommerceCustobar\plugin()->initialize();
