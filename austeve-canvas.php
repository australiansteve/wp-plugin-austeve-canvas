<?php
/**
 * Plugin Name: Create Over Cocktails - Events, Venues & Profiles
 * Plugin URI: https://github.com/australiansteve/wp-plugin-austeve-canvas
 * Description: Functionality for Create Over Cocktails website
 * Version: 1.0.1
 * Author: AustralianSteve
 * Author URI: http://weavercrawford.com
 * License: GPL2
 */

include( plugin_dir_path( __FILE__ ) . 'admin.php');
include( plugin_dir_path( __FILE__ ) . 'data-fixes.php');
include( plugin_dir_path( __FILE__ ) . 'ajax-actions.php');
include( plugin_dir_path( __FILE__ ) . 'shortcode.php');
include( plugin_dir_path( __FILE__ ) . 'widget.php');
include( plugin_dir_path( __FILE__ ) . 'austeve-events.php');
include( plugin_dir_path( __FILE__ ) . 'austeve-venues.php');
include( plugin_dir_path( __FILE__ ) . 'austeve-creations.php');
include( plugin_dir_path( __FILE__ ) . 'austeve-profiles.php');
include( plugin_dir_path( __FILE__ ) . 'woocommerce-actions.php');
include( plugin_dir_path( __FILE__ ) . 'woo-endpoint-myaccount-reviews.php');

register_activation_hook( __FILE__, 'austeve_add_roles_on_plugin_activation' );


function austeve_canvas_enqueue_style() {
	wp_enqueue_style( 'jquery-ui-css', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css');
	wp_enqueue_style( 'austeve-canvas', plugin_dir_url( __FILE__ ). '/style.css' , false , '4.9'); 
}

function austeve_canvas_enqueue_script() {
	
	if ( WP_DEBUG )
	{
		wp_enqueue_script( 'austeve-profiles-js', plugin_dir_url( __FILE__ ). '/js/front-end.js' , array( 'jquery-ui-accordion', 'jquery' ) , '1.1'); 
	}
	else 
	{
		wp_enqueue_script( 'austeve-profiles-js', plugin_dir_url( __FILE__ ). '/assets/dist/js/front-end.min.js' , array( 'jquery-ui-accordion', 'jquery' ) , '1.1'); 
	}

}

add_action( 'wp_enqueue_scripts', 'austeve_canvas_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'austeve_canvas_enqueue_script' );


function austeve_canvas_enqueue_admin_style() {
	wp_enqueue_style( 'austeve-canvas', plugin_dir_url( __FILE__ ). '/style-admin.css' , false , '4.9'); 
}

add_action( 'admin_enqueue_scripts', 'austeve_canvas_enqueue_admin_style' );

function austeve_admin_FontAwesome_icons() {
    echo '<script src="https://use.fontawesome.com/8365fd1449.js"></script>';
}
add_action('admin_head', 'austeve_admin_FontAwesome_icons');

// Flushes rewrite rules on plugin activation.
register_activation_hook( __FILE__, array( 'AUSteve_My_Account_Reviews', 'install' ) );

function austeve_acf_init() {
	
	acf_update_setting('google_api_key', 'AIzaSyCfoi49FhApNMu5BPu2YHItmdCxp6LWbVs');
}

add_action('acf/init', 'austeve_acf_init');

/**
 * Remove WooCommerce products from default WP search
 *
 */
function austeve_remove_wc_from_search() {
	global $wp_post_types;
 
	if ( post_type_exists( 'product' ) ) {
 
		// exclude from search results
		$wp_post_types['product']->exclude_from_search = true;
	}
}
add_action( 'init', 'austeve_remove_wc_from_search', 99 );

/**
 * Customise the wording of "Account Password" on the checkout page to something more clear for customers (an account is being created)
 * 
 */
function austeve_custom_override_checkout_fields( $fields ) {
     
     $fields['account']['account_password']['label'] = 'Create a password for your new account';
     return $fields;

}
add_filter( 'woocommerce_checkout_fields' , 'austeve_custom_override_checkout_fields' );


?>