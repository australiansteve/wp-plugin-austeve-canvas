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
					?>
				</header><!-- .page-header -->

				<div class="row"><!-- .row start -->

					<div class="small-12 columns"><!-- .columns start -->
					<?php
						include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/venue-filters.php');
					?>
					</div>

				</div>

				<div class="row columns">
					<ul class='venues-list'>

						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>
						<?php 

		            		if (locate_template('page-templates/partials/venues-archive.php') != '') {
								// yep, load the page template
								get_template_part('page-templates/partials/venues', 'archive');
							} else {
								// nope, load the default
								include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/venues-archive.php');
							}

						?>
						<?php endwhile; ?>

					</ul>
				</div> <!-- #venues-block-grid -->

				<?php the_posts_navigation(); ?>

			<?php else : ?>

				<?php get_template_part( 'page-templates/partials/content', 'none' ); ?>

			<?php endif; ?>

			</main><!-- #main -->
		</div><!-- #primary -->

	</div><!-- .columns end -->

</div><!-- .row end -->

<?php get_footer(); ?>
