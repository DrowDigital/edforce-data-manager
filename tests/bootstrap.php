<?php
// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Optional: define path to polyfills (not required if using Composer autoload)
define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills' );

// Load WP test functions
// $_tests_dir = getenv('WP_TESTS_DIR') ?: '/var/www/html/wp-content/plugins/edforce-data-manager/tests/phpunit';
$_tests_dir = getenv( 'WP_TESTS_DIR' ) ?: '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
    error_log('Trying to load plugin...');
    require dirname(__DIR__) . '/edforce-data-manager.php';
    error_log('Plugin loaded!');
}

tests_add_filter('muplugins_loaded', '_manually_load_plugin');

require $_tests_dir . '/includes/bootstrap.php';
