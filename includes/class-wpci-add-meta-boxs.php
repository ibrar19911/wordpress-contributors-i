<?php
/**
 * A class file
 *
 * @package WordPress Contributer
 */

if ( ! class_exists( 'WPCI_Add_Meta_Boxs' ) ) {

	/**
	 * Class WPCI_Add_Meta_Boxs
	 *
	 * @package WordPress Contributer
	 */
	class WPCI_Add_Meta_Boxs {

		/**
		 * Construct function for adding the required actions.
		 */
		public function __construct() {
			add_action( 'add_meta_boxes', array( $this, 'add_custom_boxs' ) );
			add_action( 'save_post', array( $this, 'save_contributors' ) );
		}

		/**
		 * Function to add meta boxes.
		 */
		public function add_custom_boxs() {
			add_meta_box(
				'post-contributors',
				__( 'Contributers' ),
				array( $this, 'contributors_box_callback' ),
				'post'
			);
		}

		/**
		 * Method containing the markup to show in the meta box
		 *
		 * @param (obj) $post post object.
		 */
		public function contributors_box_callback( $post ) {

			// Add an nonce field so we can check for it later.
			wp_nonce_field( 'wpci-meta-box', 'wpci-meta-box-nonce' );

			// Use get_post_meta to retrieve an existing value from the database.
			$existing_contributors = $this->get_existing_contributors( $post->ID );

			// Get list of users available by rols.
			$users = $this->get_users_by_role( 'administrator', 'author', 'contributor', 'editor' );

			?>

			<div class="wpci-contributors-section">
				<h4>Select Contributor(s):</h4>
				<div class="wpci-authors">
					<?php
					if ( $users ) {
						foreach ( $users as $key => $val ) {
							?>
							<label>
								<input type="checkbox" name="wpci-contributors[]" value="<?php echo esc_html( $val->ID ); ?>" <?php /* phpcs:ignore */ echo in_array( $val->ID, $existing_contributors ) ? 'checked="checked"' : ''; ?>> <?php echo esc_html( $val->user_nicename ); ?>
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
		public function save_contributors( $post_ID ) {

			// Check if the current user is authorised to edit the post or not.
			if ( ! current_user_can( 'edit_post', $post_ID ) && isset( $_POST['post_type'] ) && 'post' === $_POST['post_type'] ) {
				return;
			}

			// Verify that the nonce is valid.
			if ( ! isset( $_POST['wpci-meta-box-nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpci-meta-box-nonce'] ) ), 'wpci-meta-box' ) ) {
				return;
			}

			$wpci_contributors = isset( $_POST['wpci-contributors'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['wpci-contributors'] ) ) : array();

			// Add/Update the contributors in the post meta.
			update_post_meta( $post_ID, 'wpci-contributors', $wpci_contributors );
		}

		/**
		 * Method to get list of users available
		 *
		 * @param (arr) $roles to fetch users according to roles.
		 */
		public function get_users_by_role( $roles = array() ) {

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
		public function get_existing_contributors( $post_id ) {

			$wpci_contributors     = get_post_meta( $post_id, 'wpci-contributors', true );
			$existing_contributors = ( is_array( $wpci_contributors ) && ! empty( $wpci_contributors ) ) ? $wpci_contributors : array();
			return $existing_contributors;
		}

	}

	new WPCI_Add_Meta_Boxs();
}
