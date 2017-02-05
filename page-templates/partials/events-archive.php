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

	<div class="medium-2 columns show-for-medium event-painting">
		<!-- Image -->
		
		<a href="<?php echo get_permalink();?>">
			<?php
			$the_painting = get_field('painting', get_field('painting'));
			?>

			<img src="<?php echo $the_painting['sizes']['thumbnail'];?>" width="<?php echo $the_painting['sizes']['thumbnail-width'];?>" height="<?php echo $the_painting['sizes']['thumbnail-height'];?>"/>

		</a>
	</div>

	<div class="small-1 columns event-date">
		<!-- Date -->
		<?php 
		if (get_field('start_time'))
		{
			$eventDate = DateTime::createFromFormat('Y-m-d H:i:s', get_field('start_time'));
			echo $eventDate->format('M')."</br>"; 
			echo $eventDate->format('d'); 
		}
		?>


	</div>
	
	<div class="small-6 medium-5 columns">
		<!-- Event -->
		<?php 
		$venue = get_field('venue'); 
		?>

		<a href="<?php echo get_permalink();?>">
			<h3><?php echo the_title(); ?></h3>
		</a>
		<p><?php echo $venue->post_title; ?></p>
	</div>

	<div class="small-5 medium-4 columns">
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