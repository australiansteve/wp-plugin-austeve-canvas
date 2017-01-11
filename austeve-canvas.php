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


register_activation_hook( __FILE__, 'austeve_add_roles_on_plugin_activation' );


function austeve_canvas_enqueue_style() {
	wp_enqueue_style( 'austeve-canvas', plugin_dir_url( __FILE__ ). '/style.css' , false , '4.6'); 
}

function austeve_canvas_enqueue_script() {
	//wp_enqueue_script( 'my-js', 'filename.js', false );
}

add_action( 'wp_enqueue_scripts', 'austeve_canvas_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'austeve_canvas_enqueue_script' );

?>