<?php
/**
 * Template part for displaying archived profiles.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * 
 * @package AUSteve Canvas
 */
?>
<article id="post-<?php the_ID(); ?>">
	
	<div class="entry-content">

		<a href="<?php echo get_permalink(); ?>" title="Go to artist profile" alt="<?php echo get_the_title(); ?>">

			<div class="row">
			
				<div class="small-12 columns">

					<div class="profile-picture">
					
						<?php
						$picture = get_field('picture');

						if ($picture)
						{
						?>
						<img class="event-painting" src="<?php echo $picture['sizes']['medium'];?>" width="<?php echo $picture['sizes']['medium-width'];?>" height="<?php echo $picture['sizes']['medium-height'];?>"/>

						<?php
						}
						?>
					</div>

				</div>

			</div>

			<div class="row">
			
				<div class="small-12 columns">

					<div class="profile-name">
					
						<h4><?php echo get_the_title(); ?></h4>
					</div>

				</div>

			</div>

		</a>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
