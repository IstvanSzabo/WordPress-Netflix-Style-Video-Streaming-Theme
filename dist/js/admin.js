jQuery(document).ready(function($) {

    $('#_customize-input-streamium_generate_mrss_key').on('click',function(event){
        event.preventDefault();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: streamium_meta_object.ajax_url,
            data: {
                action: 'mrss_generate_key'
            },
            success: function(data){
                if(data.status){
                    // SAVE THE KEY USING THE CUSTOMIZER API::
                    wp.customize( 'streamium_mrss_key', function ( obj ) {
                        obj.set( data.key );
                    });
                    alert('Key generated! Make sure you publish this update!');
                } 
            },
            error: function(err) {
                console.log(err);
            }
        });
    });

    // Hook into the "notice-streamium-dummy-data" class we added to the notice, so
    // Only listen to YOUR notices being dismissed
    $( document ).on( 'click', '.notice-streamium-dummy-data .notice-dismiss', function () {
        
        // Read the "data-notice" information to track which notice
        // is being dismissed and send it via AJAX
        var type = $( this ).closest( '.notice-streamium-dummy-data' ).data( 'notice' );

        // Make an AJAX call
        // Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.ajax( streamium_meta_object.ajax_url,
            {
                type: 'POST',
                data: {
                    action: 'dismissed_notice_streamium_dummy_data',
                    type: type,
            }
        
        });
    });

});