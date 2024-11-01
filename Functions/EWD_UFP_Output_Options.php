<?php
/* Creates the admin page, and fills it in based on whether the user is looking at
*  the overview page or an individual item is being edited */
function EWD_UFP_Output_Options() {
	if (!isset($_GET['DisplayPage'])) {$_GET['DisplayPage'] = "";}

	include( plugin_dir_path( __FILE__ ) . '../html/AdminHeader.php');
	if ($_GET['DisplayPage'] == 'Dashboard' or $_GET['DisplayPage'] == "") {include( plugin_dir_path( __FILE__ ) . '../html/DashboardPage.php');}
	if ($_GET['DisplayPage'] == 'Options') {include( plugin_dir_path( __FILE__ ) . '../html/OptionsPage.php');}
	include( plugin_dir_path( __FILE__ ) . '../html/AdminFooter.php');
}
?>