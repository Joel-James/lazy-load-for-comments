<?php
/**
 * Eligibility detector for lazy-loading comments.
 *
 * Owns the single decision: "should the current request lazy-load its
 * comments, or render them inline as WordPress normally would?" Every
 * front-end hook (template replacer, block replacer, link rewriter,
 * asset enqueuer) defers to this class so the answer stays consistent
 * for the whole request.
 *
 * The result is computed once per request and memoised — the check
 * itself reads settings, conditional tags and the user agent, which
 * can all be expensive when called many times.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Front;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\LazyComments\Utils\Singleton;

/**
 * Class Detector
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Front
 */
class Detector extends Singleton {

	/**
	 * Cached result of {@see Detector::can_lazy_load()} for this request.
	 *
	 * `null` means "not yet computed". Once computed, the value is
	 * either `true` or `false` and is returned directly on subsequent
	 * calls without re-evaluating the underlying conditions.
	 *
	 * @since 2.0.0
	 * @var bool|null
	 */
	private $can_lazy_load = null;

	/**
	 * Determine whether comments should be lazy-loaded for this request.
	 *
	 * Runs every eligibility rule, then exposes the result through a
	 * filter so site owners can override the decision (for example, to
	 * force inline comments on a specific post type).
	 *
	 * Rules, in order:
	 *  - `load_method` setting is not `off`.
	 *  - The request targets a single post or page.
	 *  - The post has at least `minimum_count` comments.
	 *  - The visitor does not look like a search-engine bot when
	 *    `disable_for_bots` is enabled.
	 *
	 * @since 2.0.0
	 *
	 * @return bool True when the comments should be lazy-loaded.
	 */
	public function can_lazy_load(): bool {
		// Return the memoised result if already computed.
		if ( null !== $this->can_lazy_load ) {
			return $this->can_lazy_load;
		}

		$can      = true;
		$settings = lazy_load_for_comments_settings();

		if ( 'off' === $settings->get( 'load_method' ) ) {
			// Feature disabled in the settings.
			$can = false;
		} elseif ( ! is_singular() ) {
			// Only single posts and pages render comments inline.
			$can = false;
		} elseif ( (int) get_comments_number() < max( 1, (int) $settings->get( 'minimum_count', 1 ) ) ) {
			// Fewer comments than the configured minimum — not worth lazy-loading.
			$can = false;
		} elseif ( $settings->get( 'disable_for_bots', true ) && $this->is_bot() ) {
			// Serve comments inline to bots for SEO.
			$can = false;
		}

		/**
		 * Filter whether comments should be lazy-loaded for this request.
		 *
		 * @since 2.0.0
		 *
		 * @param bool $can Result of the built-in eligibility checks.
		 */
		$this->can_lazy_load = (bool) apply_filters( 'lazy_load_for_comments_can_lazy_load', $can );

		return $this->can_lazy_load;
	}

	/**
	 * Best-effort detection of search-engine bots from the User-Agent header.
	 *
	 * A missing User-Agent is treated as a bot, since legitimate browsers
	 * always send one and most crawlers identify themselves in the header.
	 *
	 * @since 2.0.0
	 *
	 * @return bool True when the request looks like it comes from a bot.
	 */
	private function is_bot(): bool {
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return true;
		}

		$agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );

		// Matches the most common crawler tokens (Googlebot, Bingbot,
		// Yahoo Slurp, generic spiders, Google Mediapartners, etc.).
		return (bool) preg_match( '/bot|crawl|slurp|spider|mediapartners/i', $agent );
	}
}
