<?php
/**
 * Build-manifest reader for compiled assets.
 *
 * `@wordpress/scripts` emits a small `*.asset.php` file alongside each
 * compiled bundle. The file is a PHP array literal containing the
 * bundle's WordPress script dependencies and a content hash used as
 * the cache-busting version. This class wraps the file-read so every
 * enqueue site reads the manifest the same way and falls back to safe
 * defaults when the manifest is missing (typically because the bundle
 * has not been built yet in a dev environment).
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Utils;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Assets
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Utils
 */
class Assets {

	/**
	 * Directory (relative to the plugin root) where bundles are emitted.
	 *
	 * Matches the `--output-path` argument passed to
	 * `@wordpress/scripts` in `package.json`.
	 *
	 * @since 2.0.0
	 */
	const BUILD_DIR = 'build/';

	/**
	 * Read the build manifest for a compiled asset.
	 *
	 * Returns an array with two keys:
	 *  - `dependencies` — WordPress script handles the bundle depends on.
	 *  - `version`      — content hash used as the asset version.
	 *
	 * When the manifest file is missing (the bundle has not been built
	 * yet), both keys fall back to safe defaults: an empty dependency
	 * list and the plugin's own version constant.
	 *
	 * @since 2.0.0
	 *
	 * @param string $handle Asset handle (e.g. `frontend`, `settings`).
	 *
	 * @return array{dependencies: string[], version: string}
	 */
	public static function manifest( string $handle ): array {
		$file = LLC_DIR . self::BUILD_DIR . $handle . '.asset.php';
		$data = is_readable( $file ) ? require $file : array();

		return wp_parse_args(
			(array) $data,
			array(
				'dependencies' => array(),
				'version'      => LLC_VERSION,
			)
		);
	}
}
