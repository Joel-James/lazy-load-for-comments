<?php

/**
 * Fired only when the plugin is un-installed.
 *
 * Removes everything that this plugin added to your db.
 *
 * @category   Core
 * @package    LLC
 * @subpackage Uninstaller
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://duckdev.com/products/lazy-load-comments/
 */
// If uninstall not called from WordPress, then exit. That's it!

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete plugin options
if ( get_option( 'lazy_load_comments' ) ) {
    delete_option( 'lazy_load_comments' );
}

/******* The end. Thanks for using Lazy Load Comments plugin ********/
