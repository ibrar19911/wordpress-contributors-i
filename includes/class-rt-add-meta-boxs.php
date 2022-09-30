<?php
/**
 * A class file
 *
 * @package WordPress Contributer
 */

if ( ! class_exists( 'RT_Add_Meta_Boxs' ) ) {

	/**
	 * Class RT_Add_Meta_Boxs
	 *
	 * @package WordPress Contributer
	 */
	class RT_Add_Meta_Boxs {

		/**
		 * Construct function for adding the required actions.
		 */
		public function __construct() {
			add_action( 'add_meta_boxes', array( $this, 'rt_add_custom_boxs' ) );
			add_action( 'save_post', array( $this, 'rt_save_contributors' ) );
		}

		/**
		 * Function to add meta boxes.
		 */
		public function rt_add_custom_boxs() {
			add_meta_box(
				'post-contributors',
				__( 'Contributers' ),
				array( $this, 'rt_contributors_box_callback' ),
				'post'
			);
		}

		/**
		 * Method containing the markup to show in the meta box
		 *
		 * @param (obj) $post post object.
		 */
		public function rt_contributors_box_callback( $post ) {

			// Add an nonce field so we can check for it later.
			wp_nonce_field( 'rt_contributors_meta_box', 'rt_contributors_meta_box_nonce' );

			// Use get_post_meta to retrieve an existing value from the database.
			$existing_contributors = $this->rt_get_existing_contributors( $post->ID );

			// Get list of users available by rols.
			$users = $this->rt_get_users_by_role( 'administrator', 'author', 'contributor', 'editor' );

			?>

			<div class="rt-contributors-section">
				<h4>Select Contributor(s):</h4>
				<div class="rt-authors">
					<?php
					if ( $users ) {
						foreach ( $users as $key => $val ) {
							?>
							<label>
								<input type="checkbox" name="rt_contributors[]" value="<?php echo esc_html( $val->ID ); ?>" <?php echo in_array( $val->ID, $existing_contributors, true ) ? 'checked="checked"' : ''; ?>> <?php echo esc_html( $val->user_nicename ); ?>
							</label>
							<?php
						}
					}
					?>
				</div>
			</div>

			<?php
		}

		/**
		 * Method to save the submission of the meta box.
		 *
		 * @param (int) $post_ID post ID.
		 */
		public function rt_save_contributors( $post_ID ) {

			// Check if the current user is authorised to edit the post or not.
			if ( ! current_user_can( 'edit_post', $post_ID ) && isset( $_POST['post_type'] ) && 'post' === $_POST['post_type'] ) {
				return;
			}

			// Verify that the nonce is valid.
			if ( ! isset( $_POST['rt_contributors_meta_box_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rt_contributors_meta_box_nonce'] ) ), 'rt_contributors_meta_box' ) ) {
				return;
			}

			$rt_contributors = isset( $_POST['rt_contributors'] ) ? wp_unslash( $_POST['rt_contributors'] ) : array();

			// Add/Update the contributors in the post meta.
			update_post_meta( $post_ID, 'rt_contributors', $rt_contributors );

		}

		/**
		 * Method to get list of users available
		 *
		 * @param (arr) $roles to fetch users according to roles.
		 */
		public function rt_get_users_by_role( $roles = array() ) {

			$args = array(
				'role'    => $roles,
				'orderby' => 'user_nicename',
				'order'   => 'ASC',
			);

			$users = get_users( $args );

			return $users;
		}

		/**
		 * Method to get the existing contributors
		 *
		 * @param (int) $post_id post ID.
		 */
		public function rt_get_existing_contributors( $post_id ) {

			$rt_contributors       = get_post_meta( $post_id, 'rt_contributors', true );
			$existing_contributors = ( is_array( $rt_contributors ) && ! empty( $rt_contributors ) ) ? $rt_contributors : array();
			return $existing_contributors;
		}

	}

	new RT_Add_Meta_Boxs();
}
