<?php

/**
 * Adds meta boxes within a post
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_video_code_meta_box_add(){

    // WOO::
    if ( class_exists( 'WooCommerce' ) ) {
        add_meta_box( 'streamium-premium-meta-box-woo', 'Woocommerce Product', 'streamium_premium_meta_box_woo', streamium_global_meta(), 'side', 'high');
    }

    add_meta_box( 'streamium-meta-box-video', 'Video', 'streamium_meta_box_video', streamium_global_meta(), 'side', 'high' );

    add_meta_box( 'streamium-meta-box-preview', 'Preview', 'streamium_meta_box_preview_video', streamium_global_meta(), 'side', 'high' );

    add_meta_box( 'streamium-meta-box-trailer', 'Trailer', 'streamium_meta_box_trailer_video', streamium_global_meta(), 'side', 'high' );

    // Repeater for premium
    add_meta_box( 'streamium-repeatable-fields', 'Multiple Videos - Seasons/Episodes', 'streamium_repeatable_meta_box_display', streamium_global_meta(), 'normal', 'high');

    // Release date extra meta 
    add_meta_box( 'streamium-meta-box-release-date', 'Override Release Date', 'streamium_meta_box_release_date', streamium_global_meta(), 'side', 'high' );

    // Global extra meta
    add_meta_box( 'streamium-meta-box-extra-meta', 'Extra Video Tile Meta', 'streamium_meta_box_extra_meta', streamium_global_meta(), 'side', 'high' );

    // Video Ratings meta
    add_meta_box( 'streamium-meta-box-ratings', 'Set Video Rating (PG|R|G|PG-13|NC-17)', 'streamium_meta_box_ratings', streamium_global_meta(), 'side', 'high' );
    
}

add_action( 'add_meta_boxes', 'streamium_video_code_meta_box_add' );

/**
 * Sets up the meta box content for the main video
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_meta_box_video(){

        global $post;
        $values = get_post_custom( $post->ID );
        wp_nonce_field( 'streamium_meta_box_video', 'streamium_meta_box_video_nonce' );

        // Roku data
        $codes     = get_post_meta($post->ID,'streamium_video_code_meta', true);
        $drm       = get_post_meta($post->ID,'streamium_video_drm_meta', true);
        $url       = get_post_meta($post->ID,'streamium_video_url_meta', true);
        $type      = get_post_meta($post->ID,'streamium_video_type_meta', true);
        $bif       = get_post_meta($post->ID,'streamium_video_bif_meta', true);
        $quality   = get_post_meta($post->ID,'streamium_video_quality_meta', true);
        $videoType = get_post_meta($post->ID,'streamium_video_videotype_meta', true);
        $duration  = get_post_meta($post->ID,'streamium_video_duration_meta', true);
        $captions  = get_post_meta($post->ID,'streamium_video_captions_meta', true);
        $ads       = get_post_meta($post->ID,'streamium_video_ads_meta', true);
        $check     = get_post_meta($post->ID,'streamium_video_360_meta', true);

    ?>
    <p class="streamium-meta-box-wrapper">
        <select class="streamium-theme-main-video-select-group chosen-select" tabindex="1" name="streamium_video_code_meta" id="streamium_video_code_meta">
            <option value="<?php echo $codes; ?>"><?php echo (empty($codes)) ? 'Select Main Video' : $codes; ?></option>
            <option value="">Remove Current Video</option>
        </select>
        <div class="spinner is-active" style=" display: none;float:none;width:auto;height:auto;padding:0px 0 0px 28px;background-position:0px -1px;font-style: italic;">Generating or Updating data below...</div>
    </p>

    <p class="episode_drm">
        <label>Drm</label>
        <input type="url" name="streamium_video_drm_meta" class="widefat" value="<?php echo $drm; ?>" placeholder="Enter video url" />
    </p>

    <p class="episode_url">
        <label>Src</label>
        <input type="url" name="streamium_video_url_meta" class="widefat" value="<?php echo $url; ?>" placeholder="Enter video url" />
    </p>

    <p class="episode_type">
        <label>Mime Type</label>
        <input type="text" name="streamium_video_type_meta" class="widefat" value="<?php echo $type; ?>" placeholder="Enter mime type" />
    </p>

    <p class="episode_bif">
        <label>Bif Thumbnail</label>
        <input type="url" name="streamium_video_bif_meta" class="widefat" value="<?php echo $bif; ?>" placeholder="Enter bif url" />
    </p>

    <p class="episode_quality">
        <label>Roku Quality</label>
        <select tabindex="1" name="streamium_video_quality_meta">
            <option value="<?php echo $quality; ?>"><?php echo (empty($quality)) ? 'Select Video Quality' : $quality; ?></option>
            <option value="HD">HD – 720p</option>
            <option value="FHD">FHD – 1080p</option>
            <option value="UHD">UHD – 4K</option>
        </select>
    </p>

    <p class="episode_video_type">
        <label>Roku Type</label>
        <select tabindex="1" name="streamium_video_videotype_meta">
            <option value="<?php echo $videoType; ?>"><?php echo (empty($videoType)) ? 'Select Video Type' : $videoType; ?></option>
            <option value="HLS">HLS</option>
            <option value="SMOOTH">SMOOTH</option>
            <option value="DASH">DASH</option>
            <option value="MP4">MP4</option>
            <option value="MOV">MOV</option>
            <option value="M4V">M4V</option>
        </select>
    </p>

    <p class="episode_duration">
        <label>Roku Duration (Runtime in seconds)</label>
        <input type="text" name="streamium_video_duration_meta" class="widefat" value="<?php echo $duration; ?>" placeholder="Enter video duration" />
    </p>

    <p class="episode_captions">
        <label>Roku Captions</label>
        <input type="text" name="streamium_video_captions_meta" class="widefat" value='<?php echo $captions; ?>' placeholder="Enter video captions" />
    </p>

    <p class="episode_ads">
        <label>Adverts Url (VAST/VMAP)</label>
        <input type="text" name="streamium_video_ads_meta" class="widefat" value="<?php echo $ads; ?>" placeholder="Enter video advert url" />
    </p>

    <p class="streamium-meta-box-wrapper">
        <label>
            <input type="checkbox" name="streamium_video_360_meta" id="streamium_video_360_meta" value="yes" <?php if ( isset ( $check ) ) checked( $check, 'yes' ); ?> />
            <?php esc_attr_e( 'This is a 360 degree video', 'streamium' ); ?>
        </label>
    </p>

    <?php    

}

/**
 * Sets up the meta box content for the video trailer
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_meta_box_trailer_video(){

    // $post is already set, and contains an object: the WordPress post
    global $post;
    $code      = get_post_meta($post->ID,'streamium_trailer_code_meta', true);
    $url       = get_post_meta($post->ID,'streamium_trailer_video_meta', true);
    $type      = get_post_meta($post->ID,'streamium_trailer_type_meta', true);

    wp_nonce_field( 'streamium_meta_box_video', 'streamium_meta_box_video_nonce' );
    ?>

    <p class="streamium-meta-box-wrapper">
        <select class="streamium-theme-video-trailer-select-group chosen-select" tabindex="1" name="streamium_trailer_code_meta" id="streamium_trailer_code_meta">
            <option value="<?php echo $code; ?>"><?php echo (empty($code)) ? 'Select Video' : $code; ?></option>
            <option value="">Remove Current Video</option>
        </select>
        <div class="spinner is-active" style=" display: none;float:none;width:auto;height:auto;padding:0px 0 0px 28px;background-position:0px -1px;font-style: italic;">Generating or Updating data below...</div>
    </p>

    <p class="trailer_video">
        <label>Src</label>
        <input type="url" name="streamium_trailer_video_meta" class="widefat" value="<?php echo $url; ?>" placeholder="Video Url" />
    </p>

    <p class="trailer_type">
        <label>Mime Type</label>
        <input type="url" name="streamium_trailer_type_meta" class="widefat" value="<?php echo $type; ?>" placeholder="Mime Type" />
    </p>

    <?php    

}

/**
 * Sets up the meta box content for the video background on home slider
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_meta_box_preview_video(){

    global $post;
    $code      = get_post_meta($post->ID,'streamium_preview_code_meta', true);
    $url       = get_post_meta($post->ID,'streamium_preview_video_meta', true);
    $type      = get_post_meta($post->ID,'streamium_preview_type_meta', true);
    $check     = get_post_meta($post->ID,'streamium_preview_meta_box_checkbox', true);

    wp_nonce_field( 'streamium_meta_box_video', 'streamium_meta_box_video_nonce' );

    ?>
        <p class="streamium-meta-box-wrapper">
            
            <select class="streamium-theme-featured-video-select-group chosen-select" tabindex="1" name="streamium_preview_code_meta" id="streamium_preview_code_meta">
                <option value="<?php echo $code; ?>"><?php echo (empty($code)) ? 'Select Video' : $code; ?></option>
                <option value="">Remove Current Video</option>
            </select>

            <div class="spinner is-active" style=" display: none;float:none;width:auto;height:auto;padding:0px 0 0px 28px;background-position:0px -1px;font-style: italic;">Generating or Updating data below...</div>

        </p>

        <p class="preview_video">
            <label>Src</label>
            <input type="url" name="streamium_preview_video_meta" class="widefat" value="<?php echo $url; ?>" placeholder="Video Url" />
        </p>

        <p class="preview_type">
            <label>Mime Type</label>
            <input type="url" name="streamium_preview_type_meta" class="widefat" value="<?php echo $type; ?>" placeholder="Mime Type" />
        </p>

        <p class="streamium-meta-box-wrapper">
            <label>
                <input type="checkbox" name="streamium_preview_meta_box_checkbox" id="streamium_preview_meta_box_checkbox" value="yes" <?php if ( isset ( $check ) ) checked( $check, 'yes' ); ?> />
                <?php esc_attr_e( 'Show in the main feature slider', 'streamium' ); ?>
            </label>
        </p>

    <?php    

}

/**
 * Setup custom repeater meta
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_repeatable_meta_box_display() {

    global $post;
    wp_nonce_field( 'streamium_meta_box_video', 'streamium_meta_box_video_nonce' ); 

    // GEt the fields
    $streamium_repeatable_series = get_post_meta($post->ID, 'streamium_repeatable_series', true);

    ?>

    <div class="accordion-container">
        <ul id="repeatable-fieldset-one">
            <?php

                if ( $streamium_repeatable_series ) {

                foreach ( streamGroupSeasons($streamium_repeatable_series,'seasons') as $seasons ) {

                    // Sort array by positions key
                    $positions = array();
                    foreach ($seasons as $key => $row)
                    {
                        $positions[$key] = $row['episode_position'];
                    }
                    array_multisort($positions, SORT_ASC, $seasons);

                    foreach ( $seasons as $key => $field ) {

                        $episode_thumb        = (isset($field['episode_thumb']) && $field['episode_thumb'] != '') ? esc_attr($field['episode_thumb']) : '';
                        $episode_season       = (isset($field['episode_season']) && $field['episode_season'] != '') ? esc_attr($field['episode_season']) : '';
                        $episode_position     = (isset($field['episode_position']) && $field['episode_position'] != '') ? esc_attr($field['episode_position']) : '';
                        $episode_title        = (isset($field['episode_title']) && $field['episode_title'] != '') ? esc_attr($field['episode_title']) : '';
                        $episode_description  = (isset($field['episode_description']) && $field['episode_description'] != '') ? esc_attr($field['episode_description']) : '';
                        $episode_code         = (isset($field['episode_code']) && $field['episode_code'] != '') ? esc_attr($field['episode_code']) : '';

                        // Roku data
                        $episode_drm        = (isset($field['episode_drm']) && $field['episode_drm'] != '') ? esc_attr($field['episode_drm']) : '';
                        $episode_url        = (isset($field['episode_url']) && $field['episode_url'] != '') ? esc_attr($field['episode_url']) : '';
                        $episode_type       = (isset($field['episode_type']) && $field['episode_type'] != '') ? esc_attr($field['episode_type']) : '';
                        $episode_bif        = (isset($field['episode_bif']) && $field['episode_bif'] != '') ? esc_attr($field['episode_bif']) : '';
                        $episode_quality    = (isset($field['episode_quality']) && $field['episode_quality'] != '') ? esc_attr( $field['episode_quality']) : '';
                        $episode_video_type = (isset($field['episode_video_type']) && $field['episode_video_type'] != '') ? esc_attr( $field['episode_video_type']) : '';
                        $episode_duration   = (isset($field['episode_duration']) && $field['episode_duration'] != '') ? esc_attr( $field['episode_duration']) : '';
                        $episode_captions   = (isset($field['episode_captions']) && $field['episode_captions'] != '') ? esc_attr( $field['episode_captions']) : '';
                        $episode_ads        = (isset($field['episode_ads']) && $field['episode_ads'] != '') ? esc_attr( $field['episode_ads']) : '';
                        $episode_360        = (isset($field['episode_360']) && $field['episode_360'] != '') ? esc_attr( $field['episode_360']) : '';
                    
            ?>
            <li class="control-section accordion-section ui-state-default">
                
                <h3 tabindex="0" class="accordion-section-title"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><small><?php echo ucfirst($episode_title); ?></small> <span class="streamium-badge-info">Season:<?php echo $episode_season; ?></span></h3>
                <input type="hidden" class="episode_position" name="episode_position[]" value="<?php echo $episode_position; ?>" />
                <ul class="accordion-section-content">
                    <li class="streamium-repeater-list">
                        <div class="streamium-repeater-left">
                            <p>
                                <label>Video Image</label>
                                <input type="hidden" class="widefat" name="episode_thumb[]" value="<?php echo $episode_thumb; ?>" />
                                <img src="<?php echo $episode_thumb; ?>" />
                                <input class="streamium_upl_button button" type="button" value="Upload Image" />
                            </p> 
                        </div>
                        <div class="streamium-repeater-right">
                            <p>
                                <label>Video Season</label>
                                <select class="widefat" tabindex="1" name="episode_season[]">
                                    <option value="<?php echo $episode_season; ?>"><?php echo $episode_season; ?></option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                </select>
                            </p>
                            <p>
                                <label>Video Title</label>
                                <input type="text" class="widefat" name="episode_title[]" value="<?php echo $episode_title; ?>" />
                            </p>
                            <p>
                                <label>Video Description</label>
                                <textarea rows="4" cols="50" class="widefat" name="episode_description[]"><?php echo $episode_description; ?></textarea>
                            </p>
                            <p>
                                <label>S3Bubble Video Code</label>
                                <select class="streamium-theme-episode-select chosen-select" tabindex="1" name="episode_code[]">
                                    <option value="<?php echo $episode_code; ?>">Select Video <?php echo $episode_code; ?></option>
                                </select>
                                <div class="spinner is-active" style=" display: none;float:none;width:auto;height:auto;padding:0px 0 0px 28px;background-position:0px -1px;font-style: italic;">Generating or Updating data below...</div>
                            </p>
                            <p class="episode_drm">
                                <label>Video DRM</label>
                                <input type="text" class="widefat" name="episode_drm[]" value="<?php echo $episode_drm; ?>" />
                            </p>
                            <p class="episode_url">
                                <label>Src</label>
                                <input type="url" name="episode_url[]" class="widefat" value="<?php echo $episode_url; ?>" placeholder="Enter video url" />
                            </p>
                            <p class="episode_type">
                                <label>Mime Type</label>
                                <input type="text" name="episode_type[]" class="widefat" value="<?php echo $episode_type; ?>" placeholder="Enter mime type" />
                            </p>
                            <p class="episode_bif">
                                <label>Bif episode_Thumb</label>
                                <input type="url" name="episode_bif[]" class="widefat" value="<?php echo $episode_bif; ?>" placeholder="Enter bif url" />
                            </p>
                            <p class="episode_quality">
                                <label>Roku Quality</label>
                                <select tabindex="1" name="episode_quality[]">
                                    <option value="<?php echo $episode_quality; ?>"><?php echo (empty($episode_quality)) ? 'Select Video Quality' : $episode_quality; ?></option>
                                    <option value="HD">HD – 720p</option>
                                    <option value="FHD">FHD – 1080p</option>
                                    <option value="UHD">UHD – 4K</option>
                                </select>
                            </p>
                            <p class="episode_video_type">
                                <label>Roku Type</label>
                                <select tabindex="1" name="episode_video_type[]">
                                    <option value="<?php echo $episode_video_type; ?>"><?php echo (empty($episode_video_type)) ? 'Select Video Type' : $episode_video_type; ?></option>
                                    <option value="HLS">HLS</option>
                                    <option value="SMOOTH">SMOOTH</option>
                                    <option value="DASH">DASH</option>
                                    <option value="MP4">MP4</option>
                                    <option value="MOV">MOV</option>
                                    <option value="M4V">M4V</option>
                                </select>
                            </p>
                            <p class="episode_duration">
                                <label>Roku Duration (Runtime in seconds)</label>
                                <input type="text" name="episode_duration[]" class="widefat" value="<?php echo $episode_duration; ?>" placeholder="Enter video duration" />
                            </p>
                            <p class="episode_captions">
                                <label>Captions</label>
                                <input type="text" name="episode_captions[]" class="widefat" value="<?php echo $episode_captions; ?>" placeholder="Enter video captions" />
                            </p>
                            <p class="episode_ads">
                                <label>Adverts Url (VAST/VMAP)</label>
                                <input type="text" name="episode_ads[]" class="widefat" value="<?php echo $episode_ads; ?>" placeholder="Enter video advert url" />
                            </p>
                            <p class="streamium-meta-box-wrapper">
                                <label>
                                    <input type="checkbox" name="episode_360[]" value="yes" <?php if ( isset ( $episode_360 ) ) checked( $episode_360, 'yes' ); ?> />
                                    <?php esc_attr_e( 'This is a 360 degree video', 'streamium' ); ?>
                                </label>
                            </p>
                            <p>
                                <a class="button button-large streamium-repeater-remove-row" href="#" data-pid="<?php echo $post->ID; ?>">Remove</a>
                            </p>

                        </div>
                    </li>
                </ul>
            </li>
            <?php 
        
                    } 
                } 

            }

            ?>
        </ul>
    </div>
    
    <div class="streamium-repeater-footer">
        <a id="streamium-add-repeater-row" class="button add-program-row button-primary" href="#">Add Video</a>
    </div>

    <?php

}

/**
 * Optional extra meta
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_meta_box_extra_meta() {
  
    global $post;
    $values = get_post_custom( $post->ID );
    $text = isset( $values['streamium_extra_meta'] ) ? $values['streamium_extra_meta'][0] : '';
    wp_nonce_field( 'streamium_meta_box_video', 'streamium_meta_box_video_nonce' );
    ?>
    <p class="streamium-meta-box-wrapper">

        <input type="text" name="streamium_extra_meta" class="widefat" id="streamium_extra_meta" value="<?php echo $text; ?>" />

    </p>

    <?php 

}

/**
 * Add video ratings PG etc
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_meta_box_ratings() {
  
    global $post;
    $values = get_post_custom( $post->ID );
    $text = isset( $values['streamium_ratings_meta'] ) ? $values['streamium_ratings_meta'][0] : '';
    wp_nonce_field( 'streamium_meta_box_video', 'streamium_meta_box_video_nonce' );
    ?>
    <p class="streamium-meta-box-wrapper">

        <input type="text" name="streamium_ratings_meta" class="widefat" id="streamium_ratings_meta" value="<?php echo $text; ?>" />

    </p>

    <?php 

}

/**
 * Overide release date
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_meta_box_release_date() {
  
    global $post;
    $values = get_post_custom( $post->ID );
    $text = isset( $values['streamium_release_date_meta'] ) ? $values['streamium_release_date_meta'][0] : '';
    wp_nonce_field( 'streamium_meta_box_video', 'streamium_meta_box_video_nonce' );
    ?>
    <p class="streamium-meta-box-wrapper">

        <input type="text" name="streamium_release_date_meta" class="widefat s3bubble-meta-datepicker" id="streamium_release_date_meta" value="<?php echo $text; ?>" />

    </p>

    <?php 

}

/**
 * Sets up the meta box content for the video background on home slider
 * https://docs.woocommerce.com/wc-apidocs/function-wc_get_product.html
 * @return null
 * @author  @s3bubble
 */
