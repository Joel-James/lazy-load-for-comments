<?php
/**
 * Cache management REST API endpoint.
 *
 * Lets the settings UI clear the cached comments block transients.
 *
 * @package LazyComments
 */

namespace DuckDev\LazyComments\Api;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_REST_Response;
use DuckDev\LazyComments\Front\Comments as FrontComments;

/**
 * Class Cache
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Api
 */
class Cache extends Endpoint {

	/**
	 * Register the cache route.
	 *
	 * Endpoint: DELETE /lazy-load-for-comments/v1/cache
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function routes() {
		register_rest_route(
			self::NAMESPACE,
			'/cache',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'clear' ),
				'permission_callback' => array( $this, 'permission' ),
			)
		);
	}

	/**
	 * Only administrators can clear the cache.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function permission() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Clear all cached comments block transients.
	 *
	 * @since 2.0.0
	 *
	 * @return WP_REST_Response
	 */
	public function clear() {
		FrontComments::flush_cache();

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Comments cache cleared.', 'lazy-load-for-comments' ),
			),
			200
		);
	}
}
