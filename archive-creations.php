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

				<div class="row" id="creations-search">
					<div class="small-12 columns">
						<form method="GET" action="#" id="search-filters" onsubmit="return validateSearch()">
							<input id="title-filter" type="text" class="filter" data-filter="title" placeholder="Search by name" value="<?php echo (isset($_GET['title']) ? $_GET['title'] : ''); ?>" />
							<input type="submit" value="Search"/>
						</form>
					</div>
				</div>
				
				<?php
				global $wp;
				$home_url = home_url();
				$current_url = home_url(add_query_arg(array(),$wp->request));
				$afterhome = strlen($current_url) - strlen($home_url);
				$request_url = substr($current_url, -($afterhome-1));
				$paging = strrpos ( $request_url , "/page/" );
				if ($paging)
				{
					$request_url = substr($request_url, 0, $paging);
				}
				?>

				<script type="text/javascript">
					function validateSearch() {

						// vars
						var url = "<?php echo home_url( $request_url ); ?>";
						var args = {};			
						
						// loop over filters
						jQuery('#search-filters .filter').each(function(){
							
							// vars
							var filter = jQuery(this).data('filter'),
								vals = [ jQuery(this).attr('value')];
							
							// append to args
							args[ filter ] = vals.join(',');
							
						});		
						
						// update url
						url += '?';
						
						
						// loop over args
						jQuery.each(args, function( name, value ){			
							url += name + '=' + value + '&';			
						});
						
						
						// remove last &
						url = url.slice(0, -1);
								
						// reload page
						window.location.replace( url );		
						return false;
					}
				</script>

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

				<?php $nonce = 'austevegetcreations'; 
				$query_type='post_type';
				$query_object='austeve-creations';
				if (is_tax())
				{
					$query_type = get_queried_object()->taxonomy;
					$query_object = get_queried_object()->slug;
				}
				?>
				<script type='text/javascript'>
		            <!--
		            function get_more_creations( nextPage ) {
		                jQuery.ajax({
		                    type: "post", 
		                    url: '<?php echo admin_url("admin-ajax.php"); ?>', 
		                    data: { 
		                        action: 'get_creations', 
		                        queryType: '<?php echo $query_type; ?>',
		                        queryObject: '<?php echo $query_object; ?>',
		                        nextPage: nextPage, 
		                        _ajax_nonce: '<?php echo $nonce; ?>' 
		                    },
		                    beforeSend: function() {
		                        jQuery("#creations-block-grid").append("<div class='column loading'><i class='fa fa-spinner fa-pulse fa-fw'></i></div>");
		                        jQuery("#more_posts").attr('disabled', true);
		                    },
			                error: function( jqXHR, textStatus, errorThrown) {
			                    jQuery("#creations-block-grid .column.loading").html("Error retreiving more creations.");
		                    	jQuery("#more_posts").attr('disabled', false);
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
			                        jQuery("#creations-block-grid .column.loading").html("More coming soon!"); //remove loading column
			                        jQuery("#more_posts").toggle();
		                    	}
		                    	jQuery("#more_posts").attr('disabled', false);
		                    }
		                }); //close jQuery.ajax(

		            }

		            // When the document loads do everything inside here ...
		            jQuery("#more_posts").on('click', function() {
		            	get_more_creations( jQuery(this).attr('data-next'))
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
