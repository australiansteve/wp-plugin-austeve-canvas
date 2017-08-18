<?php
function austeve_save_attendance_ajax() {
	check_ajax_referer( "austevesaveattendance" );

	if( $_POST[ 'eventId' ] && $_POST[ 'orderId' ]  && $_POST[ 'userId' ] )
	{

		$eventId = $_POST[ 'eventId' ];
		$orderId = $_POST[ 'orderId' ];
		$userId = $_POST[ 'userId' ];
		$increaseAttendance = $_POST[ 'increase' ] == 'true';
		error_log("Increase:".$_POST[ 'increase' ].":".$increaseAttendance);

		$user = get_current_user_id();

		error_log("AJAX save attendance for event: ".intval($eventId).". Order: ".$orderId.". userId: ".$userId.". User: ".$user. " - ".($increaseAttendance ? "UP" : "DOWN"));

		//Establish if the current user has permission to update this event

		$event_host = get_field('host', $eventId);
		error_log("Current user ID: ".print_r($user , true));
		error_log("Event host ID: ".print_r($event_host['ID'] , true));

		$venue = get_field('venue', $eventId);
		$event_territory = get_field('territory', $venue->ID);
		$event_territories = get_ancestors( $event_territory, 'austeve_territories', 'taxonomy' );
		array_push($event_territories, $event_territory);
		error_log("Event territory: ".$event_territory);
		error_log("Territories: ".print_r($event_territories, true));

		//Get current user territories	
		$ut_args = array('orderby' => 'slug', 'order' => 'ASC', 'fields' => 'ids');
		$user_territories = wp_get_object_terms( $user, 'austeve_territories', $ut_args );
		error_log("User terms:".print_r($user_territories, true));

		//If user is event host, or user is admin, or user can edit events in the event territory
		if ( $user == $event_host['ID'] || 
				current_user_can('edit_users') || 
				( current_user_can('edit_events', get_the_ID()) && count(array_intersect($event_territories, $user_territories)) >= 1 ) 
			) {

			//Get the guest list for the event
			$checked_in_guest_list = json_decode(get_field('guest_list', $eventId), true);
			error_log("Guest list before update: ".print_r($checked_in_guest_list, true));

			$guestlist = AUSteve_EventHelper::get_sorted_event_guestlist($eventId);
			error_log("Sorted guestlist [attendance]: ".print_r($guestlist, true));

			$currentNum = 0;

			if (is_array($checked_in_guest_list) && array_key_exists($orderId, $checked_in_guest_list))
			{
				if (array_key_exists('qty', $checked_in_guest_list[$orderId]))
				{
					$currentNum = intval($checked_in_guest_list[$orderId]['qty']);
				}
				else if (array_key_exists('present', $checked_in_guest_list[$orderId])) 
				{
					//The 'present' key was used in an early implementation. Added for backwards compatibility
					$currentNum = intval($checked_in_guest_list[$orderId]['present']);
					unset($checked_in_guest_list[$orderId]['present']);
					if (array_key_exists('user', $checked_in_guest_list[$orderId])) 
					{
						unset($checked_in_guest_list[$orderId]['user']);
					}
				}
			}

			if ($increaseAttendance && $currentNum >= $guestlist[$orderId]['qty'])
			{
				//echo "Already all checked in";
				echo $guestlist[$orderId]['qty'];
				die();
			}
			else if (!$increaseAttendance && $currentNum <= 0)
			{
				//echo "Already nobody checked in";
				echo 0;
				die();
			}
			else if ($increaseAttendance)
			{
				$checked_in_guest_list[$orderId]['qty'] = $currentNum + 1;
				$checked_in_guest_list[$orderId]['customer_id'] = $guestlist[$orderId]['customer_id'];
				if (update_field('guest_list', json_encode($checked_in_guest_list), $eventId))
					echo $checked_in_guest_list[$orderId]['qty'];
				else
					echo "#error#";
				
				die();
			}
			else if (!$increaseAttendance)
			{
				$checked_in_guest_list[$orderId]['qty'] = $currentNum - 1;
				$checked_in_guest_list[$orderId]['customer_id'] = $guestlist[$orderId]['customer_id'];
				if (update_field('guest_list', json_encode($checked_in_guest_list), $eventId))
					echo $checked_in_guest_list[$orderId]['qty'];
				else
					echo "#error#";
				
				die();
			}
			echo "Incomplete";
			die();

		}
		else
		{
			echo "No permission on event";
			die();
		}
	}
	echo "Incorrect paramters";
	die();
}
add_action( 'wp_ajax_save_attendance', 'austeve_save_attendance_ajax' );

