<?php
/**
 * Template part for displaying archived events.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * 
 * @package AUSteve Canvas
 */
?>


	<div class="row">
		
		<div class="small-12 medium-3 columns event-creation">
			<!-- Image -->
			
<a href="<?php echo get_permalink();?>">
			<?php
			$creationId = get_field('creation');
			if ( has_post_thumbnail( $creationId ) ) 
			{
				echo get_the_post_thumbnail( $creationId, array( 300, 300) );
			}
			else 
			{

			$the_creation_image = get_field('image', $creationId);
			?>

			<img src="<?php echo $the_creation_image['sizes']['thumbnail'];?>" width="<?php echo $the_creation_image['sizes']['thumbnail-width'];?>" height="<?php echo $the_creation_image['sizes']['thumbnail-height'];?>"/>
			<?php
			}
			?>

</a>
		</div>

		<div class="small-12 medium-6 columns event-details">

			<!-- Event -->
			<?php 
			$venue = get_field('venue'); 
			?>

			<h3 class='event-name'><a href="<?php echo get_permalink();?>"><?php echo the_title(); ?></a></h3>

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
			<div class='category'><?php echo implode($termArray, ", "); ?></div>


			<?php 
			$host_info = get_field('host');
			error_log("HOST:".print_r($host_info, true));
			
			$hostRating = austeve_calculate_host_rating($host_info['ID']);
			$hostProfile = austeve_get_host_profile($host_info['ID']);
	    	$host_event_count = austeve_get_number_host_events($host_info['ID']);

			error_log("HOST PROFILE: ".print_r($hostProfile, true));
			?>

			<div class='event-instructor' data-id='<?php echo $host_info['ID']; ?>'>Instructor: <span class='has-tooltip' title="<?php echo $hostRating;?> from <?php echo $host_event_count;?> event<?php echo count($host_event_count) != 1 ? "s": "";?>">

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

			<div class='event-creation-tags'>Tags:
			<?php
			$creation_terms = wp_get_object_terms( $creationId,  'austeve_creation_tags' );
			$termOutput = array();
			//error_log(print_r($creation_terms, true));
			if ( ! empty( $creation_terms ) ) {
				if ( ! is_wp_error( $creation_terms ) ) {
					foreach( $creation_terms as $term ) {
						//error_log(print_r($term, true));
						$termOutput[] = '<a href="' . get_term_link( $term->slug, 'austeve_creation_tags' ) . '">' . esc_html( $term->name ) . '</a>'; 
					}
				}
			}
			echo implode(", ", $termOutput);
			?>				
			</div>
		</div>

		<div class="small-12 medium-3 columns">
			<?php

		    // find date time now
		    $date_now = date('Y-m-d H:i:s');
	    	$event_start = get_field('start_time');

	    	error_log($date_now." vs ".$event_start);

	    	if ($event_start > $date_now)
	    	{

				if (get_field('wc_product') && get_field('price'))
				{
					echo do_shortcode('[canvas_to_cart id="'.get_field('wc_product').'"]');
				}
				else 
				{
					echo "Cannot add to cart";
				}
			}
			else 
			{
				echo "<div class='past-event'>Event has expired.</div>";
			}
			?>
		</div>

	</div>


