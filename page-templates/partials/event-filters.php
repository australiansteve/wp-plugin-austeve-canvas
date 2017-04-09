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
        <div class='small-12 medium-4 columns'>
            <select id='creation-category' title='Select a creation category'>
                <option value="0" class='default' >Select a creation category:</option>
                <?php

                    creation_category_options(0);

                ?>
            </select>
        </div>
    </div>

    <?php
    $nonce_location = wp_create_nonce( 'austevegetlocationevents' );
    $nonce_venues = wp_create_nonce( 'austevegetlocationvenueoptions' );
    $nonce_venue = wp_create_nonce( 'austevegetvenueevents' );
    $nonce_category = wp_create_nonce( 'austevegetcategoryevents' );
    ?>

    <script type='text/javascript'>
        <!--
        function get_territory_events( locationId, categoryId ) {
            //console.log("Get events for location: " + locationId);
            jQuery.ajax({
                type: "post", 
                url: '<?php echo admin_url("admin-ajax.php"); ?>', 
                data: { 
                    action: 'get_location_events', 
                    locationId: locationId, 
                    categoryId: categoryId, 
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
                error: function( jqXHR, textStatus, errorThrown) {
                    jQuery("#upcoming-events").html("<h2>Error</h2><p>There was an error retreiving events, if this error persists please <a href='<?php echo site_url('contact');?>'>contact us</a></p>");
                },
                success: function(html){ //so, if data is retrieved, store it in html
                    jQuery(".reveal-overlay").remove(); //remove all reveal overlays before inserting the new content
                    jQuery("#upcoming-events").html(html);
                    jQuery(document).foundation();
                }
            }); //close jQuery.ajax(
        }

        function get_venue_list( locationId ) {
            //console.log("Get venues for location: " + locationId);
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
                error: function( jqXHR, textStatus, errorThrown) {
                    jQuery("#event-venue").append("<option>Error: Could not retrieve venues.</option><option>Please referesh and try again.</option>");
                },
                success: function(html){ //so, if data is retrieved, store it in html
                    jQuery("#event-venue").append(html);
                }
            }); //close jQuery.ajax(
        }

        function get_venue_events( venueId, locationId, categoryId ) {
            //console.log("Get events for venue: " + venueId);
            jQuery.ajax({
                type: "post", 
                url: '<?php echo admin_url("admin-ajax.php"); ?>', 
                data: { 
                    action: 'get_venue_events', 
                    venueId: venueId, 
                    locationId: locationId, 
                    categoryId: categoryId, 
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
                error: function( jqXHR, textStatus, errorThrown) {
                    jQuery("#upcoming-events").html("<h2>Error</h2><p>There was an error retreiving events, if this error persists please <a href='<?php echo site_url('contact');?>'>contact us</a></p>");
                },
                success: function(html){ //so, if data is retrieved, store it in html
                    jQuery(".reveal-overlay").remove(); //remove all reveal overlays before inserting the new content
                    jQuery("#upcoming-events").html(html);
                    jQuery(document).foundation();
                }
            }); //close jQuery.ajax(
        }

        function get_creation_events( categoryId, venueId, locationId ) {
            //console.log("Get events for category: " + categoryId + " (venue: " + venueId + "; location: " + locationId + ")");
            jQuery.ajax({
                type: "post", 
                url: '<?php echo admin_url("admin-ajax.php"); ?>', 
                data: { 
                    action: 'get_category_events', 
                    categoryId: categoryId, 
                    venueId: venueId, 
                    locationId: locationId, 
                    _ajax_nonce: '<?php echo $nonce_category; ?>' 
                },
                beforeSend: function() {
                    jQuery("#upcoming-events").html("<i class='fa fa-spinner fa-pulse fa-fw'></i>");
                },
                error: function( jqXHR, textStatus, errorThrown) {
                    jQuery("#upcoming-events").html("<h2>Error</h2><p>There was an error retreiving events, if this error persists please <a href='<?php echo site_url('contact');?>'>contact us</a></p>");
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
            var categoryId = jQuery("#creation-category").attr("value");

            get_territory_events( locationId, categoryId );
            get_venue_list( locationId );
        });

        // When the document loads do everything inside here ...
        jQuery("#event-venue").on('change', function() {
            var venueId = jQuery(this).attr("value");
            var locationId = jQuery("#event-location").attr("value");
            var categoryId = jQuery("#creation-category").attr("value");

            get_venue_events( venueId , locationId, categoryId );
        });

        jQuery("#creation-category").on('change', function() {
            var categoryId = jQuery(this).attr("value");
            var venueId = jQuery("#event-venue").attr("value");
            var locationId = jQuery("#event-location").attr("value");

            get_creation_events( categoryId, venueId, locationId );
        });

        -->
    </script>

</div>
<?php
?>