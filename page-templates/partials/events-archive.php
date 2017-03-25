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

			$the_creation = get_field('creation', $creationId);
			?>

			<img src="<?php echo $the_creation['sizes']['thumbnail'];?>" width="<?php echo $the_creation['sizes']['thumbnail-width'];?>" height="<?php echo $the_creation['sizes']['thumbnail-height'];?>"/>
			<?php
			}
			?>

</a>
		</div>

		<div class="small-12 medium-6 columns event-details">

			<!-- Date -->
			<?php 
			if (get_field('start_time'))
			{
				$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time'));
				echo $eventDate->format('d M Y'); 
			}
			?>

			<!-- Event -->
			<?php 
			$venue = get_field('venue'); 
			?>

			<h3 class='event-name'><a href="<?php echo get_permalink();?>"><?php echo the_title(); ?></a></h3>
			
			<p class='event-venue'>

				<a data-open="mapModal<?php echo $venue->ID; ?>">

					<i class="fa fa-map-marker" aria-hidden="true"></i>
					<?php echo $venue->post_title; ?>

				</a>

				<?php
				if (get_field('accessible', $venue->ID)) 
				{
					echo "<i class='fa fa-wheelchair' aria-hidden='true' title='This venue is accessible'></i>";
				}

				?>
				<div class="reveal" id="mapModal<?php echo $venue->ID; ?>" data-reveal>
				  <h3><?php echo $venue->post_title; ?></h3>
				  <p> Map goes here</p>
				</div>

			</p>

			<?php 
			$host_info = get_field('host');
			?>

			<p class='event-instructor'>Instructor: <?php echo $host_info['display_name'];?></p>

			<?php
			$difficultyField = get_field_object('difficulty_level', $creationId);
			$difficultyValue = $difficultyField['value'];
			$difficultyLabel = $difficultyField['choices'][ $difficultyValue ];
			?>
			<p class='event-level'>Difficulty: <?php echo $difficultyLabel; ?></p>

			<p class='event-creation-tags'>Tags:
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
			</p>
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
				echo "<div class='past-event'>This event has passed</div>";
			}
			?>
		</div>

	</div>


