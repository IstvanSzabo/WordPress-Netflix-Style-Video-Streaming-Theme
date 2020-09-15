<?php

/**
 * Ajax post scipts for single post
 *
 * @return bool
 * @author  @s3bubble
 */
function streamium_single_video_scripts() {

    if( is_single() )
    {

    	global $post;

    	$nonce       = wp_create_nonce( 'single_nonce' );

		$position = isset($_GET['v']) ? $_GET['v'] : 0;
		$count    = 0;
		$resume   = 0;
		$back     = false;

		
		$title        = $post->post_title;
		$description  = wp_trim_words( strip_tags( $post->post_content ), 21, '...' ); 
		$poster       = get_the_post_thumbnail_url( $post->ID, 'full' ); 
		$series       = get_post_meta( $post->ID, 'streamium_repeatable_series', true );
		$is_360       = get_post_meta( $post->ID,'streamium_video_360_meta', true );
		
		
		if(!empty($series)){ 

			$src          = $series[$position]['episode_url'];
			$type         = $series[$position]['episode_type'];
			$bif          = $series[$position]['episode_bif'];
			$captions     = $series[$position]['episode_captions'];
			$ads          = $series[$position]['episode_ads'];
			$title        = $series[$position]['episode_title'];
			$description  = $series[$position]['episode_description'];
			$is_360       = $series[$position]['episode_360']; 
			$count = count($series);
			$back = get_site_url() . '/browse';

		}else{

			$src       = get_post_meta( $post->ID,'streamium_video_url_meta', true );
			$type      = get_post_meta( $post->ID,'streamium_video_type_meta', true );
			$bif       = get_post_meta( $post->ID,'streamium_video_bif_meta', true );
			$captions  = get_post_meta( $post->ID,'streamium_video_captions_meta', true );
			$ads       = get_post_meta( $post->ID,'streamium_video_ads_meta', true );
			$back      = get_site_url() . '/browse';
			
		}

		if(is_user_logged_in()){
    		
    		$userId = get_current_user_id();
    		$percentageWatched = get_post_meta( $post->ID, 'user_' . $userId, true );
    		$resume = !empty( $percentageWatched ) ? $percentageWatched : 0;

    	}

		$ajaxData = array(
			'nonce'       => $nonce, 
            'post_id'     => $post->ID,
            'index'       => (int)$position,
            'count'       => (int)$count,
            'back'        => $back,
            'subTitle'    => "You're watching",
            'title'       => $title,
            'description' => $description,
            'src'         => $src,
            'type'        => $type,
            'bif'         => $bif,
            'captions'    => unserialize($captions),
            'adverts'     => $ads,
            'poster'      => esc_url( $poster ),
            'percentage'  => $resume,
            'is_360'      => $is_360,
            'report_email'=> ( get_theme_mod( 'streamium_footer_email_url' ) ) ? get_theme_mod( 'streamium_footer_email_url' ) : 'mailto:support@s3bubble.com' 
        );
 
		// CHECK FOR VPAID::
		if(get_theme_mod( 'streamium_advertisement_enabled', false )){
			$ajaxData['vpaid'] = get_theme_mod( 'streamium_advertisement_vpaid_url' );
		}

		// Setup premium
        wp_localize_script('streamium-production', 'video_post_object', $ajaxData); 

    } 

}

add_action('wp_enqueue_scripts', 'streamium_single_video_scripts');

/**
 * Ajax post scipts for content
 *
 * @return bool
 * @author  @s3bubble
 */
