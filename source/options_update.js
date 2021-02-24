/*
* options_update_target
* Damon V. Caskey
* 2014-07-
*
* Replace value of element with content from secondary source. Mainly for
* updating options in a child select list based on a parent value.
*
* To do: Would be nice to leverage dataset to acquire data attributes, but for now let's not 
* be too snobby toward older browsers.
*
* $element_sel: Selector (ID or CSS) to child element element that will have its enclosed content 
* updated . If omitted the value of calling element's 'data-child-selector' attribute will be used.
*	 
* $form_ref: Selector (ID or CSS) to form that post data will be taken from by $source to create
* new content. If omitted	the calling element's parent form will be used.
*
* $source: URL that will deliver updated content. If omitted the vale of child (element_self) 
* element's 'data-source-url' attribute will be used.
*
* $data: Array of values to add as hidden type form fields before posting. If omitted, target 
* all of element's data attributes will be passed. 
*/
function options_update($event, $source, $element_sel, $form_sel, $data_param) {	
	
	"use strict";
	
	var $result		= null; // Return value.
	var $element 	= null;	// Primary element. Contains content to update.
	var $label		= null;	// Label of element with content to update
	var $progress	= null;	// Element displayed in place of main element while source is loading.
	var $form 		= null;	// Form that will create source data for options.
	var $posting 	= null;	// Posting object.
	var $data		= null; // Combined array of items to add as hidden type form elements before posting.
		
	$element_sel = $element_sel || $event.target.getAttribute('id');
	$element = $($element_sel);
	
	$label = $("label[for='" + $element.attr('id') + "']");
	
	/* Get form object. */
	$form_sel = $form_sel || $($element).closest('form');
	$form = $($form_sel);
	
	/* Get progress element object. */
	$progress = $($element_sel + '_progress');
		
	/* 
    * Get source url (using same method as element selector above).
	* If the source is blank, try to get it from the element's data attribute.
	*/
    $source = $source || $element.data('source-url');	
	
	//alert($source);
	
	/* Show load progress. */
	$progress.show();
	
	/*
    * To accomidate different settings and situations without needing a ton of source pages
	* we'll send the extra parameters as POST data by adding hidden fields. 
	*/
    
	/* First let's get the data attributes.	*/
	$data = $element.data();
	
	if(Array.isArray($data_param) && Array.isArray($data))
	{
		$data = $data.concat($data_param); 
	}
	
	var $append = null;
	
	for(var i in $data)
	{
		$append = $('<input />').attr('type', 'hidden')
			.attr('name', i)
			.attr('value', $data[i]);
		
		/* Debugging */
		// alert("attr: " + $append.attr('name') + ', value: ' + $append.attr('value'));

		$form.append($append);
	}
	
	/* Hide main element and its label. */
	$element.hide();	
	$label.hide();		
	
	/* Post to source page with data from form. */
	$posting = $.post($source, $form.serialize());	
	
	/* Posting (loading) complete? */
	$posting.done(function(data) 
	{		
		$element.empty();
	
		/* 
        * Add content from source to element
        * with any manually included data. 
        */
		$element.append($element.attr('data-extra-options') + data);		
		
		/* Hide the load progress. */
		$progress.hide();
		
		/* Show and enable element/label. */
		$element.prop("disabled", false);
		$element.show();
		$label.show();
		
		$result = data;		
	});	
	
	return $result;
}
