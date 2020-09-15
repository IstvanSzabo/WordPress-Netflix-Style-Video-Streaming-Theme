<?php

/*
 * Redirect to checkout not cart
 */
add_filter( 'woocommerce_add_to_cart_redirect', 'streamium_redirect_checkout_add_cart' );
 
function streamium_redirect_checkout_add_cart() {
    return wc_get_checkout_url();
}

/*
 * Remove add to cart notification
 */
add_filter( 'wc_add_to_cart_message', 'streamium_remove_add_to_cart_message' );

function streamium_remove_add_to_cart_message() {
    return;
}

/**
 * Add woo support
 *
 * @return bool
 * @author  @s3bubble
 */
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'woocommerce_support' );

// Remove some unwanted links
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

/**
 * Print the customer avatar in My Account page, after the welcome message
 */
function streamium_myaccount_customer_avatar() {

    $current_user = wp_get_current_user();
    $url = md5( strtolower( trim( $current_user->user_email ) ) );
    echo '<div class="myaccount_avatar"><a href="https://www.gravatar.com/avatar/' . $url . '?s=200" target="_blank">' . get_avatar( $current_user->user_email, 72, '', $current_user->display_name ) . '</a></div>';

}

add_action( 'woocommerce_before_my_account', 'streamium_myaccount_customer_avatar', 50 );

/*
* Add login logout link for Woo
* @author @s3bubble
* @none
*/ 
function streamium_woo_auth_menu( $items, $args ) {
    if (is_user_logged_in() && $args->theme_location == 'streamium-header-menu') {
            $items .= '<li><a class="streamium-auth" href="'. wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ) .'"><i class="fa fa-sign-out" aria-hidden="true"></i></a></li>';
    }
    elseif (!is_user_logged_in() && $args->theme_location == 'streamium-header-menu') {
            $items .= '<li><a class="streamium-auth" href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '"><i class="fa fa-sign-in" aria-hidden="true"></i></a></li>';
    }
    return $items;
}

// Can be disabled in the site identity shown by default
if ( !get_theme_mod( 'streamium_disable_login' ) ) {
    add_filter( 'wp_nav_menu_items', 'streamium_woo_auth_menu', 10, 2 );
}

/**
 * Remove related products output
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

function streamium_remove_all_quantity_fields( $return, $product ) {
    return true;
}
add_filter( 'woocommerce_is_sold_individually','streamium_remove_all_quantity_fields', 10, 2 );