<?php
/**
 * Resolver that locates a `core/comments` block in a block-theme template.
 *
 * The block replacer caches the parsed comments block per post in a
 * transient, but that transient may be missing — either because the
 * site owner disabled caching, the post has never been viewed, or
 * `switch_theme` cleared every cached entry. When that happens, the
 * REST renderer needs another way to find the markup to render.
 *
 * This service handles that fallback. It looks up the active block
 * template for the post type, walks the parsed block tree (descending
 * into `core/template-part` references), and returns the matched
 * comments block as a serialized block string ready to be passed back
 * through `do_blocks()`.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Services;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class BlockResolver
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Services
 */
class BlockResolver {

	/**
	 * Resolve the `core/comments` block from the active block template.
	 *
	 * Returns an empty string when:
	 *  - The current install is not block-theme capable (no
	 *    `resolve_block_template()`).
	 *  - The resolver cannot pick a matching template for the post.
	 *  - The block tree does not contain a comments block.
	 *
	 * @since 2.0.0
	 *
	 * @param \WP_Post $post The post the comments belong to.
	 *
	 * @return string Serialized comments block, or empty string when not found.
	 */
	public function resolve( \WP_Post $post ): string {
		if ( ! function_exists( 'resolve_block_template' ) ) {
			return '';
		}

		// Templates are tried in WordPress's normal hierarchy order:
		// specific post → post type → generic singular → index.
		$slugs = array(
			'single-' . $post->post_type . '-' . $post->post_name,
			'single-' . $post->post_type,
			'single',
			'singular',
			'index',
		);

		$template = resolve_block_template( 'singular', $slugs, '' );

		if ( ! $template || empty( $template->content ) ) {
			return '';
		}

		$block = $this->find( parse_blocks( $template->content ) );

		return $block ? serialize_block( $block ) : '';
	}

	/**
	 * Recursively search a parsed block tree for the comments block.
	 *
	 * `core/template-part` references are followed into the referenced
	 * part's own block tree, so the comments block is found even when
	 * the theme delegates it to a part (the typical block-theme pattern).
	 *
	 * @since 2.0.0
	 *
	 * @param array $blocks Parsed blocks from `parse_blocks()`.
	 *
	 * @return array|null The matched comments block, or null when not found.
	 */
	private function find( array $blocks ): ?array {
		foreach ( $blocks as $block ) {
			if ( empty( $block['blockName'] ) ) {
				continue;
			}

			if ( 'core/comments' === $block['blockName'] ) {
				return $block;
			}

			// Descend into template parts: the comments block usually
			// lives inside `footer.html` or a dedicated `comments.html`
			// part rather than directly in the singular template.
			if ( 'core/template-part' === $block['blockName'] && function_exists( 'get_block_template' ) ) {
				$slug  = isset( $block['attrs']['slug'] ) ? $block['attrs']['slug'] : '';
				$theme = isset( $block['attrs']['theme'] ) ? $block['attrs']['theme'] : wp_get_theme()->get_stylesheet();

				if ( $slug ) {
					$part = get_block_template( $theme . '//' . $slug, 'wp_template_part' );

					if ( $part && ! empty( $part->content ) ) {
						$found = $this->find( parse_blocks( $part->content ) );

						if ( $found ) {
							return $found;
						}
					}
				}
			}

			// Standard inner-block descent.
			if ( ! empty( $block['innerBlocks'] ) ) {
				$found = $this->find( $block['innerBlocks'] );

				if ( $found ) {
					return $found;
				}
			}
		}

		return null;
	}
}
