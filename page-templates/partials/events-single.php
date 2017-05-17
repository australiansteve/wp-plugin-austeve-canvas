<?php
/**
 * Template part for displaying single events.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * 
 * @package AUSteve Canvas
 */
?>
<article id="post-<?php the_ID(); ?>">
	
	<div class="entry-content">
	
		<div class="row">
			
			<div class="small-12 columns">
				<h2 class="event-title"><?php echo the_title(); ?></h2>
			</div>

		</div>


		<div class="row">
			
			<div class="small-12 medium-3 columns">
		
				<?php
				$creationId = get_field('creation');
				$artist = get_field('artist', $creationId);
				$the_creation = get_field('image', $creationId);
				?>

				<a href="<?php echo get_permalink($creationId)?>">
					<img class="event-creation" src="<?php echo $the_creation['sizes']['medium'];?>" width="<?php echo $the_creation['sizes']['medium-width'];?>" height="<?php echo $the_creation['sizes']['medium-height'];?>"/>
					<br/>
					<span class="creation-title">
						<?php echo get_the_title($creationId); ?>
					</span>
				</a>
				<br/>
				by 
				<br/>
				<span class="creation-artist">
					<a href="<?php echo get_permalink($artist)?>"><?php echo $artist->post_title; ?></a>
				</span>

			</div>
			
			<div class="small-12 medium-6 columns">

				<div class='event-date'>
				<!-- Date -->
				<?php 
				if (get_field('start_time'))
				{
					$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time'));
					echo $eventDate->format('F jS Y @g:ia'); 
				}
				?>
				</div>

				<?php 
				$venue = get_field('venue');
				?>
				<div class='event-venue'>

					<a data-open="mapModal<?php echo $venue->ID; ?>">

						<i class="fa fa-map-marker" aria-hidden="true"></i>
						<?php echo $venue->post_title; ?>

					</a>

					<?php
					if (get_field('accessible', $venue->ID)) 
					{
						echo "<i class='fa fa-wheelchair has-tooltip' aria-hidden='true' title='This venue is accessible'></i>";
					}

					?>
					<div class="reveal event-venue-modal" id="mapModal<?php echo $venue->ID; ?>" data-reveal style="min-height: 400px" data-venue-id="<?php echo $venue->ID;?>">
						<?php
						include(plugin_dir_path( __FILE__ ) . '/event-venue-modal.php');
						?>
					</div>

				</div>

				<?php 
				//Get Categories
				$terms = get_the_terms( $creationId, 'austeve_creation_categories' );
				$termArray = array();
	                         
				if ( $terms && ! is_wp_error( $terms ) )
				{
					foreach ( $terms as $term ) {
	 
					    // The $term is an object, so we don't need to specify the $taxonomy.
					    $term_link = get_term_link( $term );
					    
					    // If there was an error, continue to the next term.
					    if ( is_wp_error( $term_link ) ) {
					        continue;
					    }
					 
					    // We successfully got a link. Print it out.
					    $termArray[] = '<a href="' . esc_url( $term_link ) . '">' . $term->name . '</a>';
					}
				}
				?>
				<div class='category'>Creation category: <?php echo implode($termArray, ", "); ?></div>

				<?php 
				$host_info = get_field('host');
				error_log("HOST:".print_r($host_info, true));
				
				$hostRating = austeve_calculate_host_rating($host_info['ID']);
				$hostProfile = austeve_get_host_profile($host_info['ID']);
		    	$host_event_count = austeve_get_number_host_events($host_info['ID']);

				error_log("HOST PROFILE: ".print_r($hostProfile, true));
				?>

				<div class='event-instructor' data-id='<?php echo $host_info['ID']; ?>'>Host: <span class='has-tooltip' title="<?php echo $hostRating;?> from <?php echo $host_event_count;?> event<?php echo count($host_event_count) != 1 ? "s": "";?>">

				<?php 
				if ($hostProfile)
				{
			    	?>		    	
			    		<a href='<?php echo get_permalink($hostProfile->ID);?>'>

			    	<?php
						echo $host_info['display_name'];
					?>
						</a>
				<?php
				}
				else {
					echo $host_info['display_name'];
				}		

				?>
					</span>
				</div> <!-- END .event-instructor -->

				<?php
				$difficultyField = get_field_object('difficulty_level', $creationId);
				$difficultyValue = $difficultyField['value'];
				$difficultyLabel = $difficultyField['choices'][ $difficultyValue ];
				?>
				<div class='event-level'>Difficulty: <?php echo $difficultyLabel; ?></div>


				<div class="event-description">
					<?php echo get_field('description');?>
				</div>		
				
				<?php if (get_field('price_friendly')) : ?>
					<div class="event-price-friendly"><strong>Price:</strong> <?php echo get_field('price_friendly'); ?></div>
				<?php endif; ?>

				<?php if (get_field('arrival')) : ?>
					<div class="event-arrival"><strong>Arrival:</strong> <?php echo get_field('arrival'); ?></div>
				<?php endif; ?>

				<?php if (get_field('seating')) : ?>
					<div class="event-seating"><strong>Seating:</strong> <?php echo get_field('seating'); ?></div>
				<?php endif; ?>

				<?php if (get_field('event_duration')) : ?>
					<div class="event-duration"><strong>Event Length:</strong> <?php echo get_field('event_duration'); ?></div>
				<?php endif; ?>

				<?php if (get_field('age_requirement')) : ?>
					<div class="event-age"><strong>Age Requirement:</strong> <?php echo get_field('age_requirement'); ?></div>
				<?php endif; ?>

				<?php if (get_field('dress_code')) : ?>
					<div class="event-dress"><strong>Dress code:</strong> <?php echo get_field('dress_code'); ?></div>
				<?php endif; ?>
				
			</div>

			<div class="small-12 medium-3 columns">
	
				<span class="event-cart">
				<?php 

					$now = new DateTime();
					$wc_expiry = get_field('_expiration_date', get_field('wc_product'));
					error_log("Now: ".date('Y-m-d H:i:s'));
					error_log(get_the_ID(). "expires (raw):".print_r($wc_expiry, true));
					error_log(get_the_ID(). "expires:".$wc_expiry->format('Y-m-d H:i:s'));

					if ($wc_expiry && $wc_expiry > $now)
					{
						echo do_shortcode('[canvas_to_cart id="'.get_field('wc_product').'" include_price=true]');
					}
					else
					{
						echo "<div class='past-event'>Event has expired.</div>";
					}
					
				?>
				</span>
				
			</div>
		
		</div>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
