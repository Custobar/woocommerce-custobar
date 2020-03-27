<?php

$wproot = explode('wp-content', __DIR__);
$wp_tests_directory = $wproot[0] . 'includes';

if (!is_dir($wp_tests_directory) || !is_file($wp_tests_directory . '/functions.php')) {
    echo 'WordPress PHPUnit test helpers not found!' . PHP_EOL;
    echo 'Please install the WordPress test helpers to the WordPress base path using Subversion:' . PHP_EOL . PHP_EOL;
    echo '$ cd <wp dir>' . PHP_EOL;
    echo '$ svn co http://develop.svn.wordpress.org/trunk/tests/phpunit/includes/' . PHP_EOL . PHP_EOL;
    echo 'Then create a test config file along side wp-config.php' . PHP_EOL . PHP_EOL;
    echo 'More info: https://nerds.inn.org/2014/10/22/unit-testing-themes-and-plugins-in-wordpress/' . PHP_EOL;

    exit(1);
}

// Load helper functions.
require_once($wp_tests_directory . '/functions.php');

// Load required theme and plugins.
tests_add_filter('muplugins_loaded', function () {
    // Update array with plugins to include
    $plugins_to_active = [
        'woocommerce-custobar/woocommerce-custobar.php'
    ];

    update_option( 'active_plugins', $plugins_to_active );

    // Switch to a certain theme if needed
    //switch_theme('twentysixteen');
});

// Load WP.
require $wp_tests_directory . '/bootstrap.php';
