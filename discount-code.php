<?php
/*

Plugin Name: Discount Code
Plugin URI: #
Description: This plugin used to apply 25% discount for all products.
Author: Priyanka
Author URI: #

*/


$currentDate = date('d-m-Y');
$currentDate = date('d-m-Y', strtotime($currentDate));
   
global $sale_start_Date;    //global sales start date
global $sale_end_Date;      //global sales end date

$sale_start_Date = date('d-m-Y', strtotime("23-08-2021"));    //sales start date
$sale_end_Date = date('d-m-Y', strtotime("31-08-2021"));      //sales end date


if( ($currentDate >= $sale_start_Date) && ($currentDate <= $sale_end_Date)){


/**
 * Update Product Pricing Part 1 - WooCommerce Product
 */
 
add_filter( 'woocommerce_get_price_html', 'mytheme_discount_price_display', 9999, 2 );
 
function mytheme_discount_price_display( $price_html, $product ) {
    
    // ONLY ON FRONTEND
    if ( is_admin() ) return $price_html;
    
    // ONLY IF PRICE NOT NULL
    if ( '' === $product->get_price() ) return $price_html;
    
    // IF CUSTOMER LOGGED IN, APPLY 25% DISCOUNT   
    if ( wc_current_user_has_role( 'customer') ) {
		 
        $orig_price = wc_get_price_to_display( $product );
        $price_html = wc_price( $orig_price * 0.75 );
    
	}
    
    return $price_html; 
}
 
/**
Update Price in Cart Page
 */
 
add_action( 'woocommerce_before_calculate_totals', 'mytheme_discount_price_cart', 9999 );
 
function mytheme_discount_price_cart( $cart ) {
 
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
 
    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) return;
 
    // IF CUSTOMER NOT LOGGED IN, DONT APPLY DISCOUNT
    if ( ! wc_current_user_has_role( 'customer' ) ) return;
 
    // LOOP THROUGH CART ITEMS & APPLY 25% DISCOUNT
    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
        $product = $cart_item['data'];
        $price = $product->get_price();
        $cart_item['data']->set_price( $price * 0.75 );
    }
 
}

add_action('woocommerce_before_cart_contents', 'mytheme_cart_message');

function mytheme_cart_message( ) {

global $sale_start_Date;
global $sale_end_Date;


		
		if ( wc_current_user_has_role( 'customer') ) {
echo '<p style="font-size: 20px;"><b> Get flat 25% discount on all products. Offer valid from '. $sale_start_Date .' till '. $sale_end_Date. '.</b></p>'; } 
}
}