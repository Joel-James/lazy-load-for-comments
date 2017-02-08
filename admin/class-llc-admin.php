<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die( 'Damn it.! Dude you are looking for what?' );
}

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @category   Core
 * @package    LLC
 * @subpackage Admin
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://wordpress.org/plugins/lazy-load-for-comments
 */
class LLC_Admin {

	/**
	 * Create new option field label to the default discussion settings page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @uses   register_setting()   To register new setting.
	 * @uses   add_settings_field() To add new field to for the setting.
	 *
	 * @return void
	 */
	public function options_page() {

		register_setting( 'discussion', 'lazy_load_comments' );

		add_settings_field(
			'lazy_load_comments_label',
			'<label for="lazy_load_comments">' . __( 'Lazy Load Comments', LLC_DOMAIN ) . '</label>',
			array( &$this, 'fields' ),
			'discussion'
		);
	}

	/**
	 * Create new options field to the settings page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @uses   get_option() To get the option value.
	 *
	 * @return void
	 */
	public function fields() {

		// Get settings value
		$value = get_option( 'lazy_load_comments', 2 );

		echo '<select name="lazy_load_comments" required>';
		echo '<option value="2" ' . selected( $value, 2, false ) . '>On Scroll</option>';
		echo '<option value="1" ' . selected( $value, 1, false ) . '>On Click</option>';
		echo '<option value="0" ' . selected( $value, 0, false ) . '>No Lazy Load</option>';
		echo '</select>';
		echo '<p class="description">' . __( 'How you want to load the comments.', LLC_DOMAIN ) . '</p>';
	}

}
