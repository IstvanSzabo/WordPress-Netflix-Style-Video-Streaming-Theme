<?php

/**
 * Resume video time ajax
 *
 * @return bool
 * @author  @s3bubble
 */
function streamium_create_resume() {

	global $wpdb;

	// Get params
	$post_id = $_REQUEST['post_id'];
	$user_id = get_current_user_id();
	$percentage = $_REQUEST['percentage'];

    // Check if user is logged in
    if ( !is_user_logged_in() ) {

    	echo json_encode(
	    	array(
	    		'error' => true,
	    		'message' => 'You must be logged in' 
	    	)
	    );

	    die();

    }

    if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'single_nonce' ) || ! isset( $_REQUEST['nonce'] ) ) {
        exit( "No naughty business please" );
    }

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

    	update_post_meta( $post_id, 'user_' . $user_id, $percentage );

    	echo json_encode(
	    	array(
	    		'error' => false,
	    		'percentage' => $percentage,
	    		'message' => 'Successfully added user resume' 
	    	)
	    );

        die();

    }
    else {
        
        wp_redirect( get_permalink( $_REQUEST['post_id'] ) );
        exit();

    }

}

add_action( 'wp_ajax_streamium_create_resume', 'streamium_create_resume' );