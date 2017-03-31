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

			$currentNum = array_key_exists($orderId, $checked_in_guest_list) ? intval($checked_in_guest_list[$orderId]['qty']) : 0;

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


function austeve_get_instructor_rating_ajax() {
	check_ajax_referer( "austevegetinstructorrating" );

	if( $_POST[ 'userId' ] )
	{
		$userId = $_POST[ 'userId' ];
		echo "5";
		die();
	}
	echo "Incorrect paramters";
	die();
}
add_action( 'wp_ajax_get_instructor_rating', 'austeve_get_instructor_rating_ajax' );


?>