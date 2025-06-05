<?php
/**
 * Plugin Name: EdForce Data Manager
 * Description: Custom plugin for managing structured data.
 * Version: 1.0.0
 * Author: Prateek Sagar
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'EDMANAGER_PLUGIN_FILE', __FILE__ );

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use Hp\EdforceDataManager\DataManager;

require_once plugin_dir_path(__FILE__) . 'src/DataManager.php';

global $edforce_data_manager_instance;

add_action( 'plugins_loaded', function() {
    global $edforce_data_manager_instance;
    if ( ! isset( $edforce_data_manager_instance ) ) {
        $edforce_data_manager_instance = new DataManager();
    }
});