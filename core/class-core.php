<?php
/**
 * The core plugin class.
 *
 * Boots every part of the plugin. Only one instance is ever created.
 *
 * @package LazyComments
 */

namespace DuckDev\LazyComments;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\LazyComments\Utils\Base;

/**
 * Class Core
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments
 */
final class Core extends Base {

	/**
	 * Boot the plugin.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init() {
		$this->common();
		$this->admin();
		$this->front();
		$this->api();
		$this->compat();

		/**
		 * Action hook fired once the plugin is fully loaded.
		 *
		 * Addons should hook into this so they only load when the
		 * parent plugin is active.
		 *
		 * @since 2.0.0
		 *
		 * @param Core $core Plugin core instance.
		 */
		do_action( 'lazy_load_for_comments_init', $this );
	}

	/**
	 * Set up classes and hooks needed everywhere.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function common() {
		Settings::instance();

		add_action(
			'init',
			function () {
				load_plugin_textdomain(
					'lazy-load-for-comments',
					false,
					dirname( LLC_BASE_NAME ) . '/languages/'
				);
			}
		);

		// Clear the cached comments block markup when the theme changes.
		add_action(
			'switch_theme',
			array( Front\Comments::class, 'flush_cache' )
		);
	}

	/**
	 * Load the admin side classes.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function admin() {
		if ( is_admin() ) {
			Admin\Menu::instance();
			Admin\Assets::instance();
			Views\Admin::instance();
		}
	}

	/**
	 * Load the front end classes.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function front() {
		if ( ! is_admin() ) {
			Front\Comments::instance();
		}
	}

	/**
	 * Register the REST API endpoints.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function api() {
		new Api\Comments();
		new Api\Cache();
	}

	/**
	 * Register third-party theme compatibility.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function compat() {
		Compat\Compatibility::instance();
	}
}
