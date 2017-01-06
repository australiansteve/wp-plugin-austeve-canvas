<?php

function austeve_populate_event_types() {

	$taxonomy = 'austeve_event_types';

	if (!term_exists( 'Public', $taxonomy ) ) 
	{
		wp_insert_term(
			'Public', // the term 
			$taxonomy, // the taxonomy
			array(
				'description'=> 'Public event',
				'slug' => 'austeve-event-public'
			)
		);
	}

	if (!term_exists( 'Private', $taxonomy ) ) 
	{
		wp_insert_term(
			'Private', // the term 
			$taxonomy, // the taxonomy
			array(
				'description'=> 'Private event',
				'slug' => 'austeve-event-private'
			)
		);
	}
}

add_action('admin_init','austeve_populate_event_types', 999);

//After an Event has been saved, update the austeve_event_types taxonomy with saved values
function austeve_update_post_event_type( $post_id ) {
    
	$taxonomy = 'austeve_event_types';
	error_log("austeve_update_post_event_type: ");

    // get new value
    $value = get_field('event_type');

    if ($value)
    {
		error_log($value['value']);
    	$assignedTerms = wp_get_post_terms( $post_id, $taxonomy );
    	$slug = 'austeve-event-'.$value['value'];
    	$term = term_exists( $slug, $taxonomy );

    	//Should always be the case, since we are only using 2 preset values (public/private)
    	if ($term)
    	{
			$alreadyAssigned = false;

	    	if ($assignedTerms)
	    	{
	    		foreach($assignedTerms as $assignedTerm)
	    		{
	    			if ($assignedTerm->term_id != $term['term_id'])
	    			{
	    				//var_dump($assignedTerm);
	    				error_log("Removing: Term ".$assignedTerm->term_id. " from post_id ".$post_id);
	    				wp_remove_object_terms( $post_id, $assignedTerm->term_id, $taxonomy );
	    			}
	    			else 
	    			{
	    				$alreadyAssigned = true;
	    			}
	    		}
	    	}

	    	if (!$alreadyAssigned)
	    	{
	    		error_log("Assigning: Term ".$term['term_id']. " to post_id ".$post_id);
	    		wp_set_post_terms( $post_id, array( intval($term['term_id']) ), $taxonomy );
	    	}
    	}

    }
}

add_action('acf/save_post', 'austeve_update_post_event_type', 20);

//Filter the admin call for Events based on the current users role(s) - Only display events that the user has access to
function austeve_filter_events_for_admins( $query ) {
    
	if (!is_admin() ||  //Not in admin screens 
		(isset($query->query_vars['post_type']) && $query->query_vars['post_type'] != 'austeve-events' ) || //Not on an events admin page
		current_user_can('edit_users') ) //Has access to all regions/venues
	{
		return $query;
	}

	//If we get here the current user has access to view venues, therefore they should be able to set Regions

	//Get current user roles
	$roles = wp_get_current_user()->roles;
	$my_terms = austeve_get_my_terms($roles);
	error_log(print_r( $my_terms, true ));

	//Get all venues in the users allowed region(s)
	$args = array(
		'posts_per_page'   => -1,
		'orderby'          => 'ID',
		'order'            => 'ASC',
		'post_type'        => 'austeve-venues',
		'post_status'      => 'publish',
		'suppress_filters' => false 
	);
	$venues_array = get_posts( $args );
	error_log(print_r( $venues_array, true ));

	$value_string = '';
	if (count($venues_array) > 0)
	{
		$venue_ids = array();
		foreach($venues_array as $venue)
		{
			$venue_ids[] = $venue->ID;
		}

		$value_string = (count($venue_ids) > 1) ? implode(",", $venue_ids) : $venue_ids[0];
	}

	$meta_query = array(
		array(
			'key'     => 'venue',
			'value'   => $value_string,
			'compare' => 'IN',
			'type'    => 'NUMERIC',
		),
	);

	$query->set( 'meta_query', $meta_query );
	error_log(print_r( $query, true ));
}

