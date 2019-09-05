<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WCA_Post_Types')) {

    /**
     * The PostType class
     *
     * @since 1.0.0
     */
    class WCA_Post_Types {

        /**
         * Main constructor
         *
         * @since 1.0.0
         *
         */
        public function __construct() {
            // Hook into actions & filters
            $this->hooks();
        }

        /**
         * Hook in to actions & filters
         *
         * @since 1.0.0
         */
        private function hooks() {
            add_action('init', array($this, 'register_post_type'));
        }

        /**
         * Registers and sets up the custom post types
         *
         * @return void
         * @since 1.0
         */
        public function register_post_type() {

            $labels = array(
                'name' => _x('Authors', 'Post type general name', 'wp-custom-authors'),
                'singular_name' => _x('Author', 'Post type singular name', 'wp-custom-authors'),
                'menu_name' => _x('Authors', 'Admin Menu text', 'wp-custom-authors'),
                'name_admin_bar' => _x('Author', 'Add New on Toolbar', 'wp-custom-authors'),
                'add_new' => __('Add New', 'wp-custom-authors'),
                'add_new_item' => __('Add New Author', 'wp-custom-authors'),
                'new_item' => __('New Author', 'wp-custom-authors'),
                'edit_item' => __('Edit Author', 'wp-custom-authors'),
                'view_item' => __('View Author', 'wp-custom-authors'),
                'all_items' => __('All Authors', 'wp-custom-authors'),
                'search_items' => __('Search Authors', 'wp-custom-authors'),
                'not_found' => __('No authors found.', 'wp-custom-authors'),
                'not_found_in_trash' => __('No authors found in Trash.', 'wp-custom-authors'),
                'featured_image' => _x('Author Cover Image', 'Overrides the “Featured Image” phrase for this post type.', 'wp-custom-authors'),
                'set_featured_image' => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type.', 'wp-custom-authors'),
                'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type.', 'wp-custom-authors'),
                'use_featured_image' => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type.', 'wp-custom-authors'),
                'archives' => _x('Author archives', 'The post type archive label used in nav menus. Default “Post Archives”.', 'wp-custom-authors'),
                'insert_into_item' => _x('Insert into author', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post).', 'wp-custom-authors'),
                'uploaded_to_this_item' => _x('Uploaded to this author', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post).', 'wp-custom-authors'),
                'filter_items_list' => _x('Filter authors list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”.', 'wp-custom-authors'),
                'items_list_navigation' => _x('Authors list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”.', 'wp-custom-authors'),
                'items_list' => _x('Authors list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”.', 'wp-custom-authors'),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array('slug' => WCA_CPT_NAME),
                'capability_type' => 'post',
                'menu_icon' => 'dashicons-smiley',
                'has_archive' => true,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('editor', 'thumbnail'),
            );
            register_post_type(WCA_CPT_NAME, $args);
            flush_rewrite_rules(true);
        }

    }

}

new WCA_Post_Types();
