<?php
/**
 * Admin view helpers.
 *
 * Adds the plugin links to the Plugins list table.
 *
 * @package LazyComments
 */

namespace DuckDev\LazyComments\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\LazyComments\Plugin;
use DuckDev\LazyComments\Utils\Base;

/**
 * Class Admin
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Views
 */
class Admin extends Base {

	/**
	 * Register the admin view hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init() {
		add_filter( 'plugin_action_links_' . LLC_BASE_NAME, array( $this, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'row_meta' ), 10, 2 );
	}

	/**
	 * Add a Settings link to the plugin action links.
	 *
	 * @since 2.0.0
	 *
	 * @param array $links Existing action links.
	 *
	 * @return array
	 */
	public function action_links( $links ) {
		array_unshift(
			$links,
			sprintf(
				'<a href="%s">%s</a>',
				esc_url( Plugin::settings_url() ),
				esc_html__( 'Settings', 'lazy-load-for-comments' )
			)
		);

		return $links;
	}

	/**
	 * Add support links to the plugin row meta.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $meta Plugin row meta links.
	 * @param string   $file Plugin file path.
	 *
	 * @return array
	 */
	public function row_meta( $meta, $file ) {
		if ( LLC_BASE_NAME === $file ) {
			$meta[] = '<a href="https://wordpress.org/support/plugin/lazy-load-for-comments/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Support', 'lazy-load-for-comments' ) . '</a>';
		}

		return $meta;
	}
}
