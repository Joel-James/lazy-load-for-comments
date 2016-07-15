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
        
        global $post;
        
        $lazy_load = get_option( 'lazy_load_comments', 1 );
        
        if ( ! ( is_singular() && ( have_comments() || 'open' == $post->comment_status ) && 0 != $lazy_load ) ) {
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

        global $post;
        
        // Load only when required
        if ( ! is_singular() ) {
            return;
        }
        
        $lazy_load = get_option( 'lazy_load_comments', 1 );
        // If Lazy load is disabled, abort
        if ( $lazy_load == 0 ) {
            return;
        }
        // Get the lazy load script according to user choice.
        $file = ( $lazy_load == 2 ) ? 'llc_scroll.js' : 'llc_click.js';
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
        
        // If post/page id not found in request, abort
        if ( empty( $id = $_REQUEST['post'] ) ) {
            die();
        }
        
        query_posts( array( 'p' => $id, 'post_type' => 'any' ) );
        
        if ( have_posts() ) {
            the_post();
            remove_filter( 'comments_template', array( $this, 'llc_template' ) );
            comments_template();
            exit();
        }
        
        die();
    }

}
