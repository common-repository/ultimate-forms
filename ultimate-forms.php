<?php
/*
Plugin Name: Ultimate Forms
Plugin URI: http://www.EtoileWebDesign.com/plugins/
Description: Create and display forms, email or save the forms submissions
Author: Etoile Web Design
Author URI: http://www.EtoileWebDesign.com/
Terms and Conditions: http://www.etoilewebdesign.com/plugin-terms-and-conditions/
Text Domain: ultimate-forms
Version: 0.5
*/

global $ewd_ufp_message;
global $ewd_ufp_submissions_table_name;
global $ewd_ufp_responses_table_name;

$ewd_ufp_submissions_table_name = $wpdb->prefix . "ufp_submissions";
$ewd_ufp_responses_table_name = $wpdb->prefix . "ufp_responses";

$EWD_UFP_Version = '0.1a';

define( 'EWD_UFP_CD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'EWD_UFP_CD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

//define('WP_DEBUG', true);

register_activation_hook(__FILE__,'Set_EWD_UFP_Options');

/* Hooks neccessary admin tasks */
if ( is_admin() ){
	add_action('admin_head', 'EWD_UFP_Admin_Options');
	add_action('widgets_init', 'Update_EWD_UFP_Content');
	add_action('admin_head', 'Add_EWD_UFP_Scripts');
	add_action('admin_notices', 'EWD_UFP_Error_Notices');
}

function EWD_UFP_Enable_Menu() {
	global $submenu;

	$Admin_Approval = get_option("EWD_UFP_Admin_Approval");

	add_menu_page( 'Ultimate Forms', 'Forms', 'edit_posts', 'EWD-UFP-Options', 'EWD_UFP_Output_Options', null, '49.1' );
	$submenu['EWD-UFP-Options'][3] = $submenu['EWD-UFP-Options'][1];
	$submenu['EWD-UFP-Options'][1] = array( 'Forms', 'edit_posts', "edit.php?post_type=ufp_form", "Forms" );
	$submenu['EWD-UFP-Options'][2] = array( 'Add New', 'edit_posts', "post-new.php?post_type=ufp_form", "Add New" );
	add_submenu_page('EWD-UFP-Options', 'UFP Options', 'Settings', 'edit_posts', 'EWD-UFP-Options&DisplayPage=Options', 'EWD_UFP_Output_Options');

	//$submenu['EWD-UFP-Options'][0][0] = "Dashboard";
	ksort($submenu['EWD-UFP-Options']);
}
add_action('admin_menu' , 'EWD_UFP_Enable_Menu');
/*
function EWD_UFP_Screen_Options() {
	$screen = get_current_screen();

	// get out of here if we are not on our settings page
	if (!isset($screen) or !isset($screen->post_type) or $screen->post_type != 'ufp_form') {
		return;
	}

	$args = array(
		'label' => __('Submissions per page', 'ultimate-forms'),
		'default' => 20,
		'option' => 'ewd_ufp_submissions_per_page'
	);
	//add_screen_option( 'UPCP_per_page', $args );
	$screen->add_option( 'per_page', $args );
}
add_action("load-edit.php", "EWD_UFP_Screen_Options");
add_action("load-post.php", "EWD_UFP_Screen_Options");

function EWD_UFP_Set_Screen_Options($Status, $option, $value) {
	if ('ewd_ufp_submissions_per_page' == $option) {update_option("EWD_UFP_Debugging", $option . " - " . $value); return $value;}
}
add_filter('set-screen-option', 'EWD_UFP_Set_Screen_Options', 10, 3);
*/
function EWD_UFP_Add_Header_Bar($Called = "No") {
	global $pagenow;

	if ($Called != "Yes" and (!isset($_GET['post_type']) or $_GET['post_type'] != "ufp_form")) {return;}

	$Admin_Approval = get_option("EWD_UFP_Admin_Approval"); ?>

	<div class="EWD_UFP_Menu">
		<h2 class="nav-tab-wrapper">
		<a id="ewd-ufp-dash-mobile-menu-open" href="#" class="MenuTab nav-tab"><?php _e("MENU", 'ultimate-forms'); ?><span id="ewd-ufp-dash-mobile-menu-down-caret">&nbsp;&nbsp;&#9660;</span><span id="ewd-ufp-dash-mobile-menu-up-caret">&nbsp;&nbsp;&#9650;</span></a>
		<a id="Dashboard_Menu" href='admin.php?page=EWD-UFP-Options' class="MenuTab nav-tab <?php if (!isset($_GET['post_type']) and ($_GET['DisplayPage'] == '' or $_GET['DisplayPage'] == 'Dashboard')) {echo 'nav-tab-active';}?>"><?php _e("Dashboard", 'ultimate-forms'); ?></a>
		<a id="Reviews_Menu" href='edit.php?post_type=ufp_form' class="MenuTab nav-tab <?php if (isset($_GET['post_type']) and $_GET['post_type'] == 'ufp_form' and $pagenow == 'edit.php') {echo 'nav-tab-active';}?>"><?php _e("Forms", 'ultimate-forms'); ?></a>
		<a id="Add_New_Menu" href='post-new.php?post_type=ufp_form' class="MenuTab nav-tab <?php if (isset($_GET['post_type']) and $_GET['post_type'] == 'ufp_form' and $pagenow == 'post-new.php') {echo 'nav-tab-active';}?>"><?php _e("Add New", 'ultimate-forms'); ?></a><a id="Options_Menu" href='admin.php?page=EWD-UFP-Options&DisplayPage=Options' class="MenuTab nav-tab <?php if (!isset($_GET['post_type']) and $_GET['DisplayPage'] == 'Options') {echo 'nav-tab-active';}?>"><?php _e("Options", 'ultimate-forms'); ?></a>
		</h2>
	</div>
<?php }
add_action('admin_notices', 'EWD_UFP_Add_Header_Bar');

/* Add localization support */
function EWD_UFP_localization_setup() {
		load_plugin_textdomain('ultimate-forms', false, dirname(plugin_basename(__FILE__)) . '/lang/');
}
add_action('after_setup_theme', 'EWD_UFP_localization_setup');

// Add settings link on plugin page
function EWD_UFP_plugin_settings_link($links) {
  $settings_link = '<a href="admin.php?page=EWD-UFP-Options">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'EWD_UFP_plugin_settings_link' );

function Add_EWD_UFP_Scripts() {
	global $EWD_UFP_Version;
	global $post;

		if ((isset($_GET['post_type']) && $_GET['post_type'] == 'ufp_form') or (isset($_GET['page']) && $_GET['page'] == 'EWD-UFP-Options') or (isset($post) and $post->post_type == 'ufp_form')) {
			wp_enqueue_script(  'jquery-ui-core' );
            wp_enqueue_script(  'jquery-ui-sortable' );
			wp_enqueue_script('ewd-ufp-admin', plugins_url("ultimate-forms/js/Admin.js"), array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'ewd-ufp-confirmations'), $EWD_UFP_Version);
			wp_enqueue_script('spectrum', plugins_url("ultimate-forms/js/spectrum.js"), array('jquery'));
			wp_enqueue_script('bootstrap', plugins_url("ultimate-forms/js/bootstrap.min.js"), array('jquery'));
			wp_enqueue_script('ewd-ufp-confirmations', plugins_url("ultimate-forms/js/jquery.confirm.min.js"), array('jquery'));
		}
}

add_action( 'wp_enqueue_scripts', 'Add_EWD_UFP_FrontEnd_Scripts' );
function Add_EWD_UFP_FrontEnd_Scripts() {
	global $wpdb;

	$Form_Data = array('Regex' => array(), 'Regex_Failed' => array());
	
	$Form_Regex = $wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key='EWD_UFP_Element_Regex'");
	foreach ($Form_Regex as $Regex) {$Form_Data['Regex'][$Regex->post_id] = $Regex->meta_value;}

	$Form_Regex_Failed = $wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key='EWD_UFP_Element_Regex_Failed'");
	foreach ($Form_Regex_Failed as $Regex_Failed) {$Form_Data['Regex_Failed'][$Regex_Failed->post_id] = $Regex_Failed->meta_value;}

	wp_enqueue_script('ewd-ufp-js', plugins_url( '/js/ewd-ufp-js.js' , __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-autocomplete', 'jquery-ui-slider' ));
	wp_localize_script('ewd-ufp-js', 'ewd_ufp_form_data', $Form_Data );
}


add_action( 'wp_enqueue_scripts', 'EWD_UFP_Add_Stylesheet' );
function EWD_UFP_Add_Stylesheet() {
    //wp_enqueue_style( 'ewd-ufp-jquery-ui', plugins_url('css/ewd-ufp-jquery-ui.css', __FILE__) );
    wp_enqueue_style( 'ewd-ufp-styles', plugins_url('css/ewd-ufp-styles.css', __FILE__) );
}

function EWD_UFP_Admin_Options() {
	global $EWD_UFP_Version;
	global $post;

	if ((isset($_GET['post_type']) && $_GET['post_type'] == 'ufp_form') or (isset($_GET['page']) && $_GET['page'] == 'EWD-UFP-Options') or (isset($post) and $post->post_type == 'ufp_form')) {
		wp_enqueue_style( 'ewd-ufp-admin', plugins_url("ultimate-forms/css/Admin.css"), array(), $EWD_UFP_Version);
		wp_enqueue_style( 'spectrum', plugins_url("ultimate-forms/css/spectrum.css"));
	}
}

add_action('activated_plugin','save_ufp_error');
function save_ufp_error(){
		update_option('ufp_plugin_error',  ob_get_contents());
}

function Set_EWD_UFP_Options() {
	if (get_option("EWD_UFP_Maximum_Score") == "") {update_option("EWD_UFP_Maximum_Score", "5");}
}

include "Functions/Error_Notices.php";
include "Functions/EWD_UFP_Add_Form_To_Page.php";
include "Functions/EWD_UFP_Captcha.php";
include "Functions/EWD_UFP_Edit_Form_Page_Content.php";
include "Functions/EWD_UFP_Export_Form_Submissions.php";
include "Functions/EWD_UFP_Handle_Form_Submission.php";
include "Functions/EWD_UFP_Output_Options.php";
include "Functions/EWD_UFP_Process_Ajax.php";
include "Functions/EWD_UFP_Widgets.php";
include "Functions/Register_EWD_UFP_Posts_Taxonomies.php";
include "Functions/Update_EWD_UFP_Admin_Databases.php";
include "Functions/Update_EWD_UFP_Content.php";
include "Functions/Update_EWD_UFP_Tables.php";

include "Shortcodes/Insert_Contact_Form.php";

if ($EWD_UFP_Version != get_option('EWD_UFP_Version')) {
	Set_EWD_UFP_Options();
	Update_EWD_UFP_Tables();
	//EWD_UFP_Version_Update();
}
?>
