<?php

/*
* Creating a function to create our CPT
*/

function austeve_create_venues_post_type() {

// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Venues', 'Post Type General Name', 'austeve-venues' ),
		'singular_name'       => _x( 'Venue', 'Post Type Singular Name', 'austeve-venues' ),
		'menu_name'           => __( 'Venues', 'austeve-venues' ),
		'parent_item_colon'   => __( 'Parent Venue', 'austeve-venues' ),
		'all_items'           => __( 'All Venues', 'austeve-venues' ),
		'view_item'           => __( 'View Venue', 'austeve-venues' ),
		'add_new_item'        => __( 'Add New Venue', 'austeve-venues' ),
		'add_new'             => __( 'Add New', 'austeve-venues' ),
		'edit_item'           => __( 'Edit Venue', 'austeve-venues' ),
		'update_item'         => __( 'Update Venue', 'austeve-venues' ),
		'search_items'        => __( 'Search Venue', 'austeve-venues' ),
		'not_found'           => __( 'Not Found', 'austeve-venues' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'austeve-venues' ),
	);
	
// Set other options for Custom Post Type
	
	$args = array(
		'label'               => __( 'Venues', 'austeve-venues' ),
		'description'         => __( 'Event Venues', 'austeve-venues' ),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'author', 'revisions', ),
		// You can associate this CPT with a taxonomy or custom taxonomy. 
		'taxonomies'          => array( 'territories'),
		/* A hierarchical CPT is like Pages and can have
		* Parent and child items. A non-hierarchical CPT
		* is like Posts.
		*/	
		'hierarchical'        => false,
		'rewrite'           => array( 'slug' => 'venues' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => array( 'venue' , 'venues' ),
        'map_meta_cap'        => true,
        'menu_icon'				=> 'dashicons-location',


	);
	
	// Registering your Custom Post Type
	register_post_type( 'austeve-venues', $args );


	$taxonomyLabels = array(
		'name'              => _x( 'Territories', 'taxonomy general name' ),
		'singular_name'     => _x( 'Territory', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Territories' ),
		'all_items'         => __( 'All Territories' ),
		'parent_item'       => __( 'Parent Territory' ),
		'parent_item_colon' => __( 'Parent Territory:' ),
		'edit_item'         => __( 'Edit Territory' ),
		'update_item'       => __( 'Update Territory' ),
		'add_new_item'      => __( 'Add New Territory' ),
		'new_item_name'     => __( 'New Territory Name' ),
		'menu_name'         => __( 'Territories' ),
	);

	$taxonomyArgs = array(

		'label'               => __( 'austeve_territories', 'austeve-venues' ),
		'labels'              => $taxonomyLabels,
		'show_admin_column'	=> false,
		'hierarchical' 		=> true,
		'rewrite'           => array( 'slug' => 'territories' ),
		'capabilities'		=> array(
							    'manage_terms' => 'edit_users',
							    'edit_terms' => 'edit_users',
							    'delete_terms' => 'edit_users',
							    'assign_terms' => 'edit_venues'
							 )
		);

	register_taxonomy( 'austeve_territories', 'austeve-venues', $taxonomyArgs );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/

add_action( 'init', 'austeve_create_venues_post_type', 0 );

function venue_include_template_function( $template_path ) {
    if ( get_post_type() == 'austeve-venues' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-venues.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-venues.php';
            }
        }
        else if ( is_archive() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'archive-venues.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/archive-venues.php';
            }
        }
    }
    return $template_path;
}
add_filter( 'template_include', 'venue_include_template_function', 1 );

function venue_filter_archive_title( $title ) {

    if( is_tax('austeve_territories' ) ) {

        $title = single_cat_title( '', false ) . ' venues';

    }
    else if ( is_post_type_archive('austeve-venues') ) {

        $title = post_type_archive_title( '', false );

    }

    return $title;

}

add_filter( 'get_the_archive_title', 'venue_filter_archive_title');

function austeve_venues_enqueue_style() {
	wp_enqueue_style( 'austeve-venues', plugin_dir_url( __FILE__ ). '/style.css' , false , '4.6'); 
}

function austeve_venues_enqueue_script() {
	//wp_enqueue_script( 'my-js', 'filename.js', false );
}

add_action( 'wp_enqueue_scripts', 'austeve_venues_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'austeve_venues_enqueue_script' );

?>