<?php
/**
 * Admin menu class.
 *
 * Registers the settings page as a sub menu of the Comments menu.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Admin;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\LazyComments\Plugin;
use DuckDev\LazyComments\Utils\Permission;
use DuckDev\LazyComments\Utils\Singleton;

/**
 * Class Menu
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Admin
 */
class Menu extends Singleton {

	/**
	 * Register the admin menu hook.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		add_action( 'admin_menu', array( $this, 'register' ) );
		add_action( 'admin_init', array( $this, 'register_discussion_pointer' ) );
	}

	/**
	 * Register the settings sub menu page.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_submenu_page(
			'edit-comments.php',
			__( 'Lazy Load for Comments', 'lazy-load-for-comments' ),
			__( 'Lazy Load', 'lazy-load-for-comments' ),
			Permission::get_cap(),
			Plugin::PAGE,
			array( Page::instance(), 'render' )
		);
	}

	/**
	 * Add a pointer to the plugin settings page on the Discussion settings screen.
	 *
	 * Older versions of the plugin (< 2.0.0) added a select field directly to
	 * `options-discussion.php`. The settings now live on their own page, so we
	 * leave a small link there so existing users can still find them.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register_discussion_pointer(): void {
		add_settings_field(
			'lazy_load_for_comments_link',
			__( 'Lazy Load for Comments', 'lazy-load-for-comments' ),
			array( $this, 'render_discussion_pointer' ),
			'discussion'
		);
	}

	/**
	 * Render the link to the plugin settings page.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function render_discussion_pointer(): void {
		$url = admin_url( 'edit-comments.php?page=' . Plugin::PAGE );

		printf(
			'<a href="%1$s" class="button">%2$s</a><p class="description">%3$s</p>',
			esc_url( $url ),
			esc_html__( 'Open Lazy Load settings', 'lazy-load-for-comments' ),
			esc_html__( 'Lazy load options have moved to their own page under Comments → Lazy Load.', 'lazy-load-for-comments' )
		);
	}
}
