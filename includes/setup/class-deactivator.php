<?php
/**
 * Deactivation handler.
 *
 * Runs once, whenever the plugin is deactivated. Intentionally minimal:
 * deactivation is not the same as uninstallation, so user data and
 * settings are preserved — full cleanup happens in `uninstall.php`.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Setup;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Deactivator
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Setup
 */
class Deactivator {

	/**
	 * Run deactivation tasks.
	 *
	 * Currently only fires an action hook so addons can react to the
	 * deactivation event. No data is removed.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public static function run(): void {
		/**
		 * Action hook fired right after the plugin is deactivated.
		 *
		 * @since 2.0.0
		 */
		do_action( 'lazy_load_for_comments_deactivated' );
	}
}
