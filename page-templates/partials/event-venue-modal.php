<?php 
?>
<h3><?php echo $venue->post_title; ?></h3>
<?php 
$website = get_field('website', $venue->ID);
$phone = get_field('phone_number', $venue->ID);
if ($website || $phone){
?>
<div class='row venue-contact'>
	<?php if ($website) :?>
		<div class='small-12 medium-6 columns'><a href='<?php echo $website; ?>' target='_blank' title='Visit website'><i class="fa fa-globe" aria-hidden="true"></i>Visit website</a></div>
	<?php endif; ?>
	<?php if ($phone) :?>
		<div class='small-12 medium-6 columns'><a href='tel:<?php echo $phone; ?>' title='Telephone'><i class="fa fa-phone" aria-hidden="true"></i><?php echo $phone; ?></a></div>
	<?php endif; ?>
</div>
<?php
}

$location = get_field('address', $venue->ID);
error_log("Location:".print_r($location, true));
if( !empty($location) ) {
	//echo print_r($location, true);
?>
<div class='venue-location'><i class="fa fa-map-marker" aria-hidden="true"></i><?php echo $location['address']; ?></div>
<div class='venue-directions'><a href='https://www.google.ca/maps/place/<?php echo urlencode($location['address']);?>' target='_blank'><i class="fa fa-car" aria-hidden="true"></i>Get Directions</a></div>
<div class="acf-map">
	<div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>"></div>
</div>
<?php } ?>	
<div class='venue-link'>
	<a href='<?php echo get_permalink($venue->ID); ?>'>Go to Venue page<i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i></a>
</div>	
<button class="close-button" data-close aria-label="Close modal" type="button">
	<span aria-hidden="true">&times;</span>
</button>			  