<?php
/**
 * Template part for displaying archived events.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * 
 * @package AUSteve Canvas
 */
?>


	<div class="row">
		
		<div class="small-12 medium-3 columns event-painting">
			<!-- Image -->
			
<a href="<?php echo get_permalink();?>">
			<?php
			$paintingId = get_field('painting');
			if ( has_post_thumbnail( $paintingId ) ) 
			{
				echo get_the_post_thumbnail( $paintingId, array( 300, 300) );
			}
			else 
			{

			$the_painting = get_field('painting', $paintingId);
			?>

			<img src="<?php echo $the_painting['sizes']['thumbnail'];?>" width="<?php echo $the_painting['sizes']['thumbnail-width'];?>" height="<?php echo $the_painting['sizes']['thumbnail-height'];?>"/>
			<?php
			}
			?>

</a>
		</div>

		<div class="small-12 medium-6 columns event-details">
<a href="<?php echo get_permalink();?>">
			<!-- Date -->
			<?php 
			if (get_field('start_time'))
			{
				$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time'));
				echo $eventDate->format('d M Y'); 
			}
			?>

			<!-- Event -->
			<?php 
			$venue = get_field('venue'); 
			?>

			<h3 class='event-name'><?php echo the_title(); ?></h3>
			<p><?php echo $venue->post_title; ?></p>

</a>
		</div>

		<div class="small-12 medium-3 columns">
			<?php
			if (get_field('wc_product') && get_field('price'))
			{
				echo do_shortcode('[canvas_to_cart id="'.get_field('wc_product').'"]');
			}
			else 
			{
				echo "Cannot add to cart";
			}
			?>
		</div>

	</div>


