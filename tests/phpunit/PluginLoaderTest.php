<?php

use Hp\EdforceDataManager\DataManager;

class SampleTest extends WP_UnitTestCase {

    // setUp is fine as is, but you can remove it if you don't need any specific setup
    public function setUp(): void {
        parent::setUp();
        // You might want to unset the global instance here if you were manipulating it,
        // but WP_UnitTestCase's tearDown usually resets WP globals.
    }

    public function test_plugin_class_exists() {
        $this->assertTrue( class_exists( 'Hp\EdforceDataManager\DataManager' ), 'DataManager class should exist.' );
    }

    
    public function test_global_plugin_instance_is_set() {
        global $edforce_data_manager_instance;
        $this->assertInstanceOf( DataManager::class, $edforce_data_manager_instance, 'Global DataManager instance should be an instance of DataManager.' );
    }


   
    public function test_data_manager_init_method_exists() {
        $this->assertTrue( method_exists( DataManager::class, 'init' ), 'DataManager::init() method should exist.' );
    }


    public function test_plugin_has_action_for_admin_menu(){
        global $edforce_data_manager_instance; // <--- GET THE GLOBAL INSTANCE

        $this->assertInstanceOf( DataManager::class, $edforce_data_manager_instance, 'Global DataManager instance should be an instance of DataManager.' );

        $this->assertNotFalse(
            has_action( 'admin_menu', [$edforce_data_manager_instance, 'add_admin_menu'] ),
            'The add_admin_menu method of the global DataManager instance should be hooked to admin_menu.'
        );
    }

    public function test_plugin_has_action_to_load_css(){
        global $edforce_data_manager_instance;
        global $wp_styles; // Declare global for direct manipulation if needed

        $this->assertInstanceOf( DataManager::class, $edforce_data_manager_instance, 'Global DataManager instance should be an instance of DataManager.' );

        $this->assertNotFalse(
            has_action( 'admin_enqueue_scripts', [$edforce_data_manager_instance, 'enqueue_admin_styles'] ),
            'The enqueue_admin_styles method of the global DataManager instance should be hooked to admin_enqueue_scripts.'
        );

        $screen = \WP_Screen::get( 'toplevel_page_edforce-data-manager' );
        set_current_screen( $screen );

        $wp_styles->queue = [];
        $wp_styles->registered = []; // Make sure it's fresh for *this* test

        do_action( 'admin_enqueue_scripts', 'toplevel_page_edforce-data-manager' );

        $css_handle = 'edforce-data-manager-category-style'; // <-- Corrected handle
        $this->assertTrue(
            wp_style_is( $css_handle, 'enqueued' ),
            'The CSS file should be enqueued.'
        );

        set_current_screen( 'dashboard' );
        $wp_styles->queue = [];
        $wp_styles->registered = [];
        do_action( 'admin_enqueue_scripts', 'dashboard' );
        $this->assertFalse(
            wp_style_is( $css_handle, 'enqueued' ),
            'The CSS file should NOT be enqueued on the dashboard page.'
        );
    }
}