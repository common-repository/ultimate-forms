<?php
function EWD_UFP_AJAX_Add_Element_To_Form() {
	$Question_Type = $_POST['Question_Type'];
	$Question_Title = $_POST['Question_Title'];
	$Question_Counter = $_POST['Row_Counter'];
	$Page_Counter = $_POST['Page_Counter'];

	$Form_Element = null;
	$AJAX_Add = 'Yes';

	echo EWD_UFP_Add_Form_Element($Question_Type, $Question_Counter, $Page_Counter, $Form_Element, $AJAX_Add, $Question_Title);
}
add_action('wp_ajax_ewd_ufp_add_form_element', 'EWD_UFP_AJAX_Add_Element_To_Form');

function EWD_UFP_Clear_Form_Submissions() {
	$Path = ABSPATH . 'wp-load.php';
    include_once($Path);

    global $wpdb;
    global $ewd_ufp_submissions_table_name;
	global $ewd_ufp_responses_table_name;

	$Form_ID = $_POST['Form_ID'];

	if (!isset($Form_ID)) {return;}

	$Submissions = $wpdb->get_results($wpdb->prepare("SELECT Submission_ID FROM $ewd_ufp_submissions_table_name WHERE Form_ID=%d", $Form_ID));
	foreach ($Submissions as $Submission) {
		$wpdb->query($wpdb->prepare("DELETE FROM $ewd_ufp_responses_table_name WHERE Submission_ID=%d", $Submission->Submission_ID));
	}
	$wpdb->query($wpdb->prepare("DELETE FROM $ewd_ufp_submissions_table_name WHERE Form_ID=%d", $Form_ID));
}
add_action('wp_ajax_ewd_ufp_clear_submissions', 'EWD_UFP_Clear_Form_Submissions');

function EWD_UFP_GET_Form_Submissions() {
	$Path = ABSPATH . 'wp-load.php';
    include_once($Path);

    global $wpdb;
    global $ewd_ufp_submissions_table_name;
	global $ewd_ufp_responses_table_name;

	$Form_ID = $_POST['Form_ID'];
	$Submission_Counter = is_numeric($_POST['Submission_Counter']) ? $_POST['Submission_Counter'] : 0;
	$Per_Page = is_numeric($_POST['Per_Page']) ? $_POST['Per_Page'] : 20;
	$Column_Element_IDs = unserialize($_POST['Column_Element_IDs']);

	if (!isset($Form_ID)) {return;}
	if (!is_array($Column_Element_IDs)) {return;}

	$ReturnString = "";

	$Submissions = $wpdb->get_results($wpdb->prepare("SELECT Submission_ID FROM $ewd_ufp_submissions_table_name WHERE Form_ID=%d ORDER BY Submission_ID LIMIT $Submission_Counter, $Per_Page", $Form_ID));
	foreach ($Submissions as $Submission) {
		$Form_Answers = $wpdb->get_results($wpdb->prepare("SELECT * FROM $ewd_ufp_responses_table_name WHERE Submission_ID=%d", $Submission->Submission_ID));
		$ReturnString .= "<tr>";
		foreach ($Column_Element_IDs as $Column_Element_ID) {
			$ReturnString .= "<td>";
			foreach ($Form_Answers as $Form_Answer) {
				if ($Column_Element_ID == $Form_Answer->Form_Element_ID) {$ReturnString .= $Form_Answer->Submission_Value;}
			}
			$ReturnString .= "</td>";
		}
		$ReturnString .= "</tr>";
	}

	echo $ReturnString;

	die();
}
add_action('wp_ajax_ewd_ufp_get_submissions', 'EWD_UFP_GET_Form_Submissions');
?>