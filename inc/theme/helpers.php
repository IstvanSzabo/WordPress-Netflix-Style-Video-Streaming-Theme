<?php

/**
 * Dummy data admin notice
 *
 * @return null
 * @author  @sameast
 */
function streamium_dummy_data_notice() {
   
        if ( ! get_option('dismissed-streamium_dummy_data', FALSE ) ) { ?>
            <div class="updated notice notice-streamium-dummy-data is-dismissible" data-notice="streamium_dummy_data">
                <p>NEED HELP? Why not get started with some dummy data download below (Right Click And Save Link As) if needed. You can import the xml file in the tools menu -> import then Run Importer.</p> 
                <p>
                    <a class="button button-primary" href="https://s3b-assets-bucket.s3.amazonaws.com/animals.WordPress.2019-12-09.xml" target="_blank">Dummy Data</a>
                </p>
            </div>
        <?php }
    
}

add_action( 'admin_notices', 'streamium_dummy_data_notice' );

/**
 * AJAX Dummy data admin notice
 *
 * @return null
 * @author  @sameast
 */
function ajax_notice_streamium_dummy_data() {

    $type = $_POST['type'];
    update_option( 'dismissed-' . $type, TRUE );

}

add_action( 'wp_ajax_dismissed_notice_streamium_dummy_data', 'ajax_notice_streamium_dummy_data' );
  

/**
 * Changes the tile count
 *
 * @return null
 * @author  @sameast
 */
if ( ! function_exists ( 's3bubble_tile_count' ) ) {
    
    function s3bubble_tile_count() {
    
        return (int) get_theme_mod( 'streamium_tile_count', 6 );
    
    }

}

/**
 * Adds the main menu
 *
 * @return null
 * @author  @sameast
 */
function streamium_register_menu() {
    
    register_nav_menu('streamium-header-menu',__( 'Header Menu', 'streamium' ));

}

add_action( 'init', 'streamium_register_menu' );

/**
 * Fix for the main menu
 *
 * @return null
 * @author  @sameast
 */
function streamium_remove_ul( $menu ){
    
    return preg_replace( array( '#^<ul[^>]*>#', '#</ul>$#' ), '', $menu );

}

add_filter( 'wp_nav_menu', 'streamium_remove_ul' );

/**
 * Webview only style for native app
 *
 * @return null
 * @author  @sameast
 */
if ( ! function_exists ( 'streamium_check_webview' ) ) {
    
    function streamium_check_webview() {

        if (isset($_GET['webview']) || isset($_COOKIE["webview"])) {
            
            setcookie('webview', true);
            
            wp_enqueue_style('streamium-webview', get_template_directory_uri() . '/production/css/webview.min.css', array(), s3bubble_cache_version());

        }else{
            
            unset($_COOKIE['webview']);

        }

    }

    //add_action( 'init', 'streamium_check_webview' );

}

/*
* Recommended plugins managment and notifications
* @author sameast
* @none
*/
require_once get_template_directory() . '/inc/recommended-plugins/class-tgm-plugin-activation.php';
add_action( 'tgmpa_register', 'sb_register_required_plugins' );

function sb_register_required_plugins() {
    
    $plugins = array(
        array(
            'name'      => 'Easy Theme and Plugin Upgrades',
            'slug'      => 'easy-theme-and-plugin-upgrades',
            'required'  => false,
        ),
        array(
            'name'      => 'WP Extended Search',
            'slug'      => 'wp-extended-search',
            'required'  => false,
  	    ),
        array(
            'name'      => 'Post Types Order',
            'slug'      => 'post-types-order',
            'required'  => false,
        ),
        array(
            'name'      => 'Category Order and Taxonomy Terms Order',
            'slug'      => 'taxonomy-terms-order',
            'required'  => false,
        ),
        array(
            'name'      => 'Force Regenerate Thumbnails',
            'slug'      => 'force-regenerate-thumbnails',
            'required'  => false,
        ),
        array(
            'name'      => 'WooCommerce',
            'slug'      => 'woocommerce',
            'required'  => false,
        )
    );

    $config = array(
      'id'           => 's3bubble',
      'default_path' => '',
      'menu'         => 'tgmpa-install-plugins',
      'parent_slug'  => 'themes.php',
      'capability'   => 'edit_theme_options',
      'has_notices'  => true,
      'dismissable'  => true,
      'dismiss_msg'  => '',
      'is_automatic' => false
    );

    tgmpa( $plugins, $config );

}

/**
 * Make sure the self hosted plugin is not installed
 */
function streamium_check_for_active_plugins() {

  function streamium_check_plugin_isnot_active_notice__error() {

      $class = 'notice notice-error notice-demo-data';
      $message = __( '!IMPORTANT you have the S3Bubble self hosted plugin installed this is not needed with this theme all functionality is built in please remove the S3Bubble AWS Self Hosted Plugin', 'streamium' );

      printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ));
  }
  if ( is_plugin_active( 's3bubble-amazon-web-services-oembed-media-streaming-support/s3bubble-oembed.php' ) ) {
    add_action( 'admin_notices', 'streamium_check_plugin_isnot_active_notice__error' );
  }

}
add_action( 'admin_init', 'streamium_check_for_active_plugins' );

/**
 * Fix to flush urls
 *
 * @return null
 * @author  @sameast
 */
