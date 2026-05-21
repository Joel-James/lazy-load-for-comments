<?php
/**
 * Plugin Name:     Lazy Load for Comments
 * Plugin URI:      https://wordpress.org/plugins/lazy-load-for-comments
 * Description:     Lazy load the default WordPress comments. Comments are loaded only after the visitor clicks a button or scrolls to the comments area. Works with both classic and block themes.
 * Version:         2.0.0
 * Author:          Joel James
 * Author URI:      https://duckdev.com/
 * Donate link:     https://paypal.me/JoelCJ
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     lazy-load-for-comments
 * Domain Path:     /languages
 * Requires PHP:    7.4
 * Requires at least: 5.9
 *
 * @package LazyComments
 */

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

// Minimum PHP version is 7.4.
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	add_action(
		'admin_notices',
		function () {
			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				esc_html(
					sprintf(
						/* translators: 1: required PHP version, 2: current PHP version. */
						__( 'Lazy Load for Comments requires PHP %1$s or higher. Your site is running PHP %2$s.', 'lazy-load-for-comments' ),
						'7.4',
						PHP_VERSION
					)
				)
			);
		}
	);

	return;
}

// Plugin version.
define( 'LLC_VERSION', '2.0.0' );

// Plugin main file.
define( 'LLC_FILE', __FILE__ );

// Plugin directory path (with trailing slash).
define( 'LLC_DIR', plugin_dir_path( __FILE__ ) );

// Plugin directory URL (with trailing slash).
define( 'LLC_URL', plugin_dir_url( __FILE__ ) );

// Plugin base name.
define( 'LLC_BASE_NAME', plugin_basename( __FILE__ ) );

/**
 * Autoloader for the plugin classes.
 *
 * Maps the `DuckDev\LazyComments\` namespace to the `core/` directory.
 * Example: `DuckDev\LazyComments\Admin\Menu` => `core/admin/class-menu.php`.
 *
 * @since 2.0.0
 *
 * @param string $class Fully qualified class name.
 *
 * @return void
 */
spl_autoload_register(
	function ( $class ) {
		$prefix = 'DuckDev\\LazyComments\\';

		// Bail if the class is not in our namespace.
		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}

		$parts      = explode( '\\', substr( $class, strlen( $prefix ) ) );
		$class_name = array_pop( $parts );

		// Convert CamelCase class name to a kebab-case file name.
		$file = 'class-' . strtolower( preg_replace( '/([a-z0-9])([A-Z])/', '$1-$2', $class_name ) ) . '.php';

		// Sub-namespaces map to lowercase sub-directories.
		$sub_dir = '';
		foreach ( $parts as $part ) {
			$sub_dir .= strtolower( $part ) . '/';
		}

		$path = LLC_DIR . 'core/' . $sub_dir . $file;

		if ( is_readable( $path ) ) {
			require_once $path;
		}
	}
);

// Global helper functions.
require_once LLC_DIR . 'core/functions.php';

// Activation and deactivation hooks.
register_activation_hook( __FILE__, array( 'DuckDev\LazyComments\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'DuckDev\LazyComments\Plugin', 'deactivate' ) );

/**
 * Boot the plugin once all plugins are loaded.
 *
 * @since 2.0.0
 */
add_action(
	'plugins_loaded',
	function () {
		DuckDev\LazyComments\Core::instance();
	}
);
