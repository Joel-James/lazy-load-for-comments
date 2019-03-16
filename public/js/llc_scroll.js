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

		// Flag to check if comments are loaded already.
		var llcLoaded = 0;
		// Function that makes ajax request and loaded comments.
		var loadComments = function () {
			// Do not load again.
			if ( llcLoaded > 0 ) {
				return;
			}
			// Show loader div and element if not disabled.
			$( "#llc-comments-loader" ).show();
			// Data to send over ajax request.
			var data = {
				"action": "llc_load_comments",
				"post": $( "#llc_post_id" ).val(),
			};
			// Ajax request link.
			var llcajaxurl = $( "#llc_ajax_url" ).val();
			// Full url to get comments (Adding parameters).
			var commentUrl = llcajaxurl + '?' + $.param( data );
			// Make ajax request to get comments.
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
					if ( $( '.wc-tabs .reviews_tab' ).length > 0 ) {
						$( '#rating' ).trigger( 'init' );
						// Make sure we are on reviews tab.
						$( '.reviews_tab a' ).click();
					}
				}
			} );
			// Set comments load flag as 1.
			llcLoaded = 1;
		}

		// Load comments if #comment found in url or if no scroll bar available.
		if ( window.location.href.indexOf( "#comment" ) > -1 || document.body.scrollHeight <= window.innerHeight ) {
			loadComments();
		}

		// Woocommerce reviews compatibility.
		if ( $( '.wc-tabs .reviews_tab' ).length > 0 ) {
			$( '.reviews_tab a' ).on( 'click', function() {
				// Show loader div and element if not disabled.
				loadComments();
			});
		} else {
			// Load comments data on scroll down.
			$( window ).scroll( function () {
				// Get comments div element.
				var rect = document.getElementById( "llc_comments" ).getBoundingClientRect();
				// If comments div is visible, get comments template using ajax.
				if ( rect.top < window.innerHeight && llcLoaded == 0 ) {
					// Show loader div and element if not disabled.
					loadComments();
				}
			} );
		}
	} );
})( jQuery );