<?php

/**
 * Ajax post scipts for content
 *
 * @return bool
 * @author  @s3bubble
 */
function streamium_get_dynamic_series_content() {

	global $wpdb;

	// Get params
	$post_id = (int) $_REQUEST['post_id'];

	$episodes = get_post_meta($post_id, 'streamium_repeatable_series' , true);

	if(empty($episodes)){

		echo json_encode(
	    	array(
	    		'error' => true,
	    		'message' => 'No series found.'
	    	)
	    );

	    die();

	}

	// Order the list
	$positions = array();
	foreach ($episodes as $key => $row){
		$episodes[$key]['episode_link'] = get_permalink($post_id);
	    $positions[$key] = $row['episode_position'];
	}
	array_multisort($positions, SORT_ASC, $episodes);

	echo json_encode(
    	array(
    		'error'   => false,
    		'id'      => $post_id,
    		'title'   => get_the_title($post_id),
    		'data'    => $episodes,
    		'message' => 'Successfully returning data.'
    	)
    );

    die();

}

add_action( 'wp_ajax_nopriv_streamium_get_dynamic_series_content', 'streamium_get_dynamic_series_content' );
add_action( 'wp_ajax_streamium_get_dynamic_series_content', 'streamium_get_dynamic_series_content' );