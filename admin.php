<?php

#region Plugin activation
/**
 *  Create roles when plugin is activated
 */
function austeve_add_roles_on_plugin_activation() {

	//remove_role('austeve_territory_admin_role');

	error_log("Creating roles");
	$result = add_role( 
		'austeve_territory_admin_role', 
		'Territory Administrator', 
		array(
			'read'         => true,                      

			'read_venues'   => true,					//Venue permissions
			'read_private_venues'   => true,
			'edit_venues'   => true,
			'edit_others_venues'   => true,
			'edit_private_venues'   => true,
			'edit_published_venues'   => true,
			'publish_venues'   => true,
			'delete_venues'   => true,
			'delete_others_venues'   => true,
			'delete_private_venues'   => true,
			'delete_published_venues'   => true,			

			'read_events'   => true,					//Event permissions
			'read_private_events'   => true,
			'edit_events'   => true,
			'edit_others_events'   => true,
			'edit_private_events'   => true,
			'edit_published_events'   => true,
			'publish_events'   => true,
			'delete_events'   => true,
			'delete_others_events'   => true,
			'delete_private_events'   => true,
			'delete_published_events'   => true,

			'read_paintings'   => true,					//Painting permissions
			'read_private_paintings'   => true,
			'edit_paintings'   => true,
			'edit_others_paintings'   => true,
			'edit_private_paintings'   => true,
			'edit_published_paintings'   => true,
			'publish_paintings'   => true,
			'delete_paintings'   => true,
			'delete_others_paintings'   => true,
			'delete_private_paintings'   => true,
			'delete_published_paintings'   => true,

			'read_profiles'   => true,					//Profile permissions
			'read_private_profiles'   => true,
			'edit_profiles'   => true,
			'edit_others_profiles'   => true,
			'edit_private_profiles'   => true,
			'edit_published_profiles'   => true,
			'publish_profiles'   => true,
			'delete_profiles'   => true,
			'delete_others_profiles'   => true,
			'delete_private_profiles'   => true,
			'delete_published_profiles'   => true,

			'delete_posts' => false, 					// Use false to explicitly deny
			'delete_pages' => false, 					// Use false to explicitly deny
		) 
	);

	if ( null !== $result ) {
	    error_log( 'Territory Admin role created!' );
	}
	else {
	    error_log( 'Territory Admin role already exists.' );
	}

	//remove_role('austeve_event_host_role');
	$result = add_role( 
		'austeve_event_host_role', 
		'Event Host', 
		array(
			'read'         => true,                   
			
			'delete_posts' => false, 					// Use false to explicitly deny
			'delete_pages' => false, 					// Use false to explicitly deny
		) 
	);

	if ( null !== $result ) {
	    error_log( 'Event Host role created!' );
	}
	else {
	    error_log( 'Event Host role already exists.' );
	}

	//Add permissions to Administrator role too
    $role = get_role( 'administrator' );

    //Venues
    $role->add_cap( 'read_venues' ); 
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

    //Events
    $role->add_cap( 'read_events' ); 
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

    //Paintings
    $role->add_cap( 'read_paintings' ); 
    $role->add_cap( 'read_private_paintings' ); 
    $role->add_cap( 'edit_paintings' ); 
    $role->add_cap( 'edit_others_paintings' ); 
    $role->add_cap( 'edit_private_paintings' ); 
    $role->add_cap( 'edit_published_paintings' ); 
    $role->add_cap( 'publish_paintings' ); 
    $role->add_cap( 'delete_paintings' ); 
    $role->add_cap( 'delete_others_paintings' ); 
    $role->add_cap( 'delete_private_paintings' ); 
    $role->add_cap( 'delete_published_paintings' ); 

    //Profiles
    $role->add_cap( 'read_profiles' ); 
    $role->add_cap( 'read_private_profiles' ); 
    $role->add_cap( 'edit_profiles' ); 
    $role->add_cap( 'edit_others_profiles' ); 
    $role->add_cap( 'edit_private_profiles' ); 
    $role->add_cap( 'edit_published_profiles' ); 
    $role->add_cap( 'publish_profiles' ); 
    $role->add_cap( 'delete_profiles' ); 
    $role->add_cap( 'delete_others_profiles' ); 
    $role->add_cap( 'delete_private_profiles' ); 
    $role->add_cap( 'delete_published_profiles' ); 

}
#endregion Plugin activation

