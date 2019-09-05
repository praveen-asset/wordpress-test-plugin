<?php

/**
 * Plugin Name: WP Custom Authors
 * Plugin URI: http://praveenshekhawat.com
 * Description: Test Custom Authors Plugin
 * Version: 1.0.0
 * Author: Praveen Singh Shekhawat
 * Author URI: http://praveenshekhawat.com
 * Text Domain: wp-custom-authors
 *
 * @since     1.0.0
 * @copyright Copyright (c) 2019, Praveen Singh Shekhawat
 * @author    Praveen Singh Shekhawat
 */
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly.


if (!class_exists('WCA')) {

	/*
	 * Helper function for quick debugging
	 */
	if ( ! function_exists( 'pr' ) ) {

		function pr( $array ) {
			echo '<pre>';
			print_r( $array );
			echo '</pre>';
		}

	}

	/**
	 * Main WCA Class.
	 *
	 * @since 1.0.0
	 */
	final class WCA {

		/**
		 * Plugin Version
		 * @var string
		 */
		private $version = '1.0.0';

		private $post_type_name = 'authors';
		/**
		 * Constructor. Intentionally left empty and public.
		 *
		 * @see instance()
		 * @since  1.0.0
		 */
		public function __construct() {
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 * @since  1.0.0
		 */
		private function init_hooks() {
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		}

		/**
		 * Define Constants.
		 * @since  1.0.0
		 */
		private function define_constants() {
			$this->define( 'WCA_PLUGIN_FILE', __FILE__ );
			$this->define( 'WCA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			$this->define( 'WCA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'WCA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'WCA_VERSION', $this->version );
			$this->define( 'WCA_CPT_NAME', $this->post_type_name );
		}

		/**
		 * Define constant if not already set.
		 * @since  1.0.0
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Include required core files used in admin.
		 * @since  1.0.0
		 */
		private function includes() {
			//require_once
			include_once( 'includes/class-wca-post-types.php' );
			include_once( 'includes/class-wca-metaboxes.php' );
			include_once( 'includes/class-wca-enqueues.php' );
                        include_once( 'includes/class-wca-post-view.php' );
		}

		/**
		 * Show row meta on the plugin screen.
		 * @since 1.0.0
		 */
		public function plugin_row_meta( $links, $file ) {

			if ( $file == WCA_PLUGIN_BASENAME ) {

				$row_meta = array(
					'docs' => '<a href="#" title="' . esc_attr( __( 'View Documentation', 'wp-custom-authors' ) ) . '">' . __( 'Help', 'wp-custom-authers' ) . '</a>',
				);

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

	}
}

/**
 * Main instance of WCA.
 *
 * @since  1.0.0
 * @return WCA
 */
new WCA();
