<?php
/**
 * Global helper functions.
 *
 * Thin convenience aliases that let theme and addon developers reach
 * the plugin's long-lived services without remembering the fully
 * qualified class names. Every helper delegates to the service-locator
 * accessors on {@see \DuckDev\LazyComments\Core}, so swapping the
 * underlying class out at the core level swaps it out everywhere.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'lazy_load_for_comments_settings' ) ) {
	/**
	 * Get the shared {@see \DuckDev\LazyComments\Settings} instance.
	 *
	 * Stable public alias — safe to call from themes, must-use plugins
	 * and addons.
	 *
	 * @since 2.0.0
	 *
	 * @return \DuckDev\LazyComments\Settings
	 */
	function lazy_load_for_comments_settings() {
		return \DuckDev\LazyComments\Core::instance()->settings();
	}
}
