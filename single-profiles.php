<?php
/**
 * The template for displaying all single profiles.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package AUSteve Canvas
 */

get_header(); ?>

<div class="row"><!-- .row start -->

	<div class="small-12 columns"><!-- .columns start -->

		<div id="primary" class="content-area">
			<main id="main" class="site-main single-profile" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				
				<?php 

            		if (locate_template('page-templates/partials/profiles-single.php') != '') {
						// yep, load the page template
						get_template_part('page-templates/partials/profiles', 'single');
					} else {
						// nope, load the default
						include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/profiles-single.php');
					}

				?>

				<div class="row">
				
					<div class="small-12 columns">
						
						<h3 class='events'>Upcoming events featuring this artists creations:</h3>
						
						<?php echo do_shortcode("[show_events creation_artist=".get_the_ID()."]"); ?>
							
					</div>

				</div>

				<?php
				$host_user = get_field('user');
				if ($host_user) 
				{
					error_log("Host user: ".print_r($host_user, true));
				?>
				<div class="row">
				
					<div class="small-12 columns">
						
						<h3 class='events'>Upcoming events hosted by <?php echo $host_user['user_firstname']; ?>:</h3>
						
						<?php echo do_shortcode("[show_events host_id=".$host_user['ID']."]"); ?>
							
					</div>

				</div>
				<?php
				}
				?>
				<div class="row">
				
					<div class="small-12 columns">

						<h3 class='creations'>Creations:</h3>
							
							<?php
							    $creation_args = array(
							        'do_not_filter' => true,
							        'post_type' => 'austeve-creations',
							        'post_status' => array('publish'),
							        'posts_per_page' => '-1',
							        'meta_query' => array(
							        	array(
					                        'key'           => 'artist',
					                        'compare'       => '=',
					                        'value'         => get_the_ID(),
					                        'type'          => 'NUMERIC',
					                    )
							        )
							    );
						        $creation_query = new WP_Query( $creation_args );

						        if( $creation_query->have_posts() ){

						    		echo "<div class='row small-up-1 medium-up-2 large-up-3 align-middle text-center' id='creations-block-grid'>";

						    		/* Start the Loop */ 
									while ( $creation_query->have_posts() ) : $creation_query->the_post(); 
										echo "<div class='column'>";

					            		if (locate_template('page-templates/partials/creations-archive.php') != '') {
											// yep, load the page template
											get_template_part('page-templates/partials/creations', 'archive');
										} else {
											// nope, load the default
											include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/creations-archive.php');
										}

										echo "</div>";
									endwhile;

									echo "</div>";

						        }
						        else {
							?>
						    		<div id='creations-block-grid'>
					    		  		<em>No creations found.</em>
						    	  	</div>
							<?php	
						        }
						    	wp_reset_postdata(); //resets back to the profile loop
					        ?>
						
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
				                        artistId: '<?php echo get_the_ID(); ?>',
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
					                        jQuery("#creations-block-grid .column.loading").html("That's it! That's all of them!"); //remove loading column
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

					</div>

				</div>

			<?php endwhile; // end of the loop. ?>

			</main><!-- #main -->
		</div><!-- #primary -->

	</div><!-- .columns end -->

</div><!-- .row end -->

<?php get_footer(); ?>
