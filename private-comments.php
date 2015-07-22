<?php
/**
 * Plugin Name: Private Comments
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
	}

	public static function get_instance() {
		return self::$instance;
	}

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

	public function user_profile_update( $user_id ) {
		if ( isset( $_POST[ 'private_comments' ] ) ) {
			update_user_meta( $user_id, $this->option, true );
		} else {
			update_user_meta( $user_id, $this->option, false );
		}
	}
}

$wp_private_comments = new WP_PrivateComments();
