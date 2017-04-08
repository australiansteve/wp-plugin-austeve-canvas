<?php
/* Shortcode file */
function austeve_event_query_args($atts)
{
    // find date time now
    $date_now = date('Y-m-d H:i:s');

    $atts = shortcode_atts( array(
        'show_events' => 'true',
        'number_of_posts' => -1,
        'future_events' => 'true',
        'past_events' => 'false',
        'order' => 'ASC',
        'creation_id' => -1,
        'territory_id' => -1,
        'venue_id' => -1,
        'show_filters' => 'false',
    ), $atts );

    extract( $atts );

    $args = array(
        'from_shortcode' => true,
        'post_type' => 'austeve-events',
        'post_status' => array('publish'),
        'posts_per_page' => $number_of_posts,
        'orderby' => 'meta_value',
        'order' => $order,
        'meta_key' => 'start_time',
        'meta_type' => 'DATETIME',
        'show_filters' => $show_filters,
        'show_events' => $show_events,
    );

    $meta_query = array('relation' => 'AND');

    //Setup date query
    $date_query = array('relation' => 'OR');
    if ($past_events === 'true') //past events
    {
        $past_events_query = array(
            'key'           => 'start_time',
            'compare'       => '<=',
            'value'         => $date_now,
            'type'          => 'DATETIME',
        );
        $meta_query[] = $past_events_query;
    }
    if ($future_events === 'true') //future events
    {
        $future_events_query = array(
            'key'           => 'start_time',
            'compare'       => '>=',
            'value'         => $date_now,
            'type'          => 'DATETIME',
        );
        $meta_query[] = $future_events_query;
    }

    //Setup creation query
    if ($creation_id >= 0)
    {
        error_log("Specific creation query!");

        $creation_query = array(
            'key'           => 'creation',
            'compare'       => '=',
            'value'         => $creation_id,
            'type'          => 'NUMERIC',
        );
        $meta_query[] = $creation_query;
    }

    //Setup territory query
    if ($territory_id >= 0)
    {
        error_log("Specific territory query!");

        //Get all venues in the territory (including child territories)
        $venue_posts = get_posts(
            array(
                'posts_per_page' => -1,
                'post_type' => 'austeve-venues',
                'tax_query' => array(
                    array(
                        'taxonomy'         => 'austeve_territories',
                        'terms'            => $territory_id,
                        'field'            => 'term_id',
                        'operator'         => 'IN',
                        'include_children' => true,
                    )
                )
            )
        );
        error_log("Venue Posts: ".print_r($venue_posts, true));

        $venues = array();
        foreach($venue_posts as $venue)
        {
            $venues[] = $venue->ID;
        }
        error_log("Venues: ".print_r($venues, true));

        $territory_query = array(
            'key'           => 'venue',
            'compare'       => 'IN',
            'value'         => implode(",", $venues),
            'type'          => 'NUMERIC',
        );
        $meta_query[] = $territory_query;
    }

    //Setup venue query
    if ($venue_id >= 0)
    {
        error_log("Specific venue query!");

        $venue_query = array(
            'key'           => 'venue',
            'compare'       => '=',
            'value'         => $venue_id,
            'type'          => 'NUMERIC',
        );
        $meta_query[] = $venue_query;
    }

    $args['meta_query'] = $meta_query;

    error_log('Past events:'.print_r($past_events, true));
    error_log('Future events:'.print_r($future_events, true));
    error_log('Args:'.print_r($args, true));

    return $args;
}


function location_dropdown_options($parentId, $selectedId = 0)
{
    $taxterms = get_terms( array(
        'taxonomy' => 'austeve_territories',
        'parent' => $parentId,
    ) );

    foreach ( $taxterms as $term ) { 
        $level = count(get_ancestors($term->term_id, 'austeve_territories', 'taxonomy'));
        $prefix = "";
        for($p = 0; $p < $level; $p++) { $prefix .= "&nbsp;"; }
        if ($level > 0)
            $prefix.="- ";
        echo '<option value="' . $term->term_id . '" '.(($selectedId == $term->term_id) ? 'selected' : '').'>' . $prefix.$term->name . '</option>'; 

        location_dropdown_options($term->term_id);
    }
}

function venue_dropdown_options($territoryId)
{
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'austeve-venues',
        'post_status' => array('publish'),
        'orderby' => 'name',
        'order' => 'ASC'
    );

    if ($territoryId > 0)
    {
        $args['tax_query'] = array( 
                array(
                'taxonomy'         => 'austeve_territories',
                'terms'            => $territoryId,
                'field'            => 'term_id',
                'operator'         => 'IN',
                'include_children' => true,
            )
        );
    }
    $venue_posts = get_posts( $args );

    foreach($venue_posts as $venue)
    {
        echo '<option value="' . $venue->ID . '">' .$venue->post_title . '</option>'; 
    }
}

