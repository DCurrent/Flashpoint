/*
* options_update_target
* Damon V. Caskey
* 2014-07-
*
* Replace value of element with content from secondary source. Mainly for
* updating options in a child select list based on a parent value.
*
* $event - Triggering event.
*
* $element_id: ID used to locate target element that will have its 
* options updated.
*
* $use_current - If true, send the value_current data attribute
*
*/
async function options_update($event, $element_id, $use_current) {	
	
	"use strict";
	
    const ELEMENT_DATA_PREFIX = 'dc_options_update_';
    
    var $append            = null; // Array of elements created and appended to form.
	//var $result            = null; // Return value.
	var $element_main      = null; // Primary element. Contains content to update.
	var $element_progress  = null; // Element displayed in place of main element while source is loading.
    var $element_label     = null; // Label of element with content to update
	var $form 		       = null; // Form that will create source data for options.
	var $posting 	       = null; // Posting object.
	var $data		       = null; // Combined array of items to add as hidden type form elements before posting.
	var $source_url        = null; // URL to page that provides the option source markup.
    
    /*
    * Get the target element to update and the
    * progress element using supplied ID. We assume 
    * the progress element has same ID as target 
    * element with a "_progress" suffix added.
    *
    * Once we have the target element pointer, we 
    * can implicitly get its parent form and any
    * attached label element.
    */
    
    $element_main = $($element_id);
	$element_progress = $($element_id + '_progress');
    
	$element_label = $("label[for='" + $element_main.attr('id') + "']");	
	
	$form = $($element_main).closest('form');
		
	/* Get source url from data attributes */
    $source_url = $element_main.data(ELEMENT_DATA_PREFIX + 'source_url');	
	
   
    
    /* 
    * It make take a few moments for the 
    * source script to load options. We
    * make the target element and its 
    * label invisible while showing a 
    * progress element that lets the
    * user know what's happening.
	*/
    $element_progress.show();	
	$element_main.hide();	
	$element_label.hide();
    
	/*
    * We need to send post data to the page that 
    * generates new options. Instead of adding a 
    * bunch of static parameters and trying to 
    * send them manually, we'll use the target 
    * element's data- attributes to create and 
    * append hidden input fields. Then we can
    * send the hidden input fields as post data
    * to our option generation source page. 
	*/
    
    $data = $element_main.data();
    
    for(var i in $data)
	{       
        $append = $('<input />').attr('type', 'hidden')
			.attr('name', i.replace(ELEMENT_DATA_PREFIX,''))
			.attr('value', $data[i]);
                
        /* 
        * We don't always want to send the
        * current value.
		*/
        if($append.attr('name') == 'value_current' && !$use_current)
        {            
            continue;
        }
                
		$form.append($append);
	}
    	
	/* 
    * Post to option generation source script. When
    * the script is complete we can append its results
    * to our target element.
    */
	$posting = $.post($source_url, $form.serialize());	
	
    let promise = new Promise((resolve, reject) => {
        
        $posting.done(function($post_results){		
            
            $element_main.empty();

            /* 
            * - Append any manual prefix options. 
            * - Append generated options.
            * - Append any manual suffix options.
            */
            $element_main.append($element_main.data(ELEMENT_DATA_PREFIX + 'prefix_options'));
            $element_main.append($post_results);		
            $element_main.append($element_main.data(ELEMENT_DATA_PREFIX + 'suffix_options'));

            /*
            * The options are in place. We can remove the 
            * progress element and make the target element
            * visible to user.
            */

            $element_progress.hide();
            $element_main.prop("disabled", false);
            $element_main.show();
            $element_label.show();
        })     
    });	
    
    let result = await promise; // wait until the promise resolves (*)

   // alert('wait');
	
}
