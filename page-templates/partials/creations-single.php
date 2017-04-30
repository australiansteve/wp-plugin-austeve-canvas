<?php
/**
 * Template part for displaying single creations.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * 
 * @package AUSteve Canvas
 */
?>
<article id="post-<?php the_ID(); ?>">
	
	<div class="entry-content">

		<div class="row">
			
			<div class="small-12 columns">

					<h2 class="creation-title">
						<?php echo get_the_title(); ?>
					</h2>

			</div>
		
		</div>

		<div class="row">
			
			<div class="small-12 medium-3 columns">
		
				<?php
				$artist = get_field('artist');
				$the_creation_image = get_field('image');
				?>

				<a href="<?php echo get_permalink()?>">
					<img class="event-creation" src="<?php echo $the_creation_image['sizes']['medium'];?>" width="<?php echo $the_creation_image['sizes']['medium-width'];?>" height="<?php echo $the_creation_image['sizes']['medium-height'];?>"/>
					
				</a>				

			</div>

			<div class="small-12 medium-9 columns">

				<div class="row">

					<div class="small-12 columns">

						<div class="creation-artist">
							Artist: <a href="<?php echo get_permalink($artist)?>"><?php echo $artist->post_title; ?></a>
						</div>

					</div>

				</div>

				<?php 
				//Get Categories
				$terms = get_the_terms( get_the_ID(), 'austeve_creation_categories' );
				$termArray = array();
                         
				if ( $terms && ! is_wp_error( $terms ) )
				{
					foreach ( $terms as $term ) {
	 
					    // The $term is an object, so we don't need to specify the $taxonomy.
					    $term_link = get_term_link( $term );
					    
					    // If there was an error, continue to the next term.
					    if ( is_wp_error( $term_link ) ) {
					        continue;
					    }
					 
					    // We successfully got a link. Print it out.
					    $termArray[] = '<a href="' . esc_url( $term_link ) . '">' . $term->name . '</a>';
					}
				}
				?>
				<div class="row">

					<div class="small-12 columns">

						<div class="creation-category">
							Category: <?php echo implode($termArray, ", "); ?>
						</div>

					</div>

				</div>


				<?php 
				//Get Tags
				$terms = get_the_terms( get_the_ID(), 'austeve_creation_tags' );
				$termString = [];
	                         
				if ( $terms && ! is_wp_error( $terms ) )
				{
					foreach($terms as $term)
					{
						// The $term is an object, so we don't need to specify the $taxonomy.
					    $term_link = get_term_link( $term );
					    
					    // If there was an error, continue to the next term.
					    if ( is_wp_error( $term_link ) ) {
					        continue;
					    }
				 
					    // We successfully got a link. Print it out.
					    $termString[] = '<a href="' . esc_url( $term_link ) . '">' . $term->name . '</a>';

					}
				}

				if (count($termString) > 0) :
				?>
				<div class="row">

					<div class="small-12 columns">

						<div class="creation-tags">
							Tags: <?php echo implode(", ", $termString); ?>
						</div>

					</div>

				</div>
				<?php endif; ?>


				<?php if (get_field('difficulty_level')) : 
				$difffield = get_field_object('difficulty_level');
				$diffvalue = $difffield['value'];
				$difflabel = $difffield['choices'][ $diffvalue ];

				?>
				<div class="row">

					<div class="small-12 columns">

						<div class="creation-difficulty">
							Difficulty: <?php echo $difflabel; ?>
						</div>

					</div>

				</div>
				<?php endif; ?>

				<?php if (get_field('description')) : ?>
				<div class="row">

					<div class="small-12 columns">

						<div class="creation-description">
							<?php echo get_field('description'); ?>
						</div>

					</div>

				</div>
				<?php endif; ?>

			</div>

		</div>

		<div class="row">
			
		
		</div>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