function austeve_get_location_events_ajax() {
	check_ajax_referer( "austevegetlocationevents" );

	if( $_POST[ 'locationId' ] !== 'undefined' && $_POST[ 'pastEvents' ] && $_POST[ 'categoryId' ] !== 'undefined' && $_POST[ 'futureEvents' ] && $_POST[ 'order' ] )
	{
		$locationId = $_POST[ 'locationId' ];
		$categoryId = $_POST[ 'categoryId' ];
		$pastEvents = ($_POST[ 'pastEvents' ] == 'true') ? 'true' : 'false';
		$futureEvents = ($_POST[ 'futureEvents' ] == 'true') ? 'true' : 'false';
		$order = $_POST[ 'order' ];

		echo do_shortcode("[show_events show_filters='false' past_events='".$pastEvents."' future_events='".$futureEvents."' order='$order' ".($locationId > 0 ? "territory_id='$locationId'" : "")." ".($categoryId > 0 ? "category_id='$categoryId'" : "")."]");
		die();
	}
	echo "ERROR: There was a problem retrieving events";
	die();
}
add_action( 'wp_ajax_get_location_events', 'austeve_get_location_events_ajax' );
add_action( 'wp_ajax_nopriv_get_location_events', 'austeve_get_location_events_ajax' );

function austeve_get_location_venue_options_ajax() {
	check_ajax_referer( "austevegetlocationvenueoptions" );

	if( $_POST[ 'locationId' ] !== 'undefined' )
	{
		$args = array(
	        'posts_per_page' => -1,
	        'post_type' => 'austeve-venues',
	        'post_status' => array('publish'),
	        'orderby' => 'name',
	        'order' => 'ASC',
	        'do_not_filter' => true,
	    );

	    if ($_POST[ 'locationId' ] > 0)
	    {
	        $args['tax_query'] = array( 
	                array(
	                'taxonomy'         => 'austeve_territories',
	                'terms'            => $_POST[ 'locationId' ],
	                'field'            => 'term_id',
	                'operator'         => 'IN',
	                'include_children' => true,
	            )
	        );
	    }
	    $venue_posts = get_posts( $args );

	    foreach($venue_posts as $venue)
	    {
	    	if ($_POST[ 'style' ] !== 'undefined' && $_POST[ 'style' ] == 'listitem')
	    	{
	        	echo '<li class="venue"><a href="'.get_permalink($venue->ID).'">'.$venue->post_title.'</a></li>';
	    	}
	    	else
	    	{
	        	echo '<option value="' . $venue->ID . '">' .$venue->post_title . '</option>'; 
	    	}
	    }

		die();
	}
	echo "ERROR: There was a problem retrieving venues";
	die();
}
add_action( 'wp_ajax_get_location_venue_options', 'austeve_get_location_venue_options_ajax' );
add_action( 'wp_ajax_nopriv_get_location_venue_options', 'austeve_get_location_venue_options_ajax' );

