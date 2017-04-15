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
					<div class='venue-location'><i class="fa fa-map-marker" aria-hidden="true"></i><?php echo $location['address']; ?></div>
					<div class='venue-directions'><a href='https://www.google.ca/maps/place/<?php echo urlencode($location['address']);?>' target='_blank' title='Directions'><i class="fa fa-car" aria-hidden="true"></i>Get Directions</a></div>
				<?php
				}

				$website = get_field('website');
				$phone = get_field('phone_number');

				if ($website || $phone){
				?>
				<div class='row venue-contact'>
					<?php if ($website) :?>
						<div class='small-12 medium-6 columns'><a href='<?php echo $website; ?>' target='_blank' title='Visit website'><i class="fa fa-globe" aria-hidden="true"></i>Visit website</a></div>
					<?php endif; ?>
					<?php if ($phone) :?>
						<div class='small-12 medium-6 columns'><a href='tel:<?php echo $phone; ?>' title='Telephone'><i class="fa fa-phone" aria-hidden="true"></i><?php echo $phone; ?></a></div>
					<?php endif; ?>
				</div>
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
