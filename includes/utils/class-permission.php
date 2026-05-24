<?php
/**
 * Capability helpers for the plugin.
 *
 * Centralises the "who can manage the plugin?" decision so the answer
 * lives in one place. Every admin menu, REST endpoint and settings page
 * defers to {@see Permission::has_access()} instead of calling
 * `current_user_can()` directly.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Utils;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Permission
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Utils
 */
class Permission {

	/**
	 * Default capability required to manage the plugin.
	 *
	 * `manage_options` is the WordPress capability granted to
	 * administrators only.
	 *
	 * @since 2.0.0
	 */
	const CAPABILITY = 'manage_options';

	/**
	 * Get the capability required to manage the plugin.
	 *
	 * Filterable so site owners can grant access to other roles (for
	 * example, an editor on a multi-author site).
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function get_cap(): string {
		/**
		 * Filter the capability required to manage the plugin.
		 *
		 * @since 2.0.0
		 *
		 * @param string $cap Default capability ('manage_options').
		 */
		return (string) apply_filters( 'lazy_load_for_comments_capability', self::CAPABILITY );
	}

	/**
	 * Determine whether the current user can manage the plugin.
	 *
	 * Filterable so the access check can be replaced entirely (for
	 * example, to add an IP allow list or a feature flag).
	 *
	 * @since 2.0.0
	 *
	 * @return bool True when the current user passes the access check.
	 */
	public static function has_access(): bool {
		/**
		 * Filter the plugin access check.
		 *
		 * @since 2.0.0
		 *
		 * @param bool $has_access Result of the default capability check.
		 */
		return (bool) apply_filters(
			'lazy_load_for_comments_has_access',
			current_user_can( self::get_cap() )
		);
	}
}
