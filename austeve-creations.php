<?php
// Register Custom Post Type
function austeve_create_creations_post_type() {

	$labels = array(
		'name'                  => _x( 'Creations', 'Post Type General Name', 'austeve-canvas' ),
		'singular_name'         => _x( 'Creation', 'Post Type Singular Name', 'austeve-canvas' ),
		'menu_name'             => __( 'Creations', 'austeve-canvas' ),
		'name_admin_bar'        => __( 'Creation', 'austeve-canvas' ),
		'archives'              => __( 'Creation Archives', 'austeve-canvas' ),
		'attributes'            => __( 'Creation Attributes', 'austeve-canvas' ),
		'parent_item_colon'     => __( 'Parent Creation:', 'austeve-canvas' ),
		'all_items'             => __( 'All Creations', 'austeve-canvas' ),
		'add_new_item'          => __( 'Add New Creation', 'austeve-canvas' ),
		'add_new'               => __( 'Add Creation', 'austeve-canvas' ),
		'new_item'              => __( 'New Creation', 'austeve-canvas' ),
		'edit_item'             => __( 'Edit Creation', 'austeve-canvas' ),
		'update_item'           => __( 'Update Creation', 'austeve-canvas' ),
		'view_item'             => __( 'View Creation', 'austeve-canvas' ),
		'view_items'            => __( 'View Creations', 'austeve-canvas' ),
		'search_items'          => __( 'Search Creation', 'austeve-canvas' ),
		'not_found'             => __( 'Not found', 'austeve-canvas' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'austeve-canvas' ),
		'featured_image'        => __( 'Featured Image', 'austeve-canvas' ),
		'set_featured_image'    => __( 'Set featured image', 'austeve-canvas' ),
		'remove_featured_image' => __( 'Remove featured image', 'austeve-canvas' ),
		'use_featured_image'    => __( 'Use as featured image', 'austeve-canvas' ),
		'insert_into_item'      => __( 'Insert into Creation', 'austeve-canvas' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'austeve-canvas' ),
		'items_list'            => __( 'Creations list', 'austeve-canvas' ),
		'items_list_navigation' => __( 'Creations list navigation', 'austeve-canvas' ),
		'filter_items_list'     => __( 'Filter items list', 'austeve-canvas' ),
	);
	$args = array(
		'label'                 => __( 'Creation', 'austeve-canvas' ),
		'description'           => __( 'Creations for Canvas & Cocktails events', 'austeve-canvas' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'author', 'thumbnail', 'revisions', ),
		'taxonomies'            => array( 'creation_tags' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 10,
		'menu_icon'             => 'dashicons-admin-customizer',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => 'creations',
		'rewrite'           	=> array( 'slug' => 'creations' ),
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'     => array( 'creation' , 'creations' ),
        'map_meta_cap'        => true,
	);
	register_post_type( 'austeve-creations', $args );

	// Add new taxonomy, make it hierarchical (like categories)
	$categoryLabels = array(
		'name'              => _x( 'Categories', 'taxonomy general name', 'austeve-canvas' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name', 'austeve-canvas' ),
		'search_items'      => __( 'Search Categories', 'austeve-canvas' ),
		'all_items'         => __( 'All Categories', 'austeve-canvas' ),
		'parent_item'       => __( 'Parent Category', 'austeve-canvas' ),
		'parent_item_colon' => __( 'Parent Category:', 'austeve-canvas' ),
		'edit_item'         => __( 'Edit Category', 'austeve-canvas' ),
		'update_item'       => __( 'Update Category', 'austeve-canvas' ),
		'add_new_item'      => __( 'Add New Category', 'austeve-canvas' ),
		'new_item_name'     => __( 'New Category Name', 'austeve-canvas' ),
		'menu_name'         => __( 'Categories', 'austeve-canvas' ),
	);

	$categoryArgs = array(
		'hierarchical'      => true,
		'label'               => __( 'austeve_creation_categories', 'austeve-canvas' ),
		'labels'            => $categoryLabels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'creation-categories' ),
		'capabilities'		=> array(
							    'manage_terms' => 'edit_users',
							    'edit_terms' => 'edit_users',
							    'delete_terms' => 'edit_users',
							    'assign_terms' => 'edit_creations'
							 )
	);

	register_taxonomy( 'austeve_creation_categories', array( 'austeve-creations' ), $categoryArgs );

	$taxonomyLabels = array(
		'name'              => _x( 'Tags', 'taxonomy general name' ),
		'singular_name'     => _x( 'Tag', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Tags' ),
		'all_items'         => __( 'All Tags' ),
		'parent_item'       => __( 'Parent Tag' ),
		'parent_item_colon' => __( 'Parent Tag:' ),
		'edit_item'         => __( 'Edit Tag' ),
		'update_item'       => __( 'Update Tag' ),
		'add_new_item'      => __( 'Add New Tag' ),
		'new_item_name'     => __( 'New Tag Name' ),
		'menu_name'         => __( 'Tags' ),
	);

	$taxonomyArgs = array(

		'label'               => __( 'austeve_creation_tags', 'austeve-canvas' ),
		'labels'              => $taxonomyLabels,
		'show_admin_column'	=> false,
		'hierarchical' 		=> false,
		'show_ui'			=> true,
		'rewrite'           => array( 'slug' => 'creation-tags' ),
		'capabilities'		=> array(
							    'manage_terms' => 'edit_users',
							    'edit_terms' => 'edit_users',
							    'delete_terms' => 'edit_users',
							    'assign_terms' => 'edit_creations'
							 )
		);

	register_taxonomy( 'austeve_creation_tags', 'austeve-creations', $taxonomyArgs );

}
add_action( 'init', 'austeve_create_creations_post_type', 0 );


function creation_include_template_function( $template_path ) {
    if ( get_post_type() == 'austeve-creations' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-creations.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-creations.php';
            }
        }
        else if ( is_archive() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'archive-creations.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/archive-creations.php';
            }
        }
    }
    return $template_path;
}
add_filter( 'template_include', 'creation_include_template_function', 1 );

function creation_filter_archive_title( $title ) {

    if ( is_post_type_archive('austeve-creations') ) {

        $title = post_type_archive_title( '', false );

    }

    return $title;

}

add_filter( 'get_the_archive_title', 'creation_filter_archive_title');

function austeve_filter_objects_creations( $query ) {
    
    $numCreationPosts = 12;

	if ( is_admin() )
	{
		return $query;
	}

	//If we get here we are on the front end
	if (isset($query->query_vars['post_type']))
	{
		if ( $query->query_vars['post_type'] == 'austeve-creations' )
		{
			error_log("Get 1 creations!");
			//Always get $numCreationPosts creations at a time
			$query->set( 'posts_per_page', $numCreationPosts );
		}
	}
	if (array_key_exists('austeve_creation_categories', $query->query) || array_key_exists('austeve_creation_tags', $query->query))
	{
		error_log("Get 1 creations!");
		//Always get $numCreationPosts creations at a time
		$query->set( 'posts_per_page', $numCreationPosts );
	}
}

add_action( 'pre_get_posts', 'austeve_filter_objects_creations' , 10, 1 );

?>