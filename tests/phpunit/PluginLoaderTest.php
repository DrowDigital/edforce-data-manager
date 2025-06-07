<?php

use Hp\EdforceDataManager\DataManager;
use Hp\EdforceDataManager\EdForceDataBase;
use Hp\EdforceDataManager\SubcategoryData;
use Hp\EdforceDataManager\Interfaces\HandleCRUD;

class SampleTest extends WP_UnitTestCase {

    private $category_table;
    private $subcategory_table;
    private $non_searchable_table;
    private $searchable_table;


    // setUp is fine as is, but you can remove it if you don't need any specific setup
    public function setUp(): void {
        parent::setUp();

        global $wpdb;
        // You might want to unset the global instance here if you were manipulating it,
        // but WP_UnitTestCase's tearDown usually resets WP globals.
        $this->category_table = $wpdb->prefix . 'edforce_categories';
        $this->subcategory_table = $wpdb->prefix . 'edforce_subcategories';
        $this->non_searchable_table = $wpdb->prefix . 'edforce_data';
        $this->searchable_table = $wpdb->prefix . 'edforce_data_searchable';

        DataManager::create_plugin_tables();
    }

    public function tearDown(): void {
        global $wpdb;

        // Drop the tables created by the plugin's activation logic for a clean slate.
        // It's good practice to ensure they are dropped even if they don't exist,
        // to prevent errors on subsequent runs.
        $wpdb->query( "DROP TABLE IF EXISTS {$this->category_table}" );
        $wpdb->query( "DROP TABLE IF EXISTS {$this->subcategory_table}" );
        $wpdb->query( "DROP TABLE IF EXISTS {$this->non_searchable_table}" );
        $wpdb->query( "DROP TABLE IF EXISTS {$this->searchable_table}" );

        parent::tearDown();
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

    public function test_plugin_create_tables_method_exists() {
        $this->assertTrue( method_exists( DataManager::class, 'create_plugin_tables' ), 'DataManager::create_plugin_tables() method should exist.' );
    }

    public function test_non_existent_table() {
        global $wpdb;

        $non_existence_table = $wpdb->prefix . "non_existence" .uniqid();

        $actual_table_name = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $non_existence_table ) );

        $this->assertNull( $actual_table_name, "The table '{$non_existence_table}' should NOT exist in the database." );
    }

    public function test_existence_category_table() {
        global $wpdb;

        $actual_table_name = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $this->category_table ) );

        $this->assertNotNull( $actual_table_name, "The table '{$this->category_table}' should exist in the database." );
        $this->assertEquals( $this->category_table, $actual_table_name, "The found table name does not match the expected name." );
    }

    public function test_existence_subcategory_table() {
        global $wpdb;

        $actual_table_name = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $this->subcategory_table ) );

        $this->assertNotNull( $actual_table_name, "The table '{$this->subcategory_table}' should exist in the database." );
        $this->assertEquals( $this->subcategory_table, $actual_table_name, "The found table name does not match the expected name." );
    }
    

    public function test_has_class_exist_to_save_subcategory(){
        $this->assertTrue(class_exists("Hp\EdforceDataManager\SubcategoryData"), "Subcategory Class should Exist");
    }

    // public function test_is_subcategory_class_has_function_to_save_data(){
    //     $edforce_save_subcategory = new $
    // }
       
}