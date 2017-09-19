(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
        console.log("ready!");

        /**
		 * Query On Change event
		 * when Product selected then show product tab.
        */
        jQuery('.mdsupport-status-pagination li.active').live('click',function(){
            var page = $(this).attr('p');
            var data = {
                page: page,
                action: "mdsupport_load_posts"
            };

            jQuery.ajax({
                url:mdsajax.ajaxurl,
                type: 'post',
                data: data,
                success: function (response) {
                    jQuery('.mdsupport-status-details').html('');
                    jQuery('.mdsupport-status-details').html(response);
                }
            });


        });


		jQuery(document).on('change', '.support-query', function (e) {
			var querytype =  jQuery('.support-query').val();
			if(querytype == 'product'){
				jQuery('.product-tab').show();
			}else {
				jQuery('.product-tab').hide();
			}
		});


        // Sava Form Value For Mdsupport
        jQuery(document).on('click', '.save-support', function (e) {
            // alert('work');
            var supporttitle = jQuery('.support-title').val();
            var supportmessage = jQuery('.support-message').val();
            var username = jQuery('.user-name').val();
            var querytype = jQuery('.support-query').val();
            var priortytype = jQuery('.priorty-type').val();
            var productid = jQuery('.product-name').val();
            var supportemail = jQuery('.support-email').val();
            var file_data = jQuery('#sortpicture').prop('files')[0];
            if (jQuery('#sortpicture').val() == '') {
                jQuery('.file-require').show();
                return false;
            }
            var form_data = new FormData();
            if (supporttitle == '') {
                jQuery('.support-title').css({"border": "1px solid red"})
                return false;
            } else {
                jQuery('.support-title').css({"border": "1px solid #e3ecf0"})
            }

            form_data.append('file', file_data);
            form_data.append('action', 'md_support_save');
            form_data.append('supporttitle', supporttitle);
            form_data.append('username', username);
            if(querytype == 'product'){
                form_data.append('productid', productid);
            }else{
                form_data.append('productid','');
            }
            form_data.append('supportmessage', supportmessage);
            form_data.append('querytype', querytype);
            form_data.append('priortytype', priortytype);
            form_data.append('supportemail', supportemail);
            form_data.append('action', 'md_support_save');
            jQuery.ajax({
                url:mdsajax.ajaxurl,
                type: 'post',
                dataType: 'json',
                contentType: false,
                processData: false,
                data: form_data,
                success: function (response) {
                    jQuery('.Success-div').html("Form Submit Successfully");
                }
            });
        });

})( jQuery );
