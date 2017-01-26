<?php
/**
 * Template part for displaying archived venues.
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
					$picture = get_field('picture');
					?>
					
					<img class="venue-picture" src="<?php echo $picture['sizes']['medium'];?>" width="<?php echo $picture['sizes']['medium-width'];?>" height="<?php echo $picture['sizes']['medium-height'];?>"/>	

				</div>
			
			</div>

			<div class="row">
				
				<div class="small-12 columns">

					<h2 class="venue-title">

						<?php echo get_the_title(); ?>
						
					</h2>

				</div>
			
			</div>

		</a>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
