<?php
/**
 * The template for displaying all single events.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package AUSteve Canvas
 */
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

					
					if ( AUSteve_EventHelper::current_user_is_admin(get_the_ID())) {

						$guestlist = AUSteve_EventHelper::get_sorted_event_guestlist(get_the_ID());

						error_log("Sorted guestlist: ".print_r($guestlist, true));

						echo "<div id='event-guest-list'>";
						echo "<div class='row'>";
						echo "<div class='small-12 columns'><h2>Guestlist</h2></div>";
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

						error_log("Saved checklist: ".print_r($checklist, true));

						foreach($guestlist as $orderId=>$guest)
						{
							error_log( "Order: ".$orderId);
							$checkedInAlready = array_key_exists($orderId, $checklist) ? $checklist[$orderId]['qty'] : 0;
							$buttonClass = $checkedInAlready > 0 ? ($checkedInAlready >= $guest['qty'] ? 'all-present' : 'semi-present') : '';

							echo "<div class='row'>";
							echo "<div class='small-6 medium-8 columns'>";
							echo "<div class='row'><div class='small-12 medium-6 columns'>".$guest['customer_name']."</div><div class='small-12 medium-6 columns'><em>".$guest['customer_email']."</em></div></div>";
							echo "</div>";
							echo "<div class='small-6 medium-4 columns event-attendees-".$orderId."' data-event='".get_the_ID()."' data-user='".$guest['customer_id']."' data-max='".$guest['qty']."'>";

							echo "<a class='update-guest-list down' data-order-id='".$orderId."'><i class='fa fa-minus-circle' aria-hidden='true'></i></a>";				
							echo "<span class=''><span class='current-attendees'>$checkedInAlready</span>/".$guest['qty']."</span>";
							echo "<button class='update-guest-list up' data-order-id='".$orderId."'><i class='fa fa-plus-circle' aria-hidden='true'></i></button>";
							echo "</div>";
							echo "</div>";
						}

						echo "</div> <!-- END #event-guest-list -->";

				    	//Reset back to the main loop
						wp_reset_postdata();
					}
				?>
				
				<!--?php 
				the_post_navigation(array(
			        'prev_text'          => '<i class="fa fa-arrow-left"></i> Next',
			        'next_text'          => 'Previous <i class="fa fa-arrow-right"></i>',
			        'screen_reader_text' => __( 'More events:' ),
			    )); ?-->

			<?php endwhile; // end of the loop. 
			$nonce = wp_create_nonce( 'austevesaveattendance' );
	    	?>

			<script type='text/javascript'>
				<!--
				function save_attendance( eventId, orderId, userId, attendees, maxAttendees, increase ) {
					console.log("Saving attendance for event " + eventId);
					jQuery.ajax({
						type: "post", 
						url: '<?php echo admin_url("admin-ajax.php"); ?>', 
						data: { 
							action: 'save_attendance', 
							eventId: eventId, 
							orderId: orderId, 
							userId: userId, 
							increase : increase,
							_ajax_nonce: '<?php echo $nonce; ?>' 
						},
						beforeSend: function() {jQuery(".event-attendees-"+orderId+" .current-attendees").html("<i class='fa fa-spinner fa-pulse fa-fw'></i>");},
						success: function(html){ //so, if data is retrieved, store it in html
							console.log("Response: " + html);

							jQuery(".event-attendees-"+orderId+" .current-attendees").html(html);
						}
					}); //close jQuery.ajax(
				}
				// When the document loads do everything inside here ...
				jQuery(".update-guest-list").on('click', function() {
					var orderId = jQuery(this).attr('data-order-id');

					var eventToSave = jQuery(".event-attendees-" + orderId).attr('data-event');
					var userId = jQuery(".event-attendees-" + orderId).attr('data-user');
					var maxAttendees = jQuery(".event-attendees-" + orderId).attr('data-max');
					var attendees = jQuery(".event-attendees-" + orderId).find('current-attendees').html();

					save_attendance( eventToSave, orderId, userId, attendees, maxAttendees, jQuery(this).hasClass('up') );
				});

				-->
			</script>
			
			</main><!-- #main -->
		</div><!-- #primary -->

	</div><!-- .columns end -->

</div><!-- .row end -->

<?php get_footer(); ?>
