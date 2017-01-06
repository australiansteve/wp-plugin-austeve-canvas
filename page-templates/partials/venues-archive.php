<?php
/**
 * Template part for displaying archived venues.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * 
 * @package AUSteve Canvas
 */
?>
<?php 
	$image = get_field('image'); 
	$paddingClass='';
	if( have_rows('image') ){

	     // loop through the rows of data - there should only be 1 though
	    while ( have_rows('image') ) : the_row();

	        if( get_row_layout() == 'venue_image' ){
	        	$paddingClass='padded';
	        }

	    endwhile;
    }
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $paddingClass ); ?>>
	
	<div class="entry-content">

		Venue archive

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'austeve-venues' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->
