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
			<main id="main" class="site-main single-venue" role="main">

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

				<div class="row events">
				
					<div class="small-12 columns">
						
						<h3 class='events'>Upcoming events at this venue:</h3>
						
						<?php echo do_shortcode("[show_events venue_id=".get_the_ID()."]"); ?>
							
					</div>

				</div>

				<div class="row events">
				
					<div class="small-12 columns">
						
						<h3 class='events'>Past events at this venue:</h3>
						
						<?php echo do_shortcode("[show_events venue_id=".get_the_ID()." future_event='false' past_events='true']"); ?>
							
					</div>

				</div>

			</main><!-- #main -->
		</div><!-- #primary -->

	</div><!-- .columns end -->

</div><!-- .row end -->


<?php 
    include( plugin_dir_path( __FILE__ ) . 'google-map.php');
?>
<?php get_footer(); ?>
