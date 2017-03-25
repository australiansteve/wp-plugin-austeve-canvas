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
		'description'         => __( 'Canvas & Cocktail Events', 'austeve-canvas' ),
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

			//update metadata for the Product
			update_post_meta( $product_id, '_price', get_field('price') );
			update_post_meta( $product_id, '_regular_price', get_field('price') );

			//Update stock - impacted bu if there has been a change of venue
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

			//Update expiry
			update_post_meta( $product_id, '_expiration_date', get_field('start_time')); //Event date
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
		update_post_meta( $new_product_id, '_expiration_date', get_field('start_time')); //Event date

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
    }

}
add_action('acf/save_post', 'austeve_update_wc_product', 20);


function austeve_pre_get_posts_order_events( $query ) {
	
	// do not modify queries in the admin, or if viewing a single event page, or if being displayed from shortcode
	if( is_admin() || is_single() || array_key_exists('from_shortcode', $query->query) || array_key_exists('do_not_filter', $query->query) ) {
		
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

			$currentNum = array_key_exists($orderId, $checked_in_guest_list) ? intval($checked_in_guest_list[$orderId]) : 0;

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
				$checked_in_guest_list[$orderId] = $currentNum + 1;
				if (update_field('guest_list', json_encode($checked_in_guest_list), $eventId))
					echo $checked_in_guest_list[$orderId];
				else
					echo "#error#";
				
				die();
			}
			else if (!$increaseAttendance)
			{
				$checked_in_guest_list[$orderId] = $currentNum - 1;
				if (update_field('guest_list', json_encode($checked_in_guest_list), $eventId))
					echo $checked_in_guest_list[$orderId];
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

				$userdata = get_userdata( get_field('_customer_user', $sale->order_id));
				$sales[$id]['customer_id'] = $userdata->ID;
				$sales[$id]['customer_name'] = $userdata->first_name." ".$userdata->last_name;
				$sales[$id]['customer_email'] = $userdata->user_email;
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

}
new AUSteve_EventHelper();

?>