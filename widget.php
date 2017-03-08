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

		//Get capacity information
		$venue = get_field('venue', $event->ID);
		$address = get_field('address', $venue->ID);
		$capacity = get_field('custom_capacity', $event->ID);
		if (!$capacity)
		{
			get_field('custom_capacity', $event->ID);
			$capacity = get_field('capacity', $venue->ID);
		}

		//Get ticket sales information
		global $wpdb;
		$post_id = get_field('wc_product', $event->ID);

		// Get qty for each order
		$sale_qtys = $wpdb->get_results( $wpdb->prepare(
			"SELECT oim.order_item_id as order_item_id, oim.meta_value as qty 
				FROM {$wpdb->prefix}woocommerce_order_itemmeta oim 
				WHERE oim.meta_key = '_qty' AND oim.order_item_id IN(SELECT oi.order_item_id FROM
			{$wpdb->prefix}woocommerce_order_itemmeta oim
			INNER JOIN {$wpdb->prefix}woocommerce_order_items oi
			ON oim.order_item_id = oi.order_item_id
			INNER JOIN {$wpdb->prefix}posts o
			ON oi.order_id = o.ID
			WHERE oim.meta_key = '_product_id'
			AND oim.meta_value IN ( %s )
			AND o.post_status IN ( %s )
			ORDER BY o.ID DESC)",
			get_field('wc_product', $event->ID),
			'wc-completed'), OBJECT_K
		);

		//Add up all of the sales quantities
		$ticketSales = 0;
		foreach($sale_qtys as $key => $value)
		{
			$ticketSales += $value->qty;
		}

		$salesClass = 'bad-sales';
		if ($ticketSales / $capacity > 0.8)
		{
			$salesClass = 'good-sales';
		}
		else if ($ticketSales / $capacity > 0.5)
		{
			$salesClass = 'ok-sales';
		}

		echo "<div class='upcoming-event'>";
		echo "	<div class='head'>";
		echo "		<div class='date'>".$eventDate->format('d M Y')."</div>";
		echo "		<div class='title'><a href='".get_permalink($event->ID)."' target='_blank'>".$event->post_title."</a></div>";
		echo "		<div class='edit-event'><a href='".admin_url('/post.php?post='.$event->ID.'&action=edit')."'>[Edit]</a></div>";
		echo "		<div class='time'>".$eventDate->format('g:ia')."</div>";
		echo "	</div>"; //END .head
		echo "	<div class='body'>";
		echo "		<div class='spacer-25'></div>";
		echo "		<div class='sales'>Tickets sold: <span class='$salesClass'>".$ticketSales."/".$capacity."</span></div>";
		echo "	</div>"; //END .body
		echo "	<div class='body'>";
		echo "		<div class='spacer-25'></div>";
		echo "		<div class='venue'>$venue->post_title<br/>$address</div>";
		echo "	</div>"; //END .body
		echo "</div>"; //END .upcoming-event
	}
}

?>