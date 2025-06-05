<?php
/**
 * Admin Categories Form Template
 *
 * This file renders the HTML form for managing categories
 * within the EdForce Data Manager plugin.
 *
 * @package EdForceDataManager
 */

// Ensure this file is not accessed directly in a browser
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h1><?php esc_html_e( 'Categories', 'edforce-data-manager' ); ?></h1>
    <p class="description"><?php esc_html_e( 'Manage your categories here. Categories help organize your content and make it easier to find what they\'re looking for.', 'edforce-data-manager' ); ?></p>

    <form method="post" action=""> <?php // Use 'post' method. Action can be empty to submit to itself. ?>
        <?php
        // Always include a WordPress nonce for security in forms
        wp_nonce_field( 'add_new_category_action', 'add_new_category_nonce' );
        ?>
        <table class="form-table">
            <tbody>
                <tr class="form-field">
                    <th scope="row"><label for="category-name"><?php esc_html_e( 'Name', 'edforce-data-manager' ); ?></label></th>
                    <td>
                        <input type="text" id="category-name" name="category_name" placeholder="<?php esc_attr_e( 'Category Name', 'edforce-data-manager' ); ?>" class="regular-text" required>
                        <p class="description"><?php esc_html_e( 'The name is how it appears on your site.', 'edforce-data-manager' ); ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="category-slug"><?php esc_html_e( 'Slug', 'edforce-data-manager' ); ?></label></th>
                    <td>
                        <input type="text" id="category-slug" name="category_slug" placeholder="<?php esc_attr_e( 'Category Slug', 'edforce-data-manager' ); ?>" class="regular-text">
                        <p class="html-tag-description description"><?php esc_html_e( 'The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'edforce-data-manager' ); ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="parent-category"><?php esc_html_e( 'Parent Category', 'edforce-data-manager' ); ?></label></th>
                    <td>
                        <select id="parent-category" name="parent_category">
                            <option value="0"><?php esc_html_e( '— Select Parent Category —', 'edforce-data-manager' ); ?></option>
                            <?php
                            // Example: Dynamically populate categories (you'd do this in your PHP class)
                            // $categories = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) );
                            // if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                            //     foreach ( $categories as $category ) {
                            //         echo '<option value="' . esc_attr( $category->term_id ) . '">' . esc_html( $category->name ) . '</option>';
                            //     }
                            // }
                            ?>
                        </select>
                        <p class="description"><?php esc_html_e( 'Categories, unlike tags, can have a hierarchy. You might have a "Breads" category under a "Baked Goods" category.', 'edforce-data-manager' ); ?></p>
                    </td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="category-description"><?php esc_html_e( 'Description', 'edforce-data-manager' ); ?></label></th>
                    <td>
                        <textarea id="category-description" name="category_description" rows="5" cols="40" placeholder="<?php esc_attr_e( 'Category Description', 'edforce-data-manager' ); ?>"></textarea>
                        <p class="description"><?php esc_html_e( 'The description is not prominent by default; however, some themes may show it.', 'edforce-data-manager' ); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button( __( 'Add New Category', 'edforce-data-manager' ) ); ?>
    </form>
</div>