function austeve_events_upcoming($atts){
    
    $args = austeve_event_query_args($atts);

    $eventLocation = isset($_GET['location']) ? $_GET['location'] : 0;
    
    error_log("Show filters: ".$args['show_filters']);
    error_log("Show events: ".$args['show_events']);

    ob_start();
    
    if(array_key_exists('show_filters', $args) && $args['show_filters'] === 'true')
    {
        echo "<div id='upcoming-events-filters'>";
        include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/event-filters.php');
        echo '</div>';
	
    } //END show_filters

    if(array_key_exists('show_events', $args) && $args['show_events'] === 'true')
    {
        $query = new WP_Query( $args );

        if( $query->have_posts() ){

    		echo "<div id='upcoming-events'>";

    		//loop over query results
            while( $query->have_posts() ){
                $query->the_post();
                
                echo "<div class='upcoming-event'>";
                include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/events-archive.php');
                echo '</div>';
            }

            include( plugin_dir_path( __FILE__ ) . 'google-map.php');

        	echo '</div>';

        }
        else {
?>
    		<div id='upcoming-events'>
    		  	<div class="col-xs-12">
    		  		<em>No events found.</em>
    		  	</div>
    	  	</div>
<?php	
        }
    
        wp_reset_postdata();    
    } //END show_events

    return ob_get_clean();
}

add_shortcode( 'show_events', 'austeve_events_upcoming' );

// Enable shortcodes in text widgets
add_filter('widget_text','do_shortcode');


function austeve_add_to_cart($atts, $content) {

    $atts = shortcode_atts( array(
        'id' => -1,
        'include_price' => false,
    ), $atts );
    
    extract( $atts );
    
    $remaining = get_field('_stock', $id);

    $shortcodeOutput = "";
    
    $product = get_post( $id ); 

    if ($product->post_type == 'product_variation' && has_term( 'gift-certificate', 'product_cat', $product->post_parent ))
    {
        $shortcodeOutput .= "<div class='row add-to-cart'>";
        if ($include_price == 'true')
        {
            $shortcodeOutput .= "<div class='small-12 columns'>";
            $shortcodeOutput .= "<div class='cart-price'>$".wc_get_product($id)->get_price()."</div>";
            $shortcodeOutput .= "</div>";
        }
        $shortcodeOutput .= "<form class='custom-add-to-cart' method='post' action='".site_url('cart')."'>";

        $shortcodeOutput .= "<div class='small-12 columns'>";
        $shortcodeOutput .= "Quantity: <input type='number' min='1' name='quantity' value='1'/>";
        $shortcodeOutput .= "<input type='hidden' name='add-to-cart' value='$id'/>";
        $shortcodeOutput .= "</div>";

        $shortcodeOutput .= "<div class='small-12 columns'>";
        $shortcodeOutput .= "<input type='submit' class='button' value='Add to cart'/>";
        $shortcodeOutput .= "</div>";
        $shortcodeOutput .= "</form>";
        $shortcodeOutput .= "</div>";
    }
    else if (intval($remaining) > 0)
    {
        $shortcodeOutput .= "<div class='row add-to-cart'>";
        if ($include_price == 'true')
        {
            $shortcodeOutput .= "<div class='small-12 columns'>";
            $shortcodeOutput .= "<div class='cart-price'>$".get_field('price')."</div>";
            $shortcodeOutput .= "</div>";
        }
        $shortcodeOutput .= "<form class='custom-add-to-cart' method='post' action='".site_url('cart')."'>";

        if (get_field('custom_capacity'))
            $capacity = intval(get_field('custom_capacity'));
        else
            $capacity = intval(get_field('capacity', get_field('venue')));

        error_log(get_the_ID()." Remaining tickets: " . intval($remaining));
        error_log(get_the_ID()." Event capacity * 0.25 = " . ($capacity * 0.25));

        if (intval($remaining) <= ($capacity * 0.25))
        {
            $shortcodeOutput .= "<div class='small-12 columns'>";
            $shortcodeOutput .= "<div class='hurry-up'>Only ".$remaining." spots remaining</div>";
            $shortcodeOutput .= "</div>";
        }

        $shortcodeOutput .= "<div class='small-12 columns'>";
        $shortcodeOutput .= "Quantity: <input type='number' min='1' max='".$remaining."' name='quantity' value='1'/>";
        $shortcodeOutput .= "<input type='hidden' name='add-to-cart' value='$id'/>";
        $shortcodeOutput .= "</div>";

        $shortcodeOutput .= "<div class='small-12 columns'>";
        $shortcodeOutput .= "<input type='submit' class='button' value='Add to cart'/>";
        $shortcodeOutput .= "</div>";
        $shortcodeOutput .= "</form>";
        $shortcodeOutput .= "</div>";
    }
    else
    {

        $shortcodeOutput .= "<div class='row columns add-to-cart'>";
        $shortcodeOutput .= "<div class='sold-out'>Sold out!</div>";
        $shortcodeOutput .= "</div>";
    }

    return $shortcodeOutput;

}

add_shortcode( 'canvas_to_cart', 'austeve_add_to_cart' );

?>