<?php
/**
 * Server-side comments renderer used by the REST endpoint.
 *
 * The lazy-load REST endpoint needs to return the same HTML WordPress
 * would have rendered inline. That is more subtle than it sounds:
 *
 *  - Conditional tags (`is_singular`, `comments_open`, ...) must answer
 *    correctly, which means the main query has to point at the post.
 *  - Block themes attach the comments block to the post via the
 *    `render_block_context` filter — without that, the block renders
 *    against no post and produces an empty result.
 *  - Our own front-end replacers would intercept the render and serve
 *    back a placeholder if they were left attached.
 *
 * This service owns that orchestration: detach the replacers, swap the
 * globals, render through either `do_blocks()` (block themes) or
 * `comments_template()` (classic themes), then restore the globals.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Services;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use WP_Query;
use DuckDev\LazyComments\Cache\BlockCache;
use DuckDev\LazyComments\Front\Controller as FrontController;

/**
 * Class CommentsRenderer
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Services
 */
class CommentsRenderer {

	/**
	 * Priority used for the temporary `render_block_context` filter.
	 *
	 * Runs late (99) so it sees the same context every other filter has
	 * set up.
	 *
	 * @since 2.0.0
	 */
	const CONTEXT_PRIORITY = 99;

	/**
	 * Block resolver used as a fallback when no transient is available.
	 *
	 * @since 2.0.0
	 * @var BlockResolver
	 */
	private $resolver;

	/**
	 * Build the renderer.
	 *
	 * @since 2.0.0
	 *
	 * @param BlockResolver|null $resolver Optional resolver injection
	 *                                     point (mainly for tests). A
	 *                                     default is built when omitted.
	 */
	public function __construct( ?BlockResolver $resolver = null ) {
		$this->resolver = null === $resolver ? new BlockResolver() : $resolver;
	}

	/**
	 * Render the comments HTML for a post.
	 *
	 * Swaps the main query for a single-post query, renders the comments
	 * (either by re-rendering the cached `core/comments` block or by
	 * falling back to `comments_template()` for classic themes), then
	 * restores every global it touched.
	 *
	 * @since 2.0.0
	 *
	 * @param \WP_Post $post The post whose comments we want to render.
	 *
	 * @return string The rendered comments HTML.
	 */
	public function render( \WP_Post $post ): string {
		global $wp_query, $wp_the_query;

		// Stop our own replacers from intercepting this internal render.
		FrontController::instance()->detach_render_filters();

		// Snapshot the globals we are about to overwrite.
		$original_query = $wp_query;
		$original_post  = $GLOBALS['post'] ?? null;

		// Build a single-post main query so the conditional tags
		// (`is_singular`, `comments_open`, ...) answer correctly.
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

		// Force the block-render context to point at our post — the
		// comments block reads `postId` / `postType` from this filter
		// when it has no inherited context (which is our case here,
		// since we are rendering outside the normal page lifecycle).
		$context_filter = function ( $block_context ) use ( $post ) {
			$block_context['postId']   = $post->ID;
			$block_context['postType'] = $post->post_type;

			return $block_context;
		};
		add_filter( 'render_block_context', $context_filter, self::CONTEXT_PRIORITY );

		$block = $this->resolve_block( $post );

		ob_start();

		if ( ! empty( $block ) && is_string( $block ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo do_blocks( $block );
		} else {
			// Classic themes fall through to the standard
			// `comments_template()` lookup. Pass true so themes such as
			// Genesis receive separated comment types in $wp_query.
			comments_template( '', true );
		}

		$html = ob_get_clean();

		// Restore every global we touched, in the reverse order we touched it.
		remove_filter( 'render_block_context', $context_filter, self::CONTEXT_PRIORITY );
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
		 * @param \WP_Post $post Post the comments belong to.
		 */
		return (string) apply_filters( 'lazy_load_for_comments_rendered_html', $html, $post );
	}

	/**
	 * Pick the serialized comments block to render for a post.
	 *
	 * Looks in the per-post transient cache first; on a miss, asks the
	 * resolver to walk the active block template. Returns an empty
	 * string for classic themes (the caller then falls through to
	 * `comments_template()`).
	 *
	 * @since 2.0.0
	 *
	 * @param \WP_Post $post Post whose comments block we want.
	 *
	 * @return string Serialized block markup or an empty string.
	 */
	private function resolve_block( \WP_Post $post ): string {
		if ( ! wp_is_block_theme() ) {
			// Classic theme: skip the block path entirely. The block-theme
			// guard also protects against stale transients left behind
			// after switching from a block theme back to a classic one.
			return '';
		}

		$block = BlockCache::get( $post->ID );

		if ( ! empty( $block ) && is_string( $block ) ) {
			return $block;
		}

		// Transient missing (cache disabled, never seeded, or expired):
		// walk the active block template to find the comments block.
		return $this->resolver->resolve( $post );
	}
}
