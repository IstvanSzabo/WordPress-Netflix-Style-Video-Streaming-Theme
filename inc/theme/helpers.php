<?php  

/**
 * Changes the tile count
 *
 * @return null
 * @author  @s3bubble
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
 * @author  @s3bubble
 */
function streamium_register_menu() {
    
    register_nav_menu('streamium-header-menu',__( 'Header Menu', 'streamium' ));
    register_nav_menu('streamium-footer-menu',__( 'Footer Menu', 'streamium' ));

}

add_action( 'init', 'streamium_register_menu' );

/**
 * Fix for the main menu
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_remove_ul( $menu ){
    
    return preg_replace( array( '#^<ul[^>]*>#', '#</ul>$#' ), '', $menu );

}

add_filter( 'wp_nav_menu', 'streamium_remove_ul' );

/**
 * Webview only style for native app
 *
 * @return null
 * @author  @s3bubble
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

/**
 * Fix to flush urls
 *
 * @return null
 * @author  @s3bubble
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
 * @author  @s3bubble
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
 * @author  @s3bubble
 */
function streamium_search_distinct() {
	
    return "wp_posts.*, COUNT(wp_streamium_reviews.post_id) AS reviews";

}

/**
 * joins the stramium reviews query for search
 *
 * @return null
 * @author  @s3bubble
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
 * @author  @s3bubble
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
 * @author  @s3bubble
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
function orderCodes($post_id) {

  $episodes = get_post_meta($post_id, 'streamium_repeatable_series' , true);

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
//add_action( 'body_class', 'streamium_body_class');

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
    
        //$v['link'] = get_permalink($post_id);
    
        $response[$seasons][] = $v;

    }
    
    return $response;
    
}

/**
 *
 * @param Updated wordpress trim function
 * @param int $p
 * @return filter
 */
function streamium_trim_words( $text, $num_words = 55, $more = null ) {
    if ( null === $more ) {
        $more = __( '&hellip;' );
    }
 
    $original_text = $text;
    $text          = wp_strip_all_tags( $text );
    $num_words     = (int) $num_words;
 
    /*
     * translators: If your word count is based on single characters (e.g. East Asian characters),
     * enter 'characters_excluding_spaces' or 'characters_including_spaces'. Otherwise, enter 'words'.
     * Do not translate into your own language.
     */
    if ( strpos( _x( 'words', 'Word count type. Do not translate!' ), 'characters' ) === 0 && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
        $text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
        preg_match_all( '/./u', $text, $words_array );
        $words_array = array_slice( $words_array[0], 0, $num_words + 1 );
        $sep         = '';
    } else {
        $words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
        $sep         = ' ';
    }
 
    if ( count( $words_array ) > $num_words ) {
        array_pop( $words_array );
        $text = implode( $sep, $words_array );
        $text = $text . $more;
    } else {
        $text = implode( $sep, $words_array ) . $more;
    }
 
    /**
     * Filters the text content after words have been trimmed.
     *
     * @since 3.3.0
     *
     * @param string $text          The trimmed text.
     * @param int    $num_words     The number of words to trim the text to. Default 55.
     * @param string $more          An optional string to append to the end of the trimmed text, e.g. &hellip;.
     * @param string $original_text The text before it was trimmed.
     */
    return apply_filters( 'streamium_trim_words', $text, $num_words, $more, $original_text );
}