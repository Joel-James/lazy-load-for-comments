<?php
/**
 * The plugin core.
 *
 * Boots every subsystem (admin, front-end, REST, compatibility) in the
 * right order and exposes a small service-locator surface so callers
 * have a single, well-known place to reach the long-lived services.
 *
 * The class is intentionally `final` — the boot sequence is the source
 * of truth, and subclassing it would let third-party code reorder the
 * sequence in ways that are very hard to debug.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\LazyComments\Front\Detector;
use DuckDev\LazyComments\Utils\Singleton;

/**
 * Class Core
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments
 */
final class Core extends Singleton {

	/**
	 * Boot the plugin.
	 *
	 * Wires every subsystem up in dependency order: common services
	 * first, then admin, front-end, REST and compatibility. Each
	 * subsystem is responsible for registering its own WordPress hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		$this->common();
		$this->admin();
		$this->front();
		$this->api();
		$this->compat();

		/**
		 * Fires once the plugin is fully loaded.
		 *
		 * Addons should hook into this action so they only initialise
		 * when the parent plugin is active and ready.
		 *
		 * @since 2.0.0
		 *
		 * @param Core $core Plugin core instance.
		 */
		do_action( 'lazy_load_for_comments_init', $this );
	}

	// --------------------------------------------------------------------- //
	// Service locator.
	// --------------------------------------------------------------------- //

	/**
	 * Get the shared {@see Settings} instance.
	 *
	 * @since 2.0.0
	 *
	 * @return Settings
	 */
	public function settings(): Settings {
		return Settings::instance();
	}

	/**
	 * Get the shared {@see Detector} instance.
	 *
	 * @since 2.0.0
	 *
	 * @return Detector
	 */
	public function detector(): Detector {
		return Detector::instance();
	}

	// --------------------------------------------------------------------- //
	// Boot helpers.
	// --------------------------------------------------------------------- //

	/**
	 * Initialise services and hooks that run on every request.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function common(): void {
		Settings::instance();

		/*
		 * Translations.
		 *
		 * `load_plugin_textdomain()` is intentionally NOT called here.
		 * Since WordPress 4.6, plugins hosted on WordPress.org get their
		 * translations loaded automatically (just-in-time) the first
		 * time a `__()`-style call uses the plugin's text domain. The
		 * `.pot` template lives under `/languages` purely as a source
		 * for translate.wordpress.org; the matching `.mo` files are
		 * served and cached by WordPress itself.
		 */

		// Invalidate the cached comments block markup when the theme
		// changes — a new theme renders the block differently (or not
		// at all), so any stored markup is no longer trustworthy.
		add_action(
			'switch_theme',
			array( Cache\BlockCache::class, 'flush_all' )
		);
	}

	/**
	 * Initialise the admin-side classes.
	 *
	 * Only runs inside `wp-admin` so the front-end never pays for the
	 * admin code path.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function admin(): void {
		if ( is_admin() ) {
			Admin\Menu::instance();
			Admin\Assets::instance();
			Admin\Links::instance();
		}
	}

	/**
	 * Initialise the front-end controller.
	 *
	 * Only runs outside `wp-admin`; the REST endpoint runs through this
	 * same code path on the front-end (`is_admin()` is false during
	 * REST requests).
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function front(): void {
		if ( ! is_admin() ) {
			Front\Controller::instance();
		}
	}

	/**
	 * Register the REST API endpoints.
	 *
	 * REST endpoints are intentionally `new`'d rather than singletoned:
	 * they hold no per-request state, so a fresh instance per boot is
	 * fine and keeps testing trivial.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function api(): void {
		new Api\Comments();
		new Api\Cache();
	}

	/**
	 * Initialise third-party theme and plugin compatibility.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function compat(): void {
		Compat\Manager::instance();
	}
}
