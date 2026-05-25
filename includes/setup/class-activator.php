<?php
/**
 * Activation handler.
 *
 * Runs once, whenever the plugin is activated (or reactivated after a
 * deactivate). Keeps the work small and side-effect-free so a normal
 * activation never blocks the user — heavy work (database upgrades,
 * cache warm-up) is deferred to an admin notice or a background event.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Setup;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\LazyComments\Settings;

/**
 * Class Activator
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Setup
 */
class Activator {

	/**
	 * Run activation tasks.
	 *
	 * Currently:
	 *  - Seed the default settings (also migrates the legacy v1.x
	 *    option, if present).
	 *  - Fire `lazy_load_for_comments_activated` so addons can react
	 *    to the activation event.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public static function run(): void {
		// Make sure the default settings exist and migrate any legacy
		// option left over from v1.x.
		Settings::instance()->maybe_migrate();

		/**
		 * Action hook fired right after the plugin is activated.
		 *
		 * @since 2.0.0
		 */
		do_action( 'lazy_load_for_comments_activated' );
	}
}
