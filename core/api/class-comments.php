<?php
/**
 * Comments REST API endpoint.
 *
 * Renders and returns the comments markup for a post so the front end
 * can lazy load it on demand.
 *
 * @package LazyComments
 */

namespace DuckDev\LazyComments\Api;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use WP_Query;
use WP_REST_Request;
use WP_REST_Response;
use DuckDev\LazyComments\Front\Comments as FrontComments;

/**
 * Class Comments
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Api
 */
class Comments extends Endpoint {

	/**
	 * Register the comments route.
	 *
	 * Endpoint: GET /lazy-load-for-comments/v1/comments?post_id=123
	 *
	 * The endpoint is public (no nonce) on purpose, so it works for
	 * logged-out visitors and is friendly to full page caching.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function routes() {
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
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_comments( WP_REST_Request $request ) {
		$post = get_post( (int) $request->get_param( 'post_id' ) );

		// Bail if the post is missing or not publicly viewable.
		if ( ! $post instanceof \WP_Post || ! is_post_publicly_viewable( $post ) ) {
			return new WP_REST_Response( array( 'html' => '' ), 404 );
		}

		// Nothing to show for password protected posts.
		if ( post_password_required( $post ) ) {
			return new WP_REST_Response( array( 'html' => '' ), 200 );
		}

		return new WP_REST_Response(
			array( 'html' => $this->render( $post ) ),
			200
		);
	}

	/**
	 * Render the comments HTML for a post within a faked main query.
	 *
	 * Block themes store the parsed `core/comments` block in a transient
	 * (see Front\Comments); we re-render it here so the output stays
	 * fresh. Classic themes fall back to `comments_template()`.
	 *
	 * @since 2.0.0
	 *
	 * @param \WP_Post $post Post object.
	 *
	 * @return string
	 */
	private function render( $post ) {
		global $wp_query, $wp_the_query;

		// Stop the front end controller from intercepting our own render.
		FrontComments::instance()->detach_render_filters();

		// Back up the current globals.
		$original_query = $wp_query;
		$original_post  = $GLOBALS['post'] ?? null;

		// Build a single-post main query so conditional tags work.
		$query = new WP_Query(
			array(
				'p'              => $post->ID,
				'post_type'      => $post->post_type,
				'posts_per_page' => 1,
			)
		);

		$wp_query     = $query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		$wp_the_query = $query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride

		if ( $query->have_posts() ) {
			$query->the_post();
		}

		// Provide the post context for block rendering.
		$context = function ( $block_context ) use ( $post ) {
			$block_context['postId']   = $post->ID;
			$block_context['postType'] = $post->post_type;

			return $block_context;
		};
		add_filter( 'render_block_context', $context, 99 );

		// Block themes: re-render the stored comments block. The block
		// theme check guards against a stale transient left behind after
		// switching from a block theme to a classic one.
		$block = wp_is_block_theme()
			? get_transient( FrontComments::transient_key( $post->ID ) )
			: false;

		ob_start();

		if ( ! empty( $block ) && is_string( $block ) ) {
			echo do_blocks( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			comments_template( '' );
		}

		$html = ob_get_clean();

		// Restore everything.
		remove_filter( 'render_block_context', $context, 99 );
		wp_reset_postdata();

		$wp_query        = $original_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		$wp_the_query    = $original_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		$GLOBALS['post'] = $original_post;  // phpcs:ignore WordPress.WP.GlobalVariablesOverride

		/**
		 * Filter the rendered comments HTML returned by the REST endpoint.
		 *
		 * @since 2.0.0
		 *
		 * @param string   $html Rendered comments HTML.
		 * @param \WP_Post $post Post object.
		 */
		return apply_filters( 'lazy_load_for_comments_rendered_html', $html, $post );
	}
}
