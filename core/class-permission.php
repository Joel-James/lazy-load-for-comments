<?php
/**
 * Plugin permissions class.
 *
 * @package LazyComments
 */

namespace DuckDev\LazyComments;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Permission
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments
 */
class Permission {

	/**
	 * Capability required to manage the plugin.
	 *
	 * @since 2.0.0
	 */
	const CAPABILITY = 'manage_options';

	/**
	 * Get the capability required to manage the plugin.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function get_cap() {
		/**
		 * Filter the capability required to manage the plugin.
		 *
		 * @since 2.0.0
		 *
		 * @param string $cap Capability.
		 */
		return apply_filters( 'lazy_load_for_comments_capability', self::CAPABILITY );
	}

	/**
	 * Check if the current user can manage the plugin.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public static function has_access() {
		/**
		 * Filter the plugin access check.
		 *
		 * @since 2.0.0
		 *
		 * @param bool $has_access Whether the current user has access.
		 */
		return apply_filters(
			'lazy_load_for_comments_has_access',
			current_user_can( self::get_cap() )
		);
	}
}
