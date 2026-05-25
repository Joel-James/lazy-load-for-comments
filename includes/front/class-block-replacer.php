<?php
/**
 * Block-theme `core/comments` block replacer.
 *
 * Replaces the rendered output of the `core/comments` block with the
 * plugin's placeholder when lazy-loading is eligible. The parsed block
 * is stashed in {@see \DuckDev\LazyComments\Cache\BlockCache} so the
 * REST endpoint can re-render the same block on demand without walking
 * the template tree again.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Front;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\LazyComments\Cache\BlockCache;
use DuckDev\LazyComments\Contracts\Replacer;
use DuckDev\LazyComments\Utils\Singleton;

/**
 * Class BlockReplacer
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Front
 */
class BlockReplacer extends Singleton implements Replacer {

	/**
	 * Hook priority for the `render_block` filter.
	 *
	 * @since 2.0.0
	 */
	const FILTER_PRIORITY = 10;

	/**
	 * Register the render-block filter.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		add_filter( 'render_block', array( $this, 'replace' ), self::FILTER_PRIORITY, 2 );
	}

	/**
	 * Detach the render-block filter.
	 *
	 * Called by the REST renderer before it produces its own block
	 * output, so this filter does not intercept its own render pass.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function detach(): void {
		remove_filter( 'render_block', array( $this, 'replace' ), self::FILTER_PRIORITY );
	}

	/**
	 * Filter callback: swap the rendered `core/comments` block for the placeholder.
	 *
	 * Stashes the parsed block in {@see BlockCache} when caching is
	 * enabled so the REST endpoint can re-render it without walking the
	 * active template again.
	 *
	 * @since 2.0.0
	 *
	 * @param string $content Rendered block HTML.
	 * @param array  $block   Parsed block array.
	 *
	 * @return string Either the original content or the placeholder markup.
	 */
	public function replace( $content, $block ): string {
		// Only the comments block — every other block passes through unchanged.
		if ( empty( $block['blockName'] ) || 'core/comments' !== $block['blockName'] ) {
			return $content;
		}

		if ( ! Detector::instance()->can_lazy_load() ) {
			return $content;
		}

		$post_id = get_the_ID();

		if ( ! $post_id ) {
			return $content;
		}

		// Stash the parsed block so the REST endpoint can re-render it
		// without walking the active template again. Skipped when the
		// admin has disabled caching — the REST endpoint will then
		// re-resolve the block from the active template on each request.
		if ( (bool) lazy_load_for_comments_settings()->get( 'cache_enabled', true ) ) {
			BlockCache::set( $post_id, serialize_block( $block ) );
		}

		return Renderer::placeholder();
	}
}
