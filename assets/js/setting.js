(function($) { 
"use strict"; 

	$(function() {
	    
		jQuery('#mwrq_btn_text_color, #mwrq_btn_bg_color').wpColorPicker();

		// // make tabs
	    jQuery( function() {
			jQuery("#motif-tabs").tabs().addClass("ui-tabs-vertical ui-helper-clearfix");
		});

		// // save setting option
		jQuery(".motif_button_setting").on('click', function(e) {
	        // prevent form default behaviour
	        jQuery(".wordpress-ajax-form").submit(
	            e.preventDefault()
	        );
	        // get the form data as an array
	        var formData = jQuery(".wordpress-ajax-form").serialize();
	        jQuery(".motif_loading").show();
	        jQuery.ajax({
	            url : mwrq_data_vars.ajax_url,
	            type : 'POST',
	            data : {action: 'mwrq_admin_setting_save', data:formData},
	            success : function(response) {
	                jQuery(".motif_loading").hide();
	            }
	        });
	    });

	});

})(jQuery);