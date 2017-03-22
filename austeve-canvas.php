<?php
/**
 * Plugin Name: Canvas & Cocktails - Events, Venues & Profiles
 * Plugin URI: https://github.com/australiansteve/wp-plugin-austeve-canvas
 * Description: Functionality for Canvas & Cocktails website
 * Version: 0.0.1
 * Author: AustralianSteve
 * Author URI: http://weavercrawford.com
 * License: GPL2
 */

include( plugin_dir_path( __FILE__ ) . 'admin.php');
include( plugin_dir_path( __FILE__ ) . 'shortcode.php');
include( plugin_dir_path( __FILE__ ) . 'widget.php');
include( plugin_dir_path( __FILE__ ) . 'austeve-events.php');
include( plugin_dir_path( __FILE__ ) . 'austeve-venues.php');
include( plugin_dir_path( __FILE__ ) . 'austeve-paintings.php');
include( plugin_dir_path( __FILE__ ) . 'austeve-profiles.php');
include( plugin_dir_path( __FILE__ ) . 'woo-endpoint-myaccount-reviews.php');

register_activation_hook( __FILE__, 'austeve_add_roles_on_plugin_activation' );


function austeve_canvas_enqueue_style() {
	wp_enqueue_style( 'austeve-canvas', plugin_dir_url( __FILE__ ). '/style.css' , false , '4.6'); 
}

function austeve_canvas_enqueue_script() {
	
	if ( WP_DEBUG )
	{
		wp_enqueue_script( 'austeve-profiles-js', plugin_dir_url( __FILE__ ). '/js/front-end.js' , array( 'jquery' ) , '1.0'); 
	}
	else 
	{
		wp_enqueue_script( 'austeve-profiles-js', plugin_dir_url( __FILE__ ). '/assets/dist/js/front-end.min.js' , array( 'jquery' ) , '1.0'); 
	}

}

add_action( 'wp_enqueue_scripts', 'austeve_canvas_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'austeve_canvas_enqueue_script' );


function austeve_canvas_enqueue_admin_style() {
	wp_enqueue_style( 'austeve-canvas', plugin_dir_url( __FILE__ ). '/style-admin.css' , false , '4.6'); 
}

add_action( 'admin_enqueue_scripts', 'austeve_canvas_enqueue_admin_style' );

// Flushes rewrite rules on plugin activation.
register_activation_hook( __FILE__, array( 'AUSteve_My_Account_Reviews', 'install' ) );


?>