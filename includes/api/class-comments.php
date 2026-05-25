<?php
/**
 * Comments REST endpoint.
 *
 * Returns the rendered comments HTML for a post so the front-end
 * script can inject it on demand. The endpoint is intentionally thin:
 * all the heavy lifting (faking the main query, choosing block vs.
 * classic rendering, restoring globals) lives in
 * {@see \DuckDev\LazyComments\Services\CommentsRenderer}.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Api;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use WP_REST_Request;
use WP_REST_Response;
use DuckDev\LazyComments\Services\CommentsRenderer;

/**
 * Class Comments
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Api
 */
class Comments extends Endpoint {

	/**
	 * Renderer used to produce the comments HTML.
	 *
	 * @since 2.0.0
	 * @var CommentsRenderer
	 */
	private $renderer;

	/**
	 * Build the endpoint.
	 *
	 * @since 2.0.0
	 *
	 * @param CommentsRenderer|null $renderer Optional renderer injection
	 *                                        point (mainly for tests).
	 */
	public function __construct( ?CommentsRenderer $renderer = null ) {
		parent::__construct();

		$this->renderer = null === $renderer ? new CommentsRenderer() : $renderer;
	}

	/**
	 * Register the comments route.
	 *
	 * Endpoint: `GET /lazy-load-for-comments/v1/comments?post_id=123`.
	 *
	 * The endpoint is public (no nonce, no capability check) on purpose
	 * — the front-end script runs as the visitor, including logged-out
	 * visitors, and the response only contains markup WordPress would
	 * have served inline anyway. The endpoint is therefore safe to
	 * cache by full-page caches.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/comments',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_comments' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'post_id' => array(
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
						'validate_callback' => function ( $value ) {
							return $value > 0;
						},
					),
				),
			)
		);
	}

	/**
	 * Render and return the comments markup for a post.
	 *
	 * Responds with:
	 *  - `404` and an empty `html` for missing or non-public posts.
	 *  - `200` and an empty `html` for password-protected posts.
	 *  - `200` and the rendered markup otherwise.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request REST request object.
	 *
	 * @return WP_REST_Response JSON response with an `html` field.
	 */
	public function get_comments( WP_REST_Request $request ): WP_REST_Response {
		$post = get_post( (int) $request->get_param( 'post_id' ) );

		// Missing or non-public posts must not leak any markup.
		if ( ! $post instanceof \WP_Post || ! is_post_publicly_viewable( $post ) ) {
			return new WP_REST_Response( array( 'html' => '' ), 404 );
		}

		// Password-protected posts return an empty success — the page
		// is reachable, but the comments themselves are gated.
		if ( post_password_required( $post ) ) {
			return new WP_REST_Response( array( 'html' => '' ), 200 );
		}

		return new WP_REST_Response(
			array( 'html' => $this->renderer->render( $post ) ),
			200
		);
	}
}
