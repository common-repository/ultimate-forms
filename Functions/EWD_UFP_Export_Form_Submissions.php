<?php
function EWD_UFP_Export_Form_Submissions() {
    global $wpdb;
    global $ewd_ufp_submissions_table_name;
	global $ewd_ufp_responses_table_name;

	require_once(EWD_UFP_CD_PLUGIN_PATH . '/PHPExcel/Classes/PHPExcel.php');

	$Form_ID = $_GET['Form_ID'];
	$Format = $_GET['Format'];

	if (!isset($Form_ID)) {return;}
	if (!isset($Format)) {$Format = "Individual";}

	$Form_Element_IDs = get_post_meta($Form_ID, 'EWD_UFP_Form_Element_IDs', true);
	if (!is_array($Form_Element_IDs)) {$Form_Element_IDs = array();}

	// Instantiate a new PHPExcel object 
	$objPHPExcel = new PHPExcel();  
	// Set the active Excel worksheet to sheet 0 
	$objPHPExcel->setActiveSheetIndex(0); 

	$objPHPExcel->getActiveSheet()->setCellValue("A1", __("Submitted Date-Time")); 

	$Column = "B";
	foreach ($Form_Element_IDs as $Form_Element_ID) {
		$Form_Element = get_post($Form_Element_ID);
		$Element_Columns[$Form_Element_ID] = $Column;
		$objPHPExcel->getActiveSheet()->setCellValue($Column . "1", $Form_Element->post_title);
		$Column++;
	}

	$Submissions = $wpdb->get_results($wpdb->prepare("SELECT Submission_ID, Submitted_Datetime FROM $ewd_ufp_submissions_table_name WHERE Form_ID=%d", $Form_ID));
	$Row = 2;
	foreach ($Submissions as $Submission) {
		$objPHPExcel->getActiveSheet()->setCellValue("A" . $Row, $Submission->Submitted_Datetime);
		$Responses = $wpdb->get_results($wpdb->prepare("SELECT Form_Element_ID, Submission_Value FROM $ewd_ufp_responses_table_name WHERE Submission_ID=%d", $Submission->Submission_ID));

		foreach ($Responses as $Response) {
			$Unserialized = unserialize($Response->Submission_Value);
			if (is_array($Unserialized)) {$Response->Submission_Value = implode(",", $Unserialized);}
			$objPHPExcel->getActiveSheet()->setCellValue($Element_Columns[$Response->Form_Element_ID] . $Row, $Response->Submission_Value);
		}
		$Row++;
	}

	// Redirect output to a client’s web browser (Excel5) 
	$Title = "Form " . get_the_title($Form_ID) . " Answers";
	if (isset($Format_Type) and $Format_Type == "CSV") {
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="' . $Title . '.csv"'); 
		header('Cache-Control: max-age=0'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
		$objWriter->save('php://output');
	}
	else {
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="' . $Title . '.xls"'); 
		header('Cache-Control: max-age=0'); 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');
	}

	exit();
}

?>