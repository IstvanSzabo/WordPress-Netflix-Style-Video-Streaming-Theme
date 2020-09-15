<?php

/**
 * Update the Wordpress api url prefix
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_api_add_series_init() {

	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	foreach ( $post_types as $post_type ) {

		$post_type_name     = $post_type->name;
		$show_in_rest       = ( isset( $post_type->show_in_rest ) && $post_type->show_in_rest ) ? true : false;

		// CHECK IF SHOW REST IS SET ON CREATION:
		if ( $show_in_rest) {

			if ( function_exists( 'register_rest_field' ) ) {
				register_rest_field( $post_type_name,
					'series',
					array(
						'get_callback' => 'streamium_api_add_series_get_field',
						'schema'       => null,
					)
				);
			} elseif ( function_exists( 'register_api_field' ) ) {
				register_api_field( $post_type_name,
					'series',
					array(
						'get_callback' => 'streamium_api_add_series_get_field',
						'schema'       => null,
					)
				);
			}
		}
	}
}

add_action( 'init', 'streamium_api_add_series_init', 12 );


/**
 * Update the Wordpress api url prefix
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_api_add_series_get_field( $object, $field_name, $request ) {

	//error_log(json_encode(is_single()));

	// PARAMS:
	$id    = $object['id'];
	$title = substr( strip_tags(get_the_title($id)), 0, 200);
	$shortDescription = wp_trim_words( strip_tags(get_the_content($id)), $num_words = 20, $more = '... ' );
	$longDescription  = strip_tags(get_the_content($id));
	$releaseDate      = get_the_time('c');
 	$thumbnail        = false;

	// CHECKS:
	if (empty($id)) {
		return null;
	}

	// Check for series
	$episodes = get_post_meta($id, 'streamium_repeatable_series' , true);

	if(!empty($episodes)){

		// Allow a extra image to be added
        if (class_exists('MultiPostThumbnails')) {                              
            
            if (MultiPostThumbnails::has_post_thumbnail( get_post_type( $id ), 'roku-thumbnail-image', $id)) { 

                $thumbnail_id = MultiPostThumbnails::get_post_thumbnail_id( get_post_type( $id ), 'roku-thumbnail-image', $id );  
                $thumbnail = wp_get_attachment_image_url( $thumbnail_id,'streamium-roku-thumbnail' ); 

            }                             
         
        }; // end if MultiPostThumbnails 

		// Order the list
		$positions = array();
		foreach ($episodes as $key => $row){
		    $positions[$key] = $row['episode_position'];
		}
		array_multisort($positions, SORT_ASC, $episodes);

		// Sort the seasons
		$result = array();
		foreach ($episodes as $v) {
		    $seasons = $v['episode_season'];
		    if (!isset($result[$seasons])) $result[$seasons] = array();
		    $v['episode_link'] = get_permalink($id);
		    $result[$seasons][] = $v;
		}

		$seasonEpisodes = [];
		foreach ($result as $key => $value) {

        	$episodeObject = [];
        	foreach ($value as $key2 => $value2) {

	        	$videoData2 = [
				  	"date_added" => get_the_time('c'),
				  	"videos" => [
						[
						  "src"       => $value2['episode_url'],
						  "type"      => $value2['episode_type'],
						  "bif"       => $value2['episode_bif'],
						  "is_360"    => $value2['episode_360'],
						  "quality"   => $value2['episode_quality'],
						  "video_type"=> $value2['episode_video_type']
						]
				  	],
				  	"duration" => (int)$value2['episode_duration']
				];

	        	if($value2['episode_thumb'] && $value2['episode_url'] && $value2['episode_quality'] && $value2['episode_video_type'] && $value2['episode_duration']){

	        		$episodeObject[] = [
					  	"id"               => (string) $id . $value[0]['episode_season'] . $value[0]['episode_position'] . $key2,
					  	"episode_number"    => (int) ($key2+1),
					  	"title"            => $value2['episode_title'],
					  	"content"          => $videoData2,
					  	"thumbnail"        => $value2['episode_thumb'],
					  	"release_date"      => get_the_date('Y-m-d'),
					  	"short_description" => $value2['episode_description'],
					  	"long_description"  => $value2['episode_description']
					];

				}

        	}

			$seasonEpisodes[] = array(
				'season_number' => (int) $key, 
				'episodes' => $episodeObject, 
				"thumbnail" => $thumbnail,
			);

		}

		$series = [
		  	"id" => (string) $id,
		  	"title" => $title,
		  	"thumbnail" => $thumbnail,
		  	"releaseDate" => get_the_date('Y-m-d'),
		    "shortDescription" => $shortDescription,
		    "longDescription" => $longDescription,
		  	"seasons" => $seasonEpisodes
		];

		return apply_filters( 'streamium_api_series', $series, $id );

	}
	
}

/**
 * Update the Wordpress api url prefix
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_api_add_media_init() {

	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	foreach ( $post_types as $post_type ) {

		$post_type_name     = $post_type->name;
		$show_in_rest       = ( isset( $post_type->show_in_rest ) && $post_type->show_in_rest ) ? true : false;

		// CHECK IF SHOW REST IS SET ON CREATION:
		if ( $show_in_rest) {

			if ( function_exists( 'register_rest_field' ) ) {
				register_rest_field( $post_type_name,
					'media',
					array(
						'get_callback' => 'streamium_api_add_media_get_field',
						'schema'       => null,
					)
				);
			} elseif ( function_exists( 'register_api_field' ) ) {
				register_api_field( $post_type_name,
					'media',
					array(
						'get_callback' => 'streamium_api_add_media_get_field',
						'schema'       => null,
					)
				);
			}
		}
	}
}

add_action( 'init', 'streamium_api_add_media_init', 12 );


/**
 * Update the Wordpress api url prefix
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_api_add_media_get_field( $object, $field_name, $request ) {

	//error_log(json_encode(is_single()));

	// PARAMS:
	$postId = $object['id'];

	// CHECKS:
	if (empty($postId)) {
		return null;
	}

	// BUILD:
	$media['src']      = get_post_meta( $postId, 'streamium_video_url_meta', true );
	$media['type']     = get_post_meta( $postId, 'streamium_video_type_meta', true );
	$media['bif']      = get_post_meta( $postId, 'streamium_video_bif_meta', true );
	$media['duration'] = get_post_meta( $postId, 'streamium_video_duration_meta', true );
	$media['is_360']   = get_post_meta( $postId, 'streamium_video_360_meta', true );
 
	$media['captions'] = [];
	$getCaptions = get_post_meta( $postId, 'streamium_video_captions_meta', true );
	if($getCaptions){
		$media['captions'] = unserialize($getCaptions);
	}

	return apply_filters( 'streamium_api_media', $media, $postId );

}

/**
 * Update the Wordpress api url prefix
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_api_add_watched_init() {

	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	foreach ( $post_types as $post_type ) {

		$post_type_name     = $post_type->name;
		$show_in_rest       = ( isset( $post_type->show_in_rest ) && $post_type->show_in_rest ) ? true : false;

		// CHECK IF SHOW REST IS SET ON CREATION:
		if ( $show_in_rest) {

			if ( function_exists( 'register_rest_field' ) ) {
				register_rest_field( $post_type_name,
					'watched',
					array(
						'get_callback' => 'streamium_api_add_watched_get_field',
						'schema'       => null,
					)
				);
			} elseif ( function_exists( 'register_api_field' ) ) {
				register_api_field( $post_type_name,
					'watched',
					array(
						'get_callback' => 'streamium_api_add_watched_get_field',
						'schema'       => null,
					)
				);
			}
		}
	}
}

add_action( 'init', 'streamium_api_add_watched_init', 12 );


/**
 * Update the Wordpress api url prefix
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_api_add_watched_get_field( $object, $field_name, $request ) {

	//error_log(json_encode(is_single()));

	// PARAMS:
	$postId = $object['id'];

	// CHECKS:
	if (empty($postId)) {
		return null;
	}

	$progressBar = false;
    $progressBar = (int)get_post_meta( get_the_ID(), 'user_' . get_current_user_id(), true );

	return apply_filters( 'streamium_api_watched', $progressBar, $postId );

}

/**
 * Update the Wordpress api url prefix
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_api_add_reviews_init() {

	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	foreach ( $post_types as $post_type ) {

		$post_type_name     = $post_type->name;
		$show_in_rest       = ( isset( $post_type->show_in_rest ) && $post_type->show_in_rest ) ? true : false;

		// CHECK IF SHOW REST IS SET ON CREATION:
		if ( $show_in_rest) {

			if ( function_exists( 'register_rest_field' ) ) {
				register_rest_field( $post_type_name,
					'reviews',
					array(
						'get_callback' => 'streamium_api_add_reviews_get_field',
						'schema'       => null,
					)
				);
			} elseif ( function_exists( 'register_api_field' ) ) {
				register_api_field( $post_type_name,
					'reviews',
					array(
						'get_callback' => 'streamium_api_add_reviews_get_field',
						'schema'       => null,
					)
				);
			}
		}
	}
}

add_action( 'init', 'streamium_api_add_reviews_init', 12 );


/**
 * Update the Wordpress api url prefix
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_api_add_reviews_get_field( $object, $field_name, $request ) {

	//error_log(json_encode(is_single()));

	// PARAMS:
	$postId = $object['id'];

	// CHECKS:
	if (empty($postId)) {
		return null;
	}

	$reviews = get_streamium_likes($postId);

	return apply_filters( 'streamium_api_reviews', $reviews, $postId );

}

/**
 * Update the Wordpress api url prefix
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_api_add_extra_meta_init() {

	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	foreach ( $post_types as $post_type ) {

		$post_type_name     = $post_type->name;
		$show_in_rest       = ( isset( $post_type->show_in_rest ) && $post_type->show_in_rest ) ? true : false;

		// CHECK IF SHOW REST IS SET ON CREATION:
		if ( $show_in_rest) {

			if ( function_exists( 'register_rest_field' ) ) {
				register_rest_field( $post_type_name,
					'extra_meta',
					array(
						'get_callback' => 'streamium_api_add_extra_meta_get_field',
						'schema'       => null,
					)
				);
			} elseif ( function_exists( 'register_api_field' ) ) {
				register_api_field( $post_type_name,
					'extra_meta',
					array(
						'get_callback' => 'streamium_api_add_extra_meta_get_field',
						'schema'       => null,
					)
				);
			}
		}
	}
}

add_action( 'init', 'streamium_api_add_extra_meta_init', 12 );


/**
 * Update the Wordpress api url prefix
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_api_add_extra_meta_get_field( $object, $field_name, $request ) {

	//error_log(json_encode(is_single()));

	// PARAMS:
	$postId = $object['id'];

	// CHECKS:
	if (empty($postId)) {
		return null;
	}

	$extraMeta = "";
    $streamium_extra_meta = get_post_meta( $postId, 'streamium_extra_meta', true );
    if ( ! empty( $streamium_extra_meta ) ) {
        $extraMeta = '<h5>' . $streamium_extra_meta . '</h5>';
    }

	return apply_filters( 'streamium_api_extra_meta', $extraMeta, $postId );

}


/**
 * Update the Wordpress api url prefix
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_api_thumbnails_init() {

	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	foreach ( $post_types as $post_type ) {

		$post_type_name     = $post_type->name;
		$show_in_rest       = ( isset( $post_type->show_in_rest ) && $post_type->show_in_rest ) ? true : false;
		$supports_thumbnail = post_type_supports( $post_type_name, 'thumbnail' );

		// CHECK IF SHOW REST IS SET ON CREATION:
		if ( $show_in_rest && $supports_thumbnail ) {

			if ( function_exists( 'register_rest_field' ) ) {
				register_rest_field( $post_type_name,
					'images',
					array(
						'get_callback' => 'streamium_api_thumbnails_get_field',
						'schema'       => null,
					)
				);
			} elseif ( function_exists( 'register_api_field' ) ) {
				register_api_field( $post_type_name,
					'images',
					array(
						'get_callback' => 'streamium_api_thumbnails_get_field',
						'schema'       => null,
					)
				);
			}
		}
	}

}

add_action( 'init', 'streamium_api_thumbnails_init', 12 );

/**
 * Update the Wordpress api url prefix
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_api_thumbnails_get_field( $object, $field_name, $request ) {

	// PARAMS:
	$postId = $object['id'];

	// DEFAULTS:
	$thumbnails = [
		'tile' => [
			'url' => 'https://via.placeholder.com/350x150'
		],
		'expanded' => [
			'url' => 'https://via.placeholder.com/350x150'
		],
		'landscape' => [
			'url' => 'https://via.placeholder.com/350x150'
		],
		'roku' => [
			'url' => 'https://via.placeholder.com/350x150'
		]
	];

	if (has_post_thumbnail( $postId ) ){
		
		$tile = get_the_post_thumbnail_url( $postId, 'streamium-video-tile' );
		$thumbnails['tile'] = [
			'url' => esc_url($tile)
		];

		$thumbnails['expanded'] = [
			'url' => esc_url($tile)
		];

	}

	if (class_exists('MultiPostThumbnails')) {                              
                    
        if (MultiPostThumbnails::has_post_thumbnail(get_post_type( $postId ), 'tile-expanded-image')) { 
            
            $expanded = wp_get_attachment_image_url(
				MultiPostThumbnails::get_post_thumbnail_id( 
					get_post_type( 
						$postId 
					), 
					'tile-expanded-image', 
					$postId 
				)
				,'streamium-video-tile-expanded'
			);

			$thumbnails['expanded'] = [
				'url' => esc_url($expanded)
			];

        }

        if (MultiPostThumbnails::has_post_thumbnail(get_post_type( $postId ), 'large-landscape-image')) { 
            
            $landscape = wp_get_attachment_image_url(
				MultiPostThumbnails::get_post_thumbnail_id( 
					get_post_type( 
						$postId 
					), 
					'large-landscape-image', 
					$postId 
				)
				,'streamium-video-tile-large-expanded'
			);

			$thumbnails['landscape'] = [
				'url' => esc_url($landscape)
			];

        }  

        if (MultiPostThumbnails::has_post_thumbnail(get_post_type( $postId ), 'roku-thumbnail-image')) { 
            
            $roku = wp_get_attachment_image_url(
				MultiPostThumbnails::get_post_thumbnail_id( 
					get_post_type( 
						$postId 
					), 
					'roku-thumbnail-image', 
					$postId 
				)
				,'streamium-roku-thumbnail'
			);

			$thumbnails['roku'] = [
				'url' => esc_url($roku)
			];

        }                        
     
    };

	return apply_filters( 'streamium_api_thumbnails', $thumbnails, $postId );

}

/**
 * Resume video time ajax
 *
 * @return bool
 * @author  @s3bubble
 */
function mrss_generate_key() {

   // Generate a random salt
    $salt = base_convert(bin2hex(random_bytes(64)), 16, 36);

    // If an error occurred, then fall back to the previous method
    if ($salt === FALSE)
    {
        $salt = hash('sha256', time() . mt_rand());
    }

    $new_key = substr($salt, 0, 40);
    
	echo json_encode(
    	array(
    		'status' => true,
    		'message' => 'Success',
    		'key' => $new_key,
    	)
    );      

    die(); 

}

add_action(
	"wp_ajax_mrss_generate_key", 
	"mrss_generate_key" 
);