<?php
/**
 * Third-party theme compatibility.
 *
 * Houses small workarounds for themes that ship a non-standard
 * comments template.
 *
 * @package LazyComments
 */

namespace DuckDev\LazyComments\Compat;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\LazyComments\Utils\Base;

/**
 * Class Compatibility
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Compat
 */
class Compatibility extends Base {

	/**
	 * Register the compatibility hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init() {
		add_filter( 'et_builder_load_requests', array( $this, 'divi_load_builder_functions' ) );
	}

	/**
	 * Make sure Divi's builder functions are loaded.
	 *
	 * Divi's custom comments template depends on helpers defined in
	 * the builder's `functions.php`. The REST endpoint re-renders the
	 * comments via `comments_template()` outside the normal page
	 * lifecycle, so those helpers may not have been pulled in yet.
	 *
	 * @since 2.0.0
	 *
	 * @param array $requests Builder load requests (passed through unchanged).
	 *
	 * @return array
	 */
	public function divi_load_builder_functions( $requests ) {
		if ( defined( 'ET_BUILDER_DIR' ) ) {
			require_once ET_BUILDER_DIR . 'functions.php';
		}

		return $requests;
	}
}
