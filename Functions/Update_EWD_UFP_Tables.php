<?php
function Update_EWD_UFP_Tables() {
	/* Add in the required globals to be able to create the tables */
  	global $wpdb;
   	global $EWD_UFP_Version;
	global $ewd_ufp_submissions_table_name;
	global $ewd_ufp_responses_table_name;
		
	/* Create the submissions data table */  
   	$sql = "CREATE TABLE $ewd_ufp_submissions_table_name (
  		Submission_ID mediumint(9) NOT NULL AUTO_INCREMENT,
		Form_ID mediumint(9) DEFAULT 0 NOT NULL,
		Submitted_Datetime datetime DEFAULT '0000-00-00 00:00:00' NULL,
  		UNIQUE KEY id (Submission_ID)
    	)
		DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
   	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   	dbDelta($sql);
		
	/* Create the responses data table */  
   	$sql = "CREATE TABLE $ewd_ufp_responses_table_name (
  		Response_ID mediumint(9) NOT NULL AUTO_INCREMENT,
		Submission_ID mediumint(9) DEFAULT 0 NOT NULL,
		Form_Element_ID mediumint(9) DEFAULT 0 NOT NULL,
		Submission_Value text DEFAULT '' NOT NULL,
  		UNIQUE KEY id (Response_ID)
    	)
		DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
   	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   	dbDelta($sql);
	
}
?>
