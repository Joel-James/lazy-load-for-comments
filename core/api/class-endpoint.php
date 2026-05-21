<?php
/**
 * REST API endpoint base class.
 *
 * @package LazyComments
 */

namespace DuckDev\LazyComments\Api;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Endpoint
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Api
 */
abstract class Endpoint {

	/**
	 * REST API namespace for all plugin endpoints.
	 *
	 * @since 2.0.0
	 */
	const NAMESPACE = 'lazy-load-for-comments/v1';

	/**
	 * Register the REST routes hook.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'routes' ) );
	}

	/**
	 * Register the routes for this endpoint.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	abstract public function routes();
}
