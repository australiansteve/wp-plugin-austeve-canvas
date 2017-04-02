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
				$event_creation = get_field('creation');
				$artist = get_field('artist', $event_creation);
				error_log(print_r($event_creation, true));
				$the_creation = get_field('image', $event_creation);
				?>

				<a href="<?php echo get_permalink($event_creation)?>">
					<img class="event-creation" src="<?php echo $the_creation['sizes']['medium'];?>" width="<?php echo $the_creation['sizes']['medium-width'];?>" height="<?php echo $the_creation['sizes']['medium-height'];?>"/>
					<br/>
					<span class="creation-title">
						<?php echo get_the_title($event_creation); ?>
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

				<p class="event-venue">
					<?php 
					$venue = get_field('venue');
					?>
					<a href="<?php echo the_permalink($venue); ?>">
						<?php echo get_the_title($venue);?>
					</a>
				</p>	

				<span class="event-description">
						<?php echo get_field('description');?>
				</span>		
				

				<span class="event-host">
					<?php 
					$host_info = get_field('host');
					?>
					Instructor: <?php echo $host_info['display_name'];?>
				</span>		
				
			</div>

			<div class="small-12 medium-3 columns">
	
				<span class="event-cart">
				<?php 

					$now = new DateTime();
					$wc_expiry = get_field('_expiration_date', get_field('wc_product'));
					//echo "Now: ".date('Y-m-d H:i:s');
					//echo "<br/>Expires:".$wc_expiry->format('Y-m-d H:i:s');
					$untilExpiry = $wc_expiry->diff($now);
					//echo "<br/>".$untilExpiry->format('%Y-%m-%d %H:%i:%s');

					if ($wc_expiry > $now)
					{
						echo do_shortcode('[canvas_to_cart id="'.get_field('wc_product').'" include_price=true]');
					}
					else
					{
						echo "Event has expired.";
					}
					
				?>
				</span>
				
			</div>
		
		</div>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
