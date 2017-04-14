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
			
			<div class="small-12 medium-6 large-5 columns">
		
				<?php
				$picture = get_field('picture');
				if ($picture){
				?>
				
				<img class="venue-picture" src="<?php echo $picture['sizes']['medium'];?>" width="<?php echo $picture['sizes']['medium-width'];?>" height="<?php echo $picture['sizes']['medium-height'];?>"/>	

				<?php 
				}
				$location = get_field('address');

				if( !empty($location) ) {
				?>
					<p class='venue-address'><?php echo $location['address']; ?> <br/> <a href='https://www.google.ca/maps/place/<?php echo urlencode($location['address']);?>' target='_blank' title='Directions'><i class="fa fa-car" aria-hidden="true"></i>Get Directions</a></p>
				<?php
				}

				$website = get_field('website');

				if( !empty($website) ) {
				?>
					<p class='venue-website'><a href='<?php echo $website; ?>' target='_blank' title='Visit website'><i class="fa fa-globe" aria-hidden="true"></i>Visit website</a></p>
				<?php
				}

				$phone = get_field('phone_number');

				if( !empty($phone) ) {
				?>
					<p class='venue-phone'>Phone: <a href='tel:<?php echo $phone; ?>' title='Telephone'><?php echo $phone; ?></a></p>
				<?php
				}

				$description = get_field('venue_description');

				if( !empty($description) ) {
				?>
					<div class='venue-description show-for-medium'><?php echo $description; ?></div>
				<?php
				}
				?>

			</div>
		
			<div class="small-12 medium-6 large-7 columns">
				
				<?php
					if( !empty($location) ) {
					?>
					<div class="acf-map single">
						<div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>"></div>
					</div>
					
					<?php } ?>	

			</div>

			<div class='small-12 show-for-small-only columns'>

				<?php
				if( !empty($description) ) {
				?>
					<div class='venue-description'><?php echo $description; ?></div>
				<?php
				}
				?>

			</div>
		
		</div>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
