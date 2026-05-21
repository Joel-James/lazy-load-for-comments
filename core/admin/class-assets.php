<?php
/**
 * Admin assets class.
 *
 * Enqueues the React settings app on the plugin settings page.
 *
 * @package LazyComments
 */

namespace DuckDev\LazyComments\Admin;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\LazyComments\Plugin;
use DuckDev\LazyComments\Utils\Base;

/**
 * Class Assets
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Admin
 */
class Assets extends Base {

	/**
	 * Script and style handle for the settings app.
	 *
	 * @since 2.0.0
	 */
	const HANDLE = 'lazy-load-for-comments-settings';

	/**
	 * Register the enqueue hook.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue the settings app assets on the settings page only.
	 *
	 * @since 2.0.0
	 *
	 * @param string $hook Current admin screen hook.
	 *
	 * @return void
	 */
	public function enqueue( $hook ) {
		// Only on our settings page.
		if ( Plugin::screen_id() !== $hook ) {
			return;
		}

		$asset = Plugin::asset( 'settings' );

		wp_enqueue_script(
			self::HANDLE,
			LLC_URL . 'app/assets/settings.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_set_script_translations( self::HANDLE, 'lazy-load-for-comments', LLC_DIR . 'languages' );

		wp_localize_script(
			self::HANDLE,
			'llcSettings',
			array(
				'version' => LLC_VERSION,
			)
		);

		if ( is_readable( LLC_DIR . 'app/assets/settings.css' ) ) {
			wp_enqueue_style(
				self::HANDLE,
				LLC_URL . 'app/assets/settings.css',
				array( 'wp-components' ),
				$asset['version']
			);
		}
	}
}
