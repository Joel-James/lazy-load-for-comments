<?php
/**
 * Front-end script and style enqueuer.
 *
 * Loads the vanilla-JS lazy-loader script and its companion stylesheet
 * on requests where lazy-loading is eligible, and localises a small
 * settings object so the script knows which post to fetch, which load
 * method to use, what the button should say, and so on.
 *
 * Translations are baked into the localised payload (no `wp-i18n`
 * runtime), keeping the front-end payload as small as possible.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Front;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\LazyComments\Api\Endpoint;
use DuckDev\LazyComments\Utils\Assets as AssetManifest;
use DuckDev\LazyComments\Utils\Singleton;

/**
 * Class Assets
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Front
 */
class Assets extends Singleton {

	/**
	 * Script and style handle for the front-end bundle.
	 *
	 * Also used as the localised settings object's PHP-side handle.
	 *
	 * @since 2.0.0
	 */
	const HANDLE = 'lazy-load-for-comments-frontend';

	/**
	 * JavaScript variable name the localised settings are exposed under.
	 *
	 * Mirrors the `window.llcFrontend` reference in
	 * `assets/src/frontend.js`.
	 *
	 * @since 2.0.0
	 */
	const JS_OBJECT = 'llcFrontend';

	/**
	 * Register the front-end enqueue hook.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue the front-end script and stylesheet when eligible.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function enqueue(): void {
		if ( ! Detector::instance()->can_lazy_load() ) {
			return;
		}

		$asset    = AssetManifest::manifest( 'frontend' );
		$settings = lazy_load_for_comments_settings();

		wp_enqueue_script(
			self::HANDLE,
			LLC_URL . 'build/frontend.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		// Fall back to the default button label when the admin has
		// cleared the field — keeps the button readable.
		$button_text = $settings->get( 'button_text' );

		wp_localize_script(
			self::HANDLE,
			self::JS_OBJECT,
			array(
				'postId'       => get_the_ID(),
				'restUrl'      => rest_url( Endpoint::NAMESPACE . '/comments' ),
				'restNonce'    => wp_create_nonce( 'wp_rest' ),
				'method'       => $settings->get( 'load_method', 'scroll' ),
				'buttonText'   => '' !== $button_text ? $button_text : __( 'Load Comments', 'lazy-load-for-comments' ),
				'buttonStyle'  => $settings->get( 'button_style', 'theme' ),
				'buttonClass'  => $settings->get( 'button_class' ),
				'showLoader'   => (bool) $settings->get( 'show_loader', true ),
				'isBlockTheme' => wp_is_block_theme(),
				'loadingText'  => __( 'Loading comments…', 'lazy-load-for-comments' ),
				'errorText'    => __( 'Comments could not be loaded.', 'lazy-load-for-comments' ),
				'retryText'    => __( 'Retry', 'lazy-load-for-comments' ),
			)
		);

		// The stylesheet only exists when `npm run build` has been run;
		// guard the enqueue so the script still works in dev where the
		// SCSS may not be compiled yet.
		if ( is_readable( LLC_DIR . 'build/frontend.css' ) ) {
			wp_enqueue_style(
				self::HANDLE,
				LLC_URL . 'build/frontend.css',
				array(),
				$asset['version']
			);
		}
	}
}
