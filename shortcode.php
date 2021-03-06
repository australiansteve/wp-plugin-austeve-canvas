<?php
/* Shortcode file */
function austeve_event_query_args($atts)
{
    // find date time now
    $date_now = new DateTime(date('Y-m-d H:i:s'));

    $atts = shortcode_atts( array(
        'show_events' => 'true',
        'number_of_posts' => -1,
        'future_events' => 'true',
        'past_events' => 'false',
        'order' => 'ASC',
        'creation_id' => -1,
        'creation_artist' => -1,
        'category_id' => -1,
        'territory_id' => -1,
        'venue_id' => -1,
        'host_id' => -1,
        'show_filters' => 'false',
        'include_api' => 'true',
        'number_of_days' => -1,
    ), $atts );

    extract( $atts );

    $args = array(
        'do_not_filter' => true,
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
            'value'         => $date_now->format("Y-m-d H:i:s"),
            'type'          => 'DATETIME',
        );

        $meta_query[] = $past_events_query;

        if ($number_of_days >= 0)
        {
            $after_date_query = array(
                'key'           => 'start_time',
                'compare'       => '>=',
                'value'         => date('Y-m-d', strtotime("-".$number_of_days." days")),
                'type'          => 'DATETIME',
            );
            $meta_query[] = $after_date_query;
        }
    }
    if ($future_events === 'true') //future events
    {
        //Start dates are stored in UTC time, and when in negative timezones that means they don't show up hours before the event
        $date_now->modify("-12 hours");
        $future_events_query = array(
            'key'           => 'start_time',
            'compare'       => '>=',
            'value'         => $date_now->format("Y-m-d H:i:s"),
            'type'          => 'DATETIME',
        );
        $meta_query[] = $future_events_query;

        if ($number_of_days >= 0)
        {
            $before_date_query = array(
                'key'           => 'start_time',
                'compare'       => '<=',
                'value'         => date('Y-m-d', strtotime("+".$number_of_days." days")),
                'type'          => 'DATETIME',
            );
            $meta_query[] = $before_date_query;
        }
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

    //Setup query based on artist
    if ($creation_artist >= 0)
    {
        error_log("Specific artist query!".$creation_artist);
        $artist_creation_ids = [];

        //Get all creations by the artist
        $artist_creations = get_posts(
            array(
                'posts_per_page' => -1,
                'post_type' => 'austeve-creations',
                'meta_query' => array (
                    array(
                        'key'           => 'artist',
                        'compare'       => '=',
                        'value'         => $creation_artist,
                        'type'          => 'NUMERIC',
                    )
                )
            )
        );
        error_log("Artist creations: ".print_r($artist_creations, true));

        if (count($artist_creations) > 0)
        {
            foreach($artist_creations as $creation)
            {
                $artist_creation_ids[] = $creation->ID;
            }
        }

        $creation_query = array(
            'key'           => 'creation',
            'compare'       => 'IN',
            'value'         => implode($artist_creation_ids, ','),
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
                'do_not_filter' => true,
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

    //Setup host query
    if ($host_id >= 0)
    {
        error_log("Specific host query!");

        $venue_query = array(
            'key'           => 'host',
            'compare'       => '=',
            'value'         => $host_id,
            'type'          => 'NUMERIC',
        );
        $meta_query[] = $venue_query;
    }

    //Setup category query
    if ($category_id >= 0)
    {
        error_log("Specific category query!");

        //Get all creations in the category (including child categories)
        $creation_posts = get_posts(
            array(
                'posts_per_page' => -1,
                'post_type' => 'austeve-creations',
                'tax_query' => array(
                    array(
                        'taxonomy'         => 'austeve_creation_categories',
                        'terms'            => $category_id,
                        'field'            => 'term_id',
                        'operator'         => 'IN',
                        'include_children' => true,
                    )
                )
            )
        );
        error_log("Creation Posts: ".print_r($creation_posts, true));

        $creations = array();
        foreach($creation_posts as $creation)
        {
            $creations[] = $creation->ID;
        }
        error_log("Creations: ".print_r($creations, true));

        $category_query = array(
            'key'           => 'creation',
            'compare'       => 'IN',
            'value'         => implode(",", $creations),
            'type'          => 'NUMERIC',
        );
        $meta_query[] = $category_query;
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
        'order' => 'ASC',
        'do_not_filter' => true,
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

function creation_category_options($parentId, $selectedId = 0)
{
    $taxterms = get_terms( array(
        'taxonomy' => 'austeve_creation_categories',
        'parent' => $parentId,
    ) );

    foreach ( $taxterms as $term ) { 
        $level = count(get_ancestors($term->term_id, 'austeve_creation_categories', 'taxonomy'));
        $prefix = "";
        for($p = 0; $p < $level; $p++) { $prefix .= "&nbsp;"; }
        if ($level > 0)
            $prefix.="- ";
        echo '<option value="' . $term->term_id . '" '.(($selectedId == $term->term_id) ? 'selected' : '').'>' . $prefix.$term->name . '</option>'; 

        creation_category_options($term->term_id);
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

            if (array_key_exists('include_api', $args) && $args['include_api'] === 'true')
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
        'title' => '',
        'class' => ''
    ), $atts );
    
    extract( $atts );
    
    $remaining = get_field('_stock', $id);

    $shortcodeOutput = "";
    
    $product = get_post( $id ); 

    if ($product->post_type == 'product_variation' && has_term( 'gift-certificate', 'product_cat', $product->post_parent ))
    {
        $product_options = get_post_meta($id, 'attribute_event-type');
        $variation_name = count($product_options) > 0 ? $product_options[0] : "";
        error_log("Options: ".print_r($product_options, true));
        error_log("Name: ".print_r($variation_name, true));

        $shortcodeOutput .= "<div class='add-to-cart ".$class."'>";
        $shortcodeOutput .= "<div class='row'>";
        if ($title != '')
        {
            $shortcodeOutput .= "<div class='small-12 columns'>";
            $shortcodeOutput .= "<div class='cart-title'><h3>".$title."</h3></div>";
            $shortcodeOutput .= "</div>";
        }
        if ($include_price == 'true')
        {
            $shortcodeOutput .= "<div class='small-12 columns'>";
            $shortcodeOutput .= "<div class='cart-price'>$".wc_get_product($id)->get_price()."</div>";
            $shortcodeOutput .= "</div>";
        }
        $shortcodeOutput .= "<form class='custom-add-to-cart' method='post' action='".site_url('cart')."'>";

        $shortcodeOutput .= "<div class='small-12 columns'>";
        $shortcodeOutput .= "Quantity: <input type='number' min='1' name='quantity' value='1'/>";
        $shortcodeOutput .= "<input type='hidden' name='add-to-cart' value='$product->post_parent'/>";
        $shortcodeOutput .= "<input type='hidden' name='product_id' value='$product->post_parent'/>";
        $shortcodeOutput .= "<input type='hidden' name='variation_id' value='$id'/>";
        $shortcodeOutput .= "<input type='hidden' name='attribute_event-type' value='$variation_name'/>";
        $shortcodeOutput .= "</div>";

        $shortcodeOutput .= "<div class='small-12 columns'>";
        $shortcodeOutput .= "<input type='submit' class='button' value='Add to cart'/>";
        $shortcodeOutput .= "</div>";
        $shortcodeOutput .= "</form>";
        $shortcodeOutput .= "</div>";
        $shortcodeOutput .= "</div>";
    }
    else if (intval($remaining) > 0)
    {
        $shortcodeOutput .= "<div class='add-to-cart ".$class."'>";
        $shortcodeOutput .= "<div class='row'>";
        if ($title != '')
        {
            $shortcodeOutput .= "<div class='small-12 columns'>";
            $shortcodeOutput .= "<div class='cart-title'><h3>".$title."</h3></div>";
            $shortcodeOutput .= "</div>";
        }
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
        $shortcodeOutput .= "</div>";
    }
    else
    {

        $shortcodeOutput .= "<div class='add-to-cart ".$class."'>";
        $shortcodeOutput .= "<div class='row columns'>";
        $shortcodeOutput .= "<div class='sold-out'>Sold out!</div>";
        $shortcodeOutput .= "</div>";
        $shortcodeOutput .= "</div>";
    }

    return $shortcodeOutput;

}

add_shortcode( 'canvas_to_cart', 'austeve_add_to_cart' );


function austeve_filterable_venues($atts){
    
    $atts = shortcode_atts( array(
        'locationId' => -1,
        'style' => 'listitem'
    ), $atts );

    extract( $atts );

    $args = array(
        'do_not_filter' => true,
        'post_type' => 'austeve-venues',
        'post_status' => array('publish'),
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    );
    if ($locationId > -1)
    {
        $args['tax_query'] = array( 
                array(
                'taxonomy'         => 'austeve_territories',
                'terms'            => $locationId,
                'field'            => 'term_id',
                'operator'         => 'IN',
                'include_children' => true,
            )
        );
    }

    ob_start();

    include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/venue-filters.php');

    $query = new WP_Query( $args );

    if( $query->have_posts() ){

?>
        <div class="row columns">
            <ul class='venues-list'>
<?php
        //loop over query results
        while( $query->have_posts() ){
            $query->the_post();
            if ($style == 'listitem')
            {
                include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/venues-archive.php');
            }
            else if ($style == 'radio')
            {
                include( plugin_dir_path( __FILE__ ) . 'page-templates/partials/venues-archive-radio.php');
            }
        }

?>
            </ul>
        </div>
<?php

    }
    else {
    ?>
        <div class='row columns'>
            <em>No venues found.</em>
        </div>
    <?php   
    }

    wp_reset_postdata();    

    return ob_get_clean();
}

add_shortcode( 'show_venues', 'austeve_filterable_venues' );


?>