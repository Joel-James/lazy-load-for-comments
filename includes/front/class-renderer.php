<?php
/**
 * Placeholder markup builder for the front-end lazy-load.
 *
 * Produces the small HTML snippet that replaces the comments section
 * server-side. The front-end script attaches to the mount-point ID
 * inside it and progressively swaps in the loaded comments.
 *
 * Kept as a tiny dedicated class so the markup lives in exactly one
 * place — anything that needs the placeholder (classic template, block
 * theme replacement, future addons) calls {@see Renderer::placeholder()}.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Front;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class Renderer
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Front
 */
class Renderer {

	/**
	 * DOM ID of the placeholder mount point.
	 *
	 * Matches the constant used by the front-end script in
	 * `assets/src/frontend.js`.
	 *
	 * @since 2.0.0
	 */
	const MOUNT_ID = 'lazy-load-for-comments-frontend';

	/**
	 * Anchor ID used by "jump to comments" links.
	 *
	 * {@see LinkRewriter} rewrites every `get_comments_link()` URL to
	 * end with `#llc-comments`, which targets the outer wrapper rendered
	 * by this class so the page scrolls to the placeholder.
	 *
	 * @since 2.0.0
	 */
	const ANCHOR_ID = 'llc-comments';

	/**
	 * Build the placeholder markup the front-end script enhances.
	 *
	 * Rendered server-side by both the classic comments template
	 * replacement and the block-theme `core/comments` replacement. The
	 * outer `<div id="llc-comments">` is the jump-link anchor; the inner
	 * `<div id="lazy-load-for-comments-frontend">` is the mount point
	 * the front-end script swaps in the loaded comments on.
	 *
	 * @since 2.0.0
	 *
	 * @return string HTML for the placeholder.
	 */
	public static function placeholder(): string {
		return sprintf(
			'<div id="%1$s" class="llc-comments-area"><div id="%2$s" class="llc-mount"></div></div>',
			esc_attr( self::ANCHOR_ID ),
			esc_attr( self::MOUNT_ID )
		);
	}
}
