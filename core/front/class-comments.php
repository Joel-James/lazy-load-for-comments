<?php
/**
 * Front end comments controller.
 *
 * Replaces the normal comments output with a React mount point so the
 * comments can be lazy loaded on click or scroll. Supports both classic
 * themes (`comments_template()`) and block themes (the `core/comments`
 * block).
 *
 * @package LazyComments
 */

namespace DuckDev\LazyComments\Front;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\LazyComments\Plugin;
use DuckDev\LazyComments\Api\Endpoint;
use DuckDev\LazyComments\Utils\Base;

/**
 * Class Comments
 *
 * @since   2.0.0
 * @package DuckDev\LazyComments\Front
 */
class Comments extends Base {

	/**
	 * Prefix for the transient that stores the comments block markup.
	 *
	 * @since 2.0.0
	 */
	const TRANSIENT_PREFIX = 'llc_comments_block_';

	/**
	 * Script and style handle for the front end app.
	 *
	 * @since 2.0.0
	 */
	const HANDLE = 'lazy-load-for-comments-frontend';

	/**
	 * Get the transient key used to store a post's comments block.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string
	 */
	public static function transient_key( $post_id ) {
		return self::TRANSIENT_PREFIX . (int) $post_id;
	}

	/**
	 * Register the front end hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function init() {
		// Classic themes use comments_template().
		add_filter( 'comments_template', array( $this, 'classic_template' ), 100 );

		// Block themes render the core/comments block.
		add_filter( 'render_block', array( $this, 'block_comments' ), 10, 2 );

		// Point "jump to comments" links to our placeholder.
		add_filter( 'get_comments_link', array( $this, 'comments_link' ), 100, 2 );

		// Load the front end app.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Detach the filters that swap comments for the placeholder.
	 *
	 * The REST endpoint calls this before rendering the real comments,
	 * so it does not intercept its own render pass.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function detach_render_filters() {
		remove_filter( 'comments_template', array( $this, 'classic_template' ), 100 );
		remove_filter( 'render_block', array( $this, 'block_comments' ), 10 );
	}

	/**
	 * Swap the classic comments template for our placeholder.
	 *
	 * @since 2.0.0
	 *
	 * @param string $template Path to the comments template.
	 *
	 * @return string
	 */
	public function classic_template( $template ) {
		if ( ! $this->can_lazy_load() ) {
			return $template;
		}

		return LLC_DIR . 'app/templates/comments.php';
	}

	/**
	 * Replace the block theme `core/comments` block with our placeholder.
	 *
	 * The original parsed block is stored in a transient so the REST
	 * endpoint can re-render it on demand.
	 *
	 * @since 2.0.0
	 *
	 * @param string $content Rendered block HTML.
	 * @param array  $block   Parsed block.
	 *
	 * @return string
	 */
	public function block_comments( $content, $block ) {
		// Only the comments block.
		if ( empty( $block['blockName'] ) || 'core/comments' !== $block['blockName'] ) {
			return $content;
		}

		if ( ! $this->can_lazy_load() ) {
			return $content;
		}

		$post_id = get_the_ID();

		if ( ! $post_id ) {
			return $content;
		}

		// Stash the block markup so the REST endpoint can re-render it.
		set_transient(
			self::transient_key( $post_id ),
			serialize_block( $block ),
			WEEK_IN_SECONDS
		);

		return $this->placeholder();
	}

	/**
	 * Point comment links to our placeholder anchor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $link    Comments link.
	 * @param int    $post_id Post ID.
	 *
	 * @return string
	 */
	public function comments_link( $link, $post_id ) {
		if ( $this->can_lazy_load() ) {
			return get_permalink( $post_id ) . '#llc-comments';
		}

		return $link;
	}

	/**
	 * Enqueue the front end React app.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function enqueue() {
		if ( ! $this->can_lazy_load() ) {
			return;
		}

		$asset    = Plugin::asset( 'frontend' );
		$settings = lazy_load_for_comments_settings();

		wp_enqueue_script(
			self::HANDLE,
			LLC_URL . 'app/assets/frontend.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_set_script_translations( self::HANDLE, 'lazy-load-for-comments', LLC_DIR . 'languages' );

		wp_localize_script(
			self::HANDLE,
			'llcFrontend',
			array(
				'postId'      => get_the_ID(),
				'restUrl'     => rest_url( Endpoint::NAMESPACE . '/comments' ),
				'method'      => $settings->get( 'load_method', 'scroll' ),
				'buttonText'  => $settings->get( 'button_text' ),
				'buttonClass' => $settings->get( 'button_class' ),
				'showLoader'  => (bool) $settings->get( 'show_loader', true ),
			)
		);

		if ( is_readable( LLC_DIR . 'app/assets/frontend.css' ) ) {
			wp_enqueue_style(
				self::HANDLE,
				LLC_URL . 'app/assets/frontend.css',
				array(),
				$asset['version']
			);
		}
	}

	/**
	 * Build the React mount point markup.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function placeholder() {
		return '<div id="llc-comments" class="llc-comments-area"><div id="' . esc_attr( self::HANDLE ) . '" class="llc-mount"></div></div>';
	}

	/**
	 * Check whether the current request should lazy load comments.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function can_lazy_load() {
		$can      = true;
		$settings = lazy_load_for_comments_settings();

		if ( 'off' === $settings->get( 'load_method' ) ) {
			// Lazy loading disabled.
			$can = false;
		} elseif ( ! is_singular() ) {
			// Only on single posts/pages.
			$can = false;
		} elseif ( (int) get_comments_number() < max( 1, (int) $settings->get( 'minimum_count', 1 ) ) ) {
			// Not enough comments to bother lazy loading.
			$can = false;
		} elseif ( $settings->get( 'disable_for_bots', true ) && $this->is_bot() ) {
			// Serve comments inline to bots for SEO.
			$can = false;
		}

		/**
		 * Filter whether comments should be lazy loaded for this request.
		 *
		 * @since 2.0.0
		 *
		 * @param bool $can Whether comments can be lazy loaded.
		 */
		return apply_filters( 'lazy_load_for_comments_can_lazy_load', $can );
	}

	/**
	 * Check if the current visitor looks like a search engine bot.
	 *
	 * A missing user agent is treated as a bot.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	private function is_bot() {
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return true;
		}

		$agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );

		return (bool) preg_match( '/bot|crawl|slurp|spider|mediapartners/i', $agent );
	}
}
