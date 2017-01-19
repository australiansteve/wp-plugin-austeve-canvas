<?php
/**
 * Template part for displaying archived events.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * 
 * @package AUSteve Canvas
 */
?>
<article id="post-<?php the_ID(); ?>">
	
	<div class="entry-content">

	<a href="<?php echo get_permalink();?>"><?php echo the_title(); ?></a>
	<br/>
		<?php 

			$price = get_field('price');

			if ($price)
			{
				echo "Price: $".$price."";
			}
			
		?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->
