<?php
/**
* Project: setupstatus.
* Copyright: 2018 @Agent Design
*/
global $wpdb;
$table_name = $wpdb->prefix . "setup_status_info";
$my_products_db_version = '1.0.0';
$charset_collate = $wpdb->get_charset_collate();

$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) {

    $sql = "CREATE TABLE $table_name (
             id int(11) NOT NULL AUTO_INCREMENT,
            `setup_status` varchar(255) NOT NULL,
            `assignee` varchar(255) NOT NULL,
            `status_note` text NOT NULL,
			`page_name` text NOT NULL,
			`page_url` text NOT NULL,
			`issue_date` datetime NOT NULL,
            PRIMARY KEY  (id)
    )    $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    add_option( 'my_db_version', $my_products_db_version );
}?>