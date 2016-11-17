(function($) {
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
    $(function() {

        // Load comments data
        $( '#llc_comments_button' ).on( 'click', function() {
            $( '#llc_comments' ).html( '<h3>Loading comments....</h3>' );
            var ajaxurl = $( '#llc_ajax_url' ).val();
            var data = {
                'action': 'llc_load_comments',
                'post': $( '#llc_post_id' ).val(),
                'llc_ajax_nonce': $( '#llc_ajax_nonce' ).val()
            };

            $.ajax({
                dataType : "html",
                url : ajaxurl,
                data : data,
                type: 'post',
                success : function( response ) {
                    if ( response !== '' ) {
                        $( '#llc_comments' ).html( response );
                    } else {
                        $( '#llc_comments' ).html( '<h4 style="color: red;">Error occurred. Please reload this page.</h4>' );
                    }
                }
            });
        });
    });
})(jQuery);