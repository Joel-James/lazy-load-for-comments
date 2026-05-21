<?php
/**
 * Global helper functions.
 *
 * @package LazyComments
 */

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

if ( ! function_exists( 'lazy_load_for_comments_settings' ) ) {
	/**
	 * Get the plugin settings instance.
	 *
	 * @since 2.0.0
	 *
	 * @return \DuckDev\LazyComments\Settings
	 */
	function lazy_load_for_comments_settings() {
		return \DuckDev\LazyComments\Settings::instance();
	}
}
