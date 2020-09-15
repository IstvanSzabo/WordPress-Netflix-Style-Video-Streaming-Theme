<?php
	
	// Redirect logged in users to browse page exclude admin
	if( current_user_can('editor') || current_user_can('administrator') ) { 


	}else{

		if ( is_user_logged_in() ) {

			$browse_url = streamium_get_template_url('templates/browse-template.php');
	        wp_redirect($browse_url);
	        exit;

	    }

	}

	/*
 		Template Name: Join Template
 	*/

	get_template_part( 'header', 'blank' ); 

    // Start the loop.
    while ( have_posts() ) : the_post();

		$image       = wp_get_attachment_image_url( get_post_thumbnail_id(), 'content_tile_full_width_landscape' );
		
		$shopPage    = get_theme_mod( 'streamium_join_page_section_shop_url' );
		
		$accountPage = get_theme_mod( 'streamium_join_page_section_profile_url' );

		if ( class_exists( 'WooCommerce' ) ) {

			if(empty($shopPage)){

				$shopPage    = get_permalink( wc_get_page_id( 'shop' ) );

			}

			if(empty($accountPage)){

				$accountPage = get_permalink( wc_get_page_id( 'myaccount' ) );

			}	

		}
		
?>
	
		<section class="full-hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5),rgba(0, 0, 0, 0.5)), url(<?php echo esc_url( $image ); ?>)">
			<a class="btn btn-primary home-join-signin" href="<?php echo $accountPage; ?>"><?php _e( 'SIGN IN', 'streamium' ); ?></a>
	        <div class="full-hero-inner">
	            <h1><?php the_title(); ?></h1>
	            <p><?php the_excerpt(); ?></p>
	            <a href="<?php echo $shopPage; ?>" class="btn btn-primary btn-lg"><?php _e( 'TRY IT NOW', 'streamium' ); ?></a>
	        </div>
	    </section>

	    <main class="cd-main-content">
		
			<div class="container-fluid">
				<div class="row">

					<div class="col-sm-12 col-xs-12">
						<?php the_content(); ?>
					</div>

				</div>
			</div>

		</main><!--/.main content-->
	    

<?php 
	endwhile; 
?>

<?php get_footer(); ?>