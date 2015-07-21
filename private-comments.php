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
	}

	public static function get_instance() {
		return self::$instance;
	}


}

$wp_private_comments = new WP_PrivateComments();