function streamium_get_dynamic_content() {

	global $wpdb;

	// Get params
	$cat = $_REQUEST['cat'];
	$post_id = (int) $_REQUEST['post_id'];
 
    if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'streamium_likes_nonce' ) || ! isset( $_REQUEST['nonce'] ) ) {
       	
       	echo json_encode(
	    	array(
	    		'error' => true,
	    		'message' => 'We could not find this post.'
	    	)
	    );

    }

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

    	$post_object = get_post( $post_id );

    	if(!empty($post_object)){

    		// SECURITY::
	        $nonce = wp_create_nonce( 'streamium_likes_nonce' );

    		$like_text = '';
	    	$buildMeta = '<ul>';

	    	// REVIEWS::
			$comments = get_comments('post_id=' . $post_id . '&status=approve');
			if ($comments) {
				
				$totalComments = count($comments);
				$tallyComments = 0;
				foreach($comments as $comment) : 

					$rating         = (int) get_comment_meta( $comment->comment_ID, 'rating', true );
					$tallyComments  += $rating;

				endforeach;

				$ratingTotal = round($tallyComments/$totalComments);
				$ratingHtml = '<div class="streamium-reviews-static">';
				for ($x = 1; $x < 6; $x++) {
				    $ratingHtml .= '<span class="streamium-reviews-star-static ' . (($ratingTotal >= $x) ? 'checked' : '') . '" data-value="' . $x . '"></span>';
				} 
				$ratingHtml .= '</div>';
			    $buildMeta  .= '<li class="synopis-meta-spacer">' . $ratingHtml . '</li>';

			}

			// Close the meta tag
			$buildMeta .= '</ul>';
            
	        $like_text = '<div class="synopis-premium-meta streamium-reviews-content-btns">
	        				
	        				<a class="btn streamium-tile-btns btn-lg center-block hidden-xs" href="' .  get_the_permalink($post_id) . '"><i class="fa fa-play" aria-hidden="true"></i> ' . __( 'Play', 'streamium' ) . '</a> 

	        				<a class="btn streamium-tile-btns btn-xs center-block hidden-sm hidden-md hidden-lg" href="' .  get_the_permalink($post_id) . '"><i class="fa fa-play" aria-hidden="true"></i> ' . __( 'Play', 'streamium' ) . '</a> 

	        				<a class="streamium-tile-btns btn btn-lg center-block hidden-xs show-more-content" data-id="' . $post_id . '" data-nonce="' . $nonce . '"><i class="fa fa-info" aria-hidden="true"></i> ' .  __( 'More Info', 'streamium' ) . '</a> 

	        				<a class="streamium-tile-btns btn btn-xs center-block hidden-sm hidden-md hidden-lg show-more-content" data-id="' . $post_id . '" data-nonce="' . $nonce . '"><i class="fa fa-info" aria-hidden="true"></i> ' .  __( 'More Info', 'streamium' ) . '</a>

						</div>';

	    	$fullImage  = wp_get_attachment_image_url( get_post_thumbnail_id( $post_id ), 'streamium-video-tile-large-expanded' );
	    	// Allow a extra image to be added
            if (class_exists('MultiPostThumbnails')) {                              
                
                if (MultiPostThumbnails::has_post_thumbnail( get_post_type( $post_id ), 'large-landscape-image', $post_id)) { 

                    $image_id = MultiPostThumbnails::get_post_thumbnail_id( get_post_type( $post_id ), 'large-landscape-image', $post_id );  
                    $fullImage = wp_get_attachment_image_url( $image_id,'streamium-video-tile-large-expanded' ); 

                }                            
             
            }; // end if MultiPostThumbnails 

            // Setup content
            $content = wp_trim_words( strip_tags($post_object->post_content), 15 );  

            // PREVIEW SECTION  =>
			$preview = '';
			$preview_video = get_post_meta( $post_id, 'streamium_preview_video_meta', true );
			$preview_video_type = get_post_meta( $post_id, 'streamium_preview_type_meta', true );
			if(!empty($preview_video) && !empty($preview_video)){
				$preview = [
					'src'  => $preview_video,
					'type' => $preview_video_type
				];
			}
			// PREVIEW SECTION  =>

			// TRAILER SECTION  =>
			$trailer = '';
			$trailer_video = get_post_meta( $post_id, 'streamium_trailer_video_meta', true );
			$trailer_video_type = get_post_meta( $post_id, 'streamium_trailer_type_meta', true );
			if(!empty($trailer_video) && !empty($trailer_video)){
				$trailer = [
					'src'  => $trailer_video,
					'type' => $trailer_video_type
				];
			}
			// TRAILER SECTION  =>

	    	echo json_encode(
		    	array(
		    		'error'    => false,
		    		'cat'      => $cat,
		    		'title'    => $post_object->post_title,
		    		'content'  => $content,
		    		'meta'     => $buildMeta,
		    		'reviews'  => $like_text,
		    		'bgimage'  =>  isset($fullImage) ? $fullImage : "",
		    		'href'     => get_permalink($post_id),
		    		'preview'  => $preview,
		    		'trailer'  => $trailer,
		    		'post'     => $post_object
		    	)
		    );

	    }else{

	    	echo json_encode(
		    	array(
		    		'error' => true,
		    		'message' => 'We could not find this post.'
		    	)
		    );

	    }

        die();

    }
    else {
        
        wp_redirect( get_permalink( $_REQUEST['post_id'] ) );
        exit();

    }

}

