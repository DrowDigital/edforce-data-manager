<?php

namespace Hp\EdforceDataManager;

use Hp\EdforceDataManager\EdForceDataBase;

class DataManager {

    private $table_handler;

    public function __construct() {
        add_action("admin_menu", [$this, "add_admin_menu"]);
        add_action("admin_enqueue_scripts", [$this, "enqueue_admin_styles"]);
    }

    public function enqueue_admin_styles($hook_suffix) {
        if ( 'toplevel_page_edforce-data-manager' !== $hook_suffix ) {
           return;
        }
        
        wp_enqueue_style(
            'edforce-data-manager-category-style',
            plugins_url( 'src/css/category.css', EDMANAGER_PLUGIN_FILE ), // Path to your CSS file
            [], 
            '1.0.0'
        );
    }


    public function init() {
       
    }

    public static function create_plugin_tables() {
       EdForceDataBase::create_tables();
    }

    public function add_admin_menu() {
        
        add_menu_page(
            __( 'EdForce Data Manager', 'edforce-data-manager' ),
            __( 'EdForce Data', 'edforce-data-manager' ),
            'manage_options',
            'edforce-data-manager',
            [$this, 'render_admin_page'],
            'dashicons-media-text',
            25
        );

        add_submenu_page(
            'edforce-data-manager', // Parent slug
            __( 'Courses', 'edforce-data-manager' ), // Page title
            __( 'Courses', 'edforce-data-manager' ), // Menu title
            'manage_options', // Capability
            'courses', // Menu slug
            [$this, 'render_submenu_page'] // Use the same callback as the parent menu
        );

        add_submenu_page(
            'edforce-data-manager', // Parent slug
            __( 'Training Calender', 'edforce-data-manager' ), // Page title
            __( 'Training Calender', 'edforce-data-manager' ), // Menu title
            'manage_options', // Capability
            'training-calender', // Menu slug
            [$this, 'render_submenu_page'] // Use the same callback as the parent menu
        );

        add_submenu_page(
            'edforce-data-manager', // Parent slug
            __( 'Certifications', 'edforce-data-manager' ), // Page title
            __( 'Certifications', 'edforce-data-manager' ), // Menu title
            'manage_options', // Capability
            'certifications', // Menu slug
            [$this, 'render_submenu_page'] // Use the same callback as the parent menu
        );
    }

    
    
    public function render_submenu_page() {
        $page_slug = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';

        // Define a mapping of slugs to template file names
        $template_map = [
            'courses'            => 'courses.php',
            'training-calender'  => 'training-calender.php',
            'certifications'     => 'certifications.php',
        ];

        if (isset($template_map[$page_slug])) {
            $template_file = $template_map[$page_slug];
            $template_path = plugin_dir_path(dirname(__FILE__)) . 'src/static/' . $template_file;

            if (file_exists($template_path)) {
                include $template_path;
                return;
            } else {
                echo '<div class="wrap"><p>' . esc_html__('Template file not found: ', 'edforce-data-manager') . esc_html($template_file) . '</p></div>';
            }
        } else {
            echo '<div class="wrap"><p>' . esc_html__('Invalid submenu page.', 'edforce-data-manager') . '</p></div>';
        }
    }

    public function render_admin_page() {
        // Render the admin page content
        $template_path = plugin_dir_path( dirname( __FILE__ ) ) . 'src/static/category.php';
        
        if ( file_exists( $template_path ) ) {
            include $template_path;
        } else {
            // Fallback or error message if the template is missing
            echo $template_path;
            echo '<div class="wrap"><p>' . esc_html__( 'Admin page template not found.', 'edforce-data-manager' ) . '</p></div>';
        }
    }
}