<?php
/**
 * Template part for displaying archived creations.
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

					//get featured image if that's available
					if ( has_post_thumbnail( ) ) 
					{
        				echo get_the_post_thumbnail( null, array( 300, 300) );
					}
					else 
					{
						$the_creation_image = get_field('image');
					?>
					
					<img class="event-creation" src="<?php echo $the_creation_image['sizes']['medium'];?>" width="<?php echo $the_creation_image['sizes']['medium-width'];?>" height="<?php echo $the_creation_image['sizes']['medium-height'];?>"/>	

					<?php
					}
					?>
				</div>
			
			</div>

			<div class="row">
				
				<div class="small-12 columns">

						<h2 class="creation-title">
							<?php echo get_the_title(); ?>
						</h2>

				</div>
			
			</div>

		</a>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
