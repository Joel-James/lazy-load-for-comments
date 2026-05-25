<?php
/**
 * Front-end controller.
 *
 * Thin coordinator that boots every front-end subsystem in one place.
 * No business logic lives here — each concern (eligibility detection,
 * template replacement, block replacement, link rewriting, asset
 * enqueueing) is owned by its own dedicated class.
 *
 * The class also exposes a single `detach_render_filters()` method so
 * the REST renderer can disable the two replacers before it produces
 * its own output (otherwise the replacers would intercept their own
 * internal render and serve an empty placeholder back to themselves).
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Front;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\LazyComments\Utils\Singleton;

/**
 * Class Controller
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Front
 */
class Controller extends Singleton {

	/**
	 * Boot every front-end subsystem.
	 *
	 * Ordering: the detector is referenced by every other subsystem, so
	 * it goes first; the renderer is stateless, so order doesn't matter
	 * for it. The remaining classes are self-contained and register
	 * their own hooks in their `init()` methods.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		Detector::instance();
		TemplateReplacer::instance();
		BlockReplacer::instance();
		LinkRewriter::instance();
		Assets::instance();
	}

	/**
	 * Detach both replacers so the REST renderer can run without them.
	 *
	 * The REST endpoint that powers the lazy-loaded fetch re-renders
	 * the comments inside its handler — without this detach call the
	 * two replacers would intercept that render, hand back a placeholder
	 * and cause an empty response.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function detach_render_filters(): void {
		TemplateReplacer::instance()->detach();
		BlockReplacer::instance()->detach();
	}
}
