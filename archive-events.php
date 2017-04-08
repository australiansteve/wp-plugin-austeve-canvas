<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package AUSteve Canvas
 */

get_header(); ?>

<div class="row"><!-- .row start -->

	<div class="small-12 columns"><!-- .columns start -->

		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<?php
						the_archive_title( '<h1 class="page-title">', '</h1>' );
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
					?>
				</header><!-- .page-header -->

				<?php
					echo do_shortcode("[show_events show_events='false' show_filters='true']");					
				?>

				<div id='upcoming-events'>
				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

	       			<div class='upcoming-event'>
					<?php 

	            		if (locate_template('page-templates/partials/events-archive.php') != '') {
							// yep, load the page template
							get_template_part('page-templates/partials/events', 'archive');
						} else {
							// nope, load the default
							include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/events-archive.php');
						}

					?>
					</div>
					
				<?php endwhile; ?>

				</div>

				<?php the_posts_navigation(); ?>

			<?php else : ?>

				<?php get_template_part( 'page-templates/partials/content', 'none' ); ?>

			<?php endif; ?>

			</main><!-- #main -->
		</div><!-- #primary -->

	</div><!-- .columns end -->

</div><!-- .row end -->

<?php get_footer(); ?>