#region Admin_init
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
#endregion Admin_init

#region acf/save_post actions
//After an Event has been saved, update the austeve_event_types taxonomy with saved values

function austeve_assign_term_to_post($post_id, $term, $taxonomy, $assignedTerms) {

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
	else 
	{
		error_log("Cannot assign term to post: ".$post_id);
	}
}

function austeve_update_post_event_type( $post_id ) {
    
    $value = get_field('event_type');

    if ($value)
    {
		$taxonomy = 'austeve_event_types';
    	$assignedTerms = wp_get_post_terms( $post_id, $taxonomy );
    	$slug = 'austeve-event-'.$value['value'];
    	$term = term_exists( $slug, $taxonomy );

    	austeve_assign_term_to_post($post_id, $term, $taxonomy, $assignedTerms);
    }

}
add_action('acf/save_post', 'austeve_update_post_event_type', 20);

#endregion acf/save_post actions

#region Admin list filters to add columns to lists

/* Venues */
function austeve_venues_columns_head($defaults) {

	$res = array_slice($defaults, 0, 2, true) +
	    array("territory" => "Territory") +
	    array_slice($defaults, 2, count($defaults) - 1, true) ;

	$defaults = $res;

	//Remove the old date column
	unset($defaults['date']);

    return $defaults;
}
add_filter('manage_austeve-venues_posts_columns', 'austeve_venues_columns_head');
 
function austeve_venues_columns_content($column_name, $post_ID) {

    if ($column_name == 'territory') {

		$term_args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'names');
    	$term_list = wp_get_post_terms($post_ID, 'austeve_territories', $term_args);
    	if (count($term_list) > 1)
			echo implode(",", $term_list);
		else if (count($term_list) > 0)
			echo $term_list[0];
    }
}
add_action('manage_austeve-venues_posts_custom_column', 'austeve_venues_columns_content', 10, 2);

/* Events */
function austeve_events_columns_head($defaults) {

	$res = array_slice($defaults, 0, 2, true) +
	    array("venue" => "Venue") +
	    array("territory" => "Territory") +
	    array("event_date" => "Event Date") +
	    array_slice($defaults, 2, count($defaults) - 1, true) ;

	$defaults = $res;

	//Remove the old date column
	unset($defaults['date']);

    return $defaults;
}
add_filter('manage_austeve-events_posts_columns', 'austeve_events_columns_head');

function austeve_events_columns_content($column_name, $post_ID) {

    if ($column_name == 'venue') {

		//error_log(print_r(get_field('venue', $post_ID), true));
		echo get_field('venue', $post_ID)->post_title;

    }
    else if ($column_name == 'territory') {

    	$taxonomy_name = 'austeve_territories';
    	$venue_args = array('orderby' => 'slug', 'order' => 'ASC', 'fields' => 'names');
		$venue_territories = wp_get_object_terms( get_field('venue', $post_ID)->ID,  $taxonomy_name, $venue_args );
		echo $venue_territories[0];

    }
    else if ($column_name == 'event_date') {

		echo get_field('start_time', $post_ID);

    }
}
add_action('manage_austeve-events_posts_custom_column', 'austeve_events_columns_content', 10, 2);

/* Paintings */
function austeve_paintings_columns_head($defaults) {

	$res = array_slice($defaults, 0, 2, true) +
	    array("artist" => "Artist") +
	    array("painting_tags" => "Tags") +
	    array_slice($defaults, 2, count($defaults) - 1, true) ;

	$defaults = $res;

	//Remove the old date column
	unset($defaults['date']);

    return $defaults;
}
add_filter('manage_austeve-paintings_posts_columns', 'austeve_paintings_columns_head');

function austeve_paintings_columns_content($column_name, $post_ID) {

    if ($column_name == 'artist') {

    	$artist = get_field('artist', $post_ID);
    	error_log(print_r($artist, true));
    	if ($artist)
			echo $artist->post_title;

    }
    else if ($column_name == 'painting_tags') {

    	$taxonomy_name = 'austeve_painting_tags';
    	$tags_args = array('orderby' => 'slug', 'order' => 'ASC', 'fields' => 'names');
		$painting_tags = wp_get_object_terms( $post_ID,  $taxonomy_name, $tags_args );
		echo implode(',', $painting_tags);

    }
}
add_action('manage_austeve-paintings_posts_custom_column', 'austeve_paintings_columns_content', 10, 2);

