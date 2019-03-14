(function ( $ ) {
	'use strict';

	/**
	 * All of the code for our admin-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, we are able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
     *
     * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
     *
     * });
	 *
	 * ...and so on.
	 */
	$( function () {

		// Load comments data on button click.
		$( "#llc_comments_button" ).on( "click", function () {

			// Hide button after clicking on it.
			$( this ).hide();
			// Show loader div. Loader element will be loaded only if enabled.
			$( "#llc-comments-loader" ).show();
			// Set data to send through ajax.
			var data = {
				action: "llc_load_comments",
				post: $( "#llc_post_id" ).val()
			};
			// Ajax request link.
			var llcajaxurl = $( "#llc_ajax_url" ).val();
			// Full url to get comments (Adding parameters).
			var commentUrl = llcajaxurl + '?' + $.param( data );
			// Perform ajax request.
			$.get( commentUrl, function ( response ) {
				if ( response !== "" ) {
					$( "#llc_comments" ).html( response );
					// Initialize comments after lazy loading.
					if ( window.addComment && window.addComment.init ) {
						window.addComment.init();
					}
					// Get the comment li id from url if exist.
					var commentId = document.URL.substr( document.URL.indexOf( "#comment" ) );
					// If comment id found, scroll to that comment.
					if ( commentId.indexOf( '#comment' ) > -1 ) {
						$( window ).scrollTop( $( commentId ).offset().top );
					}

					// Woocommerce reviews compatibility.
					if ( $( '#rating' ).length > 0 ) {
						$( '#rating' ).trigger( 'init' );
						$( '.reviews_tab a' ).click();
					}
				} else {
					// Incase ajax request failed, append an error message.
					$( "#llc_comments" ).html( '<p style="color: #ff0000;">' + llcstrings.loading_error + '</p>' );
				}
			} );
		} );

		// Load comments if #comment found in url.
		if ( window.location.href.indexOf( "#comment" ) > -1 ) {
			$( "#llc_comments_button" ).trigger( "click" );
		}
	} );
})( jQuery );