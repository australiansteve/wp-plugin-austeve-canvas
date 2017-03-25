<?php
/**
 * Template part for displaying single creations.
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

					<h2 class="creation-title">
						<?php echo get_the_title(); ?>
					</h2>

			</div>
		
		</div>

		<div class="row">
			
			<div class="small-12 columns">
		
				<?php
				$artist = get_field('artist');
				$the_creation_image = get_field('image');
				?>

				<a href="<?php echo get_permalink()?>">
					<img class="event-creation" src="<?php echo $the_creation_image['sizes']['medium'];?>" width="<?php echo $the_creation_image['sizes']['medium-width'];?>" height="<?php echo $the_creation_image['sizes']['medium-height'];?>"/>
					
				</a>				

			</div>
		
		</div>

		<div class="row">
			
			<div class="small-12 columns">

				<span class="creation-artist">
					<a href="<?php echo get_permalink($artist)?>"><?php echo $artist->post_title; ?></a>
				</span>

			</div>
		
		</div>

		<div class="row">
			
			<div class="small-12 columns">

				<span class="creation-description">
					<?php echo get_field('description'); ?>
				</span>

			</div>
		
		</div>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
