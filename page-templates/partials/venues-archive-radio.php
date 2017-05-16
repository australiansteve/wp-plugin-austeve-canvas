<?php
/**
 * Template part for displaying archived venues.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * 
 * @package AUSteve Canvas
 */
?>

<input type='radio' name='venue' class='venue' value='<?php echo get_the_ID(); ?>'>
	<a href='<?php echo get_permalink(); ?>'><?php echo get_the_title(); ?></a>
</input>