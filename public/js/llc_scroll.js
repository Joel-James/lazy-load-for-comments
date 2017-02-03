( function( $ ) {
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
    $( function() {
        // Flag to check if comments are loaded already.
        var llcLoaded = 0;
        // Load comments data on scroll down.
        $( window ).scroll( function() {
            // Data to send over ajax request.
            var data = {
                "action"         : "llc_load_comments",
                "post"           : $( "#llc_post_id" ).val(),
                "llc_ajax_nonce" : $( "#llc_ajax_nonce" ).val(),
            };
            // Get comments div element.
            var rect = document.getElementById( "llc_comments" ).getBoundingClientRect();
            // Ajax request link.
            var ajaxurl = $( "#llc_ajax_url" ).val();
            // If comments div is visible, get comments template using ajax.
            if ( rect.top < window.innerHeight && llcLoaded == 0 ) {
                // Show loader div and element if not disabled.
                $( "#llc-comments-loader" ).show();
                // Make ajax request to get comments.
                $.post( ajaxurl, data, function( response ) {
                    if ( response !== "" ) {
                        $( "#llc_comments" ).html( response );
                    }
                });
                // Set comments load flag as 1.
                llcLoaded = 1;
            }
        });
    });
})( jQuery );