<?php
/**
 * Plugin Name: Private User Comments
 * Plugin URI: http://scootah.com/
 * Description: Allow users to make their comments private (visible by themselves and admins)
 * Version: 1.0
 * Author: Scott Grant
 * Author URI: http://scootah.com/
 */
class WP_PrivateComments {

	/**
	 * Store reference to singleton object.
	 */
	private static $instance = null;

	/**
	 * The domain for localization.
	 */
	const DOMAIN = 'wp-private-comments';

	/**
	 * Instantiate, if necessary, and add hooks.
	 */
	public function __construct() {
		global $wpdb;

		if ( isset( self::$instance ) ) {
			wp_die( esc_html__(
				'WP_PrivateComments is already instantiated!',
				self::DOMAIN ) );
		}

		self::$instance = $this;

		$this->option = 'private_comments';

		add_action( 'show_user_profile', array( $this, 'user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'user_profile' ) );
		add_action( 'personal_options_update', array( $this, 'user_profile_update' ) );

		add_filter( 'comments_array', array( $this, 'filter_comments' ) );
	}

	/**
	 * Return a reference to the instance.
	 */
	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * Display the checkbox on the user profile page.
	 */
	public function user_profile( $user ) {
		$private = get_user_meta( $user->ID, $this->option, true );
		$checked = ( $private == true ) ? 'checked' : '';

?>
<table class="form-table">
	<tr>
		<th>Private Comments</th>
		<td>
			<label for="private_comments">
				<input type="checkbox" name="private_comments" id="private_comments" <?php echo( $checked ); ?> />
				Make my comments private
			</label>
		</td>
	</tr>
</table>
<?php
	}

	/**
	 * Call set_private_meta to update the metadata and toggle the privacy flag on the user's comments.
	 */
	public function user_profile_update( $user_id ) {
		if ( isset( $_POST[ 'private_comments' ] ) ) {
			$this->set_private_meta( $user_id, true );
		} else {
			$this->set_private_meta( $user_id, false );
		}

	}

	/**
	 * Check the comment metadata to see if there are any marked as private.
	 * If the current user is an admin, they can see all comments, but they'll be prefixed as private.
	 * If the current user runs into their own private comment, prefix as private, but show it.
	 * Anyone else will have the comment filtered out.
	 */
	public function filter_comments( $comments ) {
		global $wpdb;

		if ( empty( $comments ) ) {
			return $comments;
		}

		$comment_ids = array();

		foreach ( $comments as $comment ) {
			array_push( $comment_ids, intval( $comment->comment_ID ) );
		}

		// Set up a single database query instead of an unknown number of get_comment_meta calls.
		$private_comment_ids = array();

		$comment_obj = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT comment_ID FROM {$wpdb->prefix}commentmeta WHERE meta_key='%s' AND meta_value='1' AND comment_ID IN (" . implode( ',', $comment_ids ) . ")",
				array( $this->option )
			),
			ARRAY_A
		);

		foreach ( $comment_obj as $comment ) {
			array_push( $private_comment_ids, $comment[ 'comment_ID' ] );
		}

		foreach ( $comments as $k => $comment ) {
			if ( in_array( $comment->comment_ID, $private_comment_ids ) ) {
				if ( current_user_can( 'manage_options' ) || $comment->user_id == get_current_user_id() ) {
					$comment->comment_content = '(Private) ' . $comment->comment_content;
					continue;
				}

				unset( $comments[ $k ] );
			}
		}

		return $comments;
	}

	/**
	 * Set the privacy toggle metadata for the specified user, and update the privacy flag on their comments.
	 */
	public function set_private_meta( $user_id, $private ) {
		update_user_meta( $user_id, $this->option, $private );

		$comments = get_comments( array( 'user_id' => $user_id ) );

		foreach ( $comments as $comment ) {
			update_comment_meta( $comment->comment_ID, $this->option, $private );
		}
	}

	/**
	 * Expose a function to see whether or not a user's comments are private.
	 */
	public function get_private_meta( $user_id ) {
		return get_user_meta( $user_id, $this->option, true );
	}
}

$wp_private_comments = new WP_PrivateComments();
