<?php
	
	// Needed for DRM
	$position = isset($_GET['v']) ? $_GET['v'] : 0;
   	setcookie("streamium_drm_token", get_video_drm(get_the_ID(), $position), time()+3600, COOKIEPATH, COOKIE_DOMAIN);

?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width" />
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">

	<?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>