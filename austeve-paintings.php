<?php
// Register Custom Post Type
function austeve_create_paintings_post_type() {

	$labels = array(
		'name'                  => _x( 'Paintings', 'Post Type General Name', 'austeve-canvas' ),
		'singular_name'         => _x( 'Painting', 'Post Type Singular Name', 'austeve-canvas' ),
		'menu_name'             => __( 'Paintings', 'austeve-canvas' ),
		'name_admin_bar'        => __( 'Painting', 'austeve-canvas' ),
		'archives'              => __( 'Painting Archives', 'austeve-canvas' ),
		'attributes'            => __( 'Painting Attributes', 'austeve-canvas' ),
		'parent_item_colon'     => __( 'Parent Painting:', 'austeve-canvas' ),
		'all_items'             => __( 'All Paintings', 'austeve-canvas' ),
		'add_new_item'          => __( 'Add New Painting', 'austeve-canvas' ),
		'add_new'               => __( 'Add Painting', 'austeve-canvas' ),
		'new_item'              => __( 'New Painting', 'austeve-canvas' ),
		'edit_item'             => __( 'Edit Painting', 'austeve-canvas' ),
		'update_item'           => __( 'Update Painting', 'austeve-canvas' ),
		'view_item'             => __( 'View Painting', 'austeve-canvas' ),
		'view_items'            => __( 'View Paintings', 'austeve-canvas' ),
		'search_items'          => __( 'Search Painting', 'austeve-canvas' ),
		'not_found'             => __( 'Not found', 'austeve-canvas' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'austeve-canvas' ),
		'featured_image'        => __( 'Featured Image', 'austeve-canvas' ),
		'set_featured_image'    => __( 'Set featured image', 'austeve-canvas' ),
		'remove_featured_image' => __( 'Remove featured image', 'austeve-canvas' ),
		'use_featured_image'    => __( 'Use as featured image', 'austeve-canvas' ),
		'insert_into_item'      => __( 'Insert into Painting', 'austeve-canvas' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'austeve-canvas' ),
		'items_list'            => __( 'Paintings list', 'austeve-canvas' ),
		'items_list_navigation' => __( 'Paintings list navigation', 'austeve-canvas' ),
		'filter_items_list'     => __( 'Filter items list', 'austeve-canvas' ),
	);
	$args = array(
		'label'                 => __( 'Painting', 'austeve-canvas' ),
		'description'           => __( 'Paintings for Canvas & Cocktails events', 'austeve-canvas' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'author', 'thumbnail', 'revisions', ),
		'taxonomies'            => array( 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 10,
		'menu_icon'             => 'dashicons-admin-customizer',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => 'paintings',
		'rewrite'           	=> array( 'slug' => 'paintings' ),
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'     => array( 'painting' , 'paintings' ),
        'map_meta_cap'        => true,
	);
	register_post_type( 'austeve-paintings', $args );

}
add_action( 'init', 'austeve_create_paintings_post_type', 0 );
?>