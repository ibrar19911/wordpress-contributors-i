<?php
/**
 * Unit test for WPCI_The_Content_Filters class.
 *
 * @package WordPress Contributer
 */

/**
 * Class Test_WPCI_The_Content_Filters.
 *
 * @package WordPress Contributer
 */
class Test_WPCI_The_Content_Filters extends WP_UnitTestCase {

	/**
	 * Constructor test to check if functions are hooked correctly.
	 */
	public function test_constructor() {
		// initiated the WPCI_The_Content_Filters class.
		$filter_content = new WPCI_The_Content_Filters();

		// Check if the display_contributors_after_content function is hooked with with the_content filter or not.
		$is_hooked    = has_action( 'the_content', array( $filter_content, 'display_contributors_after_content' ) );
		$check_result = ( 20 === $is_hooked ) ? true : false;

		// Assertion.
		$this->assertTrue( $check_result );
	}

	/**
	 * Check if the function is appending content correctly or not.
	 */
	public function test_display_contributors_after_content() {
		global $wp_query;

		// Initiated the WPCI_The_Content_Filters class.
		$filter_content = new WPCI_The_Content_Filters();

		// Created a dummy post.
		$post_id = $this->factory->post->create(
			array(
				'post_title'   => 'Test Post',
				'post_content' => 'Test content for the test post.',
				'post_status'  => 'publish',
			)
		);

		// Created 3 dummy users.
		$contributors = $this->factory->user->create_many( 3 );

		// Added the users to post meta as contributors.
		update_post_meta( $post_id, 'wpci-contributors', $contributors );

		// wp_query to get the the upper created post.
		$wp_query = new WP_Query(
			array(
				'post__in'       => array( $post_id ),
				'posts_per_page' => 1,
			)
		);

		if ( $wp_query->have_posts() ) {
			while ( $wp_query->have_posts() ) {
				$wp_query->the_post();

				$wp_query->is_single = true;

				// Trigger the function to filter the content.
				$filtered_content = $filter_content->display_contributors_after_content( get_the_content() );

				// Check if the expected string is in the response or not.
				$class_found = strpos( $filtered_content, 'wpci-contributor' );

				// Assertion.
				$this->assertTrue( false !== $class_found );
			}
		}

	}

}

