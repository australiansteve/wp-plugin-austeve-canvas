<?php
/**
 * The template for displaying all single events.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package AUSteve Canvas
 */
acf_form_head();
get_header(); ?>

<div class="row"><!-- .row start -->

	<div class="small-12 columns"><!-- .columns start -->

		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				
				<?php 

					//Display the event template first
            		if (locate_template('page-templates/partials/events-single.php') != '') {
						// yep, load the page template
						get_template_part('page-templates/partials/events', 'single');
					} else {
						// nope, load the default
						include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/events-single.php');
					}

					function get_sorted_event_guestlist() {

						//Display guest list if user is admin or host
						error_log("Finding guests for event ".get_the_ID());
						error_log("WC product ".get_field('wc_product', get_the_ID()));

						global $post, $wpdb;
						$post_id = get_field('wc_product', get_the_ID());

						// Get qty for each order
						$sale_qtys = $wpdb->get_results( $wpdb->prepare(
							"SELECT oim.order_item_id as order_item_id, oim.meta_value as qty 
								FROM wp_woocommerce_order_itemmeta oim 
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
							get_field('wc_product', get_the_ID()),
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
							get_field('wc_product', get_the_ID()),
							'wc-completed'
						), OBJECT_K );

						error_log("Item Sales: ".print_r($item_sales, true));

						$sales = array();
						foreach($item_sales as $id=>$sale)
						{	
							$sales[$id]['order_id'] = $sale->order_id;
							$sales[$id]['qty'] = $sale_qtys[$id]->qty;

							$userdata = get_userdata( get_field('_customer_user', $sale->order_id));
							$sales[$id]['customer_id'] = $userdata->ID;
							$sales[$id]['customer_name'] = $userdata->first_name." ".$userdata->last_name;
						}

						error_log("Merged array: ".print_r($sales, true));

						// Obtain a list of columns
						foreach ($sales as $key => $row) {
						    $customer_name[$key]  = $row['customer_name'];
						}

						// Sort the data with volume descending, edition ascending
						// Add $data as the last parameter, to sort by the common key
						array_multisort($customer_name, SORT_ASC, $sales);

						return $sales;
					}
					$guestlist = get_sorted_event_guestlist();

					error_log("Sorted guestlist: ".print_r($guestlist, true));

					echo "<div class='row'>";
					echo "<div class='small-12 columns'><h2>Guestlist</h2></div>";
					echo "</div>";
					echo "<div class='row'>";
					echo "<div class='small-6 columns'><strong>Name</strong></div>";
					echo "<div class='small-3 columns'><strong>Tickets</strong></div>";
					echo "<div class='small-3 columns'><strong>Checked In</strong></div>";
					echo "</div>";

					//Fetch the current checked in guestlist
					$checklist = get_field('guest_list');
					if ($checklist)
					{
						$checklist = json_decode($checklist, true);
					}
					else
					{
						$checklist = array();
					}
					//$checklist['157'] = 1;

					error_log("Saved checklist: ".print_r($checklist, true));

					foreach($guestlist as $guest)
					{
						error_log( "Order: ".$guest['order_id']);
						$checkedInAlready = array_key_exists($guest['order_id'], $checklist) ? $checklist[$guest['order_id']] : 0;
						echo "<div class='row'>";
						echo "<div class='small-6 columns'>".$guest['customer_name']."</div>";
						echo "<div class='small-3 columns'>".$guest['qty']."</div>";
						echo "<div class='small-3 columns'>";
						echo "<input type='number' value='$checkedInAlready' class='event-check-in' data-order-id='".$guest['order_id']."' min='0' max='".$guest['qty']."' />";
						echo "<input type='button' class='update-guest-list' value='Check In' />";
						echo "</div>";
						echo "</div>";
					}

					acf_form(array('fields' => array('guest_list')));

			    	//Reset back to the main loop
					wp_reset_postdata();
				?>
				
				<!--?php 
				the_post_navigation(array(
			        'prev_text'          => '<i class="fa fa-arrow-left"></i> Next',
			        'next_text'          => 'Previous <i class="fa fa-arrow-right"></i>',
			        'screen_reader_text' => __( 'More events:' ),
			    )); ?-->

			<?php endwhile; // end of the loop. ?>

			<script type="text/javascript">

				jQuery(document).ready(function($) {

					$('.update-guest-list').on("click", function(){
					    
					    var updatedCheckIn = {};
					    jQuery('.event-check-in').each(function() {
					    	var index = jQuery(this).attr('data-order-id');					    	
					    	updatedCheckIn[index] = jQuery(this).val();
					    });
					
						jQuery('.acf-field[data-name=guest_list] input').val(JSON.stringify(updatedCheckIn)); 
						jQuery( ".acf-form" ).submit();
					});


				});
			</script>
			
			</main><!-- #main -->
		</div><!-- #primary -->

	</div><!-- .columns end -->

</div><!-- .row end -->

<?php get_footer(); ?>
