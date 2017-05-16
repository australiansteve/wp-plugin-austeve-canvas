<?php

?>
<div id='venue-filters'>
    <div class='row'>
        <div class='small-12 medium-4 columns'>
            <select id='location' title='Select a location'>
                <option value="0">Select a location:</option>
                <?php

                    location_dropdown_options(0, $eventLocation);

                ?>
            </select>
        </div>
    </div>

    <?php
    $nonce_venues = wp_create_nonce( 'austevegetlocationvenueoptions' );
    ?>

    <script type='text/javascript'>
        <!--
        
        function get_venue_list( locationId ) {
            //console.log("Get venues for location: " + locationId);
            jQuery.ajax({
                type: "post", 
                url: '<?php echo admin_url("admin-ajax.php"); ?>', 
                data: { 
                    action: 'get_location_venue_options', 
                    locationId: locationId, 
                    style: 'listitem', 
                    _ajax_nonce: '<?php echo $nonce_venues; ?>' 
                },
                beforeSend: function() {
                    jQuery(".venues-list li").remove();
                    jQuery(".venues-list").append("<li>Retreiving...</li>");
                },
                error: function( jqXHR, textStatus, errorThrown) {
                    jQuery(".venues-list").append("<li>Error: Could not retrieve venues.</li><li>Please referesh and try again.</li>");
                },
                success: function(html){ //so, if data is retrieved, store it in html
                    jQuery(".venues-list li").remove();
                    jQuery(".venues-list").append(html);
                }
            }); //close jQuery.ajax(
        }

        // When the document loads do everything inside here ...
        jQuery("#location").on('change', function() {
            var locationId = jQuery(this).attr("value");

            get_venue_list( locationId );
        });

        -->
    </script>

</div>
<?php
?>