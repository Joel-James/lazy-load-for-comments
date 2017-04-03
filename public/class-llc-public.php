<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die( 'Damn it.! Dude you are looking for what?' );
}

/**
 * The public-facing functionality of the plugin.
 *
 * @category   Core
 * @package    LLC
 * @subpackage Public
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://wordpress.org/plugins/lazy-load-for-comments
 */
class LLC_Public {

	/**
	 * Lazy load comments template file
	 *
	 * This is our custom comments template file for
	 * lazy loading the commments.
	 *
	 * @param string $comment_template Current template.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string template path.
	 */
	public function llc_template( $comment_template ) {

		// Load default comment template if lazy load can not work.
		if ( ! $this->can_lazy_load() ) {
			return $comment_template;
		}

		return LLC_PLUGIN_DIR . '/public/llc-comments.php';
	}

	/**
	 * Javascript to include lazy load script.
	 *
	 * @using wp_localize_script() To make strings in JS translatable.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function comments_script() {

		// Do not load scripts to page if lazy load can not work.
		if ( ! $this->can_lazy_load() ) {
			return;
		}

		// Get the lazy load script according to user choice.
		$file = ( get_option( 'lazy_load_comments', 1 ) == 2 ) ? 'llc_scroll' : 'llc_click';
		// Minified or normal version?
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.js' : '.min.js';
		// Enqueue the script file.
		wp_enqueue_script(
			LLC_NAME,
			LLC_PATH . '/public/js/' . $file . $suffix,
			array( 'jquery' ),
			LLC_VERSION,
			'all'
		);

		// Strings to translate in JS file.
		$strings = array( 'loading_error' => esc_html__( 'Error occurred while loading comments. Please reload this page.', LLC_DOMAIN ) );

		// Make strings in js translatable.
		wp_localize_script( LLC_NAME, 'llcstrings', $strings );
	}

	/**
	 * Get the comments content for the current page.
	 *
	 * We will get the current page/post id from the
	 * request and we can load the post from that.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string html content
	 */
	public function comments_content() {

		// Security check.
		check_ajax_referer( 'llc-ajax-nonce', 'llc_ajax_nonce' );

		// If post/page id not found in request, abort.
		if ( empty( $_REQUEST['post'] ) ) {
			die();
		}
		// Query through posts.
		query_posts( array( 'p' => $_REQUEST['post'], 'post_type' => 'any' ) );
		// Render comments template and remove our custom template.
		if ( have_posts() ) {
			the_post();
			// Remove our custom comments template and load default template.
			remove_filter( 'comments_template', array( $this, 'llc_template' ) );
			comments_template();
			exit();
		}

		// Die for ajax request.
		die();
	}

	/**
	 * Check if real user is visiting.
	 *
	 * This function is used to check if the visitor
	 * is a real user or some bots. We don't need to
	 * lazy load the comments if bots are the visitors.
	 * This can help SEO for comments.
	 * We decide this based on user's browser.
	 *
	 * @global bool $is_gecko
	 * @global bool $is_opera
	 * @global bool $is_safari
	 * @global bool $is_chrome
	 * @global bool $is_IE
	 * @global bool $is_edge
	 * @global bool $is_NS4
	 * @global bool $is_lynx
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @return boolean If real user or not.
	 */
	private function is_real_user() {

		// Is real user visiting?
		$is_real = false;
		// Get global variables for browsers.
		global $is_gecko, $is_opera, $is_safari, $is_chrome, $is_IE, $is_edge, $is_NS4, $is_lynx;

		// If mobile OS is found it is real user
		if ( wp_is_mobile() ) {
			$is_real = true;
		} elseif ( $is_gecko || $is_opera || $is_safari || $is_chrome || $is_IE || $is_edge || $is_NS4 || $is_lynx ) {
			// If current browser user agent global variable is found.
			$is_real = true;
		} elseif ( ! $this->is_bot() ) {
			// If user agent is not bot/spider/crawlers.
			$is_real = true;
		}

		/**
		 * Filter to alter real user check.
		 *
		 * User this filter to add additional check for real user vs bots.
		 *
		 * @since 1.0.2
		 */
		return apply_filters( 'llc_is_real_user', $is_real );
	}

	/**
	 * Check if current visitor's user agent is a bot.
	 *
	 * Check if user agent string matches bots, spiders or crawlers.
	 * If user agent is not set, consider visitor as bot.
	 *
	 * @since 1.0.2
	 * @access private
	 *
	 * @return bool
	 */
	private function is_bot() {

		// If user agent is not set, flag them as bot.
		if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return true;
		}

		// Check if any type of bot, spider or crawler is visiting.
		if ( preg_match( '/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if it is OK to lazy load comments.
	 *
	 * We will continue only if lazy loading enabled,
	 * single post/page is being displayed, comment is
	 * available on the page or the visitor is not a bot.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @return boolean
	 */
	private function can_lazy_load() {

		global $post;

		// Can lazy load? (Default value).
		$can_lazyload = true;

		if ( 0 == get_option( 'lazy_load_comments', 1 ) ) {
			// If lazy loading is not enabled, abort.
			$can_lazyload = false;
		} elseif ( ! is_singular() ) {
			// If not on singular page, abort.
			$can_lazyload = false;
		} elseif ( ! ( have_comments() || 'open' == $post->comment_status ) ) {
			// If comments are not available, abort.
			$can_lazyload = false;
		} elseif ( ! $this->is_real_user() ) {
			// If the visitor is not real user, abort.
			$can_lazyload = false;
		}

		/**
		 * Filter to alter if comments can be lazy loaded.
		 *
		 * User this filter to add additional check before lazy loading
		 * or to bypass the default checking.
		 *
		 * @since 1.0.2
		 */
		return apply_filters( 'llc_can_lazy_load', $can_lazyload );
	}

}
