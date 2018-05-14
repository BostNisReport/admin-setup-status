<?php
/*
* Plugin Name: Admin Setup Status
* Description: Admin Setup Status.
* Plugin URI:
* Author: Agent Design
* Author URI: 
* Version: 1.0.0
*/
define('SETUPSTATUS_VERSION', '1.5.0');
define('SETUPSTATUS_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('SETUPSTATUS_IMG_URL', SETUPSTATUS_PLUGIN_URL . 'assets/img/');
define('SETUPSTATUS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SETUPSTATUS_THEME_DIR', get_stylesheet_directory());

// register custom DB on plugin activation
require_once(SETUPSTATUS_PLUGIN_DIR.'lib/db.php');

// register custom DB on plugin activation
//require_once(SETUPSTATUS_PLUGIN_DIR.'lib/modal.php');

function installer(){
    include(SETUPSTATUS_PLUGIN_DIR.'lib/installer.php');
}
register_activation_hook( __file__, 'installer' );


add_action( 'admin_enqueue_scripts', 'custom_wp_toolbar_css_admin' );
add_action( 'wp_enqueue_scripts', 'custom_wp_toolbar_css_admin' );
 
function custom_wp_toolbar_css_admin() {
	if (is_admin()){
        wp_register_style( 'add_custom_wp_toolbar_css', plugin_dir_url( __FILE__ ) . 'assets/css/setupstatus-admin.css','','', 'screen' );
        wp_enqueue_style( 'add_custom_wp_toolbar_css' );
	}
}

function wptuts_styles_with_the_lot()
{ 
	if (is_admin()){
		// Register the style like this for a plugin:
		wp_register_style( 'custom-style', plugins_url( 'assets/css/setupstatus.css', __FILE__ ), array(), '20120208', 'all' );
		wp_enqueue_style( 'custom-style' );
	}
}
add_action( 'wp_enqueue_scripts', 'wptuts_styles_with_the_lot' );

function jquery_modal() {
	if (is_admin()){
		echo '<link href="'.plugins_url( 'setupstatus/assets/css/vendor/jquery.modal.min.css').'"  rel="stylesheet">';
		echo '<script type="text/javascript" src="'.plugins_url( 'setupstatus/assets/js/jquery.min.js').'"></script>';
		echo '<script type="text/javascript" src="'.plugins_url( 'setupstatus/assets/js/jquery.modal.min.js').'"></script>';
		//echo '<script type="text/javascript" src="'.plugins_url( 'setupstatus/assets/js/setupstatus.js').'"></script>';
	}
}
add_action('admin_head', 'jquery_modal');


add_action( 'init', 'hm_my_script_enqueuer' );
function hm_my_script_enqueuer() {
	if (is_admin()){
		wp_register_script( "hm_edit_field_script", plugins_url() .'/setupstatus/assets/js/setupstatus.js');
		wp_localize_script( 'hm_edit_field_script', 'HM_Ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'savingMsg'=>'Saving ...' ));
		wp_enqueue_script( 'hm_edit_field_script' );
	}
}

if (is_admin()){

add_action('admin_bar_menu', 'add_toolbar_items', 100);
function add_toolbar_items($admin_bar){
    
	global $wpdb;
	
	$current_url = $_SERVER[HTTP_REFERER];
	
	$sqlAvailable = "SELECT * FROM ".$wpdb->prefix."setup_status_info WHERE page_name = '".get_admin_page_title()."'";
	$resultAvailale = $wpdb->get_results($sqlColor);
	
	if ($resultAvailale[0]->id>0){
		$navText = 'Edit';
	}else{
		$navText = 'Add';
	}
	
	
	$admin_bar->add_menu( array(
		'id'    => 'ss-item',		
		'title' => '<span class="ab-icon"></span>' . _( 'Setup Status' ),
		'href'  => '#',
		'meta'  => array(
			'title' => __('Setup Status'),  
			'class' => 'ss_menu_parent_class'
		),
	));
    
	/*$admin_bar->add_menu( array(
		'id'    => 'ss-sub-item',
		'parent' => 'ss-item',
		'title' => 'Notes',
		'href'  => '#ex2',
		'meta'  => array(
			'title' => __('Setup Status Notes'),
			'class' => 'ss_menu_edit_class',
			'rel' => 'modal:open'
		),
	));*/
    
	if( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$role = ( array ) $user->roles;
		$uRole = $role[0];
	}
	if ($uRole == 'administrator'){
		$admin_bar->add_menu( array(
			'id'    => 'ss-second-sub-item',
			'parent' => 'ss-item',
			'title' => $navText,
			'href'  => '#ex1',
			'meta'  => array(
				'title' => __('Setup Status '.$navText),
				'class' => 'ss_menu_edit_class',
				'rel' => 'modal:open'
			),
		));
	}	

	
	
	
	global $wpdb;

	$user = wp_get_current_user();
    $role = ( array ) $user->roles;
    	
	//$sqlColor = "SELECT setup_status, status_note FROM ".$wpdb->prefix."setup_status_info uo WHERE id = (SELECT MAX(id) FROM ".$wpdb->prefix."setup_status_info WHERE assignee = uo.assignee AND assignee = '".$role[0]."')";
	$sqlColor = "SELECT * FROM ".$wpdb->prefix."setup_status_info uo WHERE id = (SELECT MAX(id) FROM ".$wpdb->prefix."setup_status_info WHERE page_name = uo.page_name AND page_name = '".get_admin_page_title()."')";
	$resultColor = $wpdb->get_results($sqlColor);
	$colorCode = '#'.$resultColor[0]->setup_status;
	
	if ( count($resultColor)>0 && count($resultColor)!='' ) { ?>
		<style>
			<?php if ($navText=="Edit"){?>
			.ss_menu_parent_class .ab-icon::before{ color: <?php echo $colorCode;?> !important; }
			<?php }else{ ?>
			.ss_menu_parent_class .ab-icon::before{ color: #a0a5aa !important; }
			<?php }?>
		</style>
		<div id="ex1" class="modal" style="display:none;">
			<div class="modal-info">
				<div id="nds_form_feedback" style="margin-bottom:10px; text-align:center;"></div>
				<form method="post" id="nds_add_user_meta_ajax_form" action="<?php echo site_url();?>/wp-admin/admin-post.php">
					<p>Status:<br>
						<select class="statusColor" name="status_color">
							<option <?php if ($resultColor[0]->setup_status == "ffffff"){ echo 'selected="selected"';} ?> value="ffffff">Default - White</option>
							<option <?php if ($resultColor[0]->setup_status == "2331e9"){ echo 'selected="selected"';} ?> value="2331e9">Ongoing - Blue</option>
							<option <?php if ($resultColor[0]->setup_status == "c311e9"){ echo 'selected="selected"';} ?> value="c311e9">To-Do - Purple</option>
							<option <?php if ($resultColor[0]->setup_status == "e4eb17"){ echo 'selected="selected"';} ?> value="e4eb17">In Development - Yellow</option>
							<option <?php if ($resultColor[0]->setup_status == "e3951b"){ echo 'selected="selected"';} ?> value="e3951b">In Progress - Orange</option>
							<option <?php if ($resultColor[0]->setup_status == "59b61b"){ echo 'selected="selected"';} ?> value="59b61b">Done - Green</option>
							<option <?php if ($resultColor[0]->setup_status == "e91a37"){ echo 'selected="selected"';} ?> value="e91a37">Warning - Red</option>
						</select>
					</p>
					<p>Assignee:<br>				
						<?php global $wp_roles; ?>
						<select class="assigneeValData" name="assignee">
						<?php 
							echo '<optgroup label="Administrator">';
							$args1 = array(
								'role' => 'administrator',
								'orderby' => 'user_nicename',
								'order' => 'ASC'
							);
							$adminuser = get_users($args1);							
							foreach ($adminuser as $user) {
								if ($resultColor[0]->assignee == $user->display_name){ $selData = 'selected="selected"';}
								else { $selData = '';}
								echo '<option '.$selData.' value="'.$user->display_name.'">'.$user->display_name.'</option>';
							}
							echo '</optgroup>'; 
						
							foreach ( $wp_roles->roles as $key=>$value ): 
						
							if ($key!='administrator'){
								
								if ($resultColor[0]->assignee == $key){ $selData = 'selected="selected"';}
								else { $selData = '';}
								?>
								<option <?php echo $selData;?> value="<?php echo $key; ?>"><?php echo $value['name']; ?></option>
							<?php }?>
						<?php endforeach; ?>
						</select>	
					</p>
					<p>Notes:<br>
					<textarea name="notes" class="statusNote" style="width:100%;" id="notes"><?php echo $resultColor[0]->status_note;?></textarea>
					</p>
					<p>	
					<input type="hidden" name="action" value="post_setup_status_data" >
					<input type="hidden" name="nonce" value="15a401ef5e" >
					<input type="hidden" name="page_title" value="<?php echo get_admin_page_title();?>" >
					<input type="hidden" id="uid" name="uid" value="<?php echo $resultColor[0]->id;?>" >
					<input type="submit" id="setupstatus" class="button button-primary" value="Save" /></p>
				</form>
			</div>
		</div>
		<div id="ex2" class="modal note-modal" style="display:none;">
			<div class="modal-info">
				<div id="nds_form_feedback">
					<?php echo $resultColor[0]->status_note; ?>
				</div>		
			</div>
		</div>
	
	<?php } else { ?>
	
		<div id="ex2" class="modal note-modal" style="display:none;">
			<div class="modal-info">
				<div id="nds_form_feedback">
					No notes available...
				</div>		
			</div>
		</div>
		<div id="ex1" class="modal" style="display:none;">
			<div class="modal-info">
				<div id="nds_form_feedback" style="margin-bottom:10px; text-align:center;"></div>
				<form method="post" id="nds_update_setup_meta_ajax_form" action="<?php echo site_url();?>/wp-admin/admin-post.php">
					<p>Status:<br>
						<select class="statusColor" name="status_color">
							<option value="ffffff">Default - White</option>
							<option value="2331e9">Ongoing - Blue</option>
							<option value="c311e9">To-Do - Purple</option>
							<option value="e4eb17">In Development - Yellow</option>
							<option value="e3951b">In Progress - Orange</option>
							<option value="59b61b">Done - Green</option>
							<option value="e91a37">Warning - Red</option>
						</select>
					</p>
					<p>Assignee:<br>
						<?php global $wp_roles; ?>
						<select class="assigneeValData" name="assignee">
						<?php 
							echo '<optgroup label="Administrator">';
							$args1 = array(
								'role' => 'administrator',
								'orderby' => 'user_nicename',
								'order' => 'ASC'
							);
							$adminuser = get_users($args1);							
							foreach ($adminuser as $user) {
								echo '<option value="'.$user->display_name.'">'.$user->display_name.'</option>';
							}
							echo '</optgroup>'; 
						
							foreach ( $wp_roles->roles as $key=>$value ): 
						
							if ($key!='administrator'){?>
								<option value="<?php echo $key; ?>"><?php echo $value['name']; ?></option>
							<?php }?>
						<?php endforeach; ?>
						</select>	
					</p>
					<p>Notes:<br>
					<textarea name="notes" class="statusNote" style="width:100%;" id="notes"></textarea>
					</p>
					<p>	
					<input type="hidden" name="action" value="post_setup_status_data" >
					<input type="hidden" name="nonce" value="15a401ef5e" >
					<input type="hidden" name="page_title" value="<?php echo get_admin_page_title();?>" >
					<input type="submit" id="setupstatus" class="button button-primary" value="Save" /></p>
				</form>
			</div>
		</div>
	<?php } 	
} 
register_activation_hook( __FILE__, 'my_activation_func' );

function my_activation_func() {
    file_put_contents( __DIR__ . '/my_loggg.txt', ob_get_contents() );
}

function plugin_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=setupstatus">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ , 'settings');
add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link' );

function setupstatus_register_options_page() {
  add_options_page('Admin Settings Status', 'Admin Settings Status', 'manage_options', 'setupstatus', 'myplugin_options_page');
}
add_action('admin_menu', 'setupstatus_register_options_page');

function myplugin_options_page()
{
	if(is_admin())
	{
		$listTable = new Paulund_Wp_List_Table();
		$listTable->list_table_page();
	}
} 

/**
 * Paulund_Wp_List_Table class will create the page to load the table
 */
class Paulund_Wp_List_Table
{
    /**
     * Constructor will create the menu item
     */
    public function __construct()
    {
        add_action( 'admin_menu', array($this, 'add_menu_setupstatus_list_table_page' ));		
    }
    /**
     * Menu item will allow us to load the page to display the table
     */
    public function add_menu_setupstatus_list_table_page()
    { 
        add_menu_page( 'Setupstatus List Table', 'Setup Status List Table', 'manage_options', 'setupstatus.php', array($this, 'list_table_page') );
    }
    /**
     * Display the list table page
     *
     * @return Void
     */
    public function list_table_page()
    {
		
        $exampleListTable = new Example_List_Table();
        $exampleListTable->prepare_items();
        ?>
            <div class="wrap">
                <div id="icon-users" class="icon32"></div>
                <h2>Admin Setup Status List</h2>
                <?php $exampleListTable->display(); ?>
            </div>
        <?php
    }
}
// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
/**
 * Create a new table class that will extend the WP_List_Table
 */
class Example_List_Table extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
		
		
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );
        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }
    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'page_name'   	=> 'Page Name',
			'assignee'   	=> 'Assignee',
            'setup_status'  => 'Status',
            'status_note' 	=> 'Note',
			'issue_date' 	=> 'Time',
			//'action' 	=> 'Action',
        );
        return $columns;
    }
    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }
    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array('page_name' => array('page_name', false));
    }
    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
		global $wpdb;	
	    
	    	if( is_user_logged_in() ) {
			$user = wp_get_current_user();
			$role = ( array ) $user->roles;
			$uRole = $role[0];
		}
		
		$sql = "SELECT * FROM ".$wpdb->prefix."setup_status_info";
		$results = $wpdb->get_results($sql);

		$data = array();
		
		foreach($results as $result):
		
			$statusText = '';
		
			if ($result->setup_status == 'ffffff'){
				$statusText = "Default";
			}if ($result->setup_status == '2331e9'){
				$statusText = "Ongoing";
			}if ($result->setup_status == 'c311e9'){
				$statusText = "To-Do";
			}if ($result->setup_status == 'e4eb17'){
				$statusText = "In Development";
			}if ($result->setup_status == 'e3951b'){
				$statusText = "In Progress";
			}if ($result->setup_status == '59b61b'){
				$statusText = "Done";
			}if ($result->setup_status == 'e91a37'){
				$statusText = "Warning";
			}	
	    
	    		if ($uRole == 'administrator'){
				$actionString = '<a href="#ex1" id="row-'.$result->id.'" class="editDataClass" data-row="row-'.$result->id.'" data-id="'.$result->id.'" rel="modal:open">EDIT</a>';
			}else{
				$actionString = '<a href="'.$result->page_url.'" target="_blank">VIEW</a>';
			}
	    
		
		    $data[] = array(
                    'page_name'       	=> $result->page_name.'<input type="hidden" id="eid" name="eid" value="'.$result->id.'" />',
                    'assignee' 		=> $result->assignee,
                    'setup_status'      => '<div class="table-status-color" style="width:15px; height:15px; background-color:#'.$result->setup_status.'; float: left; margin-right: 10px; margin-top: 4px;"></div> <div class="table-status-text" style="float:left;">'.$statusText.'</div>',
                    'status_note'    	=> $result->status_note,
		    'issue_date' 	=> $result->issue_date,
		    'action'		=> $actionString,
                    );	
		endforeach;		
      
        return $data;
    }
    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'page_name':
            case 'assignee':
            case 'setup_status':
            case 'status_note':
			case 'issue_date':
			//case 'action':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }
    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'page_name';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }
        $result = strcmp( $a[$orderby], $b[$orderby] );
        if($order === 'asc')
        {
            return $result;
        }
        return -$result;
    }
}
}
