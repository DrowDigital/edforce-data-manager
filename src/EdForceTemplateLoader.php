<?php
namespace Hp\EdforceDataManager;


class EdForceTemplateLoader {

    /**
     * Get all Elementor page templates.
     *
     * @return array Array of template objects with ID, title, and type.
     */
    
    public static function get_all_templates() {
        $templates = get_posts([
        'post_type'      => 'elementor_library',
        'post_status'    => 'publish',
        'numberposts'    => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => [
            [
                'key'     => '_elementor_template_type',
                'value'   => 'page',
                'compare' => '=',
            ]
        ],
        ]);

        $template_data = [];
        
        foreach ($templates as $template) {
            $template_data[] = [
                'ID'    => $template->ID,
                'title' => $template->post_title,
                'type'  => get_post_meta($template->ID, '_elementor_template_type', true),
            ];
        }

        return $template_data;
    }

    /**
     * Get all Elementor Section Templates
     * 
     * @return array Array of template objects with ID, title, and type.
     */

    public static function get_all_section_templates() {
        $templates = get_posts([
            'post_type'      => 'elementor_library',
            'post_status'    => 'publish',
            'numberposts'    => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'meta_query'     => [
                [
                    'key'     => '_elementor_template_type',
                    'value'   => 'section',
                    'compare' => '=',
                ]
            ],
        ]);

        $template_data = [];
        
        foreach ($templates as $template) {
            $template_data[] = [
                'ID'    => $template->ID,
                'title' => $template->post_title,
                'type'  => get_post_meta($template->ID, '_elementor_template_type', true),
            ];
        }

        return $template_data;
    }

    /**
     * Get a specific Elementor template's content by ID.
     *
     * @param int $template_id
     * @return string|null
     */

    public static function get_template_content($template_id) {
        if (get_post_type($template_id) !== 'elementor_library') {
            return null;
        }

        return \Elementor\Plugin::$instance->frontend->get_builder_content($template_id);
    }

    public static function load_frontend_data_by_slug($slug) {
        global $wpdb;
        $data_array = null;
        // Try to get a category by slug
        $category = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}edforce_categories WHERE slug = %s", $slug),
            ARRAY_A
        );

        error_log("Category: " . print_r($category, true));

        if ($category) {
            $category_id = $category['id'];

            // Get category template (if you associate templates via category meta or similar)
            $category_template_id = isset($category['template_id']) ? $category['template_id'] : null;

            // Get subcategories
            $subcategories = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}edforce_subcategories WHERE category_id = %d", $category_id),
                ARRAY_A
            );

            // total data under this category
            $count_category_data = $wpdb->get_var(
                $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}edforce_data WHERE category_id = %d", $category_id)
            );

            // load data as per load method in the category 
            if ($category['load_method'] !== "default"){
                $subcat_data = [];

                foreach ($subcategories as $subcat){
                    $subcat_id = $subcat['id'];
                    $subcat_template_id = isset($subcat['template_id']) ? $subcat['template_id'] : null;

                    // Get data under this subcategory
                    $subcat_data[] = [
                        'subcategory' => $subcat,
                        'template_id' => $subcat_template_id,
                        'data' => $wpdb->get_results(
                            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}edforce_data WHERE subcategory_id = %d", $subcat_id),
                            ARRAY_A
                        ),
                        'data_count' => $wpdb->get_var(
                            $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}edforce_data WHERE subcategory_id = %d", $subcat_id)
                        )
                    ];
                }

                error_log("Subcat Data: " . print_r($subcat_data, true));

                $data_array = [
                    'type' => 'category',
                    'category' => $category,
                    'template_id' => $category_template_id,
                    'subcategories' => $subcat_data,
                    'data_count' => $count_category_data,
                ];
                
            } else {

                
                // Get category data rows
                $category_data = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}edforce_data WHERE category_id = %d and subcategory_id is null ORDER BY id ASC LIMIT 12", $category_id),
                ARRAY_A
            );
            
            
            foreach ($subcategories as $subcat) {
                $subcat_data[] = [
                    'subcategory' => $subcat,
                    'template_id' => isset($subcat['template_id']) ? $subcat['template_id'] : null,
                    'data_count' => $wpdb->get_var(
                        $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}edforce_data WHERE subcategory_id = %d", $subcat['id'])
                        )
                    ];
                }
                
                $data_array = [
                    'type' => 'category',
                    'category' => $category,
                    'template_id' => $category_template_id,
                    'data' => $category_data,
                    'subcategories' => $subcat_data,
                    'data_count' => $count_category_data,
                ];
            }
        }

        // If not a category, check for subcategory
        $subcategory = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}edforce_subcategories WHERE slug = %s", $slug),
            ARRAY_A
        );

        if ($subcategory && $data_array === null) {
            $subcategory_id = $subcategory['id'];

            // Get data under this subcategory
            $subcat_data = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}edforce_data WHERE subcategory_id = %d", $subcategory_id),
                ARRAY_A
            );

            $data_array = [
                'type' => 'subcategory',
                'subcategory' => $subcategory,
                'template_id' => isset($subcategory['template_id']) ? $subcategory['template_id'] : null,
                'data' => $subcat_data,
            ];
        }

        $data = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}edforce_data_searchable WHERE slug = %s", $slug),
            ARRAY_A
        );

        if ($data && $data_array === null) {
            $template_id = isset($data['template_id']) ? $data['template_id'] : null;;

            

            $data_array = [
                'type' => 'data',
                'template_id' => $template_id,
                'data' => $data,
            ];
        }

        if ($data_array){
            return $data_array;
        }

        // Not found
        return null;
    }

    public static function render_frontend_template_with_data($data_array) {
        // Prevent 404 headers
        status_header(200);
        remove_action('wp_head', '_wp_render_title_tag', 1);

        get_header();

        // Render Elementor template if template_id exists
        if (!empty($data_array['template_id'])) {
                        
            echo \Elementor\Plugin::$instance->frontend->get_builder_content($data_array['template_id']);
        } else {
            echo '<h2>No template associated with this content.</h2>';
        }

        // Expose data to JS
        echo '<script>window.__edforce_data = ' . json_encode($data_array) . ';</script>';

        get_footer();
        exit; // stop further WP processing
    }


    // get the requested template and then insert it into the page
    public static function get_template_by_id($template_id) {
        if (get_post_type($template_id) !== 'elementor_library') {
            return null;
        }

        $template = \Elementor\Plugin::$instance->frontend->get_builder_content($template_id);

        if ($template) {
            echo $template;
        } else {
            echo '<h2>No template found.</h2>';
        }
    }

    public static function get_data_by_query($query){
        // get from the edforce_data table
        global $wpdb;
        if (empty($query)) {
            return null; // No query provided
        }

        
        $sql_query = "SELECT * FROM {$wpdb->prefix}edforce_data WHERE $query";

        $data = $wpdb->get_results(
            $wpdb->prepare($sql_query),
            ARRAY_A
        );

        if ($data) {
            return $data;
        } else {
            return null; 
        }
    }

    public static function get_data_by_subcategory($subcategory_id) {
        global $wpdb;

        if (empty($subcategory_id)) {
            return null; // No subcategory ID provided
        }

        $data = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}edforce_data WHERE subcategory_id = %d", $subcategory_id),
            ARRAY_A
        );

        if ($data) {
            return $data;
        } else {
            return null; 
        }
    }
}
