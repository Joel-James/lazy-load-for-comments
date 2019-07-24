<?php

// If this file is called directly, abort.
defined( 'WPINC' ) or die( 'Damn it.! Dude you are looking for what?' );

/**
 * The compatibility functionality with other plugins.
 *
 * @author     Joel James <mail@cjoel.com>
 * @link       https://wordpress.org/plugins/lazy-load-for-comments
 * @since      1.0.7
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @package    LLC
 * @subpackage Compatibility
 * @category   Core
 */
class LLC_Compatibility {

	/**
	 * Add compatibility with theme's that needs to separate comments.
	 *
	 * @param bool $flag Should separate comments.
	 *
	 * @since 1.0.10
	 *
	 * @return bool
	 */
	public function separate_comments( $flag ) {
		// For Genesis support.
		$genesis = function_exists( 'genesis' );
		// For Divi support.
		$divi = function_exists( 'et_setup_theme' );

		// Flag should be true.
		if ( $genesis || $divi ) {
			$flag = true;
		}

		return $flag;
	}

	/**
	 * Load Divi custom comments template function.
	 *
	 * We are using this filter hook because we couldn't find other proper
	 * hook to load the custom comments template.
	 *
	 * @param array $requests Requests for loading Divi.
	 *
	 * @since 1.0.10
	 *
	 * @return array
	 */
	public function divi_load_functions( $requests ) {
		// If Divi is loaded, load functions of builder.
		if ( defined('ET_BUILDER_DIR')) {
			require_once ET_BUILDER_DIR . 'functions.php';
		}

		return $requests;
	}
}
