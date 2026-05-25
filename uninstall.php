<?php
/**
 * Plugin uninstall handler.
 *
 * Runs when the user clicks "Delete" on the plugin in `wp-admin/plugins.php`.
 * Removes every database trace the plugin leaves behind:
 *  - the v2.x settings option,
 *  - the legacy v1.x option,
 *  - every cached comments-block transient (and its `_timeout_` companion row).
 *
 * Intentionally side-effect-free aside from those `DELETE`s — we keep
 * the file readable so audits can confirm "deleting the plugin actually
 * deletes the plugin's data".
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

// Exit unless the file was loaded by WordPress's uninstall handler.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Drop the plugin's option rows.
delete_option( 'lazy_load_for_comments_settings' );
delete_option( 'lazy_load_for_comments_cache_index' );
delete_option( 'lazy_load_comments' );

global $wpdb;

// Drop every cached comments-block transient (value row AND timeout row).
// Done as a single DELETE because we are tearing down — re-running it
// through the transient API would only multiply the round-trips.
$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	"DELETE FROM {$wpdb->options}
	 WHERE option_name LIKE '\\_transient\\_llc\\_comments\\_block\\_%'
	    OR option_name LIKE '\\_transient\\_timeout\\_llc\\_comments\\_block\\_%'"
);
