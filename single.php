<?php 

	$type = get_post_type( ); 
	
	if($type === "post"){
		
		get_header();

	}else{

		// CHECK FOR WOO ASSOCIATION::
		if ( class_exists( 'WooCommerce' ) ) {

			$productId = get_post_meta( get_the_ID(), 'streamium_premium_meta_box_woo_product', true );
			if(!empty($productId)){
				$current_user = wp_get_current_user();
				if(!wc_customer_bought_product($current_user->user_email,$current_user->ID, $productId)){
					$url = get_permalink( $productId );
					wp_redirect( $url );
					exit;
				}
			}

		}

		get_template_part( 'header', 'video' );

	}

?>

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		<?php

			if($type === "post"){

				get_template_part( 'templates/content', 'blog' );

			}

			if (in_array($type, streamium_global_meta())) {

				get_template_part( 'templates/content', 'single' );

			}

			if (is_user_logged_in()) : 
		   
			   update_post_meta($post->ID,'recently_watched',current_time('mysql')); 
			   update_post_meta($post->ID,'recently_watched_user_id',get_current_user_id());

			endif;

		?>

	<?php endwhile; else : ?>

	 	<p><?php _e( 'Sorry, no posts matched your criteria.', 'streamium' ); ?></p>

	<?php endif; ?>

<?php 

	if($type === "post"){
		
		get_footer();

	}else{
		
		get_template_part( 'footer', 'video' );
	
	}

?>
