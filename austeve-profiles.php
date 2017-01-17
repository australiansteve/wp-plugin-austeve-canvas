<?php
// Register Custom Post Type
function austeve_create_canvasprofiles_post_type() {

	$labels = array(
		'name'                  => _x( 'Artist Profiles', 'Post Type General Name', 'austeve-canvas' ),
		'singular_name'         => _x( 'Artist Profile', 'Post Type Singular Name', 'austeve-canvas' ),
		'menu_name'             => __( 'Artist Profiles', 'austeve-canvas' ),
		'name_admin_bar'        => __( 'Artist Profile', 'austeve-canvas' ),
		'archives'              => __( 'Profile Archives', 'austeve-canvas' ),
		'attributes'            => __( 'Profile Attributes', 'austeve-canvas' ),
		'parent_item_colon'     => __( 'Parent Profile:', 'austeve-canvas' ),
		'all_items'             => __( 'All Artist Profiles', 'austeve-canvas' ),
		'add_new_item'          => __( 'Add New Artist Profile', 'austeve-canvas' ),
		'add_new'               => __( 'Add Artist Profile', 'austeve-canvas' ),
		'new_item'              => __( 'New Artist Profile', 'austeve-canvas' ),
		'edit_item'             => __( 'Edit Artist Profile', 'austeve-canvas' ),
		'update_item'           => __( 'Update Artist Profile', 'austeve-canvas' ),
		'view_item'             => __( 'View Artist Profile', 'austeve-canvas' ),
		'view_items'            => __( 'View Artist Profiles', 'austeve-canvas' ),
		'search_items'          => __( 'Search Artist Profile', 'austeve-canvas' ),
		'not_found'             => __( 'Not found', 'austeve-canvas' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'austeve-canvas' ),
		'featured_image'        => __( 'Featured Image', 'austeve-canvas' ),
		'set_featured_image'    => __( 'Set featured image', 'austeve-canvas' ),
		'remove_featured_image' => __( 'Remove featured image', 'austeve-canvas' ),
		'use_featured_image'    => __( 'Use as featured image', 'austeve-canvas' ),
		'insert_into_item'      => __( 'Insert into Artist Profile', 'austeve-canvas' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'austeve-canvas' ),
		'items_list'            => __( 'Artist Profiles list', 'austeve-canvas' ),
		'items_list_navigation' => __( 'Artist Profiles list navigation', 'austeve-canvas' ),
		'filter_items_list'     => __( 'Filter items list', 'austeve-canvas' ),
	);
	$args = array(
		'label'                 => __( 'Artist Profile', 'austeve-canvas' ),
		'description'           => __( 'Artist Profiles for Canvas & Cocktails paintings', 'austeve-canvas' ),
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
?>