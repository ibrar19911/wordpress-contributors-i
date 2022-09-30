<?php
/**
 * A class file
 *
 * @package WordPress Contributer
 */

if ( ! class_exists( 'WPCI_Enqueue_Styles_And_Scripts' ) ) {

	/**
	 * Class WPCI_Enqueue_Styles_And_Scripts
	 *
	 * @package WordPress Contributer
	 */
	class WPCI_Enqueue_Styles_And_Scripts {

		/**
		 * Construct function for adding the required actions.
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_styles_and_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles_and_scripts' ) );
		}

		/**
		 * Method to enqueue front side styles and scripts.
		 */
		public function wp_enqueue_styles_and_scripts() {

			// Main CSS file of the plugin.
			wp_enqueue_style( 'wpci-style', WPCI_CSS_URL . 'style.css', false, '1.0.0' );
		}

		/**
		 * Method to enqueue admin side styles and scripts.
		 *
		 * @param (str) $hook telling about the admin page that loads.
		 */
		public function admin_enqueue_styles_and_scripts( $hook ) {

			// Enqueue for only edit post and add new post.
			if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
				wp_enqueue_style( 'wpci-admin-meta-boxes', WPCI_CSS_URL . 'admin/meta-boxes.css', false, '1.0.0' );
			}

		}

	}

	new WPCI_Enqueue_Styles_And_Scripts();
}