add_action( 'wp_ajax_nopriv_streamium_get_dynamic_content', 'streamium_get_dynamic_content' );
add_action( 'wp_ajax_streamium_get_dynamic_content', 'streamium_get_dynamic_content' );


/**
 * Ajax post scipts for content
 *
 * @return bool
 * @author  @s3bubble
 */
function streamium_get_more_content() {

	global $wpdb;

	// Get params
	$post_id = (int) $_REQUEST['post_id'];
 
    if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'extra_api_nonce' ) || ! isset( $_REQUEST['nonce'] ) ) {
       	
       	echo json_encode(
	    	array(
	    		'error' => true,
	    		'message' => 'We could not find this post.'
	    	)
	    );

    }

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

    	$post_object = get_post( $post_id );

    	if(!empty($post_object)){

    		// Security
    		$nonce = wp_create_nonce( 'streamium_likes_nonce' );

	    	$fullImage  = wp_get_attachment_image_url( get_post_thumbnail_id( $post_id ), 'streamium-roku-thumbnail' );
	    	// Allow a extra image to be added
            if (class_exists('MultiPostThumbnails')) {                              
                
                if (MultiPostThumbnails::has_post_thumbnail( get_post_type( $post_id ), 'large-landscape-image', $post_id)) { 

                    $image_id = MultiPostThumbnails::get_post_thumbnail_id( get_post_type( $post_id ), 'large-landscape-image', $post_id );  
                    $fullImage = wp_get_attachment_image_url( $image_id,'streamium-video-tile-large-expanded' ); 

                }                            
             
            }; // end if MultiPostThumbnails 

	    	// REVIEWS::
	    	$reviewStars = '';
			$comments = get_comments('post_id=' . $post_id . '&status=approve');
			if ($comments) {
				
				$totalComments = count($comments);
				$tallyComments = 0;
				foreach($comments as $comment) : 

					$tallyComments  += (int) get_comment_meta( $comment->comment_ID, 'rating', true );

				endforeach;

				$ratingTotal = round($tallyComments/$totalComments);
				$ratingHtml = '<div class="streamium-reviews-static">';
				for ($x = 1; $x < 6; $x++) {
				    $ratingHtml .= '<span class="streamium-reviews-star-static ' . (($ratingTotal >= $x) ? 'checked' : '') . '" data-value="' . $x . '"></span>';
				} 
				$ratingHtml .= '</div>';
			    $reviewStars  .= '<li class="synopis-meta-spacer">' . $ratingHtml . '</li>';

			}

			// BUTTONS SECTION  =>

			$series = get_post_meta($post_id, 'streamium_repeatable_series', true);

			$buttons = '<li class="synopis-meta-spacer">';

			$buttons .= '<a class="btn streamium-tile-btns" href="' . get_permalink($post_id) . '"><i class="fa fa-play" aria-hidden="true"></i> ' .  __( 'Play', 'streamium' ) . '</a> ';

			$buttons .= '<a id="like-count-' . $post_id . '" class="streamium-review-like-btn btn streamium-tile-btns" data-toggle="tooltip" title="' .  __( 'CLICK TO REVIEW!', 'streamium' ) . '" data-id="' . $post_id . '" data-nonce="' . $nonce . '"><i class="fa fa-pencil" aria-hidden="true"></i> ' . get_streamium_likes($post_id) . '</a> ';

			$buttons .= '<a class="streamium-list-reviews btn streamium-tile-btns" data-id="' . $post_id . '" data-nonce="' . $nonce . '"><i class="fa fa-commenting" aria-hidden="true"></i> ' .  __( 'Read reviews', 'streamium' ) . '</a> ';

	    	if(!empty($series)){

	    		$buttons .= '<a class="streamium-list-episodes btn streamium-tile-btns" data-id="' . $post_id . '" data-nonce="' . $nonce . '"><i class="fa fa-list" aria-hidden="true"></i> ' .  __( 'Episodes', 'streamium' ) . '</a>';
	    	}

	    	$buttons .= '</li>';

	    	// BUTTONS SECTION  =>


			// GENRES::
			$query      = get_post_taxonomies($post_id);
			$tax        = isset($query[1]) ? $query[1] : "";
			$taxName    = get_theme_mod( 'streamium_section_input_taxonomy_' . $tax, $tax );
			$categories = get_the_terms( $post_id, $tax );
			$genres     = '';
			if ($categories) {

				$genres = ucfirst($taxName) . ': ';
				$numItems = count($categories);
				$i = 0;
			  	foreach($categories as $cat) {

			  		$genres .= '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . ucwords($cat->name) . '</a>';
			  		if(++$i !== $numItems) {
			    		$genres .= ', ';
			  		}

			  	}
			  	$genres = '<li class="synopis-meta-spacer">' . $genres . '</li>';
			}	

			// RELEASED::
			$streamiumOverrideReleaseDate = get_post_meta( $post_id, 'streamium_release_date_meta', true );
			$released = '';
			if(!empty($streamiumOverrideReleaseDate)){
				$released .= '<li class="synopis-meta-spacer">' .  __( 'Released', 'streamium' ) . ': ' . $streamiumOverrideReleaseDate . '</li>';
			}else{
				$released .= '<li class="synopis-meta-spacer">' .  __( 'Released', 'streamium' ) . ': <a href="/?s=all&date=' . get_the_date('Y/m/d', $post_id) . '">' . get_the_date('l, F j, Y', $post_id) . '</a></li>';
			}

			// RATING::
			$rating = '';
			$streamium_ratings = get_post_meta( $post_id, 'streamium_ratings_meta', true );
			if ( ! empty( $streamium_ratings ) ) {
				$rating = '<li class="synopis-meta-spacer">' . __( 'Rating', 'streamium' ) . ': ' . $streamium_ratings . '</a></li>';
			}

			// BIND EXTRA META::
			$extra_meta = '<ul>' . $reviewStars . $buttons . $genres . $released . $rating . '</ul>';

			// PREVIEW SECTION  =>
			$preview = '';
			$preview_video = get_post_meta( $post_id, 'streamium_preview_video_meta', true );
			$preview_video_type = get_post_meta( $post_id, 'streamium_preview_type_meta', true );
			if(!empty($preview_video) && !empty($preview_video)){
				$preview = [
					'src'  => $preview_video,
					'type' => $preview_video_type
				];
			}
			// PREVIEW SECTION  =>

			// TRAILER SECTION  =>
			$trailer = '';
			$trailer_video = get_post_meta( $post_id, 'streamium_trailer_video_meta', true );
			$trailer_video_type = get_post_meta( $post_id, 'streamium_trailer_type_meta', true );
			if(!empty($trailer_video) && !empty($trailer_video)){
				$trailer = [
					'src'  => $trailer_video,
					'type' => $trailer_video_type,
					'poster' => isset($fullImage) ? $fullImage : ""
				];
			}
			// TRAILER SECTION  =>

	    	echo json_encode(
		    	array(
		    		'error'    => false,
		    		'title'    => $post_object->post_title,
		    		'content'  => $extra_meta . $post_object->post_content,
		    		'bgimage'  => isset($fullImage) ? $fullImage : "",
		    		'href'     => get_permalink($post_id),
		    		'preview'  => $preview,
		    		'trailer'  => $trailer
		    	)
		    );

	    }else{

	    	echo json_encode(
		    	array(
		    		'error' => true,
		    		'message' => 'We could not find this post.'
		    	)
		    );

	    }

        die();

    }
    else {
        
        wp_redirect( get_permalink( $_REQUEST['post_id'] ) );
        exit();

    }

}

