<?php
/**
 * The template for displaying all single creations.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package AUSteve Canvas
 */

get_header(); ?>

<div class="row"><!-- .row start -->

	<div class="small-12 columns"><!-- .columns start -->

		<div id="primary" class="content-area">
			<main id="main" class="site-main single-creation" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				
				<?php 

            		if (locate_template('page-templates/partials/creations-single.php') != '') {
						// yep, load the page template
						get_template_part('page-templates/partials/creations', 'single');
					} else {
						// nope, load the default
						include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/creations-single.php');
					}

				?>				

				<div class="row"><!-- .row start -->

					<div class="small-12 columns"><!-- .columns start -->
						<h3 class='events'>Upcoming events featuring this creation:</h3>

						<?php echo do_shortcode("[show_events creation_id=".get_the_ID()."]"); ?>

					</div>

				</div>


				<div class="row"><!-- .row start -->

					<div class="small-12 columns"><!-- .columns start -->
						<h3 class='events'>Past events featuring this creation:</h3>

						<?php echo do_shortcode("[show_events creation_id=".get_the_ID()." past_events=true future_events=false order='DESC']"); ?>

					</div>

				</div>

			<?php endwhile; // end of the loop. ?>

			</main><!-- #main -->
		</div><!-- #primary -->

	</div><!-- .columns end -->

</div><!-- .row end -->

<?php get_footer(); ?>
