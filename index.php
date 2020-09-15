<?php 

// REDIRECT USERS TO JOIN PAGE::
if ( get_theme_mod( 'streamium_enable_splash_join_redirect', false )) {

	if ( !is_user_logged_in() ) {

		$join = streamium_get_template_url('templates/join-template.php');
		wp_redirect( $join );
	  	exit;

	}

}

add_filter( 'body_class', function( $classes ) {
    return array_merge( $classes, array( 'browse' ) );
} );

get_header(); ?>

	<?php 
		if ( get_theme_mod( 'streamium_enable_loader' )) {
			
			get_template_part('templates/content', 'loader');

		} 
	?>

	<main class="cd-main-content">

		<?php get_template_part('templates/content', 'slider'); ?>

		<div id="recently-watched"></div>

		<div id="custom-watched"></div>

		<div id="home-watched"></div>
		
	</main><!--/.main content-->

<?php get_footer(); ?>