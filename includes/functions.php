<?php

namespace WooCommerceCustobar;

defined('ABSPATH') or exit;

/**
 * functions.php
 */

/**
 * Get plugin main class instance.
 *
 * @return Plugin
 */
function plugin()
{
    global $plugin;

    if (!$plugin instanceof Plugin) {
        $plugin = new Plugin();
    }

    return $plugin;
}

/**
 * Get the plugin directory.
 *
 * @return string
 */
function get_plugin_dir()
{
    return dirname(__DIR__);
}

/**
 * Load the plugin text domain for this plugin.
 *
 * @return void
 */
function load_localizations()
{
    add_action('plugins_loaded', function () {
        $domain = 'woocommerce-custobar';
        $locale = apply_filters( 'plugin_locale', is_admin() ? get_user_locale() : get_locale(), $domain );
        $mofile = $domain . '-' . $locale . '.mo';
        load_textdomain($domain, get_plugin_dir().'/languages/' . $mofile );
    });
}

/**
 * Loads given locale file if found.
 * Plugin's default language is in English therefore no language file, only unload
 * @param null|string $locale 'en' to unload to plugin own language, otherwise locale code
 */
function load_specific_plugin_textdomain($locale=null) {
    $domain = 'woocommerce-custobar';
    if($locale === null) {
        $locale = apply_filters( 'plugin_locale', is_admin() ? get_user_locale() : get_locale(), $domain );
    }
    if($locale === 'en') {
        unload_textdomain($domain);
        return;
    }
    $mofile = $domain . '-' . $locale . '.mo';
    if(file_exists(get_plugin_dir().'/languages/'.$mofile)) {
        unload_textdomain($domain);
        load_textdomain($domain, get_plugin_dir().'/languages/' . $mofile );
    }
}
