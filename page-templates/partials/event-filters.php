<?php

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
    $nonce_location = wp_create_nonce( 'austevegetlocationevents' );
    $nonce_venues = wp_create_nonce( 'austevegetlocationvenueoptions' );
    $nonce_venue = wp_create_nonce( 'austevegetvenueevents' );
    ?>

    <script type='text/javascript'>
        <!--
        function get_territory_events( locationId ) {
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
                    _ajax_nonce: '<?php echo $nonce_location; ?>' 
                },
                beforeSend: function() {
                    jQuery("#upcoming-events").html("<i class='fa fa-spinner fa-pulse fa-fw'></i>");
                },
                success: function(html){ //so, if data is retrieved, store it in html
                    jQuery(".reveal-overlay").remove(); //remove all reveal overlays before inserting the new content
                    jQuery("#upcoming-events").html(html);
                    jQuery(document).foundation();
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

        function get_venue_events( venueId, locationId ) {
            console.log("Get events for venue: " + venueId);
            jQuery.ajax({
                type: "post", 
                url: '<?php echo admin_url("admin-ajax.php"); ?>', 
                data: { 
                    action: 'get_venue_events', 
                    venueId: venueId, 
                    locationId: locationId, 
                    numberOfPosts: '<?php echo $args['posts_per_page']; ?>', 
                    showFilters: 'false', 
                    pastEvents: "<?php echo isset($atts['past_events']) ? $atts['past_events'] : 'false'; ?>", 
                    futureEvents: "<?php echo isset($atts['future_events']) ? $atts['future_events'] : 'true'; ?>", 
                    order: '<?php echo $args['order']; ?>', 
                    _ajax_nonce: '<?php echo $nonce_venue; ?>' 
                },
                beforeSend: function() {
                    jQuery("#upcoming-events").html("<i class='fa fa-spinner fa-pulse fa-fw'></i>");
                },
                success: function(html){ //so, if data is retrieved, store it in html
                    jQuery(".reveal-overlay").remove(); //remove all reveal overlays before inserting the new content
                    jQuery("#upcoming-events").html(html);
                    jQuery(document).foundation();
                }
            }); //close jQuery.ajax(
        }

        // When the document loads do everything inside here ...
        jQuery("#event-location").on('change', function() {
            var locationId = jQuery(this).attr("value");

            get_territory_events( locationId );
            get_venue_list( locationId );
        });

        // When the document loads do everything inside here ...
        jQuery("#event-venue").on('change', function() {
            var venueId = jQuery(this).attr("value");
            var locationId = jQuery("#event-location").attr("value");

            get_venue_events( venueId , locationId );
        });

        -->
    </script>

</div>
<?php?>