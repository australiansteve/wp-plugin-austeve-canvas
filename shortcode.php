<?php
/* Shortcode file */

function austeve_events_upcoming(){
	ob_start();

	// find date time now
	$date_now = date('Y-m-d H:i:s');

	$meta_query = array(
        'key'			=> 'date',
        'compare'		=> '>=',
        'value'			=> $date_now,
        'type'			=> 'DATETIME',
    );


    $args = array(
        'post_type' => 'austeve-events',
        'post_status' => array('publish'),
        'meta_key'        => 'date',
        'meta_type'        => 'DATETIME',
        'orderby'        => 'meta_value',
    	'order'          => 'ASC',
		'posts_per_page' => 4,
		'paged' 		=> false,
		'meta_query' => $meta_query
    );
    //var_dump($args);
    $query = new WP_Query( $args );
	
    if( $query->have_posts() ){

		echo "<div id='upcoming-events'>";

		//loop over query results
        while( $query->have_posts() ){
            $query->the_post();
            
            echo "<div class='upcoming-event'>";
            include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/events-shortcode.php');
            echo '</div>';
        }

    	echo '</div>';

    }
    else {
?>
		<div class="row archive-container">
		  	<div class="col-xs-12">
		  		<em>No upcoming events found.</em>
		  	</div>
	  	</div>
<?php	
    }
    
    wp_reset_postdata();
    return ob_get_clean();
}

add_shortcode( 'upcoming_events', 'austeve_events_upcoming' );

// Enable shortcodes in text widgets
add_filter('widget_text','do_shortcode');
?>