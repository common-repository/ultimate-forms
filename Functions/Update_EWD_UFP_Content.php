<?php
/* This file is the action handler. The appropriate function is then called based 
*  on the action that's been selected by the user. The functions themselves are all
* stored either in Prepare_Data_For_Insertion.php or Update_Admin_Databases.php */
		
function Update_EWD_UFP_Content() {
global $ewd_ufp_message;
if (isset($_GET['Action'])) {
		switch ($_GET['Action']) {
			case "EWD_UFP_UpdateOptions":
       			$ewd_ufp_message = EWD_UFP_UpdateOptions();
				break;
			case "EWD_UFP_Export_To_Excel":
       			$ewd_ufp_message = EWD_UFP_Export_Form_Submissions();
				break;
			default:
				$ewd_ufp_message = __("The form has not worked correctly. Please contact the plugin developer.", 'ultimate-reviews');
				break;
		}
	}
}

?>