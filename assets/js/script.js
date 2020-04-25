'use strict';
jQuery(window).ready(function()
{
	jQuery(".demo_list_item .import_burtton").each(function(){
		jQuery(this).click(function(){

			jQuery(this).parent().parent().parent().find(".loader_wrapper").show();
		});

	});
	jQuery('#user_email_submit').on('click',function()
	{
		var formData=jQuery(this).closest('form').serialize();
		formData=formData+'&location='+location.href;
		var data = {action: 'submitUserForm',formData:formData};
		jQuery.post(stdisettings.ajaxurl,data,function(res)
		{
			jQuery('.email_sent').html(res);
		});	
	});
	jQuery( document ).ajaxStart(function() {

	});
	jQuery( document ).ajaxStop(function() {

	});
	jQuery( document ).ajaxError(function() {
		
	});
});