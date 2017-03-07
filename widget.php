<?php
/**
 * Add widgets to the admin dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function austeve_admin_widgets() {

	wp_add_dashboard_widget(
		'austeve_admin_recent_events_widget',	// Widget slug.
		'Recent Events',         				// Title.
		'austeve_admin_recent_events' 			// Display function.
	);

	wp_add_dashboard_widget(
		'austeve_admin_upcoming_events_widget',	// Widget slug.
		'Upcoming Events',         				// Title.
		'austeve_admin_upcoming_events'			// Display function.
	);

}
add_action( 'wp_dashboard_setup', 'austeve_admin_widgets' );


/**
 * Output the recent events to the admin dashboard
 */
function austeve_admin_recent_events() {

	$args = austeve_event_query_args(
		array('number_of_posts' => 5,
	        'future_events' => 'false',
	        'past_events' => 'true',
	        'order' => 'DESC'
	    )
    );

	// Display whatever it is you want to show.
	//echo print_r($args, true);

    $posts_array = get_posts( $args );
	
	//echo print_r($posts_array, true);

	foreach($posts_array as $event)
	{
		$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time', $event->ID));

		echo "<p>";
		echo $eventDate->format('d M - H:i');
		echo "&nbsp;";
		echo $event->post_title;
		echo "</p>";
	}
}


/**
 * Output the upcoming events to the admin dashboard
 */
function austeve_admin_upcoming_events() {

	$args = austeve_event_query_args(
		array('number_of_posts' => 5,
	        'future_events' => 'true',
	        'past_events' => 'false',
	        'order' => 'ASC'
    	)
    );

	// Display whatever it is you want to show.
	//echo print_r($args, true);

    $posts_array = get_posts( $args );
	
	//echo print_r($posts_array, true);

	foreach($posts_array as $event)
	{
		$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time', $event->ID));

		echo "<p>";
		echo $eventDate->format('d M - H:i');
		echo "&nbsp;";
		echo $event->post_title;
		echo "</p>";
	}
}

?>