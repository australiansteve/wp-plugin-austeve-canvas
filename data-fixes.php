<?php

add_action('admin_init', function () {

	//Data fix 2017-001	
	//Add a UTC Start Time field to each existing event. The field is named 'start_time_utc'

	if (!get_option( 'austeve_datafix_2017_001') && current_user_can('edit_users'))
	{
		error_log("Applying austeve_datafix_2017_001");


	    $args = array(
	        'do_not_filter' => true,
	        'post_type' => 'austeve-events',
	        'post_status' => array('publish'),
	        'posts_per_page' => -1,
	    );

	    global $post;
		$event_posts = get_posts( $args );
		foreach ( $event_posts as $post ) : 

			error_log($post->ID.": ".the_field('start_time_utc'));
			if (!get_field('start_time_utc'))
			{
				//Add UTC start time for event. Calculate from event date (start_time) and timezone (timezone)
				$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time'));
				$timezone = floatval(get_field('timezone'));
				$timezoneAdjust = ($timezone == 0) ? '+0 hour' : (($timezone < 0) ? '+'.strval($timezone * -60).' minute' : '-'.strval($timezone*60).' minute');
				$utcExpiry = clone $eventDate;
				$utcExpiry->modify($timezoneAdjust);

				error_log("Start time: ".$eventDate->format("Y-m-d H:i:s"));
				error_log("Timezone: ".$timezone);
				error_log("Timezone adjustment: ".$timezoneAdjust);
				error_log('Start Time (UTC): '. $utcExpiry->format("Y-m-d H:i:s"));
				update_field('start_time_utc', $utcExpiry->format("Y-m-d H:i:s"));
			}
			
		endforeach; 
		wp_reset_postdata();

		add_option( 'austeve_datafix_2017_001', true, '', 'no' );
		error_log("Datafix: austeve_datafix_2017_001 applied");
	}

}, 999);

?>