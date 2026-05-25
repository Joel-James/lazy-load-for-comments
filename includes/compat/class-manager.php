<?php
/**
 * Third-party theme and plugin compatibility manager.
 *
 * Holds the small workarounds we need for themes and plugins whose
 * comment templates do not survive being re-rendered outside the normal
 * page lifecycle (the REST endpoint re-renders them on demand).
 *
 * Each workaround is hooked from {@see Manager::init()} so the class
 * stays a single, readable entry point.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Compat;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\LazyComments\Utils\Singleton;

/**
 * Class Manager
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Compat
 */
class Manager extends Singleton {

	/**
	 * Register every compatibility hook.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		// Divi: make sure the builder helpers are loaded before its
		// comments.php template tries to call them.
		add_filter( 'et_builder_load_requests', array( $this, 'divi_load_builder_functions' ) );
	}

	/**
	 * Ensure Divi's builder helper functions are loaded.
	 *
	 * Divi ships its own `comments.php` template that depends on helpers
	 * defined inside the Divi Builder's `functions.php`. When our REST
	 * endpoint re-renders the comments via `comments_template()` it runs
	 * outside the normal page lifecycle, so the builder may not have
	 * pulled those helpers in yet — we force the include here.
	 *
	 * @since 2.0.0
	 *
	 * @param array $requests Divi builder load requests (passed through unchanged).
	 *
	 * @return array The original list of requests.
	 */
	public function divi_load_builder_functions( $requests ): array {
		if ( defined( 'ET_BUILDER_DIR' ) ) {
			require_once ET_BUILDER_DIR . 'functions.php';
		}

		return is_array( $requests ) ? $requests : array();
	}
}
