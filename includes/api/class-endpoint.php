<?php
/**
 * Base class for REST API endpoints.
 *
 * Centralises the REST namespace and the `rest_api_init` hook wiring,
 * so every concrete endpoint only has to implement its `routes()`
 * method. Implements {@see Routable} so the route-declaration contract
 * is discoverable at the type level.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Api;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\LazyComments\Contracts\Routable;

/**
 * Class Endpoint
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Api
 */
abstract class Endpoint implements Routable {

	/**
	 * REST API namespace shared by every plugin endpoint.
	 *
	 * Concrete endpoints register routes under this namespace, so the
	 * full route looks like `/lazy-load-for-comments/v1/<name>`.
	 *
	 * @since 2.0.0
	 */
	const NAMESPACE = 'lazy-load-for-comments/v1';

	/**
	 * Hook the route registration into `rest_api_init`.
	 *
	 * Subclasses can call `parent::__construct()` to inherit the
	 * hooking, or skip it entirely when they need to override the
	 * timing (for example, to register routes lazily).
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'routes' ) );
	}

	/**
	 * Register every REST route this endpoint exposes.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	abstract public function routes(): void;
}
