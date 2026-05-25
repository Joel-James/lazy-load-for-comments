<?php
/**
 * PHPUnit bootstrap.
 *
 * Loads the WordPress test scaffolding, then loads the plugin into it
 * on `muplugins_loaded` so every WordPress hook the plugin registers
 * is in place before the tests start running.
 *
 * Expectations:
 *  - Run `bin/install-wp-tests.sh <db_name> <db_user> <db_pass> [db_host] [wp_version]`
 *    once to install the WordPress test scaffolding under `/tmp/`.
 *  - Run `composer install` so the Yoast PHPUnit polyfills are present.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

// Resolve the WordPress test library directory.
$llc_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $llc_tests_dir ) {
	$llc_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $llc_tests_dir . '/includes/functions.php' ) ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- WordPress is not loaded yet inside the PHPUnit bootstrap.
	fwrite(
		STDERR,
		'Could not find ' . $llc_tests_dir . '/includes/functions.php — install the WordPress test suite by running bin/install-wp-tests.sh first.' . PHP_EOL
	);
	exit( 1 );
}

// Yoast PHPUnit polyfills — required by recent WordPress test suites.
require_once dirname( __DIR__, 2 ) . '/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';

// WordPress test bootstrap functions.
require_once $llc_tests_dir . '/includes/functions.php';

/**
 * Load the plugin into the test WordPress install before WP boots fully.
 *
 * `tests_add_filter` defers the closure until `muplugins_loaded` —
 * earlier than `plugins_loaded`, so the plugin's hook registrations
 * happen before any test fixture creates a post / triggers a render.
 */
tests_add_filter(
	'muplugins_loaded',
	static function (): void {
		require dirname( __DIR__, 2 ) . '/lazy-load-for-comments.php';
	}
);

// Start the WordPress test environment.
require $llc_tests_dir . '/includes/bootstrap.php';