add_action( 'wp_ajax_nopriv_streamium_get_more_content', 'streamium_get_more_content' );
add_action( 'wp_ajax_streamium_get_more_content', 'streamium_get_more_content' );

function streamium_custom_post_types_general( $hook_suffix ){

    if( in_array($hook_suffix, array('post.php', 'post-new.php') ) ){
        
        $screen = get_current_screen();

        if( is_object( $screen ) && in_array($screen->post_type, streamium_global_meta())){

            // Register, enqueue scripts and styles here
            wp_enqueue_script( 'streamium-admin-custom-post-type-general', get_template_directory_uri() . '/production/js/custom.post.type.general.min.js', array( 'jquery', 'jquery-migrate', 'jquery-ui-core', 'jquery-ui-datepicker' ),'1.1', true );

        }

    }
}

add_action( 'admin_enqueue_scripts', 'streamium_custom_post_types_general');


// ONLY MOVIE CUSTOM TYPE POSTS
add_filter('manage_posts_columns', 'streamium_columns_roku', 1);
add_action('manage_posts_custom_column', 'streamium_columns_roku_content', 10, 2);
 
// CREATE TWO FUNCTIONS TO HANDLE THE COLUMN
function streamium_columns_roku($columns) { 
    
    $new = array();
  	foreach($columns as $key => $title) {
    	if ($key=='author') // Put the Thumbnail column before the Author column
      	$new['roku'] = 'Roku';
    	$new[$key] = $title;
  	}
  	return $new;
  	
}

