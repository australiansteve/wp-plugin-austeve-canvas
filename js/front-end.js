jQuery( document ).ready(function($){

	jQuery('.upcoming-event .event-instructor').each(function() {
		var userId = jQuery(this).attr('data-id');
		console.log("Get instructor rating for: " + userId);

		jQuery.ajax({
			type: "post", 
			url: jQuery(this).attr('data-url'), 
			data: { 
				action: 'get_instructor_rating', 
				userId: userId, 
				_ajax_nonce: 'austevegetinstructorrating' 
			},
			success: function(html){ //so, if data is retrieved, store it in html
				console.log("Response: " + html);

				jQuery('.event-instructor[data-id='+userId+']').attr('title',  html + "/5");
			},
			failure: function(html){ //so, if data is retrieved, store it in html
				console.log("Failed Response: " + html);

			}
		}); //close jQuery.ajax(

	});

});
