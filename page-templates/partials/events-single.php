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
					<div class="reveal" id="mapModal<?php echo $venue->ID; ?>" data-reveal style="min-height: 400px" data-venue-id="<?php echo $venue->ID;?>">
					  <h3><?php echo $venue->post_title; ?></h3>
					  <?php 
						$location = get_field('address', $venue->ID);
						error_log("Location:".print_r($location, true));
						if( !empty($location) ) {
							//echo print_r($location, true);
						?>
						<p><?php echo $location['address']; ?> <br/> <a href='https://www.google.ca/maps/place/<?php echo urlencode($location['address']);?>' target='_blank'>Get Directions</a></p>
						<div class="acf-map">
							<div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>"></div>
						</div>
						<?php } ?>				  
					</div>

				</div>

				<?php 
				//Get Categories
				$terms = get_the_terms( $creationId, 'austeve_creation_categories' );
				$termString = '';
	                         
				if ( $terms && ! is_wp_error( $terms ) )
				{
					$term = $terms[0];

					// The $term is an object, so we don't need to specify the $taxonomy.
				    $term_link = get_term_link( $term );
				    
				    // If there was an error, continue to the next term.
				    if ( is_wp_error( $term_link ) ) {
				        continue;
				    }
				 
				    // We successfully got a link. Print it out.
				    $termString = '<a href="' . esc_url( $term_link ) . '">' . $term->name . '</a>';

				    $parent = $term->parent;
				    while ($parent != 0)
				    {
				    	$term = get_term($parent, 'austeve_creation_categories');
						//echo print_r($term, true);
						if ( $term && ! is_wp_error( $term ) )
						{
							$term_link = get_term_link( $term );
					    	if ( is_wp_error( $term_link ) ) {
						        continue;
						    }
						    $termString = "<a href='". esc_url( $term_link ) ."'>".$term->name."</a> / ".$termString; 

						    $parent = $term->parent;
						}
						else
						{
							continue; //emergency break
						}
				    }
				}
				?>
				<div class='category'><?php echo $termString; ?></div>



				<?php 
				$host_info = get_field('host');
				error_log("HOST:".print_r($host_info, true));
				
				//Get all host events to calculate rating
				$args = array(
			        'posts_per_page' => -1,
			        'post_type' => 'austeve-events',
			        'post_status' => array('publish'),
			        'orderby' => 'name',
			        'order' => 'ASC',
			        'do_not_filter' => 'true',
			    );

	    		$meta_query = array('relation' => 'AND');

				$past_events_query = array(
		            'key'           => 'start_time',
		            'compare'       => '<=',
		            'value'         => date('Y-m-d H:i:s'),
		            'type'          => 'DATETIME',
		        );
		        $meta_query[] = $past_events_query;
			
			    $host_query = array(
		            'key'           => 'host',
		            'compare'       => '=',
		            'value'         => $host_info['ID'],
		            'type'          => 'NUMERIC',
	            );
		        $meta_query[] = $host_query;

		        $args['meta_query'] = $meta_query;
			    $hosts_events = get_posts( $args );

			    $eventsWithRatingsCount = 0;
			    $hostRatingTotal = 0;
			    foreach($hosts_events as $event)
			    {
			        error_log("Host".$host_info['ID']." event:".$event->post_title);
			        $hostEventRating = get_field('host_rating', $event->ID);

			        if($hostEventRating && $hostEventRating >= 0)
			        {
			        	$hostRatingTotal += floatval($hostEventRating);
		        		$eventsWithRatingsCount++;
			        }
			    }
			    $hostRating = ($hostRatingTotal > 0 && $eventsWithRatingsCount > 0) ? "Average rating: ".round(($hostRatingTotal / $eventsWithRatingsCount), 2)."/5" : 'No ratings';

				?>

				<div class='event-instructor' data-id='<?php echo $host_info['ID']; ?>'>Instructor: <span class='has-tooltip' title="<?php echo $hostRating;?> from <?php echo count($hosts_events);?> event<?php echo count($hosts_events) != 1 ? "s": "";?>"><?php echo $host_info['display_name'];?></span></div>

				<?php
				$difficultyField = get_field_object('difficulty_level', $creationId);
				$difficultyValue = $difficultyField['value'];
				$difficultyLabel = $difficultyField['choices'][ $difficultyValue ];
				?>
				<div class='event-level'>Difficulty: <?php echo $difficultyLabel; ?></div>


				<div class="event-description">
					<?php echo get_field('description');?>
				</div>		
				

				
			</div>

			<div class="small-12 medium-3 columns">
	
				<span class="event-cart">
				<?php 

					$now = new DateTime();
					$wc_expiry = get_field('_expiration_date', get_field('wc_product'));
					//echo "Now: ".date('Y-m-d H:i:s');
					//echo "<br/>Expires:".$wc_expiry->format('Y-m-d H:i:s');

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
