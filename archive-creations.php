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

				<div class="row small-up-1 medium-up-2 large-up-3 align-middle text-center" id="creations-block-grid">

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<div class="column">
				<?php 

            		if (locate_template('page-templates/partials/creations-archive.php') != '') {
						// yep, load the page template
						get_template_part('page-templates/partials/creations', 'archive');
					} else {
						// nope, load the default
						include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/creations-archive.php');
					}

				?>
					</div>
				<?php endwhile; ?>

				</div> <!-- #creations-block-grid -->

				<a id="more_posts" class="button" data-next="2"><?php esc_html_e('Load More', 'dessertstorm') ?></a>

				<?php $nonce = 'austevegetcreations'; ?>
				<script type='text/javascript'>
		            <!--
		            function get_more_creations( nextPage ) {
		                jQuery.ajax({
		                    type: "post", 
		                    url: '<?php echo admin_url("admin-ajax.php"); ?>', 
		                    data: { 
		                        action: 'get_creations', 
		                        nextPage: nextPage, 
		                        _ajax_nonce: '<?php echo $nonce; ?>' 
		                    },
		                    beforeSend: function() {
		                        jQuery("#creations-block-grid").append("<div class='column loading'><i class='fa fa-spinner fa-pulse fa-fw'></i></div>");
		                        jQuery("#more_posts").prop('disabled', true);
		                    },
		                    success: function(html){ //so, if data is retrieved, store it in html
		                    	console.log("More creations response: " + html);
		                        if (html != '')
		                    	{
			                        jQuery("#creations-block-grid .column.loading").remove(); //remove loading column
			                        jQuery("#creations-block-grid").append(html);
			                        jQuery("#more_posts").attr('data-next', parseInt(nextPage)+1);
		                    	}
		                    	else
		                    	{
			                        jQuery("#creations-block-grid .column.loading").html("That's it! That's all of them!"); //remove loading column
			                        jQuery("#more_posts").toggle();
		                    	}
		                    	jQuery("#more_posts").prop('disabled', false);
		                    }
		                }); //close jQuery.ajax(

		            }

		            // When the document loads do everything inside here ...
		            jQuery("#more_posts").on('click', function() {
		                get_more_creations( jQuery(this).attr('data-next'));
		            });

		            -->
		        </script>

			<?php else : ?>

				<?php get_template_part( 'page-templates/partials/content', 'none' ); ?>

			<?php endif; ?>

			</main><!-- #main -->
		</div><!-- #primary -->

	</div><!-- .columns end -->

</div><!-- .row end -->

<?php get_footer(); ?>
