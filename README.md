# Woocommerce Custobar

This WordPress plugin is used to send statistics from the WooCommerce plugin to the Custobar API.

Synchronization is provided by updates of 3 data types that include sales, customers and products. This is done by asynchronous API calls (utilizing scheduled events) after the relevant create or update action has been triggered. No data is removed or synced backwards from Custobar by this plugin nor does the API provide such functionality.

Relevant WooCommerce data has been mapped to the data fields offered by Custobar. Mapped data goes through WordPress filters so it's possible for site developers to add custom fields to the data before it gets sent to the Custobar API.

This plugin uses create and update hooks of WooCommerce's CRUD commands which were introduced in version 3.0, so no support for previous versions is provided.

Plugin code has been written following the PSR-2 coding standard.

For more information on Custobar API:
https://www.custobar.com/api/docs/

## Installation

Installation for the Custobar WooCommerce Plugin is the same as any other WordPress plugin.

1. Upload the plugin .zip file to your site plugin directory at /wp-content/plugins/.

2. Unzip the plugin file. If needed change the name of the parent directory to "custobar-woocommerce-plugin".

3. Visit the plugins page in the WP admin, activate the plugin.

4. Set API credentials via Plugin settings at WooCommerce -> Settings -> Custobar

## Requires

- WooCommerce >5.0
- Tested up to PHP 7.4 and WordPress 5.4

## Notices

- This plugin supports WooCommerce Subscriptions by adding custom fields for some basic information
- This plugin adds support for Custobar marketing permissions. The plugin also adds a Rest API endpoint that allows syncing marketing permissions from Custobar to WooCommerce. Please contact Custobar for further information on how to configure the needed webhook in Custobar.

## Add custom fields

1.  Add custom field to the Custobar
    https://www.custobar.com/docs/guides/data-schema-configuration/

2.  Add custom field via plugin settings at WooCommerce -> Settings ->  Custobar -> Field Settings

    ```
    custobar_custom_field_name: data_source_field_name
    ```


## Add custom data sources


1.  Create_custom_data_source with create_custom_data_source() function

    Customer Field:
    ```
    Customer::create_custom_data_source('custom_data_source_field_name', function(\WC_Customer $wccustomer){})
    ```
    Product Field:
    ```
    Product::create_custom_data_source('custom_data_source_field_name', function(\WC_Product $wcproduct){})
    ```
    Sale Field:
    ```
    Sale::create_custom_data_source('custom_data_source_field_name', function(\WC_Order $wcorder){})
    ```


2.  Add custom data source via plugin settings at WooCommerce -> Settings ->  Custobar -> Field Settings

    ```
    custobar_field_name: custom_data_source_field_name
    ```



###### Examples:

```
use WooCommerceCustobar\DataSource\Customer;

Customer::create_custom_data_source(
	'shop_url',
	function () {
		$site_url = site_url();
		return $site_url;
	}
);

Customer::create_custom_data_source(
	'customer_name',
	function ( \WC_Customer $wccustomer ) {
		$first_name    = $wccustomer->get_first_name();
		$last_name     = $wccustomer->get_last_name();
		$customer_name = $first_name . ' ' . $last_name;
		return $customer_name;
	}
);
```
