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
				$event_painting = get_field('painting');
				$artist = get_field('artist', $event_painting);
				error_log(print_r($event_painting, true));
				$the_painting = get_field('painting', $event_painting);
				?>

				<a href="<?php echo get_permalink($event_painting)?>">
					<img class="event-painting" src="<?php echo $the_painting['sizes']['medium'];?>" width="<?php echo $the_painting['sizes']['medium-width'];?>" height="<?php echo $the_painting['sizes']['medium-height'];?>"/>
					<br/>
					<span class="painting-title">
						<?php echo get_the_title($event_painting); ?>
					</span>
				</a>
				<br/>
				by 
				<br/>
				<span class="painting-artist">
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
					echo do_shortcode('[add_to_cart id="'.get_field('wc_product').'"]');
					
				?>
				</span>
				
			</div>
		
		</div>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
