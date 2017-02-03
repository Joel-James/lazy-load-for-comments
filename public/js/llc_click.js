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
        // Load comments data on button click.
        $( "#llc_comments_button" ).on( "click", function() {
            // Hide button after clicking on it.
            $( this ).hide();
            // Show loader div. Loader element will be loaded only if enabled.
            $( "#llc-comments-loader" ).show();
            // Ajax url link for ajax requests.
            var ajaxurl = $( "#llc_ajax_url" ).val();
            var data = {
                "action"         : "llc_load_comments",
                "post"           : $( "#llc_post_id" ).val(),
                "llc_ajax_nonce" : $( "#llc_ajax_nonce" ).val()
            };
            // Perform ajax request.
            $.ajax({
                dataType : "html",
                url      : ajaxurl,
                data     : data,
                type     : "post",
                success  : function( response ) {
                    if ( response !== "" ) {
                        $( "#llc_comments" ).html( response );
                    } else {
                        // Incase ajax request failed, append an error message.
                        $( "#llc_comments" ).html( '<p style="color: #ff0000;">' + llcstrings.loading_error + '</p>' );
                    }
                }
            });
        });
    });
})( jQuery );