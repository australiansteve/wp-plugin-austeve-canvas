<?php

/*
* Creating a function to create our CPT
*/

function austeve_create_events_post_type() {

// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Events', 'Post Type General Name', 'austeve-canvas' ),
		'singular_name'       => _x( 'Event', 'Post Type Singular Name', 'austeve-canvas' ),
		'menu_name'           => __( 'Events', 'austeve-canvas' ),
		'parent_item_colon'   => __( 'Parent Event', 'austeve-canvas' ),
		'all_items'           => __( 'All Events', 'austeve-canvas' ),
		'view_item'           => __( 'View Event', 'austeve-canvas' ),
		'add_new_item'        => __( 'Add New Event', 'austeve-canvas' ),
		'add_new'             => __( 'Add New', 'austeve-canvas' ),
		'edit_item'           => __( 'Edit Event', 'austeve-canvas' ),
		'update_item'         => __( 'Update Event', 'austeve-canvas' ),
		'search_items'        => __( 'Search Event', 'austeve-canvas' ),
		'not_found'           => __( 'Not Found', 'austeve-canvas' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'austeve-canvas' ),
	);
	
// Set other options for Custom Post Type
	
	$args = array(
		'label'               => __( 'Events', 'austeve-canvas' ),
		'description'         => __( get_bloginfo( 'name' ).' events', 'austeve-canvas' ),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'author', 'revisions', ),
		// You can associate this CPT with a taxonomy or custom taxonomy. 
		'taxonomies'          => array( 'event-type'),
		/* A hierarchical CPT is like Pages and can have
		* Parent and child items. A non-hierarchical CPT
		* is like Posts.
		*/	
		'hierarchical'        => false,
		'rewrite'           => array( 'slug' => 'events' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => array( 'event' , 'events' ),
        'map_meta_cap'        => true,
		'menu_icon'				=> 'dashicons-calendar-alt',
	);
	
	// Registering your Custom Post Type
	register_post_type( 'austeve-events', $args );


	$taxonomyLabels = array(
		'name'              => _x( 'Event Types', 'taxonomy general name' ),
		'singular_name'     => _x( 'Event Type', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Event Types' ),
		'all_items'         => __( 'All Event Types' ),
		'parent_item'       => __( 'Parent Event Type' ),
		'parent_item_colon' => __( 'Parent Event Type:' ),
		'edit_item'         => __( 'Edit Event Type' ),
		'update_item'       => __( 'Update Event Type' ),
		'add_new_item'      => __( 'Add New Event Type' ),
		'new_item_name'     => __( 'New Event Type Name' ),
		'menu_name'         => __( 'Event Types' ),
	);

	$taxonomyArgs = array(

		'label'               => __( 'austeve_event_types', 'austeve-canvas' ),
		'labels'              => $taxonomyLabels,
		'show_admin_column'	=> false,
		'hierarchical' 		=> false,
		'show_ui'			=> false,
		'rewrite'           => array( 'slug' => 'event-type' ),
		'capabilities'		=> array(
							    'manage_terms' => 'edit_users',
							    'edit_terms' => 'edit_users',
							    'delete_terms' => 'edit_users',
							    'assign_terms' => 'edit_posts'
							 )
		);

	register_taxonomy( 'austeve_event_types', 'austeve-events', $taxonomyArgs );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/

add_action( 'init', 'austeve_create_events_post_type', 0 );

function event_include_template_function( $template_path ) {
    if ( get_post_type() == 'austeve-events' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-events.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-events.php';
            }
        }
        else if ( is_archive() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'archive-events.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/archive-events.php';
            }
        }
    }
    return $template_path;
}
add_filter( 'template_include', 'event_include_template_function', 1 );

