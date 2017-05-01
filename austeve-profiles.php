<?php
// Register Custom Post Type
function austeve_create_canvasprofiles_post_type() {

	$labels = array(
		'name'                  => _x( 'Profiles', 'Post Type General Name', 'austeve-canvas' ),
		'singular_name'         => _x( 'Profile', 'Post Type Singular Name', 'austeve-canvas' ),
		'menu_name'             => __( 'Profiles', 'austeve-canvas' ),
		'name_admin_bar'        => __( 'Profile', 'austeve-canvas' ),
		'archives'              => __( 'Profile Archives', 'austeve-canvas' ),
		'attributes'            => __( 'Profile Attributes', 'austeve-canvas' ),
		'parent_item_colon'     => __( 'Parent Profile:', 'austeve-canvas' ),
		'all_items'             => __( 'All Profiles', 'austeve-canvas' ),
		'add_new_item'          => __( 'Add New Profile', 'austeve-canvas' ),
		'add_new'               => __( 'Add Profile', 'austeve-canvas' ),
		'new_item'              => __( 'New Profile', 'austeve-canvas' ),
		'edit_item'             => __( 'Edit Profile', 'austeve-canvas' ),
		'update_item'           => __( 'Update Profile', 'austeve-canvas' ),
		'view_item'             => __( 'View Profile', 'austeve-canvas' ),
		'view_items'            => __( 'View Profiles', 'austeve-canvas' ),
		'search_items'          => __( 'Search Profile', 'austeve-canvas' ),
		'not_found'             => __( 'Not found', 'austeve-canvas' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'austeve-canvas' ),
		'featured_image'        => __( 'Featured Image', 'austeve-canvas' ),
		'set_featured_image'    => __( 'Set featured image', 'austeve-canvas' ),
		'remove_featured_image' => __( 'Remove featured image', 'austeve-canvas' ),
		'use_featured_image'    => __( 'Use as featured image', 'austeve-canvas' ),
		'insert_into_item'      => __( 'Insert into Profile', 'austeve-canvas' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'austeve-canvas' ),
		'items_list'            => __( 'Profiles list', 'austeve-canvas' ),
		'items_list_navigation' => __( 'Profiles list navigation', 'austeve-canvas' ),
		'filter_items_list'     => __( 'Filter items list', 'austeve-canvas' ),
	);
	$args = array(
		'label'                 => __( 'Profile', 'austeve-canvas' ),
		'description'           => __( 'Profiles for hosts and artists', 'austeve-canvas' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'author', 'revisions', ),
		'taxonomies'            => array( ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 10,
		'menu_icon'             => 'dashicons-universal-access',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => 'profiles',
		'rewrite'           	=> array( 'slug' => 'profiles' ),
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'     => array( 'profile' , 'profiles' ),
        'map_meta_cap'        => true,
	);
	register_post_type( 'austeve-profiles', $args );

}
add_action( 'init', 'austeve_create_canvasprofiles_post_type', 0 );

function profile_include_template_function( $template_path ) {
    if ( get_post_type() == 'austeve-profiles' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-profiles.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-profiles.php';
            }
        }
        else if ( is_archive() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'archive-profiles.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/archive-profiles.php';
            }
        }
    }
    return $template_path;
}
add_filter( 'template_include', 'profile_include_template_function', 1 );

function profile_filter_archive_title( $title ) {

	if ( is_post_type_archive('austeve-profiles') ) {

        $title = post_type_archive_title( '', false );

    }

    return $title;

}

add_filter( 'get_the_archive_title', 'profile_filter_archive_title');

function austeve_get_host_events($user_id)
{
	//Get all host events to calculate rating
	$args = array(
        'posts_per_page' => -1,
        'post_type' => 'austeve-events',
        'post_status' => array('publish'),
        'orderby' => 'name',
        'order' => 'ASC',
        'do_not_filter' => 'true',
    );

	$meta_query = array('relation' => 'AND');

	$past_events_query = array(
        'key'           => 'start_time',
        'compare'       => '<=',
        'value'         => date('Y-m-d H:i:s'),
        'type'          => 'DATETIME',
    );
    $meta_query[] = $past_events_query;

    $host_query = array(
        'key'           => 'host',
        'compare'       => '=',
        'value'         => $user_id,
        'type'          => 'NUMERIC',
    );
    $meta_query[] = $host_query;

    $args['meta_query'] = $meta_query;
     return get_posts( $args );
}

function austeve_calculate_host_rating($user_id)
{
	$hosts_events = austeve_get_host_events($user_id);

    $eventsWithRatingsCount = 0;
    $hostRatingTotal = 0;
    foreach($hosts_events as $event)
    {
        error_log("Host".$user_id." event:".$event->post_title);
        $hostEventRating = get_field('host_rating', $event->ID);

        if($hostEventRating && $hostEventRating >= 0)
        {
        	$hostRatingTotal += floatval($hostEventRating);
    		$eventsWithRatingsCount++;
        }
    }
    $hostRating = ($hostRatingTotal > 0 && $eventsWithRatingsCount > 0) ? "Average rating: ".round(($hostRatingTotal / $eventsWithRatingsCount), 2)."/5" : 'No ratings';

    return $hostRating;
}

function austeve_get_number_host_events($user_id)
{
	$hosts_events = austeve_get_host_events($user_id);
	$host_profile = austeve_get_host_profile($user_id);

	if ($host_profile)
	{
		$pre_launch_events = get_field('events_hosted_before_website_launch', $host_profile->ID);
		if ($pre_launch_events) {
			return count($hosts_events) + $pre_launch_events;
		}
	}
	return count($hosts_events);
}

function austeve_get_host_profile($user_id)
{
    $host_profile_args = array(
        'posts_per_page' => -1,
        'post_type' => 'austeve-profiles',
        'post_status' => array('publish'),
        'orderby' => 'name',
        'order' => 'ASC',
        'do_not_filter' => 'true',
    );

	$meta_query = array('relation' => 'AND');

	$host_profile_query = array(
        'key'           => 'user',
        'compare'       => '=',
        'value'         => $user_id,
        'type'          => 'NUMERIC',
    );
    $meta_query[] = $host_profile_query;
    $host_profile_args['meta_query'] = $meta_query;

	$host_profiles = get_posts( $host_profile_args );

	if (count($host_profiles) > 0)
	{
		return $host_profiles[0];
	}
	return null;
}
?>