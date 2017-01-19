<?php
/**
 * Template part for displaying single events.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * 
 * @package AUSteve Canvas
 */
?>
<article id="post-<?php the_ID(); ?>">
	
	<div class="entry-content">
	
		<?php
		$the_painting = get_field('painting', get_field('painting'));
		?>

		<img src="<?php echo $the_painting['sizes']['medium'];?>" width="<?php echo $the_painting['sizes']['medium-width'];?>" height="<?php echo $the_painting['sizes']['medium-height'];?>"/>

		<?php 
			echo do_shortcode("[add_to_cart id='106']");
			
		?>
		<p>

		<a href="<?php echo site_url('cart?add_to_cart='.get_field('wc_product')); ?>">Add to cart</a>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