function streamium_premium_meta_box_woo(){

    // SECURITY::
    wp_nonce_field( 'streamium_premium_meta_security', 'streamium_premium_meta_nonce' );

    $query = new WC_Product_Query( array(
        'limit' => 1000,
        'orderby' => 'date',
        'order' => 'DESC',
        'return' => 'ids',
    ) );

    $products = $query->get_products();

    $product_id = get_post_meta( get_the_ID(), 'streamium_premium_meta_box_woo_product', true );

    ?>

        <p class="streamium-meta-box-wrapper"> 

            <select tabindex="1" name="streamium_premium_meta_box_woo_product">

                <?php 

                    if(isset($product_id)){
                        
                        $get_product = wc_get_product( $product_id );

                         ?>

                            <option value="<?php echo $get_product->post->ID; ?>"><?php echo $get_product->post->post_title; ?></option>

                            <option value="">Remove Product/Plan</option>

                        <?php

                    }else{ ?>

                        <option value="">No Product/Plan</option>

                    <?php }

                ?>
                
                <?php foreach ($products as $key => $product) { 

                    $post_object = get_post($product); 

                ?>

                    <option value="<?php echo $post_object->ID; ?>"><?php echo $post_object->post_title; ?></option>

                <?php } ?>

            </select>

        </p>

    <?php

}

