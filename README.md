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

## Requires

- WooCommerce 3.x
- Tested up to PHP 7.4 and WordPress 5.4

## Notices

- This plugin supports WooCommerce Subscriptions by adding custom fields for some basic information

- Order totals include VAT but order items do not (this is the default functionality of WooCommerce)

- Permission for marketing is asked in the checkout phase only for unauthenticated users. Usually customer do not register before the first order so practically this is rarely a problem. Permission is not given if it's not asked. This functionality might have to be changed if you offer a path to registration without purchase.

- Permission for marketing sets _email_ and _sms_ permissions if selected. No functionality to edit this through filters at this point.
