<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die( 'Damn it.! Dude you are looking for what?' );
}

/**
 * Custom comments template to render on page load.
 *
 * @category   HTML
 * @package    LLC
 * @subpackage Public
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://wordpress.org/plugins/lazy-load-for-comments
 */
?>
<!-- Required values for loading comments via ajax -->
<input type="hidden" name="llc_ajax_url" id="llc_ajax_url" value="<?php echo admin_url( 'admin-ajax.php' ); ?>"/>
<input type="hidden" name="llc_post_id" id="llc_post_id" value="<?php echo get_the_ID(); ?>"/>
<input type="hidden" name="llc_ajax_nonce" id="llc_ajax_nonce" value="<?php echo wp_create_nonce( "llc-ajax-nonce" ); ?>"/>
<div id="llc_comments">
	<div style="text-align: center;">
		<div id="llc-comments-loader" style="display: none;">
			<!-- Filter to disable loader element if not needed -->
			<?php if ( apply_filters( 'llc_enable_loader_element', true ) ) : ?>
				<!-- Filter to change loader element -->
				<?php echo apply_filters( 'llc_loader_element_content', '<img src="data:image/gif;base64,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">' ); ?>
			<?php endif; ?>
		</div>
		<!-- Show comments button if "On Click" option is set -->
		<?php if ( get_option( 'lazy_load_comments', 1 ) == 1 ) : ?>
			<!-- Filter to modify loading button text and button class -->
			<button id="llc_comments_button"
			        class="btn <?php echo apply_filters( 'llc_button_class', '' ); ?>"><?php echo apply_filters( 'llc_button_text', __( 'Load Comments', LLC_DOMAIN ) ); ?></button>
		<?php endif; ?>
	</div>
</div>