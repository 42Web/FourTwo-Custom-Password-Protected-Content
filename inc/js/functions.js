var $ = jQuery.noConflict();

function fourtwo_cppt_check_visibility() {
	
	if ($('#visibility-radio-password').is(':checked')) {
		$('#fourtwo_cppt_textmeta').attr('style', 'display: block !important');
	} else {
		$('#fourtwo_cppt_textmeta').attr('style', 'display: none !important');
	}
	
	console.log('test');
	
}

(function($){

	"use strict";
	
	// Show/Hide Metabox on page load
	fourtwo_cppt_check_visibility();
	
	// Show/Hide Metabox on visibility select
	$('#post-visibility-select input[type="radio"]').on('click', function() {
		fourtwo_cppt_check_visibility();
	});
	
	
})(jQuery);