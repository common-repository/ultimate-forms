<?php
add_action('init', 'EWD_UFP_Handle_Form_Submission', 10);
function EWD_UFP_Handle_Form_Submission() {
	global $ewd_ufp_message;

	if (!isset($_POST['Form_ID'])) {return;}
	
	if ( ! isset( $_POST['Submit_Form'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['Submit_Form'], 'EWD_UFP_Submit_Form' ) ) {
		return;
	}

	if (isset($_POST['ewd_ufp_captcha'])) {
		$Captcha_Validation = EWD_UFP_Validate_Captcha();
		if ($Captcha_Validation != "Yes") {
			$ewd_ufp_message = "<div class='ewd-ufp-message ewd-ufp-failure'>" . __("The number entered did not match the number in the image.", 'ultimate-forms') . "</div>";
			return;
		}
	}

	$Form_ID = (is_numeric($_POST['Form_ID']) ? $_POST['Form_ID'] : 0);

	$Form_Element_IDs = get_post_meta($Form_ID, 'EWD_UFP_Form_Element_IDs', true);
	if (!is_array($Form_Element_IDs)) {$Form_Element_IDs = array();}
	$Form_Settings = get_post_meta($Form_ID, 'EWD_UFP_Form_Settings', true);
	if (!is_array($Form_Settings)) {$Form_Settings = array();}

	$Submitted_Successfully_Label = $Form_Settings['Submitted_Successfully_Label'] != '' ? $Form_Settings['Submitted_Successfully_Label'] : get_option("EWD_UFP_Submitted_Successfully_Label");
	if ($Submitted_Successfully_Label == '') {$Submitted_Successfully_Label = __("Form has been successfully submitted.", 'ultimate-forms');}
	$General_Failure_Label = $Form_Settings['General_Failure_Label'] != '' ? $Form_Settings['General_Failure_Label'] : get_option("EWD_UFP_General_Failure_Label");
	if ($General_Failure_Label == '') {$General_Failure_Label = __("Form has failed to submit.", 'ultimate-forms');}

	$Email_Messages_Array = $Form_Settings['Email_Messages_Array'];
	if (!is_array($Email_Messages_Array)) {$Email_Messages_Array = array();}
	
	foreach ($Form_Element_IDs as $Form_Element_ID) {
		$Question_Type = get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Type', true);
		$Possible_Answers = get_post_meta($Form_Element_ID, 'EWD_UFP_Element_Answer_Options', true);
		if (!is_array($Possible_Answers)) {$Possible_Answers = array();}

		$Answer_Number = $_POST['Answer_Number_' . $Form_Element_ID];
		$Answer = sanitize_text_field($_POST['Answer_' . $Answer_Number]);
		if (!is_array($Answer) and !empty($Possible_Answers) and !in_array($Answer, $Possible_Answers)) {$Answer = '';}

		$Answers[$Form_Element_ID] = $Answer;
	}

	$ewd_ufp_message = "<div class='ewd-ufp-message ewd-ufp-success'>" . $Submitted_Successfully_Label . "</div>";

	foreach ($Email_Messages_Array as $Email_Message_Item) {
		if ($Email_Message_Item['Send'] == "Yes") {EWD_UFP_Send_Form_Email($Email_Message_Item, $Answers);}
	}
	
	if ($Form_Settings['Save_Submissions'] == "Yes") {EWD_UFP_Save_To_Database($Form_ID, $Answers);}
}

function EWD_UFP_Send_Form_Email($Email_Message_Item, $Answers) {
	$Admin_Email = get_option('admin_email');

	$Send_To = explode(",", $Email_Message_Item['Send_To']);
	$Message = EWD_UFP_Substitute_Email_Text(EWD_UFP_Return_Email_Template($Email_Message_Item), $Answers);
	$Subject = EWD_UFP_Substitute_Email_Text($Email_Message_Item['Subject'], $Answers, true);

	$Headers = array('From: ' . $Admin_Email, 'Reply-To: ' . $Admin_Email, 'X-Mailer: PHP/' . phpversion(), 'Content-Type: text/html; charset=UTF-8');
	
	foreach ($Send_To as $Email_Address) {
		wp_mail($Email_Address, $Subject, $Message, $Headers); 
	}

	return;
}

function EWD_UFP_Save_To_Database($Form_ID, $Answers) {
	global $wpdb;
    global $ewd_ufp_submissions_table_name;
	global $ewd_ufp_responses_table_name;

	$wpdb->query($wpdb->prepare("INSERT INTO $ewd_ufp_submissions_table_name (Form_ID, Submitted_Datetime) VALUES (%d, %s)", $Form_ID, date("Y-m-d H:i:s")));
	$Submission_ID = $wpdb->insert_id;

	foreach ($Answers as $Form_Element_ID => $Answer) {
		if (is_array($Answer)) {$Answer = serialize($Answer);}
		$wpdb->query($wpdb->prepare("INSERT INTO $ewd_ufp_responses_table_name (Submission_ID, Form_Element_ID, Submission_Value) VALUES (%d, %d, %s)", $Submission_ID, $Form_Element_ID, sanitize_text_field($Answer)));
	}
}

function EWD_UFP_Substitute_Email_Text($Text, $Answers, $Subject = false) {
	global $wpdb;

	$Search_Terms = array();
	$Replace_Terms = array();
	foreach ($Answers as $Form_Element_ID => $Answer) {
		$Form_Element_Slug = get_post_meta($Form_Element_ID, 'EWD_UFP_Element_Slug', true);
		$Search_Terms[] = "[" . $Form_Element_Slug . "]";
		$Replace_Terms[] = print_r($Answer, true);
	}

	$Email_Text = str_replace($Search_Terms, $Replace_Terms, $Text);

	return EWD_UFP_Replace_Email_Content($Email_Text, $Subject);
}

function EWD_UFP_Return_Email_Template($Email_Message_Item) {
  	$Message_Title = $Email_Message_Item['Subject'];
  	$Message_Content = EWD_UFP_Replace_Email_Content(stripslashes($Email_Message_Item['Message']));

	$Email_Reminder_Background_Color = get_option("EWD_UFP_Email_Reminder_Background_Color");
	$Email_Reminder_Inner_Color = get_option("EWD_UFP_Email_Reminder_Inner_Color");
	$Email_Reminder_Text_Color = get_option("EWD_UFP_Email_Reminder_Text_Color");
	$Email_Reminder_Button_Background_Color = get_option("EWD_UFP_Email_Reminder_Button_Background_Color");
	$Email_Reminder_Button_Text_Color = get_option("EWD_UFP_Email_Reminder_Button_Text_Color");
	$Email_Reminder_Button_Background_Hover_Color = get_option("EWD_UFP_Email_Reminder_Button_Background_Hover_Color");
	$Email_Reminder_Button_Text_Hover_Color = get_option("EWD_UFP_Email_Reminder_Button_Text_Hover_Color");

  $Message =   <<< EOT
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
  <head>
  <meta name="viewport" content="width=device-width" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>$Message_Title</title>


  <style type="text/css">

	.body-wrap {
		background-color: {$Email_Reminder_Background_Color} !important;
	}
	.btn-primary {
		background-color: {$Email_Reminder_Button_Background_Color} !important;
		border-color: $Email_Reminder_Button_Background_Color !important;
		color: {$Email_Reminder_Button_Text_Color} !important;
	}
	.btn-primary:hover {
		background-color: {$Email_Reminder_Button_Background_Hover_Color} !important;
		border-color: $Email_Reminder_Button_Background_Hover_Color !important;
		color: {$Email_Reminder_Button_Text_Hover_Color} !important;
	}
	.main {
		background: $Email_Reminder_Inner_Color !important;
		color: $Email_Reminder_Text_Color;
	}

  img {
  max-width: 100%;
  }
  body {
  -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
  }
  body {
  background-color: #f6f6f6;
  }
  @media only screen and (max-width: 640px) {
    body {
      padding: 0 !important;
    }
    h1 {
      font-weight: 800 !important; margin: 20px 0 5px !important;
    }
    h2 {
      font-weight: 800 !important; margin: 20px 0 5px !important;
    }
    h3 {
      font-weight: 800 !important; margin: 20px 0 5px !important;
    }
    h4 {
      font-weight: 800 !important; margin: 20px 0 5px !important;
    }
    h1 {
      font-size: 22px !important;
    }
    h2 {
      font-size: 18px !important;
    }
    h3 {
      font-size: 16px !important;
    }
    .container {
      padding: 0 !important; width: 100% !important;
    }
    .content {
      padding: 0 !important;
    }
    .content-wrap {
      padding: 10px !important;
    }
    .invoice {
      width: 100% !important;
    }
  }
  </style>
  </head>

  <body itemscope itemtype="http://schema.org/EmailMessage" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">

  <table class="body-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6"><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
  		<td class="container" width="600" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
  			<div class="content" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
  				<table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;" bgcolor="#fff"><tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
  					<meta itemprop="name" content="Please Review" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" /><table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
              $Message_Content
        </div>
  		</td>
  		<td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
  	</tr></table></body>
  </html>

EOT;

  return $Message;
}

function EWD_UFP_Replace_Email_Content($Message_Start, $Subject = false) {
  if (strpos($Message_Start, '[footer]') === false and !$Subject) {$Message_Start .= '</table></td></tr></table>';}

  $Replace = array('[section]', '[/section]', '[footer]', '[/footer]', '[/button]');
  $ReplaceWith = array(
    '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">',
    '</td></tr>',
    '</table></td></tr></table><div class="footer" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;"><table width="100%" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="aligncenter content-block" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;" align="center" valign="top">',
    '</td></tr></table></div>',
    '</a></td></tr>'
  );
  $Message = str_replace($Replace, $ReplaceWith, $Message_Start);
  $Final_Message = EWD_UFP_Replace_Email_Links($Message);

  return $Final_Message;
}


function EWD_UFP_Replace_Email_Links($Message) {
	$Pattern = "/\[button link=\'(.*?)\'\]/";

	preg_match_all($Pattern, $Message, $Matches);

	$Replace = '<tr style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"><td class="content-block" itemprop="handler" itemscope itemtype="http://schema.org/HttpActionHandler" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top"><a href="INSERTED_LINK" class="btn-primary" itemprop="url" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #348eda; margin: 0; border-color: #348eda; border-style: solid; border-width: 10px 20px;">';
	$Result = preg_replace($Pattern, $Replace, $Message);

	if (is_array($Matches[1])) {
		foreach ($Matches[1] as $Link) {
			$Pos = strpos($Result, "INSERTED_LINK");
			if ($Pos !== false) {
			    $NewString = substr_replace($Result, $Link, $Pos, 13);
			    $Result = $NewString;
			}
		}
	}

	return $Result;
}
?>