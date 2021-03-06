<?php

/*
* Creating a function to create our CPT
*/

function austeve_create_venues_post_type() {

// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Venues', 'Post Type General Name', 'austeve-canvas' ),
		'singular_name'       => _x( 'Venue', 'Post Type Singular Name', 'austeve-canvas' ),
		'menu_name'           => __( 'Venues', 'austeve-canvas' ),
		'parent_item_colon'   => __( 'Parent Venue', 'austeve-canvas' ),
		'all_items'           => __( 'All Venues', 'austeve-canvas' ),
		'view_item'           => __( 'View Venue', 'austeve-canvas' ),
		'add_new_item'        => __( 'Add New Venue', 'austeve-canvas' ),
		'add_new'             => __( 'Add New', 'austeve-canvas' ),
		'edit_item'           => __( 'Edit Venue', 'austeve-canvas' ),
		'update_item'         => __( 'Update Venue', 'austeve-canvas' ),
		'search_items'        => __( 'Search Venue', 'austeve-canvas' ),
		'not_found'           => __( 'Not Found', 'austeve-canvas' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'austeve-canvas' ),
	);
	
// Set other options for Custom Post Type
	
	$args = array(
		'label'               => __( 'Venues', 'austeve-canvas' ),
		'description'         => __( 'Event venues', 'austeve-canvas' ),
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
		'meta_box_cb'       => false,
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


function austeve_update_wc_product_stock( $post_id ) {

	$venue_post = get_post($post_id);
	$venue_capacity = get_field('capacity');

    // If this isn't a 'venue' post, don't update anything.
    if ( "austeve-venues" != $venue_post->post_type ) return;

    //We only want to update future event capcities...
    $args = austeve_event_query_args(
		array('number_of_posts' => -1,
	        'future_events' => 'true',
	        'past_events' => 'false',
	        'order' => 'ASC'
    	)
    );

    ob_start();
    $query = new WP_Query( $args );
	
    if( $query->have_posts() ){

		//loop over EVENT query results
        while( $query->have_posts() ){
            $query->the_post();
            
            //If the event is at the venue being edited, and the event does NOT have a custom capacity
            if (get_field('venue')->ID == $post_id && !get_field('custom_capacity', get_the_ID()))
            {
            	//Get the WC product from the event
    			$product_id = get_field('wc_product', get_the_ID());

    			//Find how many tickets have been sold
            	$sold_so_far = get_post_meta($product_id, 'total_sales', true);
				error_log("Stock: ".print_r($sold_so_far, true)." vs ".$venue_capacity);
				$still_remaining = $venue_capacity - $sold_so_far;
				update_post_meta( $product_id, '_stock', ($still_remaining > 0) ? $still_remaining : '0'); //Update the WC product stock count
            }
            //If the event does have a custom capacity the stock level will be managed by the austeve_update_wc_product() function
        }
    }
    
    wp_reset_postdata();
    return ob_get_clean();

}
add_action('acf/save_post', 'austeve_update_wc_product_stock', 20);

function austeve_pre_get_posts_order_venues( $query ) {
	
	// do not modify queries in the admin, or if viewing a single event page, or if being displayed from shortcode
	if( is_admin() || is_single() || array_key_exists('do_not_filter', $query->query) ) {
		
		return $query;
		
	}

	//If here we are basically just modifying the archive page of venues 
	// only modify queries for 'venue' post type
	if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'austeve-venues' ) {
		
		//Find the venues, order by title
		$query->set('posts_per_page', -1);	
		$query->set('orderby', 'title');	
		$query->set('order', 'ASC');	 

	}

	// return
	return $query;

}

add_action('pre_get_posts', 'austeve_pre_get_posts_order_venues');
?>