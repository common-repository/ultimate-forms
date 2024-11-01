<?php
/* The file contains all of the functions which make changes to the WordPress tables */

function EWD_UFP_UpdateOptions() {
	if (isset($_POST['Options_Submit'])) {update_option('EWD_UFP_Custom_CSS', sanitize_text_field($_POST['custom_css']));}
	
	if (isset($_POST['submitted_successfully_label'])) {update_option('EWD_UFP_Submitted_Successfully_Label', sanitize_text_field($_POST['submitted_successfully_label']));}
	if (isset($_POST['general_failure_label'])) {update_option('EWD_UFP_General_Failure_Label', sanitize_text_field($_POST['general_failure_label']));}
	if (isset($_POST['email_failure_label'])) {update_option('EWD_UFP_Email_Failure_Label', sanitize_text_field($_POST['email_failure_label']));}
	if (isset($_POST['save_failure_label'])) {update_option('EWD_UFP_Save_Failure_Label', sanitize_text_field($_POST['save_failure_label']));}

	$update_message = __("Options have been successfully updated.", 'ultimate-wp-mail');
	$update['Message'] = $update_message;
	$update['Message_Type'] = "Update";
	return $update;
}

?>
