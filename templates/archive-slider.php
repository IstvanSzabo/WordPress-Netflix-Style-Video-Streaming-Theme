<section class="streamium-slider">
	<?php 
			
		$query = $wp_query->get_queried_object(); 
		$tax = isset($query->taxonomies[1]) ? $query->taxonomies[1] : "";
		$rewrite = (get_theme_mod( 'streamium_section_input_taxonomy_' . $tax )) ? get_theme_mod( 'streamium_section_input_taxonomy_' . $tax ) : $tax; 

		$args = array(
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'post_type' => $query->name,
			'meta_key' => 'streamium_preview_meta_box_checkbox',
			'meta_value' => 'yes'
		);
		  
		$loop = new WP_Query( $args ); 
		$slider_count = 0;
		if($loop->have_posts()):
			while ( $loop->have_posts() ) : $loop->the_post();
			    
			    $image   = wp_get_attachment_image_url( get_post_thumbnail_id(), 'streamium-home-slider' );

			    // Allow a extra image to be added
	            if (class_exists('MultiPostThumbnails')) {                              
	                
	                if (MultiPostThumbnails::has_post_thumbnail( get_post_type( get_the_ID() ), 'large-landscape-image', get_the_ID())) { 

	                    $image_id = MultiPostThumbnails::get_post_thumbnail_id( get_post_type( get_the_ID() ), 'large-landscape-image', get_the_ID() );  
	                    $image = wp_get_attachment_image_url( $image_id,'streamium-video-tile-large-expanded' ); 

	                }                            
	             
	            }; // end if MultiPostThumbnails 

				$title   = wp_trim_words( get_the_title(), 10, '... ' );

				$nonce = wp_create_nonce( 'streamium_likes_nonce' );
		        
		        $link = admin_url('admin-ajax.php?action=streamium_likes&post_id='.get_the_ID().'&nonce='.$nonce);

		        $content = streamium_trim_words( strip_tags(get_the_content()), 15 );

		        $preview_video = get_post_meta( get_the_ID(), 'streamium_preview_video_meta', true );
				
				$preview_video_type = get_post_meta( get_the_ID(), 'streamium_preview_type_meta', true );
            

		?>
		<?php if ( ! empty( $preview_video ) && ! empty( $preview_video_type ) && (!wp_is_mobile()) && ($slider_count < 1)) : ?>

			<div class="streamium-slider-div">
				
				<video id="streamium-featured-background-<?php echo get_the_ID(); ?>" class="video-js vjs-default-skin vjs-streamium vjs-streamium-background vjs-fluid" preload="auto" poster="https://s3.amazonaws.com/s3bubble-cdn/theme-images/streamium-video-blank.png" playsinline="true" data-setup='{"autoplay":true,"muted":true,"loop":true}'>
 					<source src="<?php echo $preview_video; ?>" type="<?php echo $preview_video_type; ?>" />
 					<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
				</video>

				<article class="content-overlay">
					<div class="content-overlay-grad"></div>
					<div class="container-fluid rel">
						<div class="row rel">
							<div class="col-sm-4 col-xs-6 rel">
								<div class="synopis-outer">
									<div class="synopis-middle">
										<div class="synopis-inner">
											
											<h2><?php echo (isset($title) ? $title : __( 'No Title', 'streamium' )); ?></h2>

											<div class="synopis content hidden-xs">

												<?php echo $content; ?>

											</div>

											<div class="synopis-premium-meta streamium-reviews-content-btns">
												
												<a class="btn streamium-tile-btns btn-md center-block hidden-xs" href="<?php the_permalink(); ?>"><i class="fa fa-play" aria-hidden="true"></i> <?php _e( 'Play', 'streamium' ); ?></a>

												<a class="btn streamium-tile-btns btn-xs center-block hidden-sm hidden-md hidden-lg" href="<?php the_permalink(); ?>"><i class="fa fa-play" aria-hidden="true"></i> <?php _e( 'Play', 'streamium' ); ?></a>

         										<a class="btn streamium-tile-btns btn-md center-block hidden-xs show-more-content btn" data-id="<?php echo get_the_ID(); ?>" data-nonce="<?php echo $nonce; ?>"><i class="fa fa-info" aria-hidden="true"></i> <?php _e( 'More Info', 'streamium' ); ?></a>

         										<a class="btn streamium-tile-btns btn-xs center-block  hidden-sm hidden-md hidden-lg show-more-content btn" data-id="<?php echo get_the_ID(); ?>" data-nonce="<?php echo $nonce; ?>"><i class="fa fa-info" aria-hidden="true"></i> <?php _e( 'More Info', 'streamium' ); ?></a>

											</div>
											
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-8 col-xs-6 rel">

								<a class="play-icon-wrap visible-sm" href="<?php the_permalink(); ?>">
									<div class="play-icon-wrap-rel">
										<div class="play-icon-wrap-rel-ring"></div>
										<span class="play-icon-wrap-rel-play">
											<i class="fa fa-play fa-3x" aria-hidden="true"></i>
							        	</span>
						        	</div>
					        	</a>
					        	
					        	<a class="streamium-unmute hidden-xs" href="#" data-pid="streamium-featured-background-<?php echo get_the_ID(); ?>"><i class="fa fa-volume-off" aria-hidden="true"></i></a>

							</div>
						</div>
					</div>
				</article><!--/.content-overlay-->

			</div>

		<?php else : ?>

			<div class="streamium-slider-div" style="background-image: url(<?php echo esc_url($image); ?>);">

				<article class="content-overlay">
					<div class="content-overlay-grad"></div>
					<div class="container-fluid rel">
						<div class="row rel">
							<div class="col-sm-4 col-xs-5 rel">
								<div class="synopis-outer">
									<div class="synopis-middle">
										<div class="synopis-inner">
											
											<h2><?php echo (isset($title) ? $title : __( 'No Title', 'streamium' )); ?></h2>

											<div class="synopis content hidden-xs">

												<?php echo $content; ?>

											</div>

											<div class="synopis-premium-meta streamium-reviews-content-btns">
												
												<a class="btn streamium-tile-btns btn-md center-block hidden-xs" href="<?php the_permalink(); ?>"><i class="fa fa-play" aria-hidden="true"></i> <?php _e( 'Play', 'streamium' ); ?></a>

												<a class="btn streamium-tile-btns btn-xs center-block hidden-sm hidden-md hidden-lg" href="<?php the_permalink(); ?>"><i class="fa fa-play" aria-hidden="true"></i> <?php _e( 'Play', 'streamium' ); ?></a>

         										<a class="btn streamium-tile-btns btn-md center-block hidden-xs show-more-content btn" data-id="<?php echo get_the_ID(); ?>" data-nonce="<?php echo $nonce; ?>"><i class="fa fa-info" aria-hidden="true"></i> <?php _e( 'More Info', 'streamium' ); ?></a>

         										<a class="btn streamium-tile-btns btn-xs center-block  hidden-sm hidden-md hidden-lg show-more-content btn" data-id="<?php echo get_the_ID(); ?>" data-nonce="<?php echo $nonce; ?>"><i class="fa fa-info" aria-hidden="true"></i> <?php _e( 'More Info', 'streamium' ); ?></a>

											</div>

										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-8 col-xs-7 rel">

								<a class="play-icon-wrap" href="<?php the_permalink(); ?>">
									<div class="play-icon-wrap-rel">
										<div class="play-icon-wrap-rel-ring"></div>
										<span class="play-icon-wrap-rel-play">
											<i class="fa fa-play fa-3x" aria-hidden="true"></i>
							        	</span>
						        	</div>
					        	</a>

							</div>
						</div>
					</div>
				</article><!--/.content-overlay-->

			</div>

		<?php endif; ?>

		<?php
		    $slider_count++; 
			endwhile; 
		else: 
		?>
		<div class="streamium-slider-div">
			<div class="slider-no-content">
				<h2><?php _e( 'S3Bubble Media Streaming', 'streamium' ); ?></h2>
				<p><?php _e( 'To display a image here go to your custom post and look for the metabox (Main Slider Video) and check it.', 'streamium' ); ?></p>
			</div><!--/.content-overlay-->
		</div>
		<?php
		endif;
		wp_reset_query(); 

	?>
</section><!--/.streamium-slider-->