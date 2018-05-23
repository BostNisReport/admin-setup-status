jQuery( document ).ready( function($){ 
"use strict";	        
jQuery( '.nds_add_user_meta_ajax_form' ).submit( function( event ) {
            
			event.preventDefault(); // Prevent the default form submit.            
            
            // serialize the form data
            var ajax_form_data = jQuery(".nds_add_user_meta_ajax_form").serialize();
            
            //add our own ajax check as X-Requested-With is not always reliable
            ajax_form_data = ajax_form_data+'&ajaxrequest=true&submit=Save';

            jQuery.ajax({
                url:    HM_Ajax.ajaxurl, // domain/wp-admin/admin-ajax.php
                type:   'post',                
                data:   ajax_form_data,
				action: 'post_setup_status_data',
            }).done( function( response ) { // response from the PHP action
				
				//if (response.replace(/(<([^>]+)>)/ig,"")==2){
					var formData = jQuery(".nds_add_user_meta_ajax_form").serialize().split("&");
					var obj={};
					var nstatusText;
					
					for(var key in formData)
						{
							obj[formData[key].split("=")[0]] = formData[key].split("=")[1];
						} 
						
					var noteText = obj.notes;
					noteText = (noteText.split('+').join(' '));
					noteText = (noteText.split('%20').join(' '));
					
					jQuery('.statusColor option').removeAttr('selected');
					jQuery('.assigneeValData option').removeAttr('selected');
										
					jQuery(".assigneeValData option[value='"+obj.assignee+"']").attr('selected', 'selected');
					jQuery(".statusColor option[value='"+obj.status_color+"']").attr('selected', 'selected');
					
					jQuery('.statusNote').val();
					jQuery('.statusNote').text(decodeURIComponent(noteText));
					
										
					switch (jQuery.trim(obj.status_color)) {
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
					
					var dataTableAssignee = obj.assignee;
					dataTableAssignee = (dataTableAssignee.split('+').join(' '));
					dataTableAssignee = (dataTableAssignee.split('%20').join(' '));
					
					nstatusText = (nstatusText.split('+').join(' '));
					nstatusText = (nstatusText.split('%20').join(' '));
					
					var $editrow = jQuery('a#row-'+obj.uid).closest('tr');					
					$editrow.find('.table-status-color').css('background-color', '#'+jQuery.trim(obj.status_color));
					$editrow.find('.table-status-text').text(decodeURIComponent(nstatusText));
					$editrow.find('.column-assignee').text(decodeURIComponent(dataTableAssignee));	
					$editrow.find('.column-status_note').text(decodeURIComponent(noteText));		
				//}			
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
			
			jQuery('#ex3 .statusNote').val();
			jQuery('#ex3 .statusNote').val($columns[3].innerHTML);
			
			var strColor = String($columns[2].innerHTML.replace(/(<([^>]+)>)/ig,"").toLowerCase());
						
			switch (jQuery.trim(strColor)) {
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
			
			jQuery('#ex3 .statusColor option').removeAttr('selected');
			jQuery("#ex3 .statusColor option[value='"+statusText+"']").prop('selected', true);
			
			jQuery('#ex3 .assigneeValData option').removeAttr('selected');			
			var assigneeValData = jQuery.trim(String($columns[1].innerHTML).toLowerCase());		
			jQuery("#ex3 .assigneeValData option[value='"+assigneeValData+"']").prop('selected', true);
			jQuery("#ex3 .uid").val(jQuery(this).data('id'));			
		});				
});