function austeve_update_wc_product( $post_id ) {

	$event_post = get_post($post_id);

    // If this isn't an 'event' post, don't update anything.
    if ( "austeve-events" != $event_post->post_type ) return;

    //Get the product
    $product_id = get_field('wc_product');

    if ($product_id)
    {
	    //Get the product
		$product = get_post($product_id);

		if ($product && $product->post_type == 'product')
		{
			error_log(print_r($product, true));
		    
			$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time'));
			$new_product_title = array(
				'ID'           => $product_id,
				'post_title'   => 'Event ticket: '.$event_post->post_title.', '.get_the_title(get_field('venue')).', '.$eventDate->format('F jS Y'),
			);

			// Update the post title into the database
			if ($new_product_title['post_title'] != $product->post_title)
			{
				wp_update_post( $new_product_title );
			}

			//update metadata for the Product
			update_post_meta( $product_id, '_price', get_field('price') );
			update_post_meta( $product_id, '_regular_price', get_field('price') );

			//Update stock - impacted bu if there has been a change of venue or a change in custom capacity
			if (get_field('custom_capacity'))
				$new_capacity = intval(get_field('custom_capacity'));
			else
				$new_capacity = intval(get_field('capacity', get_field('venue')));
			$sold_so_far = get_post_meta($product_id, 'total_sales', true);
			error_log("Stock: ".print_r($sold_so_far, true)." vs ".$new_capacity);
			$still_remaining = $new_capacity - $sold_so_far;
			update_post_meta( $product_id, '_stock', ($still_remaining > 0) ? $still_remaining : '0'); //Venue capacity

			//Update thumbnail
			error_log(get_field('creation'));
			error_log(print_r(get_field('image', get_field('creation')), true));
			update_post_meta( $product_id, '_thumbnail_id',  get_field('image', get_field('creation'))['ID']); //Media ID

			//Update expiry - consider timezone
			$timezone = floatval(get_field('timezone'));
			$timezoneAdjust = ($timezone == 0) ? '+0 hours' : (($timezone < 0) ? '+'.strval($timezone * -60).' minutes' : '-'.strval($timezone*60).' minutes');
			$utcExpiry = clone $eventDate;
			$utcExpiry->modify($timezoneAdjust);
			error_log("timezone: ".print_r($timezone, true));
			error_log("timezoneAdjust: ".print_r($timezoneAdjust, true));
			error_log("utcExpiry: ".print_r($utcExpiry, true));
			update_post_meta( $product_id, '_expiration_date', $utcExpiry); //WC product expiry date - stored as a DateTime object
			update_field('start_time_utc', $utcExpiry->format("Y-m-d H:i:s")); //UTC start time of event

			//Add link to Tax Rate taxonomy from the Tax Region of the venue
			$tax_region = get_field('tax_region', get_field('venue'));
			if ($tax_region)
			{
				error_log("Existing product tax region: ".$tax_region);
				wp_set_object_terms( $product_id, $tax_region, 'pa_tax-rate', false );
			}
			else 
			{
				error_log("No tax region set for venue");
			}

			//Also set the 'event-ticket' category - for backwards compatibility
			wp_set_object_terms( $product_id, 'event-ticket', 'product_cat', false );
		}
		else 
		{
    		error_log("ERROR: Product doesn't exist");
		}
    }
    else
    {
    	error_log("Product needs to be added");
		$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time'));

    	$new_product_id = wp_insert_post( array(
		    'post_title' => 'Event ticket: '.$event_post->post_title.', '.get_the_title(get_field('venue')).', '.$eventDate->format('F jS Y'),
		    'post_content' => '',
		    'post_status' => 'publish',
		    'post_type' => "product",
		    'post_excerpt' => '<a href='.get_permalink($post_id).'>See full event details</a>',
		    'comment_status' => "closed",
		) );

    	error_log("Product added: ".$new_product_id);

		// update Event
		update_field('wc_product', $new_product_id);

		//Event tickets will always be simple products
		wp_set_object_terms( $new_product_id, 'simple', 'product_type' );

		//update metadata for the Product
		update_post_meta( $new_product_id, '_price', get_field('price') );
		update_post_meta( $new_product_id, '_regular_price', get_field('price') );
		update_post_meta( $new_product_id, '_thumbnail_id',  get_field('image', get_field('creation'))['ID']); //Media ID

		//Update expiry - consider timezone
		$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time'));
		$timezone = floatval(get_field('timezone'));
		$timezoneAdjust = ($timezone == 0) ? '+0 hours' : (($timezone < 0) ? '+'.strval($timezone * -60).' minutes' : '-'.strval($timezone*60).' minutes');
		$utcExpiry = clone $eventDate;
		$utcExpiry->modify($timezoneAdjust);
		error_log("timezone: ".print_r($timezone, true));
		error_log("timezoneAdjust: ".print_r($timezoneAdjust, true));
		error_log("utcExpiry: ".print_r($utcExpiry, true));
		update_post_meta( $new_product_id, '_expiration_date', $utcExpiry); //WC product expiry date - stored as a DateTime object
		update_field('start_time_utc', $utcExpiry->format("Y-m-d H:i:s")); //UTC start time of event

		if (get_field('custom_capacity'))
		{
			//Custom event capacity
			update_post_meta( $new_product_id, '_stock', get_field('custom_capacity') ); 
		}
		else
		{
			//Venue capacity
			update_post_meta( $new_product_id, '_stock', get_field('capacity', get_field('venue')) ); 
		}

		//Pre-sale related info
		update_post_meta( $new_product_id, '_sale_price', '' );
		update_post_meta( $new_product_id, '_sale_price_dates_from', '' );
		update_post_meta( $new_product_id, '_sale_price_dates_to', '' );

		//Other stuff shouldn't ever change
		update_post_meta( $new_product_id, '_visibility', 'visible' );
		update_post_meta( $new_product_id, '_stock_status', 'instock');
		update_post_meta( $new_product_id, '_tax_status', 'taxable');
		update_post_meta( $new_product_id, 'total_sales', '0' );
		update_post_meta( $new_product_id, '_downloadable', 'no' );
		update_post_meta( $new_product_id, '_virtual', 'yes' );
		update_post_meta( $new_product_id, '_purchase_note', '' );
		update_post_meta( $new_product_id, '_featured', 'no' );
		update_post_meta( $new_product_id, '_sku', '' );
		update_post_meta( $new_product_id, '_product_attributes', array() );
		update_post_meta( $new_product_id, '_sold_individually', '' );
		update_post_meta( $new_product_id, '_manage_stock', 'yes' );
		update_post_meta( $new_product_id, '_backorders', 'no' );

		//Add link to Tax Rate taxonomy from the Tax Region of the venue
		$tax_region = get_field('tax_region', get_field('venue'));
		if ($tax_region)
		{
			error_log("New product tax region: ".$tax_region);
			wp_set_object_terms( $new_product_id, $tax_region, 'pa_tax-rate', false );
		}
		else 
		{
			error_log("No tax region set for venue");
		}

		//Also set the 'event-ticket' category
		wp_set_object_terms( $new_product_id, 'event-ticket', 'product_cat', false );

    }

}
add_action('acf/save_post', 'austeve_update_wc_product', 20);

