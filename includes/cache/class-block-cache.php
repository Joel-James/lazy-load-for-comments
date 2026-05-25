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
	 * Option key for the index of post IDs that currently have a cached
	 * block transient stored.
	 *
	 * We keep this index so {@see BlockCache::flush_all()} can iterate
	 * the known keys and delete them through the transient API, instead
	 * of relying on a `wp_options` `LIKE` query. The latter only sees
	 * DB-backed transients and silently misses every site running a
	 * persistent object cache (Redis, Memcached, hosts like Pantheon /
	 * WP Engine), where transients live in the object cache instead.
	 *
	 * The index is stored with `autoload = no` so it never bloats the
	 * autoloaded-options payload.
	 *
	 * @since 2.0.0
	 */
	const INDEX_KEY = 'lazy_load_for_comments_cache_index';

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
		$stored = set_transient( self::key( $post_id ), $block, self::TTL );

		if ( $stored ) {
			self::index_add( $post_id );
		}

		return $stored;
	}

	/**
	 * Delete every cached entry stored by this cache.
	 *
	 * Iterates the post-ID index and deletes each transient through the
	 * standard WordPress API — that way the flush works regardless of
	 * whether the transient lives in `wp_options` or in a persistent
	 * object cache.
	 *
	 * Run on `switch_theme` and on demand from the cache REST endpoint.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public static function flush_all(): void {
		foreach ( self::index() as $post_id ) {
			delete_transient( self::key( (int) $post_id ) );
		}

		delete_option( self::INDEX_KEY );
	}

	/**
	 * Read the cache index.
	 *
	 * @since 2.0.0
	 *
	 * @return int[]
	 */
	private static function index(): array {
		$index = get_option( self::INDEX_KEY, array() );

		return is_array( $index ) ? array_values( array_unique( array_map( 'intval', $index ) ) ) : array();
	}

	/**
	 * Add a post ID to the cache index if not already present.
	 *
	 * Stored with `autoload = no` so the (potentially large) list never
	 * lands inside WordPress's autoloaded-options payload.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	private static function index_add( int $post_id ): void {
		$index = self::index();

		if ( in_array( $post_id, $index, true ) ) {
			return;
		}

		$index[] = $post_id;

		update_option( self::INDEX_KEY, $index, false );
	}
}
