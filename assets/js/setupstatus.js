/*jQuery(document).ready(function(){

	jQuery("#setupstatus").click(function(){

        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'http://localhost/wordpress/wp-admin/admin-ajax.php',
            data: { 
                'action': 'post_setup_status_data', 
                'status_color': jQuery('#status_color').val(), 
                'assignee': jQuery('#assignee').val(), 
                'snotes': jQuery('#snotes').val() },
            success: function(data){
				alert(data);
                document.location.href = HM_Ajax.redirecturl;
                
            }
        });
		
	});
	
	
});
*/

jQuery( document ).ready( function( $ ) {

        "use strict";
	/**
         * The file is enqueued from inc/admin/class-admin.php.
	 */        
        jQuery( '#nds_add_user_meta_ajax_form' ).submit( function( event ) {
            
            event.preventDefault(); // Prevent the default form submit.            
            
            // serialize the form data
            var ajax_form_data = jQuery("#nds_add_user_meta_ajax_form").serialize();
            
            //add our own ajax check as X-Requested-With is not always reliable
            ajax_form_data = ajax_form_data+'&ajaxrequest=true&submit=Save';

            jQuery.ajax({
                url:    HM_Ajax.ajaxurl, // domain/wp-admin/admin-ajax.php
                type:   'post',                
                data:   ajax_form_data,
				action: 'post_setup_status_data',
            })
            
            .done( function( response ) { // response from the PHP action
				if (response==2){
					var formData = jQuery("#nds_add_user_meta_ajax_form").serialize().split("&");
					var obj={};
					var nstatusText;
					
					for(var key in formData)
						{
							obj[formData[key].split("=")[0]] = formData[key].split("=")[1];
						}
					
					jQuery(".assigneeValData option[value='"+obj.assignee+"']").attr('selected', 'selected');
					jQuery(".statusColor option[value='"+obj.status_color+"']").attr('selected', 'selected');
					
					switch ($.trim(obj.status_color)) {
						case "e91a37" :
							nstatusText = "Warning"; 
							break;
						case '59b61b' :
							nstatusText = "Done"; 
							break;
						case "e3951b" :
							nstatusText = "In Progress"; 
							break;
						case "2331e9" :
							nstatusText = "Ongoing"; 
							break;
						case "c311e9" :
							nstatusText = "To-Do"; 
							break;
						case "e4eb17" :
							nstatusText = "In Development"; 
							break;					
						default :
							nstatusText = "Default"; 
							break;
					}
					
					
					var $editrow = jQuery('a#row-'+obj.uid).closest('tr');					
					$editrow.find('.table-status-color').css('background-color', '#'+$.trim(obj.status_color));
					$editrow.find('.table-status-text').text(nstatusText);
					$editrow.find('.column-assignee').text(obj.assignee);					
				}			
                jQuery("#nds_form_feedback ").html( "<h2 class='success-msg'>Data Saved Successfully </h2><br>" );
	        })
            
            // something went wrong  
            .fail( function() {
                jQuery(" #nds_form_feedback ").html( "<h2 class='error-msg'>Something went wrong.</h2><br>" );                  
            })
        
            // after all this time?
            .always( function() {
                event.target.reset();
            });
        
       });   
	   
	   
	   
	   
	   
		jQuery('.editDataClass').on('click', function (event) { 
			
			var $row = jQuery(this).closest('tr');
			var $columns = $row.find('td');
			var statusText = null;
			
			jQuery('.statusNote').text($columns[3].innerHTML);
			
			var strColor = String($columns[2].innerHTML.replace(/(<([^>]+)>)/ig,"").toLowerCase());
						
			switch ($.trim(strColor)) {
				case "warning" :
					statusText = "e91a37"; 
					break;
				case 'done' :
					statusText = "59b61b"; 
					break;
				case "in progress" :
					statusText = "e3951b"; 
					break;
				case "ongoing" :
					statusText = "2331e9"; 
					break;
				case "to-do" :
					statusText = "c311e9"; 
					break;
				case "in development" :
					statusText = "e4eb17"; 
					break;					
				default :
					statusText = "ffffff"; 
					break;
			}
			
			jQuery(".statusColor option[value='"+statusText+"']").prop('selected', true);
						
			var assigneeValData = $.trim(String($columns[1].innerHTML).toLowerCase());		
			jQuery(".assigneeValData option[value='"+assigneeValData+"']").prop('selected', true);
			jQuery("#uid").val(jQuery(this).data('id'));
			
		});
					
});



