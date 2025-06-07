<?php
/**
 * PHPUnit bootstrap file for edforce-data-manager plugin.
 */

// Define the path to the WordPress test library.
// Use the environment variable if set, otherwise default to /tmp/wordpress-tests-lib.
$_tests_dir = getenv( 'WP_TESTS_DIR' ) ?: '/tmp/wordpress-tests-lib';

// Ensure the WordPress test functions are available.
if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
    echo "Could not find {$_tests_dir}/includes/functions.php, have you installed the WordPress test suite?" . PHP_EOL;
    exit( 1 );
}

// Load the WordPress test functions.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually loads your plugin.
 * This function is hooked into 'muplugins_loaded' by tests_add_filter().
 */
function _manually_load_plugin() {
    error_log('Attempting to load edforce-data-manager plugin...');
    // This path must point to your main plugin file.
    require dirname(__DIR__) . '/edforce-data-manager.php';
    error_log('edforce-data-manager plugin loaded successfully!');
}

// Hook your plugin into the WordPress test environment.
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WordPress testing environment.
// This loads WP_UnitTestCase and other core WordPress test utilities.
require $_tests_dir . '/includes/bootstrap.php';

// Finally, load your Composer autoloader.
// This makes your plugin's namespaced classes and interfaces available to your tests.
if ( file_exists( dirname(__DIR__) . '/vendor/autoload.php' ) ) {
    require_once dirname(__DIR__) . '/vendor/autoload.php';
    error_log('Composer autoloader loaded for plugin files.');
} else {
    error_log('Composer autoloader not found at ' . dirname(__DIR__) . '/vendor/autoload.php');
}

// Optional: define path to polyfills (if you are using phpunit-polyfills directly, otherwise Composer handles it)
// define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills' );