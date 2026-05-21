<?php
/**
 * Admin menu class.
 *
 * Registers the settings page as a sub menu of the Comments menu.
 *
 * @package LazyComments
 */

namespace DuckDev\LazyComments\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\LazyComments\Plugin;
use DuckDev\LazyComments\Permission;
use DuckDev\LazyComments\Views;
use DuckDev\LazyComments\Utils\Base;

/**
 * Class Menu
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Admin
 */
class Menu extends Base {

	/**
	 * Register the admin menu hook.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init() {
		add_action( 'admin_menu', array( $this, 'register' ) );
	}

	/**
	 * Register the settings sub menu page.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register() {
		add_submenu_page(
			'edit-comments.php',
			__( 'Lazy Load for Comments', 'lazy-load-for-comments' ),
			__( 'Lazy Load', 'lazy-load-for-comments' ),
			Permission::get_cap(),
			Plugin::PAGE,
			array( Views\Settings::instance(), 'render' )
		);
	}
}
