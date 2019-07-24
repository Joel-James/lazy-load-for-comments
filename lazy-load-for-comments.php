<?php

/**
 * Plugin Name:     Lazy Load for Comments
 * Plugin URI:      https://wordpress.org/plugins/lazy-load-for-comments
 * Description:     Lazy Load default WordPress comments. Load comments only after user clicking on a button or scrolling down. It saves page load time.
 * Version:         1.0.10
 * Author:          Joel James
 * Author URI:      https://duckdev.com/
 * Donate link:     https://paypal.me/JoelCJ
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     lazy-load-for-comments
 * Domain Path:     /languages
 *
 * Comment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Comment is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Comment. If not, see <http://www.gnu.org/licenses/>.
 *
 * @category Core
 * @package  LLC
 * @author   Joel James <mail@cjoel.com>
 * @license  http://www.gnu.org/licenses/ GNU General Public License
 * @link     https://wordpress.org/plugins/lazy-load-for-comments
 */
// If this file is called directly, abort.
defined( 'WPINC' ) or die( 'Damn it.! Dude you are looking for what?' );

if ( ! class_exists( 'Lazy_Load_Comments' ) ) {

	// Constants array
	$constants = array(
		'LLC_NAME'       => 'lazy-load-for-comments',
		'LLC_DOMAIN'     => 'lazy-load-for-comments',
		'LLC_VERSION'    => '1.0.10',
		'LLC_PATH'       => plugins_url( '', __FILE__ ),
		'LLC_PLUGIN_DIR' => dirname( __FILE__ ),
		'LLC_PERMISSION' => 'manage_options'
	);

	foreach ( $constants as $constant => $value ) {
		// Set constants if not set already.
		if ( ! defined( $constant ) ) {
			define( $constant, $value );
		}
	}

	// The core plugin class that is used to define.
	// dashboard-specific hooks, and public-facing site hooks.
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lazy-load-comments.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	function llc_run_plugin() {

		$plugin = new Lazy_Load_Comments();
		$plugin->run();
	}

	llc_run_plugin();
}

//*** Thank you for your interest in Lazy Load Comments - Developed and managed by Joel James ***//