add_action( 'pre_get_posts', 'austeve_filter_events_for_admins' , 10, 1 );

?>

<?php

//Helper function that returns an array of term slugs for the given set of user roles
function austeve_get_my_terms($roles)
{
	//Pull our map from the options table
	$option_name = 'austeve_regions_role_terms' ;
	$role_map = json_decode( get_option( $option_name ), true);

	$my_terms = array();
	foreach($roles as $role)
	{
		if (array_key_exists($role, $role_map))
		{
			array_push($my_terms, $role_map[$role]);
		}
	}

	return $my_terms;
}


function austeve_add_roles_on_taxonomy_creation($term_id, $tt_id, $taxonomy) {

	if ($taxonomy == 'austeve_regions')
	{
		$term = get_term($term_id, $taxonomy);
		$term_slug = $term->slug;

		add_role( 
		   	'austeve_'.$term_slug.'_role', 
		   	$term->name.' Administrator', 
		   	array(
		        'read'         => true,  // true allows this capability
		        'read_venues'   => true,
		        'edit_venues'   => true,
		        'edit_others_venues'   => true,
		        'edit_private_venues'   => true,
		        'edit_published_venues'   => true,
		        'publish_venues'   => true,
		        'delete_venues'   => true,
		        'delete_others_venues'   => true,
		        'delete_private_venues'   => true,
		        'delete_published_venues'   => true,
		        'delete_posts' => false, // Use false to explicitly deny
		        'delete_pages' => false, // Use false to explicitly deny
		    ) 
		);
	}

}
add_action( 'edit_term', 'austeve_add_roles_on_taxonomy_creation', 10, 3 );
add_action( 'create_term', 'austeve_add_roles_on_taxonomy_creation', 10, 3 );


function austeve_delete_roles_on_taxonomy_deletion($term_id, $tt_id, $taxonomy, $deleted_term, $object_ids ) {

	if ($taxonomy == 'austeve_regions')
	{
		$term_slug = $deleted_term->slug;

		if( get_role( 'austeve_'.$term_slug.'_role' ) ){
			remove_role( 'austeve_'.$term_slug.'_role' );
		}

	}
}

add_action( 'delete_term', 'austeve_delete_roles_on_taxonomy_deletion', 10, 5 );

function austeve_add_venue_role_caps() {

	// Add the roles you'd like to administer the custom post types
	$roles = array('editor','administrator');

	//Get all regions
	$terms = get_terms( array(
	    'taxonomy' => 'austeve_regions',
	    'hide_empty' => false,
	) );

	// Loop through each region - adding the associated role to the list to update
	foreach($terms as $the_term) { 
		array_push($roles,  'austeve_'.$the_term->slug.'_role' );
	}

	// Loop through each role and assign capabilities
	foreach($roles as $the_role) { 

		$role = get_role($the_role);

		$role->add_cap( 'read' );
		$role->add_cap( 'read_venues');
		$role->add_cap( 'read_private_venues' );
		$role->add_cap( 'edit_venues' );
		$role->add_cap( 'edit_others_venues' );
		$role->add_cap( 'edit_private_venues' );
		$role->add_cap( 'edit_published_venues' );
		$role->add_cap( 'publish_venues' );
		$role->add_cap( 'delete_venues' );
		$role->add_cap( 'delete_others_venues' );
		$role->add_cap( 'delete_private_venues' );
		$role->add_cap( 'delete_published_venues' );

		//Also add capabilities for events
		$role->add_cap( 'read_events');
		$role->add_cap( 'read_private_events' );
		$role->add_cap( 'edit_events' );
		$role->add_cap( 'edit_others_events' );
		$role->add_cap( 'edit_private_events' );
		$role->add_cap( 'edit_published_events' );
		$role->add_cap( 'publish_events' );
		$role->add_cap( 'delete_events' );
		$role->add_cap( 'delete_others_events' );
		$role->add_cap( 'delete_private_events' );
		$role->add_cap( 'delete_published_events' );

	}

}
 
