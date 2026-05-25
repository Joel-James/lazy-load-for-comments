<?php
/**
 * Plugin row links on the Plugins screen.
 *
 * Adds the "Settings" action link and a "Support" row-meta link to the
 * plugin's entry in `wp-admin/plugins.php`, so admins can jump to the
 * settings page (or the support forum) without leaving the list table.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Admin;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\LazyComments\Plugin;
use DuckDev\LazyComments\Utils\Singleton;

/**
 * Class Links
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Admin
 */
class Links extends Singleton {

	/**
	 * Hook the plugin row filters.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init(): void {
		// "Activate | Settings | Deactivate" — prepended to the action links.
		add_filter( 'plugin_action_links_' . LLC_BASE_NAME, array( $this, 'action_links' ) );

		// "Version | By ... | View details | Support" — appended to the row meta.
		add_filter( 'plugin_row_meta', array( $this, 'row_meta' ), 10, 2 );
	}

	/**
	 * Prepend a "Settings" link to the plugin action links.
	 *
	 * @since 2.0.0
	 *
	 * @param array $links Existing action links rendered by WordPress.
	 *
	 * @return array The action links with our "Settings" link in front.
	 */
	public function action_links( $links ): array {
		$links = is_array( $links ) ? $links : array();

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
	 * Append a "Support" link to the plugin's row meta.
	 *
	 * Only adds the link to our own plugin's row — `plugin_row_meta`
	 * fires for every plugin in the list.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $meta Existing row-meta links for this plugin.
	 * @param string   $file Plugin basename of the row currently being rendered.
	 *
	 * @return array The row meta, possibly with our "Support" link appended.
	 */
	public function row_meta( $meta, $file ): array {
		$meta = is_array( $meta ) ? $meta : array();

		if ( LLC_BASE_NAME === $file ) {
			$meta[] = '<a href="https://wordpress.org/support/plugin/lazy-load-for-comments/" target="_blank" rel="noopener noreferrer">'
				. esc_html__( 'Support', 'lazy-load-for-comments' )
				. '</a>';
		}

		return $meta;
	}
}
