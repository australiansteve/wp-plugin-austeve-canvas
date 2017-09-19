<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package AUSteve Canvas
 */

get_header(); ?>

<?php
function austeve_print_child_term_options($taxonomy, $parentId, $spacing, $prefix, $selectedTerms = array())
{
	$child_terms = get_terms( array( 
	    'taxonomy' => $taxonomy,
	    'hide_empty'   => true,
	    'parent' => $parentId,
	    'order_by' => 'name'
	) );

	foreach ( $child_terms as $term )
	{
		echo "<option value='".$term->slug."' ".(in_array($term->slug, $selectedTerms) ? 'selected=selected': '').">".$spacing.$prefix." ".ucfirst($term->name)."</option>";
		austeve_print_child_term_options($taxonomy, $term->term_id, "&nbsp&nbsp".$spacing, "-", $selectedTerms);
	}									
}
?>

<div class="row"><!-- .row start -->

	<div class="small-12 columns"><!-- .columns start -->

		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">


			<header class="page-header">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<div class="row" id="creations-search">
				<div class="small-12 columns">
					<form method="GET" action="#" id="search-filters" onsubmit="return validateSearch()">
						
						<div class="row">
							<div class="small-12 medium-4 columns">
								<label>Categories</label>
								<select id="category-filter" class="filter" data-filter="categories" multiple size="6">
									<?php
										austeve_print_child_term_options('austeve_creation_categories', 0, "", "", isset($_GET['categories']) ? explode(',', $_GET['categories']) : []);
									?>
								</select>
							</div>
							<div class="small-12 medium-4 columns">
								<label>Tags</label>
								<select id="tag-filter" class="filter" data-filter="tags" multiple size="6">
									<?php
										austeve_print_child_term_options('austeve_creation_tags', 0, "", "", isset($_GET['tags']) ? explode(',', $_GET['tags']) : []);
									?>
								</select>
								<label>Hold Ctrl to select multiple categories and tags</label>
							</div>
							<div class="small-12 medium-4 columns">
								<label>Difficulty</label>
								<input type="checkbox" name="difficulty-filter" data-filter="difficulty" <?php echo isset($_GET['difficulty']) ? (in_array('easy', explode(',', $_GET['difficulty'])) ? 'checked=checked' : '' ) : '' ?> value="easy">Easy</input><br/>
								<input type="checkbox" name="difficulty-filter" data-filter="difficulty" <?php echo isset($_GET['difficulty']) ? (in_array('medium', explode(',', $_GET['difficulty'])) ? 'checked=checked' : '' ) : '' ?> value="medium">Moderate</input><br/>
								<input type="checkbox" name="difficulty-filter" data-filter="difficulty" <?php echo isset($_GET['difficulty']) ? (in_array('expert', explode(',', $_GET['difficulty'])) ? 'checked=checked' : '' ) : '' ?> value="expert">Expert</input>
							</div>
						</div>

						<div class="row">
							<div class="small-12 medium-10 columns">
								<input id="title-filter" type="text" class="filter" data-filter="title" placeholder="Search by name" value="<?php echo (isset($_GET['title']) ? $_GET['title'] : ''); ?>" />
							</div>
							<div class="small-12 medium-1 columns">
								<input type="submit" value="Search"/>
							</div>
							<div class="small-12 medium-1 columns">
								<input type="button" onclick="return resetSearch()" value="Reset"/>
							</div>
						</div>

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
							vals = [ jQuery(this).val()];
						
						// append to args
						args[ filter ] = vals.join(',');
						
					});		

					var diffVals = [];
					jQuery('#search-filters input[name=difficulty-filter]').each(function(){

						//console.log(jQuery(this).val() + " " + jQuery(this).attr('checked'))
						if (jQuery(this).attr('checked') == 'checked')
							diffVals.push(jQuery(this).val());

					});

					// append to args
					args[ 'difficulty' ] = diffVals.join(',');
					
					// update url
					url += '?';
					
					
					// loop over args
					jQuery.each(args, function( name, value ){	
						if (value.length > 0)		
							url += name + '=' + value + '&';			
					});
					
					
					// remove last &
					url = url.slice(0, -1);


					// reload page
					window.location.replace( url );
					//console.log(url);
					return false;
				}

				function resetSearch() {
					// vars
					var url = "<?php echo home_url( $request_url ); ?>";

					// reload page
					window.location.replace( url );		
					return false;
				}
			</script>

			<?php if ( have_posts() ) : ?>

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

				<a id="more_posts" class="button" data-next="2" data-title="<?php echo isset($_GET['title'])?$_GET['title']:'';?>" data-categories="<?php echo isset($_GET['categories'])?$_GET['categories']:'';?>" data-tags="<?php echo isset($_GET['tags'])?$_GET['tags']:'';?>" data-difficulty="<?php echo isset($_GET['difficulty'])?$_GET['difficulty']:'';?>" ><?php esc_html_e('Load More', 'dessertstorm') ?></a>

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
		            function get_more_creations( nextPage, titleFilter, categoryFilter, tagFilter, difficultyFilter ) {
		                jQuery.ajax({
		                    type: "post", 
		                    url: '<?php echo admin_url("admin-ajax.php"); ?>', 
		                    data: { 
		                        action: 'get_creations', 
		                        queryType: '<?php echo $query_type; ?>',
		                        queryObject: '<?php echo $query_object; ?>',
		                        nextPage: nextPage, 
		                        titleFilter: titleFilter, 
		                        categoryFilter: categoryFilter, 
		                        tagFilter: tagFilter, 
		                        difficultyFilter: difficultyFilter, 
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
		            	var nextPage = jQuery(this).attr('data-next');
		            	var titleFilter = jQuery(this).attr('data-title');
		            	var categoryFilter = jQuery(this).attr('data-categories');
		            	var tagFilter = jQuery(this).attr('data-tags');
		            	var difficultyFilter = jQuery(this).attr('data-difficulty');

						get_more_creations( nextPage, titleFilter, categoryFilter, tagFilter, difficultyFilter );
		            });

		            -->
		        </script>

			<?php else : ?>

				<div class="row small-up-1 medium-up-2 large-up-3 align-middle text-center" id="creations-block-grid">

					<div class="column">Nothing found. Try broadening your search</div>

				</div>
				
			<?php endif; ?>

			</main><!-- #main -->
		</div><!-- #primary -->

	</div><!-- .columns end -->

</div><!-- .row end -->

<?php get_footer(); ?>
