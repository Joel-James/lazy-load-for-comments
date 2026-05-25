<?php
/**
 * Contract for classes that register REST API routes.
 *
 * Implemented by every concrete REST endpoint (and required by
 * {@see \DuckDev\LazyComments\Api\Endpoint}, which provides the
 * boilerplate hook wiring). The interface keeps the route declaration
 * discoverable at the type level — a class that `implements Routable`
 * is a class that registers one or more REST routes.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Contracts;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Interface Routable
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Contracts
 */
interface Routable {

	/**
	 * Register every REST route this class is responsible for.
	 *
	 * Called from the `rest_api_init` action; implementations issue
	 * one or more `register_rest_route()` calls.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function routes(): void;
}
