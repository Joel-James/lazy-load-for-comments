<?php
/**
 * Plugin identity and URL helpers.
 *
 * Holds the small set of constants and helpers that describe the
 * plugin to the outside world (slug, page slug, settings URL, admin
 * screen ID). The lifecycle (activation / deactivation) used to live
 * here too, but now lives in its own dedicated classes inside the
 * `Setup` namespace.
 *
 * The class is intentionally static — there is only one plugin, so
 * carrying around an instance just to read its name would be noise.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments
 */
class Plugin {

	/**
	 * Plugin slug.
	 *
	 * Matches the WordPress.org slug and is used as a prefix for
	 * options, REST namespaces and admin page slugs.
	 *
	 * @since 2.0.0
	 */
	const SLUG = 'lazy-load-for-comments';

	/**
	 * Admin settings page slug.
	 *
	 * Equal to {@see Plugin::SLUG} today, but kept as a separate
	 * constant so the two can diverge without a search-and-replace.
	 *
	 * @since 2.0.0
	 */
	const PAGE = 'lazy-load-for-comments';

	/**
	 * Get the human-readable plugin name.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function name(): string {
		return 'Lazy Load for Comments';
	}

	/**
	 * Get the current plugin version.
	 *
	 * Resolves to whatever the `Version:` header in the main plugin
	 * file declares (loaded into `LLC_VERSION` at boot).
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function version(): string {
		return LLC_VERSION;
	}

	/**
	 * Build the admin URL of the settings page.
	 *
	 * Used by plugin row links, redirects and "Settings" calls-to-action.
	 *
	 * @since 2.0.0
	 *
	 * @return string Absolute admin URL.
	 */
	public static function settings_url(): string {
		return admin_url( 'edit-comments.php?page=' . self::PAGE );
	}

	/**
	 * Get the admin screen ID of the settings page.
	 *
	 * Because the settings page is registered as a sub-menu of the
	 * Comments menu (`edit-comments.php`), WordPress prefixes the
	 * screen ID with `comments_page_`.
	 *
	 * @since 2.0.0
	 *
	 * @return string Screen ID, e.g. `comments_page_lazy-load-for-comments`.
	 */
	public static function screen_id(): string {
		return 'comments_page_' . self::PAGE;
	}
}
