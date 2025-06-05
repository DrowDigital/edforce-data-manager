<?php

namespace Hp\EdforceDataManager;

class DataManager {

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