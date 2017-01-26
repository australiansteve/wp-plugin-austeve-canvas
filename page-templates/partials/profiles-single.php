<?php
/**
 * Template part for displaying single profiles.
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

				<div class="profile-name">
				
					<h1><?php echo get_the_title(); ?></h1>
					
				</div>

			</div>

		</div>

		<div class="row">
		
			<div class="small-3 columns">

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

			<div class="small-9 columns">

				<?php 
				if (get_field('home_town'))
				{
				?>
				<div class="row">
				
					<div class="small-12 columns">

						<p class="profile-hometown">
						
							Home town: <?php echo get_field('home_town'); ?>
							
						</p>

					</div>

				</div>
				<?php 
				} 
				?>

				<div class="row">
				
					<div class="small-12 columns">

						<span class="profile-bio">
						
							<?php echo get_field('bio'); ?>
							
						</span>

					</div>

				</div>

				<?php 
				if (get_field('url'))
				{
				?>
				<div class="row">
				
					<div class="small-12 columns">

						<p class="profile-link">

							<a href="<?php echo get_field('url'); ?>" title="Visit website" alt="Visit website" target="blank">Visit website</a>

						</p>
							
					</div>

				</div>
				<?php 
				} 
				?>

				<div class="row">
				
					<div class="small-12 columns">

						<span class="upcoming-events">
						
							Upcoming events where a painting by this artist is used...
							
						</span>

					</div>

				</div>

				<div class="row">
				
					<div class="small-12 columns">

						<span class="artist-paintings">
						
							Gallery of all paintings...
							
						</span>

					</div>

				</div>

			</div>

		</div>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
