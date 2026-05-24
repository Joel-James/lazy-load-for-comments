<?php
/**
 * Contract for classes that swap the rendered comments for the placeholder.
 *
 * Two implementations exist today: {@see \DuckDev\LazyComments\Front\TemplateReplacer}
 * for classic themes, and {@see \DuckDev\LazyComments\Front\BlockReplacer}
 * for block themes. The interface documents the shared contract — a
 * replacer hooks into WordPress, decides whether the current request
 * is eligible, and (when it is) returns the placeholder markup in
 * place of the real comments.
 *
 * It also exposes a `detach()` method so the REST renderer can take
 * the replacer off the corresponding WordPress hook before producing
 * its own output — without that, the replacer would intercept its own
 * internal render and serve back a placeholder.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Contracts;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Interface Replacer
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Contracts
 */
interface Replacer {

	/**
	 * Remove the WordPress filter this replacer hooked into.
	 *
	 * Called by the REST renderer before it produces the real comments
	 * output, so the replacer does not intercept its own render pass.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function detach(): void;
}
