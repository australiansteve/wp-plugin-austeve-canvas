<?php
/*
 * Add custom endpoint that appears in My Account Page - WooCommerce 2.6
 * Ref - https://gist.github.com/claudiosmweb/a79f4e3992ae96cb821d3b357834a005#file-custom-my-account-endpoint-php
 */
class AUSteve_My_Account_Reviews {
	/**
	 * Custom endpoint name.
	 *
	 * @var string
	 */
	public static $endpoint = 'my-reviews';
	/**
	 * Plugin actions.
	 */
	public function __construct() {
		// Actions used to insert a new endpoint in the WordPress.
		add_action( 'init', array( $this, 'add_endpoints' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
		// Change the My Accout page title.
		add_filter( 'the_title', array( $this, 'endpoint_title' ) );
		// Insering your new tab/page into the My Account page.
		add_filter( 'woocommerce_account_menu_items', array( $this, 'new_menu_items' ) );
		add_action( 'woocommerce_account_' . self::$endpoint .  '_endpoint', array( $this, 'endpoint_content' ) );
	}
	/**
	 * Register new endpoint to use inside My Account page.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
	 */
	public function add_endpoints() {
		add_rewrite_endpoint( self::$endpoint, EP_ROOT | EP_PAGES );
	}
	/**
	 * Add new query var.
	 *
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = self::$endpoint;
		return $vars;
	}
	/**
	 * Set endpoint title.
	 *
	 * @param string $title
	 * @return string
	 */
	public function endpoint_title( $title ) {
		global $wp_query;
		$is_endpoint = isset( $wp_query->query_vars[ self::$endpoint ] );
		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'Reviews', 'woocommerce' );
			remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
		}
		return $title;
	}
	/**
	 * Insert the new endpoint into the My Account menu.
	 *
	 * @param array $items
	 * @return array
	 */
	public function new_menu_items( $items ) {
		// Remove the logout menu item.
		$logout = $items['customer-logout'];
		//unset( $items['customer-logout'] );
		// Insert your custom endpoint.
		$items[ self::$endpoint ] = __( 'Reviews', 'woocommerce' );
		// Insert back the logout item.
		//$items['customer-logout'] = $logout;
		return $items;
	}

	private function wc_get_customer_orders() {
    
	    // Get all customer orders
	    $customer_orders = get_posts( array(
	        'numberposts' => -1,
	        'meta_key'    => '_customer_user',
	        'meta_value'  => get_current_user_id(),
	        'post_type'   => wc_get_order_types(),
	        'post_status' => array_keys( wc_get_order_statuses() ),
	    ) );
	    
	    return $customer_orders;
    }

	private function review_form($eventID) {
    	?>

		<div class='event' data-event='<?php echo $eventID; ?>'>
			<label>How would you rate the host of the event?</label>
			<select class='host-rating'>
				<option value='5'>5 - Great</option>
				<option value='4'>4 - Good</option>
				<option value='3'>3 - OK</option>
				<option value='2'>2 - Bad</option>
				<option value='1'>1 - Terrible</option>
			</select>

			<label>Feedback</label>
			<textarea class='feedback' rows='6'></textarea>

			<button class='save-review' data-event='<?php echo $eventID; ?>'>Send review</button>
		</div>

		<?php
    }

	private function client_side_script() {
		$nonce = wp_create_nonce( 'austevesavereview' );
    	?>

		<script type='text/javascript'>
			<!--
			function save_review( eventId, rating, feedback ) {
				console.log("Saving event " + eventId);
				jQuery.ajax({
					type: "post", 
					url: '<?php echo admin_url("admin-ajax.php"); ?>', 
					data: { 
						action: 'save_review', 
						eventId: eventId, 
						rating: rating, 
						feedback: feedback, 
						_ajax_nonce: '<?php echo $nonce; ?>' 
					},
					beforeSend: function() {
						jQuery("span.progress").each(function(){jQuery(this).remove()});
						jQuery(".save-review[data-event="+eventId+"]").after("<span class='progress'>Saving...</span>");
					},
					success: function(html){ //so, if data is retrieved, store it in html
						console.log("Response: " + html);

						if (html == '1')
						{
							jQuery(".event[data-event="+eventId+"] select").attr('disabled', 'disabled');
							jQuery(".event[data-event="+eventId+"] textarea").attr('disabled', 'disabled');
							jQuery(".save-review[data-event="+eventId+"] + .progress").html("Thankyou for your feedback!").addClass('success');
						}
						else
						{
							jQuery(".save-review[data-event="+eventId+"] + .progress").html(html).addClass('error');
						}
					}
				}); //close jQuery.ajax(
			}
			// When the document loads do everything inside here ...
			jQuery(".save-review").on('click', function() {
				var eventToSave = jQuery(this).attr('data-event');
				var rating = jQuery('.event[data-event='+eventToSave+']').find('.host-rating').val();
				var feedback = jQuery('.event[data-event='+eventToSave+']').find('.feedback').val();

				console.log("Sending save for event " + eventToSave);
				save_review( eventToSave, rating, feedback );
			});
			-->
		</script>

		<?php
    }