function austeve_validate_event_venue( $valid, $value, $field, $input ){
	
	// bail early if value is already invalid
	if( !$valid ) {
		
		return $valid;
		
	}
	
	//Get tax region for venue
	$tax_region = get_field('tax_region', $value);
	if (!$tax_region)
	{
		$valid = "Venue MUST have tax region set before it can be used for an event. Please update the Venue information before updating the event.";
	}
		
	// return
	return $valid;	
}

add_filter('acf/validate_value/name=venue', 'austeve_validate_event_venue', 10, 4);

function austeve_pre_get_posts_order_events( $query ) {
	
	// do not modify queries in the admin, or if viewing a single event page, or if being displayed from shortcode
	if( is_admin() || is_single() || array_key_exists('do_not_filter', $query->query) ) {
		
		return $query;
		
	}

	//If here we are basically just modifying the archive page of events 
	// only modify queries for 'event' post type
	if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'austeve-events' ) {
		
		// find date time now
		$date_now = date('Y-m-d H:i:s');

		//Find the next events
		$query->set('posts_per_page', isset($query->query_vars['posts_per_page']) ? $query->query_vars['posts_per_page'] : -1);	
		$query->set('orderby', 'meta_value');	
		$query->set('meta_key', 'start_time');	 	
		$query->set('meta_type', 'DATETIME');	 
		$query->set('order', 'ASC');	 
		$query->set('meta_query', array(
	        'key'			=> 'start_time',
	        'compare'		=> '>=',
	        'value'			=> $date_now,
	        'type'			=> 'DATETIME',
	    ));

		error_log("Getting future events: ".print_r($query, true));
	}

	// return
	return $query;

}

add_action('pre_get_posts', 'austeve_pre_get_posts_order_events');

function event_filter_archive_title( $title ) {

	if ( is_post_type_archive('austeve-events') ) {

        $title = post_type_archive_title( '', false );

    }

    return $title;

}

add_filter( 'get_the_archive_title', 'event_filter_archive_title');

class AUSteve_EventHelper {
		/**
	 * Plugin actions.
	 */
	public function __construct() {
		
	}

