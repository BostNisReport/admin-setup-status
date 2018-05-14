<?php
/**
* Project: setupstatus.
* Copyright: 2018 @Agent Design
*/
function post_setup_status_data(){
	$sstatus = $_POST['status_color'];
	$sassignee = $_POST['assignee'];
	$snotes = $_POST['notes'];
	$pageTitle = $_POST['page_title'];
	$dataId =  $_POST['uid'];
		
	if( isset( $_POST['ajaxrequest'] ) && $_POST['ajaxrequest'] === 'true' ) {
		global $wpdb;
		
		$current_url = $_SERVER[HTTP_REFERER];
				
			if ($dataId>0){ 
				$wpdb->update( $wpdb->prefix.'setup_status_info', array('setup_status' => $sstatus, 'assignee' => $sassignee, 'status_note' => $snotes, 'issue_date' => current_time( 'mysql' )), array( 'id'=>$dataId));
				echo "2";
			}else{
				$wpdb->insert( $wpdb->prefix.'setup_status_info', array("setup_status" => $sstatus, "assignee" => $sassignee, "status_note" => $snotes, "page_name" => $pageTitle, "page_url" => $current_url, "issue_date" => current_time( 'mysql' )));					
			}
		
		die();
		return true;
	}		
}
add_action('wp_ajax_post_setup_status_data', 'post_setup_status_data');
?>
