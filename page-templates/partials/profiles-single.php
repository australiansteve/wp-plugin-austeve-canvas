<?php
/**
 * Template part for displaying single profiles.
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

				<div class="profile-name">
				
					<h1><?php echo get_the_title(); ?></h1>
					
				</div>

			</div>

		</div>

		<div class="row">
		
			<div class="small-12 medium-3 columns">

				<div class="profile-picture">
				
					<?php
					$picture = get_field('picture');

					if ($picture)
					{
					?>
					<img class="event-painting" src="<?php echo $picture['sizes']['medium'];?>" width="<?php echo $picture['sizes']['medium-width'];?>" height="<?php echo $picture['sizes']['medium-height'];?>"/>

					<?php
					}
					?>
				</div>

			</div>

			<div class="small-12 medium-9 columns">

				<?php 
				if (get_field('home_town'))
				{
				?>
				<div class="row">
				
					<div class="small-12 columns">

						<p class="profile-hometown">
						
							Home town: <?php echo get_field('home_town'); ?>
							
						</p>

					</div>

				</div>
				<?php 
				} 
				?>

				<?php 
				$mediums = get_field('mediums');
				$mediumarray = [];
				if ($mediums)
				{
					error_log("Artist mediums".print_r($mediums, true));
					foreach($mediums as $medium)
					{
						$term = get_term($medium, 'austeve_creation_categories');
						if ($term && !is_wp_error($term))
						{
							// The $term is an object, so we don't need to specify the $taxonomy.
						    $term_link = get_term_link( $term );
						    
						    // If there was an error, continue to the next term.
						    if ( is_wp_error( $term_link ) ) {
						        continue;
						    }
						 
						    // We successfully got a link. Print it out.
						    $mediumarray[] = '<a href="' . esc_url( $term_link ) . '">' . $term->name . '</a>';

						}
					}
				?>
				<div class="row">
				
					<div class="small-12 columns">

						<p class="profile-mediums">
						
							Mediums: <?php echo implode($mediumarray, ', '); ?>
							
						</p>

					</div>

				</div>
				<?php 
				} 
				?>

				<div class="row">
				
					<div class="small-12 columns">

						<span class="profile-bio">
						
							<?php echo get_field('bio'); ?>
							
						</span>

					</div>

				</div>

				<?php 
				$rows = get_field('photos');

				if ($rows)
				{
				?>
				<div class="row columns">
					<h3>
				</div>
				<div class="row small-up-2 medium-up-3 profile-photos">
				
					<?php
					foreach($rows as $photo)
					{
				        error_log(print_r($photo, true));
					
						echo '<div class="column">';

				        // display a sub field value
				        $image = $photo['image'];
				        error_log(print_r($image, true));
				        echo '<img src="'.$image['sizes']['thumbnail'].'" title="'.$image['description'].'" alt="'.$image['alt'].'"/>';

				        echo '</div>';
				    }
					?>

				</div>
				<?php 
				} 
				?>

			</div>

		</div>

	</div><!-- .entry-content -->

</article><!-- #post-## -->
