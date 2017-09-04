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
			<main id="main" class="site-main single-event" role="main">

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
						$emailList=array();
						foreach($guestlist as $orderId=>$guest)
						{
							error_log( "Order: ".$orderId);
							$emailList[] = $guest['customer_email'];
							$checkedInAlready = 0;
							if (array_key_exists($orderId, $checklist))
							{
								if (array_key_exists('qty', $checklist[$orderId]))
								{
									$checkedInAlready = $checklist[$orderId]['qty'];
								}
								else if (array_key_exists('present', $checklist[$orderId]))
								{
									//early implementation used 'present' to store qty of guests present for each order. Added for backward compatibility
									$checkedInAlready = $checklist[$orderId]['present'];
								}
							} 

							$colourClass = $checkedInAlready > 0 ? ($checkedInAlready >= $guest['qty'] ? 'all-present' : 'semi-present') : '';

							echo "<div class='row guest-order'>";
							echo "<div class='small-7 medium-8 columns'>";
							echo "<div class='row'><div class='small-12 medium-6 columns'>".$guest['customer_name']."</div><div class='small-12 medium-6 columns'><em>".$guest['customer_email']."</em></div></div>";
							echo "</div>";
							echo "<div class='small-5 medium-4 columns event-attendees event-attendees-".$orderId." ".$colourClass."' data-event='".get_the_ID()."' data-user='".$guest['customer_id']."' data-max='".$guest['qty']."'>";

							echo "<button class='update-guest-list down' data-order-id='".$orderId."'><i class='fa fa-minus-circle' aria-hidden='true'></i></button>";				
							echo "<span class='attendees'><span class='current-attendees'>$checkedInAlready</span>/".$guest['qty']."</span>";
							echo "<button class='update-guest-list up' data-order-id='".$orderId."'><i class='fa fa-plus-circle' aria-hidden='true'></i></button>";
							echo "</div>";
							echo "</div>";
						}

						?>
						<div class="row columns send-guest-emails">
							<button class='button' data-open='email-guests-1'>Reminder email details</button>
							<button class='button' data-open='email-guests-2'>Event review follow-up</button>
						</div>

						<div class="reveal email-guests" id="email-guests-1" data-reveal>
						  <h1><?php echo the_title(); ?></h1>
						  <h3><?php echo get_field('venue')->post_title; ?>, <?php 
								$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time'));
								echo $eventDate->format('F jS Y @g:ia');?></h3>
						  <p class="guest-list">Guestlist: <?php echo implode($emailList, '; ');?></p>
						  <div class="message">
							<p>It's almost time to create!  We're looking forward to seeing you at <?php echo get_the_title(get_field('venue')); ?> for "<?php echo the_title(); ?>". </p>
							<p>Start time: <?php echo $eventDate->format('g:ia, F jS Y');?></p>
							<p>Address: <?php echo get_field('address', get_field('venue'))['address']; ?></p>							
							<?php echo get_field('arrival');?>
							<p>See you there!</p>
						  </div>
						  <button class="close-button" data-close aria-label="Close modal" type="button">
						    <span aria-hidden="true">&times;</span>
						  </button>
						</div>

						<div class="reveal email-guests" id="email-guests-2" data-reveal>
						  <h1><?php echo the_title(); ?></h1>
						  <h3><?php echo get_field('venue')->post_title; ?>, <?php 
								$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time'));
								echo $eventDate->format('F jS Y @g:ia');?></h3>
						  <p class="guest-list">Guestlist: <?php echo implode($emailList, '; ');?></p>
						  <div class="message">
						  	<p>Hello Create over Cocktails Insiders!

							<p>How was your latest Create over Cocktails experience?  Now you can <a href="<?php echo site_url('my-account'); ?>">log in to your account</a> & rate your most recent experience as well as any other previously attended events purchased through our new website!  We appreciate your feedback and look forward to hearing from you! :)
						  </div>
						  <button class="close-button" data-close aria-label="Close modal" type="button">
						    <span aria-hidden="true">&times;</span>
						  </button>
						</div>

						<?php
						echo "</div> <!-- END #event-guest-list -->";

				    	//Reset back to the main loop
						wp_reset_postdata();
					}
				?>
				
			<?php endwhile; // end of the loop. 
			$nonce = wp_create_nonce( 'austevesaveattendance' );
	    	?>

			<script type='text/javascript'>
				<!--
				function save_attendance( eventId, orderId, userId, maxAttendees, increase ) {
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

							var intResponse = Number.isInteger(parseInt(html));

							if (intResponse && parseInt(html) == maxAttendees)
							{
								jQuery(".event-attendees-"+orderId).addClass("all-present");
								jQuery(".event-attendees-"+orderId).removeClass("semi-present");
							}
							else if (intResponse && parseInt(html) == "0" )
							{
								jQuery(".event-attendees-"+orderId).removeClass("all-present");
								jQuery(".event-attendees-"+orderId).removeClass("semi-present");
							}
							else if (intResponse)
							{
								jQuery(".event-attendees-"+orderId).removeClass("all-present");
								jQuery(".event-attendees-"+orderId).addClass("semi-present");
							}
						}
					}); //close jQuery.ajax(
				}
				// When the document loads do everything inside here ...
				jQuery(".update-guest-list").on('click', function() {
					var orderId = jQuery(this).attr('data-order-id');

					var eventToSave = jQuery(".event-attendees-" + orderId).attr('data-event');
					var userId = jQuery(".event-attendees-" + orderId).attr('data-user');
					var maxAttendees = jQuery(".event-attendees-" + orderId).attr('data-max');

					save_attendance( eventToSave, orderId, userId, maxAttendees, jQuery(this).hasClass('up') );
				});

				-->
			</script>
			
			</main><!-- #main -->
		</div><!-- #primary -->

	</div><!-- .columns end -->

</div><!-- .row end -->

<?php 
    include( plugin_dir_path( __FILE__ ) . 'google-map.php');
?>
<?php get_footer(); ?>