function streamium_columns_roku_content($column_name, $post_ID) {

    if ($column_name == 'roku') {

    	$thumbnail = false;
        if (class_exists('MultiPostThumbnails')) {                              
            if (MultiPostThumbnails::has_post_thumbnail( get_post_type( $post_ID ), 'roku-thumbnail-image', $post_ID)){
            	$thumbnail = true;
            }                             
        }; 

        $url       = get_post_meta( $post_ID, 'streamium_video_url_meta', true );
        $quality   = get_post_meta( $post_ID, 'streamium_video_quality_meta', true );
        $videotype = get_post_meta( $post_ID, 'streamium_video_videotype_meta', true );
        $duration  = get_post_meta( $post_ID, 'streamium_video_duration_meta', true );
        if($url && $quality && $videotype && $duration && $thumbnail){
	        echo '<span class="post-state button-primary">Roku Data Set</span>';
	    }else{
	    	echo '<span class="post-state button-secondary">No Roku Data</span>';
	    }

    }

}


// ONLY MOVIE CUSTOM TYPE POSTS
add_filter('manage_posts_columns', 'streamium_columns_main_slider', 1);
add_action('manage_posts_custom_column', 'streamium_columns_main_slider_content', 10, 2);
 
// CREATE TWO FUNCTIONS TO HANDLE THE COLUMN
function streamium_columns_main_slider($columns) { 
    
    $new = array();
  	foreach($columns as $key => $title) {
    	if ($key=='author') // Put the Thumbnail column before the Author column
      	$new['main_slider'] = 'Slider';
    	$new[$key] = $title;
  	}
  	return $new;
  	

}
function streamium_columns_main_slider_content($column_name, $post_ID) {

    if ($column_name == 'main_slider') {

        $main_slider = get_post_meta( $post_ID, 'streamium_preview_meta_box_checkbox', true );
        if(!empty($main_slider)){
	        echo '<span class="post-state button-primary">' . ucfirst($main_slider) . '</span>';
	    }else{
	    	echo '<span class="post-state button-secondary">No</span>';
	    }

    }

}

