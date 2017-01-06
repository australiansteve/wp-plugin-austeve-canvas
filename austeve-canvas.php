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

?>