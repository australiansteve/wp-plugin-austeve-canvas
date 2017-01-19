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
<a href="<?php echo get_permalink();?>">
	<?php
	$the_painting = get_field('painting', get_field('painting'));
	?>

	<img src="<?php echo $the_painting['sizes']['thumbnail'];?>" width="<?php echo $the_painting['sizes']['thumbnail-width'];?>" height="<?php echo $the_painting['sizes']['thumbnail-height'];?>"/><br/>

	<h3><?php echo the_title(); ?></h3></a>
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
