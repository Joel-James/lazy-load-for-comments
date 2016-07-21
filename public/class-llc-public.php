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
 * @author     Joel James <j@thefoxe.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://thefoxe.com/products/lazy-load-comments/
 */
class LLC_Public {

    /**
     * Lazy load comments template file
     * 
     * This is our custom comments template file for 
     * lazy loading the commments.
     * 
     * @since  1.0.0
     * @access public
     * 
     * @return file
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
        $file = ( get_option( 'lazy_load_comments', 1 ) == 2 ) ? 'llc_scroll.js' : 'llc_click.js';
        // Enqueue the script file.
        wp_enqueue_script(
            LLC_NAME,
            LLC_PATH . '/public/js/' . $file,
            array( 'jquery' ),
            LLC_VERSION,
            'all'
        );
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
        if ( empty( $id = $_REQUEST['post'] ) ) {
            die();
        }
        // Query through posts.
        query_posts( array( 'p' => $id, 'post_type' => 'any' ) );
        
        if ( have_posts() ) {
            the_post();
            remove_filter( 'comments_template', array( $this, 'llc_template' ) );
            comments_template();
            exit();
        }
        
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
        
        // If mobile OS is found it is real user
        if ( wp_is_mobile() ) {
            return true;
        }
        
        global $is_gecko, $is_opera, $is_safari, $is_chrome, $is_IE, $is_edge, $is_NS4, $is_lynx;
        
        return $is_gecko || $is_opera || $is_safari || $is_chrome || $is_IE || $is_edge || $is_NS4 || $is_lynx;        
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
        
        // If lazy loading is not enabled, abort.
        if ( 0 == get_option( 'lazy_load_comments', 1 ) ) {
            return false;
        }
        // If not on singular page, abort.
        if ( ! is_singular() ) {
            return false;
        }
        // If comments are not available, abort.
        if ( ! ( have_comments() || 'open' == $post->comment_status ) ) {
            return false; 
        }
        // If the visitor is not real user, abort.
        if ( ! $this->is_real_user() ) {
            return false;
        }
        
        return true;
    }

}
