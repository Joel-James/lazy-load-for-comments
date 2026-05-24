<?php
/**
 * Settings page renderer.
 *
 * Outputs the mount-point markup the React settings app attaches to.
 * The mount point ID is matched by `assets/src/settings.js`, which
 * boots the React app in its place.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Admin;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\LazyComments\Utils\Singleton;

/**
 * Class Page
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Admin
 */
class Page extends Singleton {

	/**
	 * DOM ID of the React settings app mount point.
	 *
	 * Kept in sync with the matching constant in `assets/src/settings.js`.
	 *
	 * @since 2.0.0
	 */
	const MOUNT_ID = 'lazy-load-for-comments-settings';

	/**
	 * Render the settings page mount point.
	 *
	 * Called from {@see Menu::register()} as the page callback.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function render(): void {
		printf(
			'<div id="%s" class="llc-wrap"></div>',
			esc_attr( self::MOUNT_ID )
		);
	}
}
