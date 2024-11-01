<?php
add_filter( 'the_content', 'EWD_UFP_Add_Form_To_Page', 3, 1);
function EWD_UFP_Add_Form_To_Page($content) {
	$ID = get_the_id();

	if( is_singular() && is_main_query() ) {
		$content .= EWD_UFP_Contact_Form_HTML($ID);
	}

	return $content;
}

function EWD_UFP_Contact_Form_HTML($ID) {
	global $ewd_ufp_message;

	$post = get_post($ID);

	$ReturnString = "";

	if ($post->post_type != 'ufp_form') {return $ReturnString;}

	$Form_Element_IDs = get_post_meta($post->ID, 'EWD_UFP_Form_Element_IDs', true);
	if (!is_array($Form_Element_IDs)) {$Form_Element_IDs = array();}
	$Form_Settings = get_post_meta($post->ID, 'EWD_UFP_Form_Settings', true);
	if (!is_array($Form_Settings)) {$Form_Settings = array();}
	$Form_Stylings = get_post_meta($post->ID, 'EWD_UFP_Form_Stylings', true);
	if (!is_array($Form_Stylings)) {$Form_Stylings = array();}

	$Form_Layout = $Form_Stylings['Form_Layout'];

	$ReturnString .= EWD_UFP_Add_Form_Styling();
	$ReturnString .= "<div class='ewd-ufp-form " . $Form_Layout . "'>";
	if ($ewd_ufp_message != '') {$ReturnString .= $ewd_ufp_message;}
	$ReturnString .= "<form method='post'>";
	$ReturnString .= wp_nonce_field('EWD_UFP_Submit_Form', 'Submit_Form');
	$ReturnString .= "<input type='hidden' name='Form_ID' value='" . $ID . "' />";
	$Question_Counter = 0;
	$Current_Page_ID = 0;
	$Previous_Page_ID = 0;
	foreach ($Form_Element_IDs as $Form_Element_ID) {
		$Form_Element = get_post($Form_Element_ID);
		$Question_Type = get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Type', true);
		$Possible_Answers = get_post_meta($Form_Element_ID, 'EWD_UFP_Element_Answer_Options', true);
		if (!is_array($Possible_Answers)) {$Possible_Answers = array();}

		$Required = get_post_meta($Form_Element_ID, 'EWD_UFP_Element_Required', true);
		$Conditional_Page_Answer = get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Page_Display_Answer', true);
		$Conditional_Page_Destination = get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Page_Display_Destination', true);
		$Conditional_Question_Question = get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Question_Display_Question', true);
		$Conditional_Question_Logic = get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Question_Display_Logic', true);
		$Conditional_Question_Answer = get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Question_Display_Answer', true);

		$Conditional_Data = '';
		if (get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Page_Display_Enabled', true) == "Yes") {
			$Conditional_Data .= ' data-conditionalpageanswer="' . $Conditional_Page_Answer . '"';
			$Conditional_Data .= ' data-conditionalpagedestination="' . $Conditional_Page_Destination . '"';
		}
		if (get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Question_Display_Enabled', true) == "Yes") {
			$Conditional_Data .= ' data-conditionalquestionquestion="' . $Conditional_Question_Question . '"';
			$Conditional_Data .= ' data-conditionalquestionlogic="' . $Conditional_Question_Logic . '"';
			$Conditional_Data .= ' data-conditionalquestionanswer="' . $Conditional_Question_Answer . '"';
		}

		if ($Question_Type == 'page_break') {
			if ($Current_Page_ID != 0) {
				if ($Previous_Page_ID != 0) {$ReturnString .= "<div class='ewd-ufp-page-navigation ewd-ufp-previous-page' data-pageid='" . $Previous_Page_ID . "' data-questioncounters='" . implode(",", $Page_Counters) . "'>" . __('Previous Page', 'ultimate-forms') . "</div>";}
				$ReturnString .= "<div class='ewd-ufp-page-navigation ewd-ufp-next-page' data-pageid='" . $Form_Element->ID . "'  data-questioncounters='" . implode(",", $Page_Counters) . "'>" . __('Next Page', 'ultimate-forms') . "</div>";
				$ReturnString .= "</div>"; //close the current page
				$Previous_Page_ID = $Current_Page_ID;
			}
			 
			$ReturnString .= "<div class='ewd-ufp-form-page " . ($Current_Page_ID != 0 ? 'ewd-ufp-hidden' : '') . "' data-pageid='" . $Form_Element->ID . "'>";
			$Current_Page_ID = $Form_Element->ID;
			$Page_Counters = array();

			continue;
		}

		if ($Question_Type == 'title') {
			$ReturnString .= EWD_UFP_Add_Question_Styling($Form_Element->ID);
			$ReturnString .= "<h3>" . $Form_Element->post_title . "</h3>";

			continue;
		}

		if ($Question_Type == 'instructions') {
			$ReturnString .= EWD_UFP_Add_Question_Styling($Form_Element->ID);
			$ReturnString .= "<div class='ewd-ufp-instruction-paragraph'>" . $Form_Element->post_content . "</div>";

			continue;
		}

		if ($Question_Type == 'section_break') {
			$ReturnString .= EWD_UFP_Add_Question_Styling($Form_Element->ID);
			$ReturnString .= "<div class='ewd-ufp-section-break'></div>";

			continue;
		}

		if ($Question_Type == 'captcha') {
			$ReturnString .= EWD_UFP_Add_Captcha();

			continue;
		}

		$ReturnString .= "<div id='ewd-ufp-question-" . $Form_Element->ID . "' class='ewd-ufp-question " . $Form_Layout . "' data-row='" . $Question_Counter . "' data-elementid='" . $Form_Element->ID . "' data-questiontype='" . $Question_Type . "' " . $Conditional_Data . " >";
		$ReturnString .= EWD_UFP_Add_Question_Styling($Form_Element->ID);
		$ReturnString .= "<input type='hidden' name='Answer_Number_" . $Form_Element->ID . "' value='" . $Question_Counter . "' />";
		$ReturnString .= "<div class='ewd-ufp-question-header " . $Form_Layout . "' data-row='" . $Question_Counter . "'>";
		$ReturnString .= "<div class='ewd-ufp-question-icon dashicons " . $Form_Layout . " " . get_post_meta($Form_Element->ID, 'EWD_UFP_Element_Icon', true) . "' data-row='" . $Question_Counter . "'></div>";
		$ReturnString .= "<div class='ewd-ufp-question-title " . $Form_Layout . "' data-row='" . $Question_Counter . "'>" . $Form_Element->post_title ."</div>";
		$ReturnString .= "</div>";
		if ($Form_Layout == "Side_By_Side") {$ReturnString .= "<div class='ewd-ufp-input-and-instructions " . $Form_Layout . "'>";}
		$ReturnString .= "<div class='ewd-ufp-answer-options " . $Form_Layout . "' data-row='" . $Question_Counter ."'>";
		if ($Question_Type == 'text' or $Question_Type == 'tel' or $Question_Type == 'email' or $Question_Type == 'url') {$ReturnString .= "<input type='" . $Question_Type . "' class='ewd-ufp-question-input' name='Answer_" . $Question_Counter . "' " . $Required . "/>";}
		elseif ($Question_Type == 'textarea') {$ReturnString .= "<textarea class='ewd-ufp-question-input' name='Answer_" . $Question_Counter . "' " . $Required . "></textarea>";}
		elseif ($Question_Type == 'radio' or $Question_Type == 'checkbox') {
			foreach ($Possible_Answers as $Possible_Answer) {
				$ReturnString .= "<div class='ewd-ufp-answer-option " . $Form_Layout . "'>";
				$ReturnString .= "<input type='" . $Question_Type . "' class='ewd-ufp-question-input' name='Answer_" . $Question_Counter . ($Question_Type == 'checkbox' ? '[]' : '') . "' value='" . $Possible_Answer . "' " . $Required . "/><span>" . $Possible_Answer . "</span>";
				$ReturnString .= "</div>";
			} 
		}
		elseif ($Question_Type == 'select') {
			$ReturnString .= "<select class='ewd-ufp-question-input' name='Answer_" . $Question_Counter . "' " . $Required . ">";
			foreach ($Possible_Answers as $Possible_Answer) {
				$ReturnString .= "<option value='" . $Possible_Answer . "' />" . $Possible_Answer . "</option>";
			}
			$ReturnString .= "</select>";
		}
		$ReturnString .= "</div>";
		$ReturnString .= "<div class='ewd-ufp-question-instructions " . $Form_Layout . "' data-row='" . $Question_Counter . "'>" . $Form_Element->post_content . "</div>";
		if ($Form_Layout == "Side_By_Side") {$ReturnString .= "</div>";}
		$ReturnString .= "</div>";
		$ReturnString .= "<div class='ewd-ufp-clear'></div>";

		$Page_Counters[] = $Question_Counter;	

		$Question_Counter++;
	}
	if ($Previous_Page_ID != 0) {$ReturnString .= "<div class='ewd-ufp-page-navigation ewd-ufp-previous-page' data-pageid='" . $Previous_Page_ID . "' data-questioncounters='" . implode(",", $Page_Counters) . "'>" . __('Previous Page', 'ultimate-forms') . "</div>";}
	$ReturnString .= "<input type='submit' name='EWD_UFP_Submit' value='Submit Form' class='button-primary " . $Form_Layout . "' />";
	$ReturnString .= "</div>"; //close the current page
	$ReturnString .= "</form>";
	$ReturnString .= "</div>";

	return $ReturnString;
}

