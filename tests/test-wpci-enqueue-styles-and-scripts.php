<?php
/**
 * Unit test for WPCI_Enqueue_Styles_And_Scripts class.
 *
 * @package WordPress Contributer
 */

/**
 * Class WPCI_Enqueue_Styles_And_Scripts.
 *
 * @package WordPress Contributer
 */
class Test_WPCI_Enqueue_Styles_And_Scripts extends WP_UnitTestCase {

	/**
	 * Constructor test to check if functions are hooked correctly.
	 */
	public function test_constructor() {

		// Class initiated to test.
		$wpci_enqueuing = new WPCI_Enqueue_Styles_And_Scripts();

		// Check if functions are register with the respective hooks.
		$wp_scripts_function    = has_action( 'wp_enqueue_scripts', array( $wpci_enqueuing, 'wp_enqueue_styles_and_scripts' ) );
		$admin_scripts_function = has_action( 'admin_enqueue_scripts', array( $wpci_enqueuing, 'admin_enqueue_styles_and_scripts' ) );

		// Check the result.
		$result = ( 10 === $wp_scripts_function && 10 === $admin_scripts_function ) ? true : false;

		// Assertion.
		$this->assertTrue( $result );
	}

	/**
	 * Constructor test to check if functions are hooked correctly.
	 */
	public function wp_enqueue_styles_and_scripts() {

		global $wp_scripts;

		// Class initiated to test.
		$wpci_enqueuing = new WPCI_Enqueue_Styles_And_Scripts();

		$wpci_enqueuing->wp_enqueue_styles_and_scripts();

		// Check if the rt-style is enqueued.
		$style_enqueued = wp_style_is( 'rt-style' );

		// Assertion.
		$this->assertTrue( $style_enqueued );
	}

	/**
	 * Check if backend styles are enqueued correctly.
	 */
	public function test_admin_enqueue_styles_and_scripts() {
		global $wp_scripts;

		// Class initiated to test.
		$wpci_enqueuing = new WPCI_Enqueue_Styles_And_Scripts();
		$hook           = 'post.php';

		$wpci_enqueuing->admin_enqueue_styles_and_scripts( $hook );

		// Check if the rt-style is enqueued.
		$admin_meta_boxes = wp_style_is( 'wpci-admin-meta-boxes' );

		// Assertion.
		$this->assertTrue( $admin_meta_boxes );
	}

}