function austeve_get_venue_events_ajax() {
	check_ajax_referer( "austevegetvenueevents" );

	if( $_POST[ 'venueId' ] !== 'undefined' && $_POST[ 'locationId' ] !== 'undefined' && $_POST[ 'categoryId' ] !== 'undefined' && $_POST[ 'pastEvents' ] && $_POST[ 'futureEvents' ] && $_POST[ 'order' ] )
	{
		$venueId = $_POST[ 'venueId' ];
		$locationId = $_POST[ 'locationId' ];
		$categoryId = $_POST[ 'categoryId' ];
		$pastEvents = ($_POST[ 'pastEvents' ] == 'true') ? 'true' : 'false';
		$futureEvents = ($_POST[ 'futureEvents' ] == 'true') ? 'true' : 'false';
		$order = $_POST[ 'order' ];

		echo do_shortcode("[show_events show_filters='false' past_events='".$pastEvents."' future_events='".$futureEvents."' order='$order' ".($_POST[ 'venueId' ] > 0 ? "venue_id='$venueId'" : "")." ".($_POST[ 'locationId' ] > 0 ? "territory_id='$locationId'" : "")." ".($_POST[ 'categoryId' ] > 0 ? "category_id='$categoryId'" : "")."]");
		die();
	}
	echo "ERROR: There was a problem retrieving events";
	die();
}
add_action( 'wp_ajax_get_venue_events', 'austeve_get_venue_events_ajax' );
add_action( 'wp_ajax_nopriv_get_venue_events', 'austeve_get_venue_events_ajax' );

function austeve_get_category_events_ajax() {
	check_ajax_referer( "austevegetcategoryevents" );

	if( $_POST[ 'categoryId' ] !== 'undefined' && $_POST[ 'venueId' ] !== 'undefined' && $_POST[ 'locationId' ] !== 'undefined' )
	{
		$categoryId = $_POST[ 'categoryId' ];
		$venueId = $_POST[ 'venueId' ];
		$locationId = $_POST[ 'locationId' ];

		echo do_shortcode("[show_events show_filters='false' past_events='false' future_events='true' ".($categoryId > 0 ? "category_id='$categoryId'" : "")." ".($venueId > 0 ? "venue_id='$venueId'" : "")." ".($locationId > 0 ? "territory_id='$locationId'" : "")."]");
		die();
	}
	echo "ERROR: There was a problem retrieving events";
	die();
}
add_action( 'wp_ajax_get_category_events', 'austeve_get_category_events_ajax' );
add_action( 'wp_ajax_nopriv_get_category_events', 'austeve_get_category_events_ajax' );

function austeve_get_creations_ajax() {
	//Not really sure why but checking the referrer here always gives a 403 error. Removing since it's a front get function anyway, and only gets public data
	//check_ajax_referer( 'austevegetcreations', '_ajax_nonce' );

	if( array_key_exists('nextPage', $_POST) && $_POST[ 'nextPage' ] !== 'undefined')
	{
		$numCreationPosts = 12;
		$queryType = 'post_type';
		$queryObject = 'austeve-creations';		

		if (array_key_exists('queryType', $_POST))
			$queryType = $_POST[ 'queryType' ];
		if (array_key_exists('queryObject', $_POST))
			$queryObject = $_POST[ 'queryObject' ];

		$args = array(
	        'posts_per_page' => $numCreationPosts,
	        $queryType => $queryObject,
	        'post_status' => array('publish'),
			'orderby'=> 'title',	
			'order' => 'ASC',	 
	        'paged' => $_POST[ 'nextPage' ]
	    );

		if (array_key_exists('artistId', $_POST) && $_POST['artistId'] !== 'undefined')
		{
			error_log("Artist ID: ".$_POST[ 'artistId' ]);
			$artist_meta = array(
				array(
                    'key'           => 'artist',
                    'compare'       => '=',
                    'value'         => $_POST['artistId'],
                    'type'          => 'NUMERIC',
                )
			);
			$args['meta_query'] = $artist_meta;
		}
		
		error_log("Get creations args: ".print_r($args, true));
	    // the query
		$the_query = new WP_Query( $args );
		
		if ( $the_query->have_posts() )
	    {
	    	
			while ( $the_query->have_posts() ) : $the_query->the_post();

			echo "<div class='column'>";

			if (locate_template('page-templates/partials/creations-archive.php') != '') 
			{
				// yep, load the page template
				get_template_part('page-templates/partials/creations', 'archive');
			} 
			else 
			{
				// nope, load the default
				include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/creations-archive.php');
			}
						
			echo "</div>";

			endwhile;
		}

		wp_reset_postdata();
	}
	die();
}
add_action( 'wp_ajax_get_creations', 'austeve_get_creations_ajax' );
add_action( 'wp_ajax_nopriv_get_creations', 'austeve_get_creations_ajax' );

?>