	public static function get_sorted_event_guestlist($eventID) {

		//Display guest list if user is admin or host
		error_log("Finding guests for event ".$eventID);
		error_log("WC product ".get_field('wc_product', $eventID));

		global $post, $wpdb;
		$post_id = get_field('wc_product', $eventID);

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
			get_field('wc_product', $eventID),
			'wc-completed'), OBJECT_K
		);

		error_log("Qty Sales: ".print_r($sale_qtys, true));

    	//Reset back to the main loop
		wp_reset_postdata();

		// Query the orders related to the WC product
		$item_sales = $wpdb->get_results( $wpdb->prepare(
			"SELECT oi.order_item_id, o.ID as order_id  FROM
			{$wpdb->prefix}woocommerce_order_itemmeta oim
			INNER JOIN {$wpdb->prefix}woocommerce_order_items oi
			ON oim.order_item_id = oi.order_item_id
			INNER JOIN {$wpdb->prefix}posts o
			ON oi.order_id = o.ID
			WHERE oim.meta_key = '_product_id'
			AND oim.meta_value IN ( %s )
			AND o.post_status IN ( %s )
			ORDER BY o.ID DESC",
			get_field('wc_product', $eventID),
			'wc-completed'
		), OBJECT_K );

		error_log("Item Sales: ".print_r($item_sales, true));

		$sales = array();
		if (count($item_sales) > 0)
		{
			foreach($item_sales as $id=>$sale)
			{	
				$sales[$id]['order_id'] = $sale->order_id;
				$sales[$id]['qty'] = $sale_qtys[$id]->qty;

				$customer = get_field('_customer_user', $sale->order_id);
				error_log("Customer: ".print_r($customer, true));

				if ($customer > 0)
				{
					//Customer has account
					$userdata = get_userdata($customer);

					$sales[$id]['customer_id'] = $userdata->ID;
					$sales[$id]['customer_name'] = $userdata->first_name." ".$userdata->last_name;
					$sales[$id]['customer_email'] = $userdata->user_email;
				}
				else
				{
					//Customer made purchase as guest

					$sales[$id]['customer_id'] = 0;
					$sales[$id]['customer_name'] = get_field('_billing_first_name', $sale->order_id)." ".get_field('_billing_last_name', $sale->order_id);
					$sales[$id]['customer_email'] = get_field('_billing_email', $sale->order_id);
				}
			}

			error_log("Merged array: ".print_r($sales, true));

			// Obtain a list of columns
			foreach ($sales as $key => $row) {
			    $customer_name[$key] = $row['customer_name'];
			}

			// Add $data as the last parameter, to sort by the common key
			array_multisort($customer_name, SORT_ASC, $sales);

			foreach($sales as $sale)
			{
				$returnArray[$sale['order_id']] = $sale;
			}
			return $returnArray;
		}
		return $sales;
	}

	public static function current_user_is_admin($eventId) {
		//If event host is viewing
		$current_user = wp_get_current_user();
		$event_host = get_field('host', $eventId);
		error_log("Current user ID: ".print_r($current_user->ID , true));
		error_log("Event host ID: ".print_r($event_host['ID'] , true));

		$event_territory = get_field('territory', get_field('venue', $eventId)->ID);
		$event_territories = get_ancestors( $event_territory, 'austeve_territories', 'taxonomy' );
		array_push($event_territories, $event_territory);

		error_log("Event territory: ".$event_territory);
		error_log("Territories: ".print_r($event_territories, true));

		//Get current user territories	
		$ut_args = array('orderby' => 'slug', 'order' => 'ASC', 'fields' => 'ids');
		$user_territories = wp_get_object_terms( $current_user->ID,  'austeve_territories', $ut_args );

		error_log("User terms:".print_r($user_territories, true));

		return ($current_user->ID == $event_host['ID'] || 
			current_user_can('edit_users') || 
			( current_user_can('edit_events', $eventId) && count(array_intersect($event_territories, $user_territories)) >= 1 ));	
	}

}
new AUSteve_EventHelper();

add_action( 'admin_menu', 'austeve_event_reviews_menu' );

function austeve_event_reviews_menu() {
	add_submenu_page('edit.php?post_type=austeve-events', 'Reviews', 'Reviews', 'edit_events', 'austeve-event-reviews', 'austeve_display_reviews' );
}

