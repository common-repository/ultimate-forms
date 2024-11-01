<?php

function EWD_UFP_Add_Captcha() {
	$Code = rand(1000,9999);
	$ModifiedCode = EWD_UFP_Encrypt_Captcha_Code($Code);

	$ReturnString = "";
	
	$ReturnString .= "<div class='ewd-ufp-captcha-div'><label for='captcha_image'></label>";
	$ReturnString .= "<img src=" . EWD_UFP_CD_PLUGIN_URL . "Functions/EWD_UFP_Create_Captcha_Image.php?Code=" . $ModifiedCode . " />";
	$ReturnString .= "<input type='hidden' name='ewd_ufp_modified_captcha' value='" . $ModifiedCode . "' />";
	$ReturnString .= "</div>";
	$ReturnString .= "<div class='ewd-ufp-captcha-response'><label for='captcha_text'>" . __("Image Number: ", 'ultimate-forms') . "</label>";
	$ReturnString .= "<input type='text' name='ewd_ufp_captcha' value='' />";
	$ReturnString .= "</div>";

	return $ReturnString;
}

function EWD_UFP_Validate_Captcha() {
	$ModifiedCode = $_POST['ewd_ufp_modified_captcha'];
	$UserCode = $_POST['ewd_ufp_captcha'];

	$Code = EWD_UFP_Decrypt_Catpcha_Code($ModifiedCode);

	if ($Code == $UserCode) {$Validate_Captcha = "Yes";}
	else {$Validate_Captcha = "No";}

	return $Validate_Captcha;
}

function EWD_UFP_Encrypt_Captcha_Code($Code) {
	$ModifiedCode = ($Code + 5) * 3;

	return $ModifiedCode;
}

function EWD_UFP_Decrypt_Catpcha_Code($ModifiedCode) {
	$Code = ($ModifiedCode / 3) - 5;

	return $Code;
}
?>
