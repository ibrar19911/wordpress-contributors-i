<?php
/**
 * Unit test for WPCI_Add_Meta_Boxes class.
 *
 * @package WordPress Contributer
 */

/**
 * Class Test_WPCI_Add_Meta_Boxes.
 *
 * @package WordPress Contributer
 */
class Test_WPCI_Add_Meta_Boxes extends WP_UnitTestCase {

	/**
	 * Constructor test to check if functions are hooked correctly.
	 */
	public function test_constructor() {

		// Class initiated to test.
		$add_meta_boxes = new WPCI_Add_Meta_Boxes();

		// Check if functions are register with the respective hooks.
		$hooked_add_custom_boxes = has_action( 'add_meta_boxes', array( $add_meta_boxes, 'add_custom_boxes' ) );
		$save_contributors       = has_action( 'save_post', array( $add_meta_boxes, 'save_contributors' ) );

		// Check the result.
		$result = ( 10 === $hooked_add_custom_boxes && 10 === $save_contributors ) ? true : false;

		// Assertion.
		$this->assertTrue( $result );
	}

	/**
	 * Check if the add_meta_box() is working correctly.
	 */
	public function test_add_custom_boxes() {

		global $wp_meta_boxes;

		// Class initiated to test.
		$add_meta_boxes = new WPCI_Add_Meta_Boxes();
		$add_meta_boxes->add_custom_boxes();

		// get array of meta boxes for post.
		$post_contributors_meta_box = $wp_meta_boxes['post']['advanced']['default'];

		// Check result.
		$result = ( array_key_exists( 'post-contributors', $post_contributors_meta_box ) ) ? true : false;

		// Assertion.
		$this->assertTrue( $result );
	}

	/**
	 * Check if callback of post-contributors metabox working as expected.
	 */
	public function test_contributors_box_callback() {
		global $wp_query, $post;

		// Class initiated to test.
		$add_meta_boxes = new WPCI_Add_Meta_Boxes();

		// Created 3 Dummy users.
		$user_ids = $this->factory->user->create_many( 3 );

		// Creatig a dummy post.
		$post_id = $this->factory->post->create(
			array(
				'post_type'   => 'post',
				'post_author' => 1,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
			)
		);

		// Overiting default $wp_query.
		$wp_query = new WP_Query(
			array(
				'post__in'       => array( $post_id ),
				'posts_per_page' => 1,
			)
		);

		// Overiting default $post.
		if ( $wp_query->have_posts() ) {
			while ( $wp_query->have_posts() ) {
				$wp_query->the_post();
			}
		}

		// Adding contributors to post meta.
		update_post_meta( $post_id, 'wpci_contributors', $user_ids );

		// Storing the HTML in output buffering.
		ob_start();
		$add_meta_boxes->contributors_box_callback( $post );
		$contributors_meta_box_html = ob_get_clean();

		// Checking if the list of user availble with checkboxes in the metabox.
		$list_of_contributors_available = strpos( $contributors_meta_box_html, 'checkbox' );

		// Check results.
		$result = $list_of_contributors_available ? true : false;

		// Assertion.
		$this->assertTrue( $result );

		wp_reset_postdata();
	}

	/**
	 * Check if contributors are being saved in database correctly.
	 */
	public function test_save_contributors() {

		// Class initiated to test.
		$add_meta_boxes = new WPCI_Add_Meta_Boxes();

		// Created Dummy author.
		$author = $this->factory->user->create_and_get(
			array(
				'user_login' => 'jdoe',
				'user_pass'  => null,
				'role'       => 'administrator',
			)
		);

		// Creatig a dummy post.
		$post_id = $this->factory->post->create(
			array(
				'post_type'   => 'post',
				'post_author' => $author->ID,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
			)
		);

		// Created 3 Dummy users.
		$user_ids = $this->factory->user->create_many( 3 );

		// Seting up the current user.
		wp_set_current_user( $author->ID );

		// Adding required data in $_POST.
		$nonce                        = wp_create_nonce( 'wpci-meta-box' );
		$_POST['wpci-meta-box-nonce'] = $nonce;
		$_POST['post_type']           = 'post';
		$_POST['wpci-contributors']   = $user_ids;

		// Run the function to test.
		$add_meta_boxes->save_contributors( $post_id );

		// Get updated contributors.
		$saved_contributors = get_post_meta( $post_id, 'wpci-contributors', true );

		// check results.
		$result = ( $saved_contributors == $user_ids ) ? true : false;

		// Assertion.
		$this->assertTrue( $result );
	}

	/**
	 * Check if this function is returning correct data or not.
	 */
	public function test_get_existing_contributors() {

		// Class initiated to test.
		$add_meta_boxes = new WPCI_Add_Meta_Boxes();

		// Creatig a dummy post.
		$post_id = $this->factory->post->create(
			array(
				'post_type'   => 'post',
				'post_author' => 1,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
			)
		);

		// Created 3 dummy users.
		$user_ids = $this->factory->user->create_many( 3 );

		// Adding contributors to post meta.
		update_post_meta( $post_id, 'wpci-contributors', $user_ids );

		// Get updated contributors.
		$get_contributors = $add_meta_boxes->get_existing_contributors( $post_id );

		// Check results.
		$result = ( $get_contributors === $user_ids ) ? true : false;

		// Assertion.
		$this->assertTrue( $result );
	}

}

