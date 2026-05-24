<?php
/**
 * Per-post cache for parsed `core/comments` blocks.
 *
 * Block themes can place the comments block anywhere in their template
 * tree, including inside template parts. Walking that tree on every
 * REST request is expensive, so the first time a visitor renders a post
 * the parsed block is stashed in a transient keyed by post ID. Every
 * subsequent REST render reads the block straight from the transient
 * and skips the resolver entirely.
 *
 * The cache is invalidated automatically on `switch_theme` (because the
 * new theme may render comments differently — or not at all) and can
 * be cleared manually from the settings UI through the cache REST
 * endpoint.
 *
 * @package LazyComments
 */

declare( strict_types = 1 );

namespace DuckDev\LazyComments\Cache;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Class BlockCache
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Cache
 */
class BlockCache {

	/**
	 * Prefix used for every transient stored by this cache.
	 *
	 * The full transient name is `{prefix}{post_id}` — that combination
	 * is what {@see BlockCache::key()} returns.
	 *
	 * @since 2.0.0
	 */
	const PREFIX = 'llc_comments_block_';

	/**
	 * How long a cached block stays valid before WordPress garbage
	 * collects the transient.
	 *
	 * One week is a balance between "fresh enough to pick up theme
	 * tweaks" and "long enough to survive normal traffic patterns".
	 *
	 * @since 2.0.0
	 */
	const TTL = WEEK_IN_SECONDS;

	/**
	 * Build the transient key for a given post.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string Transient key.
	 */
	public static function key( int $post_id ): string {
		return self::PREFIX . $post_id;
	}

	/**
	 * Retrieve the cached serialized block for a post.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string|false Serialized block markup, or `false` when no
	 *                      cached entry exists for this post.
	 */
	public static function get( int $post_id ) {
		return get_transient( self::key( $post_id ) );
	}

	/**
	 * Cache a serialized block for a post.
	 *
	 * @since 2.0.0
	 *
	 * @param int    $post_id Post ID.
	 * @param string $block   Serialized block markup.
	 *
	 * @return bool True when the transient was stored.
	 */
	public static function set( int $post_id, string $block ): bool {
		return set_transient( self::key( $post_id ), $block, self::TTL );
	}

	/**
	 * Delete every cached entry stored by this cache.
	 *
	 * Run on `switch_theme` and on demand from the cache REST endpoint.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public static function flush_all(): void {
		global $wpdb;

		// Find every stored transient by its option_name. WordPress
		// stores transients with an `_transient_` prefix in front of
		// the actual key, so the LIKE pattern accounts for that.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$options = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( '_transient_' . self::PREFIX ) . '%'
			)
		);

		// Delete via the WordPress API so the object cache is
		// invalidated alongside the database row.
		foreach ( $options as $option ) {
			delete_transient( substr( $option, strlen( '_transient_' ) ) );
		}
	}
}