function EWD_UFP_Add_Question_Styling($Post_ID) {
	$Question_Title_Font = get_post_meta($Post_ID, 'EWD_UFP_Question_Title_Font', true);
	$Question_Title_Font_Size = get_post_meta($Post_ID, 'EWD_UFP_Question_Title_Font_Size', true);
	$Question_Title_Font_Color = get_post_meta($Post_ID, 'EWD_UFP_Question_Title_Font_Color', true);
	$Question_Instructions_Font = get_post_meta($Post_ID, 'EWD_UFP_Question_Instructions_Font', true);
	$Question_Instructions_Font_Size = get_post_meta($Post_ID, 'EWD_UFP_Question_Instructions_Font_Size', true);
	$Question_Instructions_Font_Color = get_post_meta($Post_ID, 'EWD_UFP_Question_Instructions_Font_Color', true);
	$Question_Answers_Font = get_post_meta($Post_ID, 'EWD_UFP_Question_Answers_Font', true);
	$Question_Answers_Font_Size = get_post_meta($Post_ID, 'EWD_UFP_Question_Answers_Font_Size', true);
	$Question_Answers_Font_Color = get_post_meta($Post_ID, 'EWD_UFP_Question_Answers_Font_Color', true);
	$Question_Spacing_Padding_Above = get_post_meta($Post_ID, 'EWD_UFP_Question_Spacing_Padding_Above', true);
	$Question_Spacing_Padding_Below = get_post_meta($Post_ID, 'EWD_UFP_Question_Spacing_Padding_Below', true);
	$Question_Spacing_Padding_Before = get_post_meta($Post_ID, 'EWD_UFP_Question_Spacing_Padding_Before', true);
	$Question_Spacing_Padding_After = get_post_meta($Post_ID, 'EWD_UFP_Question_Spacing_Padding_After', true);
	$Question_Spacing_Margin_Between_Elements = get_post_meta($Post_ID, 'EWD_UFP_Question_Spacing_Margin_Between_Elements', true);
	$Question_Sizing_Question_Area_Width = get_post_meta($Post_ID, 'EWD_UFP_Question_Sizing_Question_Area_Width', true);
	$Question_Sizing_Question_Area_Height = get_post_meta($Post_ID, 'EWD_UFP_Question_Sizing_Question_Area_Height', true);
	$Question_Sizing_Input_Width = get_post_meta($Post_ID, 'EWD_UFP_Question_Sizing_Input_Width', true);
	$Question_Sizing_Input_Height = get_post_meta($Post_ID, 'EWD_UFP_Question_Sizing_Input_Height', true);
	$Question_Background_Color = get_post_meta($Post_ID, 'EWD_UFP_Question_Background_Color', true);
	$Question_Background_Input_Color = get_post_meta($Post_ID, 'EWD_UFP_Question_Background_Input_Color', true);

	$Style_String = '<style>';
	if ($Question_Title_Font != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-question-title {font-family:' . $Question_Title_Font . ';}';}
	if ($Question_Title_Font_Size != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-question-title {font-size:' . $Question_Title_Font_Size . ';}';}
	if ($Question_Title_Font_Color != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-question-title {color:' . $Question_Title_Font_Color . ';}';}
	if ($Question_Instructions_Font != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-question-instructions {font-family:' . $Question_Instructions_Font . ';}';}
	if ($Question_Instructions_Font_Size != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-question-instructions {font-size:' . $Question_Instructions_Font_Size . ';}';}
	if ($Question_Instructions_Font_Color != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-question-instructions {color:' . $Question_Instructions_Font_Color . ';}';}
	if ($Question_Answers_Font != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-answer-options {font-family:' . $Question_Answers_Font . ';}';}
	if ($Question_Answers_Font_Size != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-answer-options {font-size:' . $Question_Answers_Font_Size . ';}';}
	if ($Question_Answers_Font_Color != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-answer-options {color:' . $Question_Answers_Font_Color . ';}';}
	if ($Question_Spacing_Padding_Above != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' {padding-top:' . $Question_Spacing_Padding_Above . ';}';}
	if ($Question_Spacing_Padding_Below != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' {padding-bottom:' . $Question_Spacing_Padding_Below . ';}';}
	if ($Question_Spacing_Padding_Before != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' {padding-left:' . $Question_Spacing_Padding_Before . ';}';}
	if ($Question_Spacing_Padding_After != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' {padding-right:' . $Question_Spacing_Padding_After . ';}';}
	if ($Question_Spacing_Margin_Between_Elements != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-answer-options {margin:' . $Question_Spacing_Margin_Between_Elements . ' 0;}';}
	if ($Question_Sizing_Question_Area_Width != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' {width:' . $Question_Sizing_Question_Area_Width . ';}';}
	if ($Question_Sizing_Question_Area_Height != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' {height:' . $Question_Sizing_Question_Area_Height . ';}';}
	if ($Question_Sizing_Input_Width != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-answer-options input[type="text"], #ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-answer-options textarea {width:' . $Question_Sizing_Input_Width . ';}';}
	if ($Question_Sizing_Input_Height != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-answer-options input[type="text"], #ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-answer-options textarea {height:' . $Question_Sizing_Input_Height . ';}';}
	if ($Question_Background_Color != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' {background-color:' . $Question_Background_Color . ';}';}
	if ($Question_Background_Input_Color != "") {$Style_String .= '#ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-answer-options input[type="text"], #ewd-ufp-question-' . $Post_ID . ' .ewd-ufp-answer-options textarea {background-color:' . $Question_Background_Input_Color . ';}';}
	$Style_String .= '</style>';

	return $Style_String;
}

function EWD_UFP_Add_Form_Styling() {
	$ID = get_the_id();
	$post = get_post($ID);

	$Form_Stylings = get_post_meta($post->ID, 'EWD_UFP_Form_Stylings', true);
	if (!is_array($Form_Stylings)) {$Form_Stylings = array();}

	$Form_Layout = $Form_Stylings['Form_Layout'];
	$Form_Text_Color = $Form_Stylings['Form_Text_Color'];
	$Form_Background_Color = $Form_Stylings['Form_Background_Color'];
	$Form_Padding_Above = $Form_Stylings['Form_Padding_Above'];
	$Form_Padding_Below = $Form_Stylings['Form_Padding_Below'];
	$Form_Padding_Before = $Form_Stylings['Form_Padding_Before'];
	$Form_Padding_After = $Form_Stylings['Form_Padding_After'];


	$Form_Styles_String = '<style>';
	if ($Form_Text_Color != "") {$Form_Styles_String .= '.ewd-ufp-form {color:' . $Form_Text_Color . ';}';}
	if ($Form_Background_Color != "") {$Form_Styles_String .= '.ewd-ufp-form {background-color:' . $Form_Background_Color . ';}';}
	if ($Form_Padding_Above != "") {$Form_Styles_String .= '.ewd-ufp-form {padding-top:' . $Form_Padding_Above . ';}';}
	if ($Form_Padding_Below != "") {$Form_Styles_String .= '.ewd-ufp-form {padding-bottom:' . $Form_Padding_Below . ';}';}
	if ($Form_Padding_Before != "") {$Form_Styles_String .= '.ewd-ufp-form {padding-left:' . $Form_Padding_Before . ';}';}
	if ($Form_Padding_After != "") {$Form_Styles_String .= '.ewd-ufp-form {padding-right:' . $Form_Padding_After . ';}';}
	$Form_Styles_String .= '</style>';

	return $Form_Styles_String;
}