	/**
	 * Endpoint HTML content.
	 */
	public function endpoint_content() {
    	$date_now = date('Y-m-d H:i:s');
		?>

		<div class="woocommerce-MyAccount-reviews">

		<?php 

			$orders = self::wc_get_customer_orders();
			//error_log("Customer orders: ".print_r($orders, true));

			//If the customer has previous orders
			if (count($orders) > 0)
			{
				//Build up an array of all of the products that the user has purchased
				$product_ids = array();

				foreach($orders as $orderPost)
				{
					$order = new WC_Order( $orderPost->ID );
					$items = $order->get_items();
					//error_log("Order ".$orderPost->ID." items: ".print_r($items, true));

					foreach($items as $orderItem)
					{
						//error_log("Order ".$orderPost->ID." item data: ".print_r($orderItem->get_data(), true));
						array_push($product_ids, $orderItem->get_data()['product_id']);
					}
				}

				//Find the past events that the user has purchased tickets for from the array of product IDs
				$events = get_posts( array(
					'do_not_filter' => true,
			        'numberposts' => -1,
			        'post_type'   => 'austeve-events',
			        'post_status' => 'publish',
			        'meta_query'  => array(
						array(
							'key'     => 'wc_product',
							'value'   => implode(',', $product_ids),
							'compare' => 'IN',
							'type'    => 'NUMERIC',
						),
						array(
							'key'     => 'start_time',
							'value'   => $date_now,
							'compare' => '<=',
							'type'    => 'DATETIME',
						),

					)
			    ) );

				//Output a review form for each (if not already submitted)
				foreach($events as $event)
				{
					$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time', $event->ID));
					
					echo "<div class='event-review'>";				
					echo "<h4>".$event->post_title." - ".$eventDate->format('d M Y')."</h4>";

					$event_reviews = get_field('reviews', $event->ID);

					if ($event_reviews)
					{
						$event_review_array = json_decode($event_reviews, true);

						if (array_key_exists( get_current_user_id(), $event_review_array ))
						{
							error_log(get_current_user_id()." included? : ". print_r($event_review_array, true));
							echo "You've already reviewed this event. Thanks for your feedback<br/>";
							echo "<p>Host rating: ".$event_review_array[get_current_user_id()]['rating']."<br/>Feedback: <em>".$event_review_array[get_current_user_id()]['feedback']."</em></p>";
						}
						else 
						{
							self::review_form($event->ID);
						}
					}
					else 
					{
						self::review_form($event->ID);
					}

					echo "</div>";	
				}

				self::client_side_script();
			}
			else {
				echo "You have attended any events yet. Why not <a href=".site_url('/events').">find one now</a>?";
			}

		?>

		</div>

		<?php
	}
	/**
	 * Plugin install action.
	 * Flush rewrite rules to make our custom endpoint available.
	 */
	public static function install() {
		flush_rewrite_rules();
	}
}
new AUSteve_My_Account_Reviews();

function austeve_save_review_ajax() {
	check_ajax_referer( "austevesavereview" );

	if( $_POST[ 'eventId' ] )
	{
		//If rating is not given, or it's not a number, or it's out of bounds, do not save the review - indicative of tampering with the request
		if( !$_POST[ 'rating' ] || ! filter_var($_POST[ 'rating' ], FILTER_VALIDATE_INT) || intval($_POST['rating']) < 1 || intval($_POST['rating']) > 5 )
		{
			echo "Failed - Illegal rating";
			die();
		}

		$eventId = $_POST[ 'eventId' ];
		$rating = intval($_POST[ 'rating' ]);
		$feedback = $_POST[ 'feedback' ] ? $_POST[ 'feedback' ] : "";
		$user = get_current_user_id();

		error_log("AJAX save review for event: ".intval($eventId).". Rating: ".$rating.". Feedback: ".$feedback.". User: ".$user);

		//Get the guest list for the event
		$guest_list = json_decode(get_field('guest_list', $eventId), true);

		if (!is_array($guest_list))
		{
			echo "There was an error reviewing this event (Event ID: ".$eventId."). Please contact ".get_option('admin_email');
			die();
		}

		error_log(print_r($guest_list, true));
		foreach($guest_list as $order_id=>$info)
		{
			if ($info['customer_id'] == $user)
			{
				//User is allowed to leave a review since they attended the event

				//So, get the existing reviews
				$reviewsJSON = get_field('reviews', $eventId);
				$reviews = $reviewsJSON ? json_decode($reviewsJSON) : [];
				error_log("Reviews array: ".print_r($reviews, true));

				if( !is_array($reviews) && property_exists($reviews, $user) )
				{
					echo "It looks like you've already reviewed this event. You can only leave one review per event, sorry.";
					die();
				}

				$user_review = array();
				$user_review['rating'] = $rating;
				$user_review['feedback'] = $feedback;

				$reviews[$user] = $user_review;

				error_log("Reviews to save: ".print_r($reviews, true));

				echo update_field('reviews', json_encode($reviews), $eventId);

				//Update the overall host_rating field for the event
				$reviewTotal = 0;
				foreach($reviews as $review)
				{
					$reviewTotal += $review['rating'];
				}
				update_field('host_rating', round($reviewTotal/count($reviews), 2), $eventId);
				die();
			}
		}
		echo "You are not authorized to review this event";

	}
	die();
}
add_action( 'wp_ajax_save_review', 'austeve_save_review_ajax' );

function austeve_get_event_reviews_ajax() {
	check_ajax_referer( "austevegeteventreviews" );

	if( $_POST[ 'eventId' ] !== 'undefined' )
	{
		$event_id = $_POST[ 'eventId' ];
		if (get_field('reviews', $event_id))
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
		else {
			echo "No reviews for this event";
		}
		die();
	}
	error_log("ERROR: There was a problem retrieving event reviews");
	die();
}
add_action( 'wp_ajax_get_event_reviews', 'austeve_get_event_reviews_ajax' );