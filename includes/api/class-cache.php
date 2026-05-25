<?php
/**
 * Cache management REST API endpoint.
 *
 * Lets the settings UI clear the cached comments block transients.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Api;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use WP_REST_Response;
use DuckDev\LazyComments\Cache\BlockCache;
use DuckDev\LazyComments\Utils\Permission;

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
	public function routes(): void {
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
	 * Only users who pass the plugin's access check can clear the cache.
	 *
	 * Delegates to {@see Permission::has_access()} so the capability and
	 * the access decision both flow through the plugin's filters
	 * (`lazy_load_for_comments_capability`, `lazy_load_for_comments_has_access`).
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function permission(): bool {
		return Permission::has_access();
	}

	/**
	 * Clear all cached comments block transients.
	 *
	 * @since 2.0.0
	 *
	 * @return WP_REST_Response
	 */
	public function clear(): WP_REST_Response {
		BlockCache::flush_all();

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Comments cache cleared.', 'lazy-load-for-comments' ),
			),
			200
		);
	}
}
