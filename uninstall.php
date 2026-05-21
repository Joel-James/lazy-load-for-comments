<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Removes all plugin data: settings, the legacy option and the cached
 * comment block transients.
 *
 * @package LazyComments
 */

// Exit if not called by WordPress.
defined( 'WP_UNINSTALL_PLUGIN' ) || die;

// Delete plugin options.
delete_option( 'lazy_load_for_comments_settings' );
delete_option( 'lazy_load_comments' );

global $wpdb;

// Delete the cached comment block transients.
$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	"DELETE FROM {$wpdb->options}
	 WHERE option_name LIKE '\_transient\_llc\_comments\_block\_%'
	    OR option_name LIKE '\_transient\_timeout\_llc\_comments\_block\_%'"
);
