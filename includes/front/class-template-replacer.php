<?php
/**
 * Classic-theme comments-template replacer.
 *
 * Swaps the theme's `comments.php` template for the plugin's small
 * placeholder template on requests where lazy-loading is eligible. The
 * placeholder template echoes the markup built by {@see Renderer} —
 * the front-end script then fetches and injects the real comments.
 *
 * Only classic themes go through `comments_template()`; block themes
 * use the `core/comments` block and are handled by {@see BlockReplacer}.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Front;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\LazyComments\Contracts\Replacer;
use DuckDev\LazyComments\Utils\Singleton;

/**
 * Class TemplateReplacer
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Front
 */
class TemplateReplacer extends Singleton implements Replacer {

	/**
	 * Hook priority used when adding the `comments_template` filter.
	 *
	 * Runs late (100) so it overrides theme-level filters that point at
	 * the theme's own comments template.
	 *
	 * @since 2.0.0
	 */
	const FILTER_PRIORITY = 100;

	/**
	 * Register the comments-template filter.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		add_filter( 'comments_template', array( $this, 'replace' ), self::FILTER_PRIORITY );
	}

	/**
	 * Detach the comments-template filter.
	 *
	 * Called by {@see \DuckDev\LazyComments\Services\CommentsRenderer}
	 * before it re-renders the comments inside the REST endpoint, so
	 * the swap does not intercept that internal render.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function detach(): void {
		remove_filter( 'comments_template', array( $this, 'replace' ), self::FILTER_PRIORITY );
	}

	/**
	 * Filter callback: return our placeholder template when eligible.
	 *
	 * @since 2.0.0
	 *
	 * @param string $template Absolute path to the theme's comments template.
	 *
	 * @return string Absolute path to either the placeholder template or
	 *                the original theme template (when not eligible).
	 */
	public function replace( string $template ): string {
		if ( ! Detector::instance()->can_lazy_load() ) {
			return $template;
		}

		return LLC_DIR . 'templates/comments.php';
	}
}
