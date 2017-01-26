<?php
/**
 * The template for displaying all single venues.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package AUSteve Canvas
 */

get_header(); ?>

<div class="row"><!-- .row start -->

	<div class="small-12 columns"><!-- .columns start -->

		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				
				<?php 

            		if (locate_template('page-templates/partials/venues-single.php') != '') {
						// yep, load the page template
						get_template_part('page-templates/partials/venues', 'single');
					} else {
						// nope, load the default
						include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/venues-single.php');
					}

				?>
				
			<?php endwhile; // end of the loop. ?>

			</main><!-- #main -->
		</div><!-- #primary -->

	</div><!-- .columns end -->

</div><!-- .row end -->

<?php get_footer(); ?>