/**
 * Saves the meta box content
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_post_meta_box_save( $post_id )
{
    // Bail if we're doing an auto save
    if(defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) return;

    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['streamium_meta_box_video_nonce']) || !wp_verify_nonce( $_POST['streamium_meta_box_video_nonce'], 'streamium_meta_box_video')) return;
     
    // if our current user can't edit this post, bail
    if(!current_user_can('edit_posts')) return;

    // ROKU VIDEO DATA::
    update_post_meta($post_id, 'streamium_video_code_meta', $_POST['streamium_video_code_meta']);
    update_post_meta($post_id, 'streamium_video_drm_meta', $_POST['streamium_video_drm_meta']);
    update_post_meta($post_id, 'streamium_video_url_meta', $_POST['streamium_video_url_meta']);
    update_post_meta($post_id, 'streamium_video_type_meta', $_POST['streamium_video_type_meta']);
    update_post_meta($post_id, 'streamium_video_bif_meta', $_POST['streamium_video_bif_meta']);
    update_post_meta($post_id, 'streamium_video_quality_meta', $_POST['streamium_video_quality_meta']);
    update_post_meta($post_id, 'streamium_video_videotype_meta', $_POST['streamium_video_videotype_meta']);
    update_post_meta($post_id, 'streamium_video_duration_meta', $_POST['streamium_video_duration_meta']);
    update_post_meta($post_id, 'streamium_video_ads_meta', $_POST['streamium_video_ads_meta']);

    if(empty($_POST['streamium_video_captions_meta'])){
        update_post_meta($post_id, 'streamium_video_captions_meta', null);
    }else{
        update_post_meta($post_id, 'streamium_video_captions_meta', $_POST['streamium_video_captions_meta']);
    }

    if(!empty($_POST['streamium_video_360_meta'])) {
        update_post_meta($post_id, 'streamium_video_360_meta', 'yes');
    }else{
        update_post_meta($post_id, 'streamium_video_360_meta', '');
    }

    // TRAILER SECTION  =>
    update_post_meta($post_id, 'streamium_trailer_code_meta', $_POST['streamium_trailer_code_meta']);
    update_post_meta($post_id, 'streamium_trailer_video_meta', $_POST['streamium_trailer_video_meta']);
    update_post_meta($post_id, 'streamium_trailer_type_meta', $_POST['streamium_trailer_type_meta']);
    // TRAILER SECTION  =>

    // PREVIEW SECTION  =>
    update_post_meta($post_id, 'streamium_preview_code_meta', $_POST['streamium_preview_code_meta']);
    update_post_meta($post_id, 'streamium_preview_video_meta', $_POST['streamium_preview_video_meta']);
    update_post_meta($post_id, 'streamium_preview_type_meta', $_POST['streamium_preview_type_meta']);
    if(!empty($_POST['streamium_preview_meta_box_checkbox'])) {
        update_post_meta($post_id, 'streamium_preview_meta_box_checkbox', 'yes');
    }else{
        update_post_meta($post_id, 'streamium_preview_meta_box_checkbox', '');
    }
    // PREVIEW SECTION  =>
    

    // EXTRA META SECTION  =>
    update_post_meta($post_id, 'streamium_extra_meta', $_POST['streamium_extra_meta']);
    update_post_meta($post_id, 'streamium_ratings_meta', $_POST['streamium_ratings_meta']);
    update_post_meta($post_id, 'streamium_release_date_meta', $_POST['streamium_release_date_meta']);
    // EXTRA META SECTION  =>

    // WOO SECTION  =>
    if( isset( $_POST['streamium_premium_meta_box_woo_product'] ) ){

        update_post_meta( $post_id, 'streamium_premium_meta_box_woo_product', $_POST['streamium_premium_meta_box_woo_product'] );
      
    }
    // WOO SECTION  =>

    // SEASONS SECTION  =>
    $seasons_new = array();

    $media_fields = ['episode_thumb', 'episode_season', 'episode_position', 'episode_title', 'episode_code', 'episode_description', 'episode_drm', 'episode_url', 'episode_type', 'episode_bif', 'episode_quality', 'episode_video_type', 'episode_duration', 'episode_captions', 'episode_ads', 'episode_360'];

    foreach ($media_fields as $key => $field) {

        if(isset($_POST[$field])){

            foreach ($_POST[$field] as $key => $value) {
                
                if($value != ''){

                    $seasons_new[$key][$field]   = stripslashes( strip_tags( $value ));

                }

            }

        }

    }

    update_post_meta( $post_id, 'streamium_repeatable_series', $seasons_new );
    // SEASONS SECTION  <= 

}

add_action( 'save_post', 'streamium_post_meta_box_save', 10, 3 );

/**
 * Get the websites domain needed for connected websites
 *
 * @return null
 * @author  @s3bubble
 */
function streamium_website_connection(){

    if(isset($_SERVER['HTTP_HOST'])){

        $host = preg_replace('#^www\.(.+\.)#i', '$1', $_SERVER['HTTP_HOST']); // remove the www
        update_option("streamium_connected_website", $host);

    }

}

add_action( 'init', 'streamium_website_connection' );