// ONLY MOVIE CUSTOM TYPE POSTS
add_filter('manage_posts_columns', 'streamium_columns_series_video_count', 1);
add_action('manage_posts_custom_column', 'streamium_columns_series_video_count_content', 10, 2);
 
// CREATE TWO FUNCTIONS TO HANDLE THE COLUMN
function streamium_columns_series_video_count($columns) { 
    
    $new = array();
  	foreach($columns as $key => $title) {
    	if ($key=='author') // Put the Thumbnail column before the Author column
      	$new['series_video_count'] = 'Code/Series';
    	$new[$key] = $title;
  	}
  	return $new;
  	

}
function streamium_columns_series_video_count_content($column_name, $post_ID) {

    if ($column_name == 'series_video_count') {

    	$movie_video_code   = get_post_meta($post_ID, 'streamium_video_code_meta', true);
    	$series_video_count = get_post_meta($post_ID, 'streamium_repeatable_series', true);

    	if(!empty($series_video_count)){
    		
    		echo '<span class="post-state button-primary">Series Seasons:' . count($series_video_count) . '</span>';
    	
    	}else{

    		if(empty($movie_video_code)){

    			echo '';

    		}else{

    			echo '<a class="post-state button-primary" href="https://s3bubble.com/app/?us-east-1#/wpplayer/' . $movie_video_code . '" target="_blank">' . $movie_video_code . '</a>';

    		}

    	}

    }

}

/**
 * Ajax remove series from list 
 *
 * @return bool
 * @author  @s3bubble
 */
function streamium_get_roku_data_code() {

	global $wpdb;

	// Get params
	$post_id = (int) $_REQUEST['post_id'];

	$code = get_post_meta($post_id, 'streamium_video_code_meta', true);
	if($code){

		wp_send_json(array(
            'status'  => true,
	    	'message' => __( 'Success', 'streamium' ),
	    	'code'    => $code
        ));

	}else{

		wp_send_json(array(
            'status'  => false,
	    	'message' => __( 'Failed to find the movie code please make sure you have set the main video code!', 'streamium' ),
        ));

	}

}

add_action( 'wp_ajax_streamium_get_roku_data_code', 'streamium_get_roku_data_code' );

/**
 *  DRM Proxy
 *
 * @return bool
 * @author  @s3bubble
 */
function drm_protected_video_streaming_proxy_token(){

	//error_log(print_r($_COOKIE['streamium_drm_token'],true));

	if(isset($_COOKIE['streamium_drm_token'])){

		$key = $_COOKIE['streamium_drm_token'];
		echo shell_exec("echo $key | xxd -r -p");

	}

	die(); // !IMPORTANT

}


/**
 *  DRM Proxy
 *
 * @return bool
 * @author  @s3bubble
 */
function get_video_drm($id, $position){

	$series = get_post_meta($id, 'streamium_repeatable_series', true);

	if(!empty($series)){ 

		$position = isset($position) ? $position : 0;
		$drm  = $series[$position]['episode_drm'];

	}else{

		$drm  = get_post_meta($id,'streamium_video_drm_meta', true);

	}

	return $drm;

}

/*
 * Get the DRM token
 * @author s3bubble
 * @params none
 */
add_action('wp_ajax_drm_protected_video_streaming_proxy_token', 'drm_protected_video_streaming_proxy_token' ); 
add_action('wp_ajax_nopriv_drm_protected_video_streaming_proxy_token', 'drm_protected_video_streaming_proxy_token' );