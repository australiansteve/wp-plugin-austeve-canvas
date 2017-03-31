<?php
/**
 * Template part for displaying single venues.
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

				<h1 class="venue-title">

					<?php echo get_the_title(); ?>
					
				</h1>

			</div>
		
		</div>

		<div class="row">
			
			<div class="small-3 columns">
		
				<?php
				$picture = get_field('picture');
				?>
				
				<img class="venue-picture" src="<?php echo $picture['sizes']['medium'];?>" width="<?php echo $picture['sizes']['medium-width'];?>" height="<?php echo $picture['sizes']['medium-height'];?>"/>	

			</div>
		
			<div class="small-9 columns">
				
				<span class="venue-address">

					<?php 
					$location = get_field('address');

					if( !empty($location) ) {
					?>
					<div class="acf-map single">
						<div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>"></div>
					</div>
					
					<?php } ?>					
				</span>

			</div>
		
		</div>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
