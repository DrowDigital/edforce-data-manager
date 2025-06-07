<?php

namespace Hp\EdforceDataManager;

class EdForceDataBase {
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_categories = $wpdb->prefix . 'edforce_categories';
        $table_subcategories = $wpdb->prefix . 'edforce_subcategories';
        $table_data_without_slug = $wpdb->prefix . 'edforce_data';
        $table_data_with_slug = $wpdb->prefix . 'edforce_data_searchable';

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Categories
        dbDelta("
            CREATE TABLE $table_categories (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255),
                template_id INT NULL,
                load_method VARCHAR(50) DEFAULT 'default',
                content LONGTEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id)
            ) $charset_collate;
        ");

        // Subcategories (removed foreign key!)
        dbDelta("
            CREATE TABLE $table_subcategories (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                category_id BIGINT(20) UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255),
                subcategory_uid VARCHAR(100),
                template_id INT NULL,
                content LONGTEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_subcategory_uid (subcategory_uid),
                PRIMARY KEY (id)
            ) $charset_collate;
        ");

        // Data Without Slug
        dbDelta("
            CREATE TABLE $table_data_without_slug (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                category_id BIGINT UNSIGNED NOT NULL,
                subcategory_id BIGINT UNSIGNED NULL,
                content LONGTEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;
        ");

        // Data With Slug
        dbDelta("
            CREATE TABLE $table_data_with_slug (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                category_id BIGINT UNSIGNED NOT NULL,
                subcategory_id BIGINT UNSIGNED NULL,
                content LONGTEXT NOT NULL,
                template_id INT NULL,
                template_based_content LONGTEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;
        ");
    }
}