if ( ! function_exists ( 'streamium_flush_rewrite_rules' ) ) {
    
    function streamium_flush_rewrite_rules(){
    
        flush_rewrite_rules();
    
    }
    
    add_action( 'admin_init', 'streamium_flush_rewrite_rules' );

}

/**
 * Is mobile check for theme styling
 *
 * @return bool
 * @author  @sameast
 */
function streamium_get_device($type){

	// include classes
 	if( wp_is_mobile() ){
	
     	$device = array('count' => 2);
	
    }else{
	
    	$device = array('count' => 6);
	
    }
	
    return $device[$type];

}

/**
 * appends the stramium reviews query for search
 *
 * @return null
 * @author  @sameast
 */
function streamium_search_distinct() {
	
    return "wp_posts.*, COUNT(wp_streamium_reviews.post_id) AS reviews";

}

/**
 * joins the stramium reviews query for search
 *
 * @return null
 * @author  @sameast
 */
function streamium_search_join($join) {
    
    global $wpdb;
    
    $posts_stats_view_join = "LEFT JOIN wp_streamium_reviews ON ($wpdb->posts.ID = wp_streamium_reviews.post_id)";
    
    $join .= $posts_stats_view_join;
    
    return $join;

}

/**
 * groups the stramium reviews query for search
 *
 * @return null
 * @author  @sameast
 */
function streamium_search_groupby($groupby) {
    
    global $wpdb;
    
    $groupby = "wp_streamium_reviews.post_id";
    
    return $groupby;

}

/**
 * joins the stramium reviews query for search
 *
 * @return null
 * @author  @sameast
 */
function streamium_search_orderby($orderby_statement) {
	
    global $wpdb;
	
    $orderby_statement = "reviews DESC";
	
    return $orderby_statement;

}

/**
 *
 * @param Array $list
 * @param int $p
 * @return multitype:multitype:
 * @link http://www.php.net/manual/en/function.array-chunk.php#75022
 */
function partition(Array $list, $p) {
    
    $listlen = count($list);
    
    $partlen = floor($listlen / $p);
    
    $partrem = $listlen % $p;
    
    $partition = array();
    
    $mark = 0;
    
    for($px = 0; $px < $p; $px ++) {
    
        $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
    
        $partition[$px] = array_slice($list, $mark, $incr);
    
        $mark += $incr;
    
    }
    
    return $partition;

}

/**
 *
 * @param Array $list
 * @param int $p
 * @return streamGroupSeasons:
 */
function streamGroupSeasons($array, $key) {
    
    $return = array();
    
    foreach($array as $val) {
    
        $return[$val[$key]][] = $val;
    
    }
    
    ksort($return);
    
    return $return;

}

/**
 *
 * @param Array $list
 * @param int $p
 * @return multitype:multitype:
 */
function orderCodes($postId) {

  $episodes = get_post_meta($postId, 'repeatable_fields' , true);

  if(empty($episodes)){
    return false;
  }

  // Group by seasons
  $firstSort = streamGroupSeasons($episodes,'seasons');

  $codes = [];
  foreach ($firstSort as $key => $flatten) {

    foreach ($flatten as $key => $value) {
      
        $codes[] = (isset($value['service']) && $value['service'] != '') ? $value['service'] : $value['codes'];

    }
  
  }

  return array(
    "seasons" => count($firstSort),
    "episodes" => count($codes),
    "codes" => $codes
  );

}

/**
 *
 * @param  Filter to fix Wordpress not using https for urls
 * @param int $p
 * @return filter
 */
function ssl_post_thumbnail_urls($url, $post_id) {

  //Skip file attachments
  if(!wp_attachment_is_image($post_id)) {
        
    return $url;

  }

  //Correct protocol for https connections
  list($protocol, $uri) = explode('://', $url, 2);

  if(is_ssl()) {
    if('http' == $protocol) {
      $protocol = 'https';
    }
  } else {
    if('https' == $protocol) {
      $protocol = 'http';
    }
  }

  return $protocol.'://'.$uri;
}
add_filter('wp_get_attachment_url', 'ssl_post_thumbnail_urls', 10, 2);

/**
 *
 * @param  Add a body class for fixed nav
 * @param int $p
 * @return filter
 */
function streamium_body_class( $classes ) {

    // Add fixed nav class for home only
    if(is_home() ){
      //$classes[] = 'nav-is-fixed';
    }

    return $classes;
    
}
add_action( 'body_class', 'streamium_body_class');

/**
 *
 * @param Checks for ssl returns https if needed
 * @param int $p
 * @return filter
 */
function get_theme_mod_ssl($mod_name){
    
    if (is_ssl()) {
    
      return preg_replace("/^http:/i", "https:", get_theme_mod($mod_name));
    
    }else{
    
      return get_theme_mod($mod_name);
    
    }
    
}

/**
 *
 * @param Checks for ssl returns https if needed
 * @param int $p
 * @return filter
 */
function streamium_sort_episodes($episodes){
    
    $positions = array();

    foreach ($episodes as $key => $row){
    
        $positions[$key] = $row['positions'];
    
    }
    
    array_multisort($positions, SORT_ASC, $episodes);

    $response = array();
    
    foreach ($episodes as $v) { 

        $seasons = $v['seasons'];
    
        if (!isset($response[$seasons])) $response[$seasons] = array();
    
        //$v['link'] = get_permalink($postId);
    
        $response[$seasons][] = $v;

    }
    
    return $response;
    
}