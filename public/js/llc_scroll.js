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
        var llcLoaded = 0;
        // Load comments data
        window.onscroll = function () {
            
            var data = {
                'action': 'llc_load_comments',
                'post': $( '#llc_post_id' ).val()
            };
            var rect = document.getElementById('llc_comments').getBoundingClientRect();
            var ajaxurl = $( '#llc_ajax_url' ).val();
            
            if ( rect.top < window.innerHeight && llcLoaded == 0 ) {
                $.post( ajaxurl, data, function( response ) {
                    if ( response !== '' ) {
                        $( '#llc_comments' ).html( response );
                        llcLoaded = 1;
                    }
                    
                });
                window.onscroll = null;
            }
        }
    });
})(jQuery);