function austeve_display_reviews() {
	if ( !current_user_can( 'edit_events' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<h1>Event Reviews</h1>';

	//Get all previous events
	$args = austeve_event_query_args(
		array('number_of_posts' => -1,
	        'future_events' => 'false',
	        'past_events' => 'true',
	        'order' => 'DESC',
	        'number_of_days' => 30
	    )
    );

    $posts_array = get_posts( $args );
    $event_id = isset($_GET['event_id']) ? $_GET['event_id']: 0;
	
    //Display dropdown to select event
    echo "<select id='event-id'>";
    echo "<option value='0' ".(($event_id == 0) ? "selected" : "").">Select an event</option>";
	foreach($posts_array as $event)
	{
		$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time', $event->ID));

		echo "<option value='".$event->ID."' ".(($event_id == $event->ID) ? "selected" : "").">".$eventDate->format('d M Y')." - ".$event->post_title.", ".get_field('venue', $event->ID)->post_title."</option>";

	}
	echo "</select>";
	echo '<div class="load-more-event-reviews">';
	echo '<input type="hidden" id="days-back" value="30" />';
	echo '<button id="load-more-event-reviews" >Load more events</button>';
	echo '</div>';
	echo '<div class="event-reviews">';
	if ($event_id > 0)
	{
		if (!AUSteve_EventHelper::current_user_is_admin($event_id))
		{
			echo "<p>You do not have access to view this events reviews.</p>";
		}
		else if (get_field('reviews', $event_id))
		{
			$reviews = json_decode(get_field('reviews', $event_id), true);
			echo "<div class='event-review header'>";
			echo "<div class='user'>User</div>";
			echo "<div class='rating'>Rating</div>";
			echo "<div class='feedback'>Feedback</div>";
			echo "</div>";

			foreach($reviews as $user_id=>$review)
			{
				$userdata = get_userdata($user_id);
				echo "<div class='event-review'>";
				echo "<div class='user'>".$userdata->first_name." ".$userdata->last_name." (<a href='mailto:".$userdata->user_email."' target='_blank'>".$userdata->user_email."</a>)</div>";
				echo "<div class='rating'>".$review['rating']."/5</div>";
				echo "<div class='feedback'>".$review['feedback']."</div>";
				echo "</div>";
			}
		}
		else 
		{
			echo "There are no reviews on this event";
		}
	}
	else
	{
		echo "Select an event to view reviews"; 
	}
	echo '</div>';  // END .event-reviews

	$nonce = wp_create_nonce( 'austevegeteventreviews' );
	?>

	<script type='text/javascript'>
		<!--
		function get_reviews( eventId ) {
			console.log("Getting event reviews for event " + eventId);
			jQuery.ajax({
				type: "post", 
				url: '<?php echo admin_url("admin-ajax.php"); ?>', 
				data: { 
					action: 'get_event_reviews', 
					eventId: eventId, 
					_ajax_nonce: '<?php echo $nonce; ?>' 
				},
				beforeSend: function() {
					jQuery(".event-reviews").html("<i class='fa fa-spinner fa-pulse fa-fw'></i>");
				},
				success: function(html){ //so, if data is retrieved, store it in html
					console.log("Response: " + html);
					jQuery(".event-reviews").html(html);
				}
			}); //close jQuery.ajax(
		}

		function get_events( daysBack ) {
			console.log("Getting events for last " + daysBack + " days");
			jQuery.ajax({
				type: "post", 
				url: '<?php echo admin_url("admin-ajax.php"); ?>', 
				data: { 
					action: 'get_events_for_reviews', 
					daysBack: daysBack, 
					_ajax_nonce: '<?php echo wp_create_nonce( 'austevegeteventsforreviews' ); ?>' 
				},
				beforeSend: function() {
					jQuery("#event-id").html("<option>Loading...</option>");
					jQuery("#event-id").after("<i class='fa fa-spinner fa-pulse fa-fw load-events-spinner'></i>");
				},
				statusCode: {
					403: function (xhr) {
						console.log('403 response');
					}
				},
				success: function(html){ //so, if data is retrieved, store it in html
					console.log("Response: " + html);
					jQuery("#event-id").html(html);
					jQuery(".load-events-spinner").remove();
					jQuery('#days-back').val(daysBack); 
				},
				error: function(html){ //so, if data is retrieved, store it in html
					console.log("Response: " + html);
					jQuery("#event-id").html("<option>Error</option>");
					jQuery(".load-events-spinner").remove();
				}
			}); //close jQuery.ajax(
		}

		// When the document loads do everything inside here ...
		jQuery("#event-id").on('change', function() {
			var eventId = jQuery('#event-id').val();

			console.log("Getting reviews for event: " + eventId);
			get_reviews( eventId );
		});

		jQuery("#load-more-event-reviews").on('click', function() {
			var daysBack = parseInt(jQuery('#days-back').val()) + parseInt(30);

			console.log("Getting events for last " + daysBack + " days");
			get_events( daysBack );
		});

		-->
	</script>

	<?php

	echo '</div>';
}

?>