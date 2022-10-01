<?php
/**
 * A class file.
 *
 * @package WordPress Contributer.
 */

if ( ! class_exists( 'WPCI_The_Content_Filters' ) ) {

	/**
	 * Class WPCI_The_Content_Filters.
	 *
	 * @package WordPress Contributer.
	 */
	class WPCI_The_Content_Filters {

		/**
		 * Constructor function to hook the method with the_content filter
		 */
		public function __construct() {
			add_filter( 'the_content', array( $this, 'display_contributors_after_content' ), 20 );
		}

		/**
		 * Hooked with the_content filter to append it with contributors.
		 *
		 * @param (str) $content the content which will be filtered.
		 */
		public function display_contributors_after_content( $content ) {

			// if not single return back.
			if ( ! is_single() ) {
				return;
			}

			$post_id              = get_the_ID();
			$contributors_ids     = get_post_meta( $post_id, 'wpci-contributors', true );
			$contributors_content = '';

			if ( is_array( $contributors_ids ) && ! empty( $contributors_ids ) ) {
				$contributors_content =
					'<div class="wpci-contributors-box">' .
						'<h4 class="wpci-contributors-heading">' . __( 'Contributors' ) . '</h4>' .
						'<div class="wpci-contributors">';

				foreach ( $contributors_ids as $id ) {

					$user_data = get_userdata( $id );
					$user_name = $user_data->user_nicename;
					$avatar    = ( ! is_category() && ! is_tag() ) ? get_avatar( $id, 50 ) : '';
					$user_url  = get_author_posts_url( $id );

					$contributors_content .=
						'<div class="wpci-contributor">' .
							'<a href="' . esc_url( $user_url ) . '">' .
								$avatar .
								'<span class="wpci-username">' . $user_name . '</span>' .
							'</a>' .
						'</div>';
				}
				$contributors_content .=
						'</div>' .
					'</div>';
			}

			// Appending the content with contributors.
			$new_content = $content . $contributors_content;
			return $new_content;
		}

	}

	new WPCI_The_Content_Filters();
}