add_action('admin_init','austeve_add_venue_role_caps',999);


//Store the relationship between the custom roles we've created and the taxonomy that relates to. So that we can filter taxonomies displayed to the admins
function austeve_save_role_term_relationships() {

	$option_name = 'austeve_regions_role_terms' ;
	$relationship_array = array();

	//Get all regions
	$terms = get_terms( array(
	    'taxonomy' => 'austeve_regions',
	    'hide_empty' => false,
	) );

	// Loop through each region - adding the associated role to the list to update
	foreach($terms as $the_term) { 
		$relationship_array['austeve_'.$the_term->slug.'_role'] = $the_term->slug;
	}


	if ( get_option( $option_name ) !== false ) {

	    // The option already exists, so we just update it.
	    update_option( $option_name, json_encode($relationship_array) );

	} else {

	    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
	    $deprecated = null;
	    $autoload = 'no';
	    add_option( $option_name, json_encode($relationship_array), $deprecated, $autoload );
	}
}

add_action('admin_init','austeve_save_role_term_relationships',999);


// Add Project Type column to admin header
function austeve_venues_columns_head($defaults) {
    $defaults['region'] = 'Region';

    return $defaults;
}
add_filter('manage_austeve-venues_posts_columns', 'austeve_venues_columns_head');
 
// Add Project Type column content to admin table
function austeve_venues_columns_content($column_name, $post_ID) {
    if ($column_name == 'region') {

    	$term_list = wp_get_post_terms($post_ID, 'austeve_regions', array("fields" => "all"));
    	$string_list = "";
		foreach($term_list as $term_single) {
			$string_list .= $term_single->name.", "; //do something here
		}

		echo substr($string_list, 0, -2);
    }
}
add_action('manage_austeve-venues_posts_custom_column', 'austeve_venues_columns_content', 10, 2);


//Filter the admin call for Regions based on the current users role(s) - Only display regions that the user has access to
function austeve_filter_regions_for_admins( $args, $taxonomies ) {
    
	if (!is_admin() ||  //Not in admin screens 
		(isset($query->query_vars['post_type']) && $query->query_vars['post_type'] != 'austeve-venues' ) || //Not on a venues admin page
		!current_user_can('edit_venues') || //Cannot edit venues
		current_user_can('edit_users')) //Has access to all regions
	{
		return $args;
	}

	//If we get here the current user has access to edit venues, therefore they should be able to set Regions

	//Get current user roles
	$roles = wp_get_current_user()->roles;

	//Set slug filter for each term 
	$args['slug'] = austeve_get_my_terms($roles);

    return $args;
}

add_filter( 'get_terms_args', 'austeve_filter_regions_for_admins' , 10, 2 );

//Filter the admin call for Regions based on the current users role(s) - Only display regions that the user has access to
function austeve_filter_venues_for_admins( $query ) {
    
	if (!is_admin() ||  //Not in admin screens 
		(isset($query->query_vars['post_type']) && $query->query_vars['post_type'] != 'austeve-venues' ) || //Not on a venues admin page
		current_user_can('edit_users') ) //Has access to all regions/venues
	{
		return $query;
	}

	//If we get here the current user has access to view venues, therefore they should be able to set Regions

	//Get current user roles
	$roles = wp_get_current_user()->roles;

	//Create new tax_query
	$tax_query = array (
		array (
			'taxonomy'         => 'austeve_regions',
			'terms'            => austeve_get_my_terms($roles),
			'field'            => 'slug',
			'operator'         => 'IN',
		),
	);

	$query->set( 'tax_query', $tax_query );
}

add_action( 'pre_get_posts', 'austeve_filter_venues_for_admins' , 10, 1 );

?>