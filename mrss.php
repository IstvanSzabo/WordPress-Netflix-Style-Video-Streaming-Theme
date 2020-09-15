<?php 

	/*
	 	Template Name: Roku Direct Publisher Mrss Template
	*/

	// CHECK FOR RESTRICTION:
	if(
		!empty(get_theme_mod('streamium_mrss_key'))
	){

		// CHECK:
		$key = $_GET['key'];
		if($key != get_theme_mod('streamium_mrss_key')){
			
			echo 'This url has restrictions enabled...';
			die();

		}

	}

	// globally loop through post types.
	$args = array(
        'posts_per_page' => -1,
        'post_type' => streamium_global_meta(),
        'post_status' => 'publish'
    );

    $loop = new WP_Query($args);

    // Latest build update
    $datetime = new DateTime();

    $genresList = [
    	"action",
		"adventure",
		"animals",
		"animated",
		"anime",
		"children",
		"comedy",
		"crime",
		"documentary",
		"drama",
		"educational",
		"fantasy",
		"faith",
		"food",
		"fashion",
		"gaming",
		"health",
		"history",
		"horror",
		"miniseries",
		"mystery",
		"nature",
		"news",
		"reality",
		"romance",
		"science",
		"science fiction",
		"sitcom",
		"special",
		"sports",
		"thriller",
		"technology"
	];

	$cats = [];
	foreach ($genresList as $key => $value) {
		$cats[] = [
			"name" => ucfirst($value),
		    "query" => strtolower($value),
		    "order" => "most_popular"
		];
	}

    $json = [
    	"providerName" => "S3Bubble AWS Media Streaming",
	    "lastUpdated" => $datetime->format('c'),
	    "language" => "en-US",
	    "movies" => [],
	    "series" => []
    ];

    if ($loop->have_posts()):

        while ($loop->have_posts()) : $loop->the_post();

        	// TOP LEVEL DATA:
        	$id               = get_the_ID();
        	$title            = substr( strip_tags( get_the_title() ), 0, 200);
        	$shortDescription = wp_trim_words( strip_tags( get_the_content() ), 20, '... ' );
        	$longDescription  = wp_trim_words( strip_tags( get_the_content() ) ) ;
        	$releaseDate      = get_the_time('c');
    	 	$thumbnail        = false;

    	 	// ROKU META DATA:
    	 	$videoUrl      = get_post_meta( $post->ID, 'streamium_video_url_meta', true );
    	 	$videoBif      = get_post_meta( $post->ID, 'streamium_video_bif_meta', true );
    	 	$videoQuality  = get_post_meta( $post->ID, 'streamium_video_quality_meta', true );
    	 	$VideoType     = get_post_meta( $post->ID, 'streamium_video_videotype_meta', true );
    	 	$videoDuration = get_post_meta( $post->ID, 'streamium_video_duration_meta', true );

    	 	// EXTRA THUMBNAILS:
    	 	$thumbnail = get_the_post_thumbnail_url( $post->ID,'streamium-roku-thumbnail' ); 

            if (class_exists('MultiPostThumbnails')) {                              
                
                if (MultiPostThumbnails::has_post_thumbnail( get_post_type( get_the_ID() ), 'roku-thumbnail-image', get_the_ID())) { 

                    $thumbnail_id = MultiPostThumbnails::get_post_thumbnail_id( get_post_type( get_the_ID() ), 'roku-thumbnail-image', get_the_ID() );  
                    $thumbnail = wp_get_attachment_image_url( $thumbnail_id, 'streamium-roku-thumbnail' ); 

                }                             
              
            }; 

        	$taxonomy_names = get_post_taxonomies();
        	$categories = get_the_terms( $id, $taxonomy_names[1] );
        	$genres = [];
        	$cats = [];
        	if ($categories) {
	    		foreach ($categories as $key => $value) {
	    			if (in_array(strtolower($value->name), $genresList)) {
			    		$genres[] = strtolower($value->name);
			    	}
			    	$cats[] = strtolower($value->name);
		    	}
	    	}    	

			// CHECK IF CUSTOM POST IS A SERIES:
			$episodes = get_post_meta(get_the_ID(), 'streamium_repeatable_series' , true);

			if(!empty($episodes)){

				$seasonEpisodes = [];
				foreach (streamium_sort_episodes($episodes) as $key => $value) {

		        	$episodeObject = [];
		        	foreach ($value as $key2 => $value2) {

		        		$captions = [];
						if(!empty($value2['episode_captions'])){
							$captions = unserialize($getCaptions);
						}

		        		$bifs = [];
						if(!empty($value2['episode_bif'])){

							$bifs = [
								"url"     => $value2['episode_bif'],
			  					"quality" => "HD"
							];

						}

			        	$videoData2 = [
						  	"dateAdded" => get_the_time('c'),
						  	"videos" => [
								[
								  "url"       => $value2['episode_url'],
								  "quality"   => $value2['episode_quality'],
								  "videoType" => $value2['episode_video_type']
								]
						  	],
						  	"duration"       => (int)$value2['episode_duration'],
						  	"captions"       => $captions,
					  		"trickPlayFiles" => $bifs
						];

			        	if($value2['episode_thumb'] && $value2['episode_url'] && $value2['episode_quality'] && $value2['episode_video_type'] && $value2['episode_duration']){
 
			        		$episodeObject[] = [
							  	"id"               => (string) $id . $value[0]['episode_season'] . $value[0]['episode_position'] . $key2,
							  	"title"            => $value2['episode_title'],
							  	"content"          => $videoData2,
							  	"thumbnail"        => $value2['episode_thumb'],
							  	"episodeNumber"    => (int) ($key2+1),
							  	"releaseDate"      => get_the_date('Y-m-d'),
							  	"shortDescription" => $value2['episode_description'],
							  	"longDescription"  => $value2['episode_description']
							];

						}

		        	}

					$seasonEpisodes[] = array(
						'seasonNumber' => (int) $key, 
						'episodes'     => $episodeObject, 
						"thumbnail"    => $thumbnail,
					);

				}

				$data = [
				  	"id"               => (string) $id,
				  	"title"            => $title,
				  	"seasons"          => $seasonEpisodes,
				  	"genres"           => $genres, 
				    "tags"             => $cats, 
				  	"thumbnail"        => $thumbnail,
				  	"releaseDate"      => get_the_date('Y-m-d'),
				    "shortDescription" => $shortDescription,
				    "longDescription"  => $longDescription
				];

				// ONLY RETURN IF IT HAS EPISODES:
				if(count($episodeObject) > 0){
					
					$json['series'][] = $data;

				}
	
			}else{

				$captions = [];
				$getCaptions = get_post_meta( $post->ID, 'streamium_video_captions_meta', true );
				if($getCaptions){
					$captions = unserialize($getCaptions);
				}

				$bifs = [];
				if(!empty($videoBif)){

					$bifs = [
						"url" => $videoBif,
	  					"quality" => "HD"
					];

				}

				// Not a series
				$data = [
	        		"id"    => (string) $id,
				    "title" => $title,
				    "content" => [
					  	"dateAdded" => $releaseDate,
					  	"videos" => [
							[
							  "url"       => $videoUrl,
							  "quality"   => $videoQuality,
							  "videoType" => $VideoType
							]
					  	],
					  	"duration"       => (int)$videoDuration,
					  	"captions"       => $captions,
					  	"trickPlayFiles" => $bifs
					],
				    "genres"           => $genres, 
				    "tags"             => $cats, 
				    "thumbnail"        => $thumbnail,
				    "releaseDate"      => $releaseDate,
				    "shortDescription" => $shortDescription,
				    "longDescription"  => $longDescription
	        	];

				// ONLY RUN IF THE CORRECT IMAGE EXISTS:
				if($thumbnail && $videoUrl && $videoQuality && $VideoType && $videoDuration){

					$json['movies'][] = $data;

				}

			}

        endwhile;
    endif;
	
	header('Content-Type: application/json');
	echo json_encode($json);