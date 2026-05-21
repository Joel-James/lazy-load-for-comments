<?php
/**
 * Settings page view.
 *
 * Renders the React mount point for the admin settings page.
 *
 * @package LazyComments
 */

namespace DuckDev\LazyComments\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\LazyComments\Utils\Base;

/**
 * Class Settings
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Views
 */
class Settings extends Base {

	/**
	 * Render the React app mount point.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function render() {
		echo '<div id="lazy-load-for-comments-settings" class="llc-wrap"></div>';
	}
}
