<?php
/**
 * Template part for displaying archived paintings.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * 
 * @package AUSteve Canvas
 */
?>

<article id="post-<?php the_ID(); ?>">
	
	<div class="entry-content">

		<a href="<?php echo get_permalink()?>">

			<div class="row">
				
				<div class="small-12 columns">
			
					<?php
					$artist = get_field('artist');
					$the_painting = get_field('painting');
					?>
					
					<img class="event-painting" src="<?php echo $the_painting['sizes']['medium'];?>" width="<?php echo $the_painting['sizes']['medium-width'];?>" height="<?php echo $the_painting['sizes']['medium-height'];?>"/>	

				</div>
			
			</div>

			<div class="row">
				
				<div class="small-12 columns">

						<h2 class="painting-title">
							<?php echo get_the_title(); ?>
						</h2>

				</div>
			
			</div>

		</a>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