#endregion Admin list filters to add columns to lists

#region Filter Territory taxonomy
//Filter the admin call for Territories based on the current users role(s) - Only display territories that the user has access to
function austeve_filter_territories_for_admins( $args, $taxonomies ) {
    
	$taxonomy_name = 'austeve_territories';

	//Only filter for austeve_territories in admin screens here, and only if not looking at a specific object (ie querying which taxonomies are available to a user)
	if ( !is_admin() || !in_array($taxonomy_name, $args['taxonomy']) || is_array($args['object_ids']))
	{
		return $args;
	}

	if ( current_user_can('edit_users') ) //User has access to all territories
	{
		return $args;
	}

	//If we get here the current user has access to edit venues, therefore they should be able to set Territories

	//Get current user territories	
	$ut_args = array('orderby' => 'slug', 'order' => 'ASC', 'fields' => 'all');
	$user_territories = wp_get_object_terms( get_current_user_id(),  $taxonomy_name, $ut_args );
	$user_territories_inc_children = array();

	if ( ! empty( $user_territories ) ) {
		if ( ! is_wp_error( $user_territories ) ) {

			foreach($user_territories as $territory)
			{
				//Add each territory as well as it's child territories to the array of slugs
				$user_territories_inc_children[] = $territory->slug;
				//Get children
				$territory_child = get_term_children( $territory->term_id, $taxonomy_name );
				foreach ( $territory_child as $child ) {
					//Get the actual child term
					$term = get_term_by( 'id', $child, $taxonomy_name );
					//Add it to the array
					$user_territories_inc_children[] = $term->slug;
				}
			}

			//Set slug filter for territories
			$args['slug'] = $user_territories_inc_children;

			//error_log("Returning Args: ".print_r($args, true));
		}
	}
	
    return $args;
}
add_filter( 'get_terms_args', 'austeve_filter_territories_for_admins' , 10, 2 );
#endregion Filter Territory taxonomy

#region pre_get_posts filter
//Filter the admin call for Territories and Venues based on the current users role(s) - Only display items that the user has access to
function austeve_filter_objects_for_admins( $query ) {
    
	if ( !is_admin() ||  //Not in admin screens 
		current_user_can('edit_users') ) //User has access to all territories/venues
	{
		return $query;
	}

	//If we get here the current user has access to view venues, therefore they should be able to set Territories

	if (isset($query->query_vars['post_type']))
	{
		if ( $query->query_vars['post_type'] == 'austeve-venues' )
		{
			//Filter the list of venues

			//Get current user territories
			$ut_args = array('orderby' => 'slug', 'order' => 'ASC', 'fields' => 'slugs');
			$user_territories = wp_get_object_terms( get_current_user_id(),  'austeve_territories', $ut_args );

			if ( ! empty( $user_territories ) ) {
				if ( ! is_wp_error( $user_territories ) ) {
					
					//Create new tax_query
					$tax_query = array (
						array (
							'taxonomy'         => 'austeve_territories',
							'terms'            => $user_territories,
							'field'            => 'slug',
							'operator'         => 'IN',
						),
					);

					$query->set( 'tax_query', $tax_query );
					error_log("Tax Query: ".print_r($tax_query, true));

				}
			}
		}
		else if ( $query->query_vars['post_type'] == 'austeve-events' )
		{
			//Filter the list of events

			//Get all venues in the users allowed territory(s)
			$args = array(
				'posts_per_page'   => -1,
				'orderby'          => 'ID',
				'order'            => 'ASC',
				'post_type'        => 'austeve-venues',
				'post_status'      => 'publish'
			);
			$venues = get_posts( $args );
			//error_log("Venues: ".print_r( $venues_array, true ));

			if ($venues)
			{
				$venue_ids = array();
				foreach($venues as $venue)
				{
					$venue_ids[] = $venue->ID;
				}
				
				$meta_query = array(
					array(
						'key'     => 'venue',
						'value'   => (count($venue_ids) > 1) ? implode(",", $venue_ids) : $venue_ids[0],
						'compare' => 'IN',
						'type'    => 'NUMERIC',
					),
				);

				$query->set( 'meta_query', $meta_query );
			}
		}
	}
}

add_action( 'pre_get_posts', 'austeve_filter_objects_for_admins' , 10, 1 );
#endregion pre_get_posts filter
?>