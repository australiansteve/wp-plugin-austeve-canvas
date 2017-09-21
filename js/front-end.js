jQuery( document ).ready(function($){

	var icons = {
      header: "ui-icon-circle-plus",
      activeHeader: "ui-icon-circle-minus"
    };

    var accordionArgs = {
		icons : icons,
		collapsible: true,
	};

	//If there's nothing in the query string - hide the filters by default, otherwise they will always be open
	if(window.location.search.substring(1).length == 0 )
	{
		accordionArgs.active=false;
	}

    $( "#creations-accordion" ).accordion(accordionArgs);

});
