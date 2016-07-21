<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die( 'Damn it.! Dude you are looking for what?' );
}

/**
 * The public-facing functionality of the plugin.
 *
 * @category   HTML
 * @package    LLC
 * @subpackage Public
 * @author     Joel James <j@thefoxe.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://thefoxe.com/products/lazy-load-comments/
 */
?>
<input type="hidden" name="llc_ajax_url" id="llc_ajax_url" value="<?php echo admin_url( 'admin-ajax.php' ); ?>" />
<input type="hidden" name="llc_post_id" id="llc_post_id" value="<?php echo get_the_ID(); ?>" />
<input type="hidden" name="llc_ajax_nonce" id="llc_ajax_nonce" value="<?php echo wp_create_nonce( "llc-ajax-nonce" ); ?>" />
<div id="llc_comments">
    <div  align="center">
        <?php if ( get_option( 'lazy_load_comments', 1 ) == 1 ) { ?>
            <button id="llc_comments_button" class="btn"><?php _e( 'Load Comments', LLC_DOMAIN ); ?></button>
        <?php } ?>
    </div>
</div>