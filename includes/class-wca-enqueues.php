<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

if (!class_exists('WCA_Enqueues')) {

    /**
     * The PostType class
     *
     * @since 1.0.0
     */
    class WCA_Enqueues {

        /**
         * Enqueues the required admin style and scripts.
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
            add_action('admin_enqueue_scripts', array($this, 'wca_load_admin_scripts_and_style'), 100);
            add_action('wp_enqueue_scripts', array($this, 'wca_load_frontend_scripts_and_style'), 100);
        }

        public function wca_load_admin_scripts_and_style($hook) {
            global $post;
            $css_dir = WCA_PLUGIN_URL . 'assets/admin/css/';
            $js_dir = WCA_PLUGIN_URL . 'assets/admin/js/';

            if ($hook == 'post-new.php' || $hook == 'post.php') {
                if (WCA_CPT_NAME === $post->post_type) {
                    wp_enqueue_style('wca-style', $css_dir . 'wca-admin-style.css', WCA_VERSION);
                    wp_enqueue_script('wca-gallery-js', $js_dir . 'wca-admin-gallery.js', array('jquery'), WCA_VERSION, true);
                }
            }
        }

        public function wca_load_frontend_scripts_and_style($hook) {
            global $post;
            $css_dir = WCA_PLUGIN_URL . 'assets/frontend/css/';
            $js_dir = WCA_PLUGIN_URL . 'assets/frontend/js/';
            if (WCA_CPT_NAME === $post->post_type) {
                wp_enqueue_style('wca-frontend-style', $css_dir . 'wca-frontend-style.css', WCA_VERSION);
                wp_enqueue_style('wca-frontend-lightgallery', $css_dir . 'lightgallery.min.css', WCA_VERSION);
                
                wp_enqueue_script('wca-frontend-js', $js_dir . 'wca-frontend-gallery.js', array('jquery'), WCA_VERSION, true);
                wp_enqueue_script('wca-mousewheel-js', $js_dir . 'jquery.mousewheel.min.js', array('jquery'), WCA_VERSION, true);
                wp_enqueue_script('wca-lightgallery-all-js', $js_dir . 'lightgallery-all.min.js', array('jquery'), WCA_VERSION, true);
                
            }
        }

    }

}
new WCA_Enqueues();
