<?php
/* Shortcode file */
function austeve_event_query_args($atts)
{
    // find date time now
    $date_now = date('Y-m-d H:i:s');

    $atts = shortcode_atts( array(
        'number_of_posts' => -1,
        'future_events' => 'true',
        'past_events' => 'false',
        'order' => 'ASC',
        'creation_id' => -1,
        'territory_id' => -1,
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

        //Get all territories under the specified territory_id
        //$all_territories = get_term_children( $territory_id, 'austeve_territories' );
        //array_push($all_territories, $territory_id);
        
        //error_log("Territories: ".print_r($all_territories, true));

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

    ob_start();
    $query = new WP_Query( $args );

    $eventLocation = isset($_GET['location']) ? $_GET['location'] : 0;
    
    error_log("Show filters: ".$args['show_filters']);

    if(array_key_exists('show_filters', $args) && $args['show_filters'] === 'true')
    {
?>
    <div id='events-filters'>
        <div class='row'>
            <div class='small-12 medium-4 columns'>
                <select id='event-location' title='Select a location'>
                    <option value="0" <?php ($eventLocation == '') ? "selected": ""; ?> >Select a location:</option>
                    <?php

                        location_dropdown_options(0, $eventLocation);

                    ?>
                </select>
            </div>
            <div class='small-12 medium-4 columns'>
                <select id='event-venue' title='Select a venue'>
                    <option value="0" <?php ($eventLocation == '') ? "selected": ""; ?> class='default' >Select a venue:</option>
                    <?php

                        venue_dropdown_options(0);

                    ?>
                </select>
            </div>
        </div>

        <?php
        $nonce = wp_create_nonce( 'austevegetlocationevents' );
        $nonce_venues = wp_create_nonce( 'austevegetlocationvenueoptions' );
        ?>

        <script type='text/javascript'>
            <!--
            function get_event_list( locationId ) {
                console.log("Get events for location: " + locationId);
                jQuery.ajax({
                    type: "post", 
                    url: '<?php echo admin_url("admin-ajax.php"); ?>', 
                    data: { 
                        action: 'get_location_events', 
                        locationId: locationId, 
                        numberOfPosts: '<?php echo $args['posts_per_page']; ?>', 
                        showFilters: 'false', 
                        pastEvents: "<?php echo isset($atts['past_events']) ? $atts['past_events'] : 'false'; ?>", 
                        futureEvents: "<?php echo isset($atts['future_events']) ? $atts['future_events'] : 'true'; ?>", 
                        order: '<?php echo $args['order']; ?>', 
                        _ajax_nonce: '<?php echo $nonce; ?>' 
                    },
                    beforeSend: function() {
                        jQuery("#upcoming-events").html("<i class='fa fa-spinner fa-pulse fa-fw'></i>");
                    },
                    success: function(html){ //so, if data is retrieved, store it in html
                        jQuery(".reveal-overlay").remove(); //remove all reveal overlays before inserting the new content
                        jQuery("#upcoming-events").html(html);
                        jQuery(document).foundation();
                        //Foundation.reInit('reveal');
                    }
                }); //close jQuery.ajax(
            }

            function get_venue_list( locationId ) {
                console.log("Get venues for location: " + locationId);
                jQuery.ajax({
                    type: "post", 
                    url: '<?php echo admin_url("admin-ajax.php"); ?>', 
                    data: { 
                        action: 'get_location_venue_options', 
                        locationId: locationId, 
                        _ajax_nonce: '<?php echo $nonce_venues; ?>' 
                    },
                    beforeSend: function() {
                        jQuery("#event-venue option:not(.default)").remove();
                    },
                    success: function(html){ //so, if data is retrieved, store it in html
                        console.log("Venue options: " + html);
                        jQuery("#event-venue").append(html);
                    }
                }); //close jQuery.ajax(
            }
            // When the document loads do everything inside here ...
            jQuery("#event-location").on('change', function() {
                var locationId = jQuery(this).attr("value");

                get_event_list( locationId );
                get_venue_list( locationId );
            });

            -->
        </script>

    </div>
<?php	
    } //END show_filters

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