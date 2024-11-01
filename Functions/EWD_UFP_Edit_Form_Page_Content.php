<?php
add_filter("get_sample_permalink_html", "EWD_UFP_Add_Review_Shortcode", 10, 5);
function EWD_UFP_Add_Review_Shortcode($HTML, $post_id, $title, $slug, $post) {
	/*if ($post->post_type == "ufp_form") {
		$HTML .= "<div class='ewd-urp-shortcode-help'>";
		$HTML .= __("Use the following shortcode to add this review to a page:", 'ultimate-forms') . "<br>";
		$HTML .= "[select-review review_id='" . $post_id . "']";
		$HTML .= "</div>";
	}*/

	return $HTML;
}

add_filter( 'get_sample_permalink_html', 'EWD_UFP_Add_Content_Editor_Toggle', 11, 2 );
function EWD_UFP_Add_Content_Editor_Toggle( $content, $post_id ) {
  $post = get_post($post_id);

  if ($post->post_type == 'ufp_form') {$content = $content . "<div id='add-content-before-form'>" . __('Add Content Before Form', 'ultimate-forms') . "</div>";}
  return $content;
}

add_action( 'add_meta_boxes', 'EWD_UFP_Add_Meta_Boxes' );
function EWD_UFP_Add_Meta_Boxes () {
	add_meta_box("form-meta", __("Build Form", 'ultimate-forms'), "EWD_UFP_Meta_Box", "ufp_form", "normal", "high");
}

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function EWD_UFP_Meta_Box( $post ) {
	global $wpdb;
	global $ewd_ufp_submissions_table_name;
	global $ewd_ufp_responses_table_name;

	$Form_Element_IDs = get_post_meta($post->ID, 'EWD_UFP_Form_Element_IDs', true);
	if (!is_array($Form_Element_IDs)) {$Form_Element_IDs = array();}
	$Form_Settings = get_post_meta($post->ID, 'EWD_UFP_Form_Settings', true);
	if (!is_array($Form_Settings)) {$Form_Settings = array();}
	$Form_Stylings = get_post_meta($post->ID, 'EWD_UFP_Form_Stylings', true);
	if (!is_array($Form_Stylings)) {$Form_Stylings = array();}

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'EWD_UFP_Save_Meta_Box_Data', 'EWD_UFP_meta_box_nonce' ); ?>

	<div class='ewd-ufp-build-form-headers'>
		<div class='ewd-ufp-build-form-header' data-box='form-elements'><?php _e("Form Elements", 'ultimate-forms'); ?></div>
		<div class='ewd-ufp-build-form-header-divider'></div>
		<div class='ewd-ufp-build-form-header' data-box='form-submission'><?php _e("Submissions", 'ultimate-forms'); ?></div>
		<div class='ewd-ufp-build-form-header-divider'></div>
		<div class='ewd-ufp-build-form-header' data-box='form-settings'><?php _e("Settings", 'ultimate-forms'); ?></div>
		<div class='ewd-ufp-build-form-header-divider'></div>
		<div class='ewd-ufp-build-form-header' data-box='form-styling'><?php _e("Styling", 'ultimate-forms'); ?></div>
		<div class='ewd-ufp-clear'></div>
	</div>

	<div id='ewd-ufp-background-overlay'></div>

	<div class='ewd-ufp-build-form-body' data-box='form-elements'>
		<?php if (sizeOf($Form_Element_IDs) == 0) { ?>
			<div class='ewd-ufp-form-templates'>
				<p><?php _e("Start building with a form template:", 'ultimate-forms'); ?></p>
				<div class='ewd-ufp-template' data-template='contact_us'>
					<img src='<?php echo EWD_UFP_CD_PLUGIN_URL . "/images/templates/Contact_Us.png"; ?>' />
					<div class='ewd-ufp-template-title'><?php _e("Contact Us", 'ultimate-forms'); ?></div>
				</div>
				<div class='ewd-ufp-template' data-template='email_sign_up'>
					<img src='<?php echo EWD_UFP_CD_PLUGIN_URL . "/images/templates/Email_Sign_Up.png"; ?>' />
					<div class='ewd-ufp-template-title'><?php _e("Email Sign Up", 'ultimate-forms'); ?></div>
				</div>
				<div class='ewd-ufp-template' data-template='service_request'>
					<img src='<?php echo EWD_UFP_CD_PLUGIN_URL . "/images/templates/Service_Request.png"; ?>' />
					<div class='ewd-ufp-template-title'><?php _e("Service Request", 'ultimate-forms'); ?></div>
				</div>
				<div class='ewd-ufp-template' data-template='contact_info'>
					<img src='<?php echo EWD_UFP_CD_PLUGIN_URL . "/images/templates/Contact_Info.png"; ?>' />
					<div class='ewd-ufp-template-title'><?php _e("Contact Information", 'ultimate-forms'); ?></div>
				</div>
				<div class='ewd-ufp-template' data-template='reservation'>
					<img src='<?php echo EWD_UFP_CD_PLUGIN_URL . "/images/templates/Reservation.png"; ?>' />
					<div class='ewd-ufp-template-title'><?php _e("Reservation", 'ultimate-forms'); ?></div>
				</div>
				<div class='ewd-ufp-template' data-template='product_inquiry'>
					<img src='<?php echo EWD_UFP_CD_PLUGIN_URL . "/images/templates/Product_Inquiry.png"; ?>' />
					<div class='ewd-ufp-template-title'><?php _e("Product Inquiry", 'ultimate-forms'); ?></div>
				</div>
				<hr>
				<p><?php _e("Or start building a form manually below:", 'ultimate-forms'); ?></p>
			</div>
		<?php } ?>
		<h3><?php _e("Form Elements", 'ultimate-forms'); ?></h3>
		<div class='ewd-ufp-build-form-explanation'><?php _e("Use the area below to add, delete and style questions on your form.", 'ultimate-forms'); ?></div>
		<?php //echo "<pre>" . print_r(get_option("EWD_UFP_Debugging"), true) . "</pre>"; ?>
		<table id='ewd-ufp-form-elements-table'><tbody>
			<?php 
			$Question_Counter = 0;
			$Page_Counter = 1;
			if (sizeOf($Form_Element_IDs) == 0) {
				echo '<tr class="ewd-ufp-page-container" data-questioncount="' . $Question_Counter . '">';
				echo "<td><span class='ewd-ufp-page-number'>" . __("Page ", 'ultimate-forms') . $Page_Counter . "</span>";
				echo "<input type='hidden' name='Element_ID_" . $Question_Counter . "' value='' />";
				echo "<input type='hidden' name='Element_Type_" . $Question_Counter . "' value='page_break' />";
				echo "<input type='hidden' name='Element_Order_" . $Question_Counter . "' value='" . $Question_Counter . "'/>";
				echo "<table class='ewd-ufp-page-table'><tbody><tr><td></td></tr></tbody></table></td></tr>";

				$Question_Counter++;
				$Page_Counter++;
			}
			foreach ($Form_Element_IDs as $Form_Element_ID) {
				$Form_Element = get_post($Form_Element_ID);
				$Question_Type = get_post_meta($Form_Element->ID, 'EWD_UFP_Question_Type', true);
				if ($Question_Type == "page_break") {$Last_Element_ID = get_post_meta($Form_Element->ID, 'EWD_UFP_Last_Element_ID', true);}

				EWD_UFP_Add_Form_Element($Question_Type, $Question_Counter, $Page_Counter, $Form_Element, 'No');

				if ($Form_Element->ID == $Last_Element_ID) {echo '</tbody></table></td></tr>';}

				if ($Question_Type == "page_break") {$Page_Counter++;}
				$Question_Counter++;
			} ?>
			<tr class='ewd-ufp-add-form-element' data-nextcount='<?php echo $Question_Counter; ?>'><td>
				<div class='ewd-ufp-add-question-header'><?php _e("Add Form Element", 'ultimate-forms'); ?></div>
				<select name='Question_Types'>
					<optgroup label='Input Elements'>
						<option value='text'><?php _e("Text", 'ultimate-forms'); ?></option>
						<option value='textarea'><?php _e("Paragraph Text", 'ultimate-forms'); ?></option>
						<option value='radio'><?php _e("Multiple Choice", 'ultimate-forms'); ?></option>
						<option value='checkbox'><?php _e("Checkboxes", 'ultimate-forms'); ?></option>
						<option value='tel'><?php _e("Phone Number", 'ultimate-forms'); ?></option>
						<option value='email'><?php _e("Email", 'ultimate-forms'); ?></option>
						<option value='url'><?php _e("URL", 'ultimate-forms'); ?></option>
						<option value='number'><?php _e("Number", 'ultimate-forms'); ?></option>
						<option value='captcha'><?php _e("Captcha", 'ultimate-forms'); ?></option>
					</optgroup>
					<optgroup label='Structural Elements'>
						<option value='page_break'><?php _e("Page Break", 'ultimate-forms'); ?></option>
						<option value='section_break'><?php _e("Section Break", 'ultimate-forms'); ?></option>
						<option value='title'><?php _e("Title", 'ultimate-forms'); ?></option>
						<option value='instructions'><?php _e("Instructions", 'ultimate-forms'); ?></option>
					<optgroup>
				</select>
				<div class='ewd-ufp-clear'></div>
				<div class='ewd-ufp-add-question-submit'><?php _e("Add to Form", 'ultimate-forms'); ?></div>
			</td></tr> 
		</tbody></table>
	</div>

	<div class='ewd-ufp-build-form-body ewd-ufp-hidden' data-box='form-submission'>
		<h3><?php _e("Form Submissions", 'ultimate-forms'); ?></h3>
		<div class='ewd-ufp-build-form-explanation'><?php _e("Use the area below to specify what happens when your form is submitted.", 'ultimate-forms'); ?></div>
		<?php 
		$Email_Messages_Array = $Form_Settings['Email_Messages_Array'];
		if (!is_array($Email_Messages_Array)) {$Email_Messages_Array = array();}
		?>
		<h3><?php _e('Email Messages', 'ultimate-forms'); ?></h3>
		<table>
			<tr>
				<td>
					<table id='ewd-ufp-email-messages-table'>
						<tr>
							<th></th>
							<th>Send Message?</th>
							<th>Email Address(es)</th>
							<th>Message Subject</th>
							<th>Message</th>
						</tr>
						<?php
							$Counter = 0;
							foreach ($Email_Messages_Array as $Email_Message_Item) {
								echo "<tr id='ewd-ufp-email-message-" . $Counter . "'>";
									echo "<td><input type='hidden' name='Form_Email_" . $Counter . "' value='Set' /><a class='ewd-ufp-delete-message' data-messagecounter='" . $Counter . "'>Delete</a></td>";
									echo "<td><input class='ewd-ufp-array-checkbox-input' type='checkbox' name='Form_Email_Send_" . $Counter . "' value='Yes' " . ($Email_Message_Item['Send'] == "Yes" ? 'checked' : '') . "/></td>";
									echo "<td><input class='ewd-ufp-array-checkbox-input' type='test' name='Form_Email_Send_To_" . $Counter . "' value='" . $Email_Message_Item['Send_To'] . "'/></td>";
									echo "<td><input class='ewd-ufp-array-text-input' type='text' name='Form_Email_Subject_" . $Counter . "' value='" . $Email_Message_Item['Subject'] . "'/></td>";
									echo "<td><textarea class='ewd-ufp-array-textarea' name='Form_Email_Message_" . $Counter . "' rows='5'>" . stripslashes($Email_Message_Item['Message']) . "</textarea></td>";
								echo "</tr>";
								$Counter++;
							}
							echo "<tr><td colspan='5'><a class='ewd-ufp-add-email' data-nextcounter='" . $Counter . "'>" . __('Add', 'ultimate-forms') . "</a></td></tr>";
						?>
					</table>
					<ul>
						<li>Use the table above to build emails that are sent automatically on submission.</li>
						<li>You can use [section]...[/section] and [footer]...[/footer] to split up the content of your email. You can also include a link button, like so: [button link='LINK_URL_GOES_HERE']BUTTON_TEXT[/button]</li>
						<li>You can also insert any of the values for the form elements you've created by putting in [form-element-slug] (the form elements's slug surrounded by square brackets, found under "Advanced" in the form elements area).</li>
					</ul>
					</fieldset>
				</td>
			</tr>
		</table>
		<hr />
		<h3><?php _e("Save Submissions", 'ultimate-forms'); ?></h3>
		<?php
			$Submission_Count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(Submission_ID) FROM $ewd_ufp_submissions_table_name WHERE Form_ID=%d", $post->ID));
			if ($Submission_Count > 0) { ?>
				<div class='ewd-ufp-submission-count'>
					<?php echo __("Total Submissions: ", 'ultimate-forms') . $Submission_Count; ?>
				</div>
			<?php }
		?>
		<div class='ewd-ufp-form-submission-option'>
			<div class='ewd-ufp-form-submission-select-options'>
				<input type='radio' name='ewd_ufp_save_submissions' value='Yes' <?php echo ($Form_Settings['Save_Submissions'] == "Yes" ? 'checked' : '');  ?>/><span><?php _e("Yes", 'ultimate-forms'); ?></span>
				<div class="ewd-ufp-clear"></div>
				<input type='radio' name='ewd_ufp_save_submissions' value='No' <?php echo ($Form_Settings['Save_Submissions'] != "Yes" ? 'checked' : '');  ?>/><span><?php _e("No", 'ultimate-forms'); ?></span>
			</div>
			<span><?php _e("Should submitted forms be saved to your database, so that responses can be aggregated, summarized and exported later?", 'ultimate-forms'); ?>
		</div>
		<div class='ewd-ufp-clear'></div>
		<?php if ($Submission_Count > 0) { ?>
			<?php 
				/*$user = get_current_user_id();
				$screen = get_current_screen();
				$screen_option = $screen->get_option('per_page', 'option');
				$per_page = get_user_meta($user, $screen_option, true);
			
				if (empty($per_page) or is_array($per_page) or $per_page < 1 ) {
					$per_page = $screen->get_option('per_page', 'default');
				}*/

				$per_page = 20;
			?>
			<div class='ewd-ufp-form-submissions-actions'>
				<div class='ewd-ufp-form-submission-option'>
					<button class='ewd-ufp-action-button ewd-ufp-download-submissions button button-primary' data-formid='<?php echo $post->ID; ?>'><?php _e("Download Submitted Answers", 'ultimate-forms'); ?></button>
				</div>
				<?php if ($Submission_Count > $per_page) { ?>
					<div class='ewd-ufp-form-submission-table-pagination ewd-ufp-pagination-top'>
						<div class='ewd-ufp-pagination-button ewd-ufp-pagination-first-page'>«</div>
						<div class='ewd-ufp-pagination-button ewd-ufp-previous-page'>‹</div>
						<div class='ewd-ufp-pagination-page-count'><?php echo __("Page 1 of ", 'ultimate-forms') . ceil($Submission_Count / $per_page); ?></div>
						<div class='ewd-ufp-pagination-button ewd-ufp-next-page'>›</div>
						<div class='ewd-ufp-pagination-button ewd-ufp-pagination-max-page'>»</div>
					</div>
				<?php } ?>
				<div class='ewd-ufp-clear'></div>
				<div class='ewd-ufp-submissions-table-div'>
					<table class='ewd-ufp-submissions-table' data-submissioncounter='0' data-formid='<?php echo $post->ID; ?>' data-perpage='<?php echo $per_page; ?>' data-maxpage='<?php echo ceil($Submission_Count / $per_page); ?>'>
						<thead>
							<tr>
								<?php 
									$Column_Element_IDs = array();
									foreach ($Form_Element_IDs as $Form_Element_ID) {
										$Form_Element = get_post($Form_Element_ID);
										$Question_Type = get_post_meta($Form_Element->ID, 'EWD_UFP_Question_Type', true);
										$Skip_Types = array('page_break', 'section_break', 'captcha', 'title', 'instructions');
										if (!in_array($Question_Type, $Skip_Types)) {
											echo "<th>" . $Form_Element->post_title . "</th>";
											$Column_Element_IDs[] = $Form_Element->ID;
										}
									}
								?>
							</tr>
						</thead>
						<tbody data-columnelementids='<?php echo serialize($Column_Element_IDs); ?>'>
						</tbody>
					</table>
				</div>
				<div class='ewd-ufp-form-submission-option'>
					<button class='ewd-ufp-action-button ewd-ufp-clear-submissions button button-primary' data-formid='<?php echo $post->ID; ?>'><?php _e("Delete Submitted Answers", 'ultimate-forms'); ?></button>
				</div>
				<?php if ($Submission_Count > $per_page) { ?>
					<div class='ewd-ufp-form-submission-table-pagination ewd-ufp-pagination-bottom'>
						<div class='ewd-ufp-pagination-button ewd-ufp-pagination-first-page'>«</div>
						<div class='ewd-ufp-pagination-button ewd-ufp-previous-page'>‹</div>
						<div class='ewd-ufp-pagination-page-count'><?php echo __("Page 1 of ", 'ultimate-forms') . ceil($Submission_Count / $per_page); ?></div>
						<div class='ewd-ufp-pagination-button ewd-ufp-next-page'>›</div>
						<div class='ewd-ufp-pagination-button ewd-ufp-pagination-max-page'>»</div>
					</div>
				<?php } ?>
				<div class='ewd-ufp-clear'></div>
			</div>
		<?php } ?>
	</div>

	<div class='ewd-ufp-build-form-body ewd-ufp-hidden' data-box='form-settings'>
		<h3><?php _e("Form Settings", 'ultimate-forms'); ?></h3>
		<div class='ewd-ufp-build-form-explanation'><?php _e("Use the area below to specify specific form settings.", 'ultimate-forms'); ?></div>
		<h3>Success/Failure Messages</h3>
		<div id='labelling-view-options' class="ufp-options-div ufp-options-flex">
			<div class='ufp-option ufp-label-option'>
				<?php _e("Form submitted successfully", 'ufp')?>
				<fieldset>
					<input type='text' name='submitted_successfully_label' value='<?php echo $Form_Settings['Submitted_Successfully_Label']; ?>' />
				</fieldset>
			</div>
			<div class='ufp-option ufp-label-option'>
				<?php _e("There was an error submitting the form", 'ufp')?>
				<fieldset>
					<input type='text' name='general_failure_label' value='<?php echo $Form_Settings['General_Failure_Label']; ?>' />
				</fieldset>
			</div>
			<!--<div class='ufp-option ufp-label-option'>
				<?php _e("Submission did not send successfully", 'ufp')?>
				<fieldset>
					<input type='text' name='email_failure_label' value='<?php echo $Form_Settings['Email_Failure_Label']; ?>' />
				</fieldset>
			</div>
			<div class='ufp-option ufp-label-option'>
				<?php _e("Submission did not save successfully", 'ufp')?>
				<fieldset>
					<input type='text' name='save_failure_label' value='<?php echo $Form_Settings['Save_Failure_Label']; ?>' />
				</fieldset>
			</div>-->
		</div>
	</div>

	<div class='ewd-ufp-build-form-body ewd-ufp-hidden' data-box='form-styling'>
		<h3><?php _e("Form Styling", 'ultimate-forms'); ?></h3>
		<div class='ewd-ufp-build-form-explanation'><?php _e("Use the area below to style how your form looks.", 'ultimate-forms'); ?></div>
		<div class='ewd-ufp-form-styling-options'>
			<div class='ewd-ufp-form-styling-option'>
				<div class='ewd-ufp-form-styling-option-label'>Form Layout</div>
				<div class='ewd-ufp-form-styling-select-options'>
					<input type='radio' name='ewd_ufp_form_layout' value='Side_By_Side' <?php echo (($Form_Stylings['Form_Layout'] == "Side_By_Side" or $Form_Styling['Form_Layout'] == "") ? 'checked' : '');  ?>/><span><?php _e("Side-By-Side", 'ultimate-forms'); ?></span>
					<div class="ewd-ufp-clear"></div>
					<input type='radio' name='ewd_ufp_form_layout' value='Stacked' <?php echo ($Form_Stylings['Form_Layout'] == "Stacked" ? 'checked' : '');  ?>/><span><?php _e("Stacked", 'ultimate-forms'); ?></span>
				</div>
				<span><?php _e("Which form layout should be used?", 'ultimate-forms'); ?>
			</div>
			<div class="ewd-ufp-clear"></div>
			<div class='ewd-ufp-form-styling-option'>
				<div class='ewd-ufp-form-styling-option-label'>Form Text Color</div>
				<div class='ewd-ufp-form-styling-select-options'>
					<input type='text' class='ufp-spectrum' name='ewd_ufp_form_text_color' value='<?php echo $Form_Stylings["Form_Text_Color"]; ?>' />
				</div>
			</div>
			<div class='ewd-ufp-form-styling-option'>
				<div class='ewd-ufp-form-styling-option-label'>Form Background Color</div>
				<div class='ewd-ufp-form-styling-select-options'>
					<input type='text' class='ufp-spectrum' name='ewd_ufp_form_background_color' value='<?php echo $Form_Stylings["Form_Background_Color"]; ?>' />
				</div>
			</div>
			<div class="ewd-ufp-clear"></div>
			<div class='ewd-ufp-form-styling-option'>
				<div class='ewd-ufp-form-styling-option-label'>Padding Above Form (e.g. 24px)</div>
				<div class='ewd-ufp-form-styling-select-options'>
					<input type='text' name='ewd_ufp_form_padding_above' value='<?php echo $Form_Stylings["Form_Padding_Above"]; ?>' />
				</div>
			</div>
			<div class='ewd-ufp-form-styling-option'>
				<div class='ewd-ufp-form-styling-option-label'>Padding Below Form</div>
				<div class='ewd-ufp-form-styling-select-options'>
					<input type='text' name='ewd_ufp_form_padding_below' value='<?php echo $Form_Stylings["Form_Padding_Below"]; ?>' />
				</div>
			</div>
			<div class='ewd-ufp-form-styling-option'>
				<div class='ewd-ufp-form-styling-option-label'>Padding Before Form</div>
				<div class='ewd-ufp-form-styling-select-options'>
					<input type='text' name='ewd_ufp_form_padding_before' value='<?php echo $Form_Stylings["Form_Padding_Before"]; ?>' />
				</div>
			</div>
			<div class='ewd-ufp-form-styling-option'>
				<div class='ewd-ufp-form-styling-option-label'>Padding After Form</div>
				<div class='ewd-ufp-form-styling-select-options'>
					<input type='text' name='ewd_ufp_form_padding_after' value='<?php echo $Form_Stylings["Form_Padding_After"]; ?>' />
				</div>
			</div>
			<div class="ewd-ufp-clear"></div>
		</div>
	</div>

	<?php
	
}

function EWD_UFP_Add_Form_Element($Question_Type, $Question_Counter, $Page_Counter, $Form_Element = null, $AJAX_Add = 'Yes', $Question_Title = '') {
	if ($Question_Type == 'page_break') {
		echo '<tr class="ewd-ufp-page-container" data-pageid="' . $Form_Element->ID . '" data-questioncount="' . $Question_Counter . '">';
		echo "<td><span class='ewd-ufp-page-number'>" . __("Page ", 'ultimate-forms') . $Page_Counter . "</span>";
		echo "<input type='hidden' name='Element_ID_" . $Question_Counter . "' value='" . $Form_Element->ID . "' />";
		echo "<input type='hidden' name='Element_Type_" . $Question_Counter . "' value='" . $Question_Type . "' />";
		echo "<input type='hidden' name='Element_Order_" . $Question_Counter . "' value='" . $Question_Counter . "'/>";
		echo "<table class='ewd-ufp-page-table'><tbody>";
		if ($AJAX_Add == "Yes") {echo '</tbody></table></td></tr>';}
		$Last_Element_ID = get_post_meta($Form_Element->ID, 'EWD_UFP_Last_Element_ID', true);
	}
	elseif ($Question_Type == 'section_break') {
		echo '<tr class="ewd-ufp-form-element ewd-ufp-section-break" data-elementid="' . $Form_Element->ID . '" data-questioncount="' . $Question_Counter . '">';
		echo "<td><input type='hidden' name='Element_ID_" . $Question_Counter . "' value='' />";
		echo "<input type='hidden' name='Element_Type_" . $Question_Counter . "' value='section_break' />";
		echo "<input type='hidden' name='Element_Order_" . $Question_Counter . "' value='" . $Question_Counter . "'/></td></tr>";
	}
	elseif ($Question_Type == 'captcha') {
		echo '<tr class="ewd-ufp-form-element ewd-ufp-captcha" data-elementid="' . $Form_Element->ID . '" data-questioncount="' . $Question_Counter . '">';
		echo "<td><input type='hidden' name='Element_ID_" . $Question_Counter . "' value='' />";
		echo "<input type='hidden' name='Element_Type_" . $Question_Counter . "' value='captcha' />";
		echo "<input type='hidden' name='Element_Order_" . $Question_Counter . "' value='" . $Question_Counter . "'/>";
		echo "<div class='ewd-ufp-build-form-actions-container'>";
		echo "<div class='ewd-ufp-build-form-delete-question'>" . __('Delete', 'ultimate-forms') . "</div>";
		echo "</div>";
		echo "<div class='ewd-ufp-captcha-label'>" . __("Captcha Field", 'ultimate-forms') . "</div>";
		echo "</td></tr>";
	}
	else {echo EWD_UFP_Add_Customizable_Form_Element($Question_Type, $Question_Counter, $Form_Element, $Question_Title);}
}

function EWD_UFP_Add_Customizable_Form_Element($Question_Type, $Question_Counter, $Form_Element = null, $Question_Title = '') {
	$Required = get_post_meta($Form_Element->ID, 'EWD_UFP_Element_Required', true);
	$Possible_Answers = get_post_meta($Form_Element->ID, 'EWD_UFP_Element_Answer_Options', true);
	if (!is_array($Possible_Answers)) {$Possible_Answers = array();}

	$Form_Element_ID = ($Form_Element ? $Form_Element->ID : 0);
	$Question_Title = ($Form_Element ? $Form_Element->post_title : ($Question_Title != '' ? $Question_Title : __('Question Title', 'ultimate-forms')));
	$Question_Content = ($Form_Element ? $Form_Element->post_content : __('Question Content', 'ultimate-forms'));
	$Element_Icon = ($Form_Element ? get_post_meta($Form_Element_ID, 'EWD_UFP_Element_Icon', true) : 'dashicons-edit');

	if ($Question_Type == 'title') {$Capabilities = array('icon', 'title');}
	elseif ($Question_Type == 'instructions') {$Capabilities = array('icon', 'instructions');}
	elseif ($Question_Type == 'radio' or $Question_Type == 'checkbox' or $Question_Type == 'select') {$Capabilities = array('icon', 'title', 'instructions', 'possible_answers', 'advanced_question');}
	else {$Capabilities = array('icon', 'title', 'instructions', 'advanced_question');} 

	$Form_Element_HTML = "";

	$Form_Element_HTML .= "<tr class='ewd-ufp-form-element' data-questioncount='" . $Question_Counter . "'><td>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-actions-container'>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-delete-question'>" . __('Delete', 'ultimate-forms') . "</div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-question-section-toggle ewd-ufp-build-form-main-question ewd-ufp-selected' data-section='main' data-row='" . $Question_Counter . "'>" . __("Main", 'ultimate-forms') . "</div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-question-section-toggle ewd-ufp-build-form-style-question' data-section='styling' data-row='" . $Question_Counter . "'>" . __("Style", 'ultimate-forms') . "</div>";
	if (in_array('advanced_question', $Capabilities)) {$Form_Element_HTML .= "<div class='ewd-ufp-question-section-toggle ewd-ufp-build-form-advanced-question' data-section='advanced' data-row='" . $Question_Counter . "'>" . __("Advanced", 'ultimate-forms') . "</div>";}
	$Form_Element_HTML .= "</div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-clear'></div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-container'>";
	if (in_array('icon', $Capabilities)) {
		$Form_Element_HTML .= "<div id='ewd-ufp-dashicon-selector' class='ewd-ufp-hidden'>";
		$Form_Element_HTML .= "<div id='ewd-ufp-dashicon-selector-inside'>";
		$Form_Element_HTML .= "<div id='ewd-ufp-dashicon-selector-inside-choose-text'>";
		$Form_Element_HTML .= __("Select an icon below", "ultimate-forms");
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-clear'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-admin-post' data-dashiconclass='dashicons-admin-post'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-admin-links' data-dashiconclass='dashicons-admin-links'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-admin-comments' data-dashiconclass='dashicons-admin-comments'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-admin-appearance' data-dashiconclass='dashicons-admin-appearance'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-admin-plugins' data-dashiconclass='dashicons-admin-plugins'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-admin-users' data-dashiconclass='dashicons-admin-users'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-admin-tools' data-dashiconclass='dashicons-admin-tools'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-admin-network' data-dashiconclass='dashicons-admin-network'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-admin-generic' data-dashiconclass='dashicons-admin-generic'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-format-chat' data-dashiconclass='dashicons-format-chat'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-video-alt3' data-dashiconclass='dashicons-video-alt3'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-format-status' data-dashiconclass='dashicons-format-status'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-dashboard' data-dashiconclass='dashicons-dashboard'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-arrow-right' data-dashiconclass='dashicons-arrow-right'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-arrow-left' data-dashiconclass='dashicons-arrow-left'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-arrow-up' data-dashiconclass='dashicons-arrow-up'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-arrow-down' data-dashiconclass='dashicons-arrow-down'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-share' data-dashiconclass='dashicons-share'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-share-alt' data-dashiconclass='dashicons-share-alt'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-share-alt2' data-dashiconclass='dashicons-share-alt2'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-twitter' data-dashiconclass='dashicons-twitter'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-facebook-alt' data-dashiconclass='dashicons-facebook-alt'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-email-alt' data-dashiconclass='dashicons-email-alt'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-googleplus' data-dashiconclass='dashicons-googleplus'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-heart' data-dashiconclass='dashicons-heart'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-tag' data-dashiconclass='dashicons-tag'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-yes' data-dashiconclass='dashicons-yes'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-no' data-dashiconclass='dashicons-no'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-minus' data-dashiconclass='dashicons-minus'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-star-filled' data-dashiconclass='dashicons-star-filled'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-star-empty' data-dashiconclass='dashicons-star-empty'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-location' data-dashiconclass='dashicons-location'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-paperclip' data-dashiconclass='dashicons-paperclip'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-phone' data-dashiconclass='dashicons-phone'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-microphone' data-dashiconclass='dashicons-microphone'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-index-card' data-dashiconclass='dashicons-index-card'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-clock' data-dashiconclass='dashicons-clock'></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-dashicon dashicons dashicons-lightbulb' data-dashiconclass='dashicons-lightbulb'></div>";
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "</div>";
	}
	$Form_Element_HTML .= "<div class='ewd-ufp-question-section ewd-ufp-build-form-main-question-options' data-section='main' data-row='" . $Question_Counter . "' data-elementid='" . $Form_Element_ID . "'>";
	$Form_Element_HTML .= "<input type='hidden' name='Element_Order_" . $Question_Counter . "' value='" . $Question_Counter . "'/>";
	$Form_Element_HTML .= "<input type='hidden' name='Element_ID_" . $Question_Counter . "' value='" . $Form_Element_ID . "' />";
	$Form_Element_HTML .= "<input type='hidden' name='Element_Type_" . $Question_Counter . "' value='" . $Question_Type . "' />";
	$Form_Element_HTML .= "<input type='hidden' name='Element_Icon_" . $Question_Counter . "' value='" . $Element_Icon . "' />";
	$Form_Element_HTML .= "<input type='hidden' name='Element_Title_" . $Question_Counter . "' value='" . $Question_Title . "' />";
	$Form_Element_HTML .= "<input type='hidden' name='Element_Instructions_" . $Question_Counter . "' value='" . $Question_Content . "' />";
	$Form_Element_HTML .= "<input type='hidden' name='Element_Answer_Options_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Element_Answer_Options', true) . "' />";
	if (in_array('icon', $Capabilities)) {$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-icon dashicons " . $Element_Icon . "' data-row='" . $Question_Counter . "' title='" . __("Click to change icon", "ultimate-forms") . "'></div>";}
	if (in_array('title', $Capabilities)) {$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-title' data-row='" . $Question_Counter . "'>" . $Question_Title . "</div>";}
	if (in_array('instructions', $Capabilities)) {$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-instructions' data-row='" . $Question_Counter . "'>" . $Question_Content . "</div>";}
	if (in_array('possible_answers', $Capabilities)) {
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-answer-options' data-row='" . $Question_Counter . "'>";
		foreach ($Possible_Answers as $Possible_Answer) {
			$Form_Element_HTML .= "<input type='text' name='Element_Answer_Options_" . $Question_Counter . "[]' placeholder='Option One' class='ewd-ufp-edit-answers' value='" . $Possible_Answer . "' />";
		}
		$Form_Element_HTML .= "<div class='ewd-ufp-add-answer-option'>Add Another Option</div>";
		$Form_Element_HTML .= "</div>";
	}
	$Form_Element_HTML .= "</div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-question-section ewd-ufp-build-form-question-styles ewd-ufp-hidden' data-section='styling' data-row='" . $Question_Counter . "'>";
	if (in_array('title', $Capabilities)) {
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-section'>";
		$Form_Element_HTML .= "<div class='ewd-ufp-form-question-style-heading ewd-ufp-down-caret' data-questioncount='" . $Question_Counter . "' data-section='Title'>Title</div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-form-question-style-section ewd-ufp-hidden' data-questioncount='" . $Question_Counter . "' data-section='Title'>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Title Font Family:</span><input type='text' name='Question_Title_Font_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Title_Font', true) . "' /></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Title Font Size:</span><input type='text' name='Question_Title_Font_Size_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Title_Font_Size', true) . "' /></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Title Font Color:</span><input type='text' class='ufp-spectrum' name='Question_Title_Font_Color_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Title_Font_Color', true) . "' /></div>";
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "</div>"; // ewd-ufp-build-form-question-style-section
	}
	if (in_array('instructions', $Capabilities)) {
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-section'>";
		$Form_Element_HTML .= "<div class='ewd-ufp-form-question-style-heading ewd-ufp-down-caret' data-questioncount='" . $Question_Counter . "' data-section='Instructions'>Instructions</div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-form-question-style-section ewd-ufp-hidden' data-questioncount='" . $Question_Counter . "' data-section='Instructions'>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Instructions Font Family:</span><input type='text' name='Question_Instructions_Font_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Instructions_Font', true) . "' /></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Instructions Font Size:</span><input type='text' name='Question_Instructions_Font_Size_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Instructions_Font_Size', true) . "' /></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Instructions Font Color:</span><input type='text' class='ufp-spectrum' name='Question_Instructions_Font_Color_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Instructions_Font_Color', true) . "' /></div>";
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "</div>"; // ewd-ufp-build-form-question-style-section
	}
	if (in_array('possible_answers', $Capabilities)) {		
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-section'>";
		$Form_Element_HTML .= "<div class='ewd-ufp-form-question-style-heading ewd-ufp-down-caret' data-questioncount='" . $Question_Counter . "' data-section='Answers'>Answers</div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-form-question-style-section ewd-ufp-hidden' data-questioncount='" . $Question_Counter . "' data-section='Answers'>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Answers Font Family:</span><input type='text' name='Question_Answers_Font_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Answers_Font', true) . "' /></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Answers Font Size:</span><input type='text' name='Question_Answers_Font_Size_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Answers_Font_Size', true) . "' /></div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Answers Font Color:</span><input type='text' class='ufp-spectrum' name='Question_Answers_Font_Color_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Answers_Font_Color', true) . "' /></div>";
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "</div>"; // ewd-ufp-build-form-question-style-section
	}			
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-section'>";
	$Form_Element_HTML .= "<div class='ewd-ufp-form-question-style-heading ewd-ufp-down-caret' data-questioncount='" . $Question_Counter . "' data-section='Spacing'>Spacing</div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-form-question-style-section ewd-ufp-hidden' data-questioncount='" . $Question_Counter . "' data-section='Spacing'>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Padding Above (e.g. 8px)</span><input type='text' name='Question_Spacing_Padding_Above_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Spacing_Padding_Above', true) . "' /></div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Padding Below</span><input type='text' name='Question_Spacing_Padding_Below_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Spacing_Padding_Below', true) . "' /></div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Padding Before</span><input type='text' name='Question_Spacing_Padding_Before_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Spacing_Padding_Before', true) . "' /></div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Padding After</span><input type='text' name='Question_Spacing_Padding_After_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Spacing_Padding_After', true) . "' /></div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Margin Between Elements</span><input type='text' name='Question_Spacing_Margin_Between_Elements_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Spacing_Margin_Between_Elements', true) . "' /></div>";
	$Form_Element_HTML .= "</div>";
	$Form_Element_HTML .= "</div>"; // ewd-ufp-build-form-question-style-section			
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-section'>";
	$Form_Element_HTML .= "<div class='ewd-ufp-form-question-style-heading ewd-ufp-down-caret' data-questioncount='" . $Question_Counter . "' data-section='Sizing'>Sizing</div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-form-question-style-section ewd-ufp-hidden' data-questioncount='" . $Question_Counter . "' data-section='Sizing'>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Width of Element Area (e.g. 400px)</span><input type='text' name='Question_Sizing_Question_Area_Width_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Sizing_Question_Area_Width', true) . "' /></div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Height of Element Area</span><input type='text' name='Question_Sizing_Question_Area_Height_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Sizing_Question_Area_Height', true) . "' /></div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Width of Input</span><input type='text' name='Question_Sizing_Input_Width_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Sizing_Input_Width', true) . "' /></div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Height of Input</span><input type='text' name='Question_Sizing_Input_Height_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Sizing_Input_Height', true) . "' /></div>";
	$Form_Element_HTML .= "</div>";
	$Form_Element_HTML .= "</div>"; // ewd-ufp-build-form-question-style-section			
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-section'>";
	$Form_Element_HTML .= "<div class='ewd-ufp-form-question-style-heading ewd-ufp-down-caret' data-questioncount='" . $Question_Counter . "' data-section='Background'>Background</div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-form-question-style-section ewd-ufp-hidden' data-questioncount='" . $Question_Counter . "' data-section='Background'>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Element Background Color:</span><input type='text' class='ufp-spectrum' name='Question_Background_Color_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Background_Color', true) . "' /></div>";
	$Form_Element_HTML .= "<div class='ewd-ufp-build-form-question-style-option'><span>Element Input Background Color:</span><input type='text' class='ufp-spectrum' name='Question_Background_Input_Color_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Question_Background_Input_Color', true) . "' /></div>";
	$Form_Element_HTML .= "</div>";
	$Form_Element_HTML .= "</div>"; // ewd-ufp-build-form-question-style-section		
	$Form_Element_HTML .= "</div>";
	if (in_array('advanced_question', $Capabilities)) {
		$Form_Element_HTML .= "<div class='ewd-ufp-question-section ewd-ufp-build-form-question-advanced ewd-ufp-hidden' data-section='advanced' data-row='" . $Question_Counter . "'>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option'>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option-label'>Slug:</div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option-input'>";
		$Form_Element_HTML .= "<input type='text' name='Element_Slug_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Element_Slug', true) . "' />";
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option'>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option-label'>Required:</div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option-input'>";
		$Form_Element_HTML .= "Yes <input type='radio' name='Element_Required_" . $Question_Counter . "' value='required' " . ($Required == "required" ? 'checked' : '') . " /><br />";
		$Form_Element_HTML .= "No <input type='radio' name='Element_Required_" . $Question_Counter . "' value='' " . ($Required == "required" ? '' : 'checked') . " />";
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option'>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option-label'>Allowed Values (can include regex):</div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option-input'>";
		$Form_Element_HTML .= "<input type='text' name='Element_Regex_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Element_Regex', true) . "' />";
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option'>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option-label'>Validation Failed Message:</div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option-input'>";
		$Form_Element_HTML .= "<input type='text' name='Element_Regex_Failed_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Element_Regex_Failed', true) . "' />";
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "<h4>" . __("Conditional Actions", 'ultimate-forms') . "</h4>";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option'>";
		$Form_Element_HTML .= "<input type='checkbox' name='Conditional_Page_Display_Enabled_" . $Question_Counter . "' value='Yes' " . (get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Page_Display_Enabled', true) == "Yes" ? 'checked' : '') . " />";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option-conditional'>" . __('If the answer to this question is', 'ultimate-forms');
		if (in_array('possible_answers', $Capabilities)) {
			$Form_Element_HTML .= "<select name='Conditional_Page_Display_Answer_" . $Question_Counter . "' class='ewd-ufp-page-conditional-answers'>";
			$Conditional_Page_Answer = get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Page_Display_Answer', true);
			foreach ($Possible_Answers as $Possible_Answer) {
				$Form_Element_HTML .= "<option value='" . $Possible_Answer . "' " . ($Conditional_Page_Answer == $Possible_Answer ? 'selected' : '') . " />" . $Possible_Answer . "</option>";
			}
			$Form_Element_HTML .= "</select>";
		}
		else {$Form_Element_HTML .= "<input type='text' name='Conditional_Page_Display_Answer_" . $Question_Counter . "' value='" . get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Page_Display_Answer', true) . "' />";}
		$Form_Element_HTML .= __('go to page', 'ultimate-forms');
		$Form_Element_HTML .= "<select name='Conditional_Page_Display_Destination_" . $Question_Counter . "' class='ewd-ufp-page-conditional-pages'>";
		$Conditional_Page_Destination = get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Page_Display_Destination', true);
		if ($Conditional_Page_Destination != "") {$Form_Element_HTML .= "<option value='" . $Conditional_Page_Destination . "' selected>" . $Conditional_Page_Destination . "</option>";}
		$Form_Element_HTML .= "</select>";
		$Form_Element_HTML .= "</div>";		
		$Form_Element_HTML .= "</div>";	
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option'>";
		$Form_Element_HTML .= "<input type='checkbox' name='Conditional_Question_Display_Enabled_" . $Question_Counter . "' value='Yes' " . (get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Question_Display_Enabled', true) == "Yes" ? 'checked' : '') . " />";
		$Form_Element_HTML .= "<div class='ewd-ufp-build-form-advanced-option-conditional'>" . __('If the answer to ', 'ultimate-forms');
		$Form_Element_HTML .= "<select name='Conditional_Question_Display_Questions_" . $Question_Counter . "' class='ewd-ufp-conditional-questions ewd-ufp-fill-select' data-row='" . $Question_Counter . "'>";
		$Form_Element_HTML .= "<option value=''></option>";
		$Conditional_Question_Question = get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Question_Display_Question', true);
		if ($Conditional_Question_Question != "") {$Form_Element_HTML .= "<option value='" . $Conditional_Question_Question . "'>" . $Conditional_Question_Question . "</option>";}
		$Form_Element_HTML .= "</select>";
		$Conditional_Question_Logic = get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Question_Display_Logic', true);
		$Form_Element_HTML .= "<select name='Conditional_Question_Display_Logic_" . $Question_Counter . "' class='ewd-ufp-question-logic'>";
		$Form_Element_HTML .= "<option value='Equal' " . ($Conditional_Question_Logic == 'Equal' ? 'selected' : '') . ">" . __("is", 'ultimate-forms') . "</option>";
		$Form_Element_HTML .= "<option value='NotEqual' " . ($Conditional_Question_Logic == 'NotEqual' ? 'selected' : '') . ">" . __("isn't", 'ultimate-forms') . "</option>";
		$Form_Element_HTML .= "</select>";
		$Form_Element_HTML .= "<select name='Conditional_Question_Display_Answers_Select_" . $Question_Counter . "' class='ewd-ufp-question-conditional-answers-select ewd-ufp-hidden' data-row='" . $Question_Counter . "'>";
		$Conditional_Question_Answer = get_post_meta($Form_Element_ID, 'EWD_UFP_Conditional_Question_Display_Answer', true);
		if ($Conditional_Question_Answer != "") {$Form_Element_HTML .= "<option value='" . $Conditional_Question_Answer . "'>" . $Conditional_Question_Answer . "</option>";}
		$Form_Element_HTML .= "</select>";
		$Form_Element_HTML .= "<input type='text' name='Conditional_Question_Display_Answers_Text_" . $Question_Counter . "' value='" . $Conditional_Question_Answer . "' class='ewd-ufp-question-conditional-answers-text' data-row='" . $Question_Counter . "' />";
		$Form_Element_HTML .= __("don't display this question.", 'ultimate-forms');
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "<div class='ewd-ufp-clear'></div>";
		$Form_Element_HTML .= __("Please save the form to display any newly added questions in the dropdowns above.", 'ultimate-forms');		
		$Form_Element_HTML .= "</div>";
		$Form_Element_HTML .= "</div>"; //close advanced-option		
	}
	$Form_Element_HTML .= "</div>";
	$Form_Element_HTML .= "</td></tr>";

	return $Form_Element_HTML;
}

function EWD_UFP_Edit_Element_Answer_Options($Answers) {
	return $Answers;
}

add_action( 'save_post', 'EWD_UFP_Save_Meta_Box_Data' );
function EWD_UFP_Save_Meta_Box_Data($post_id) {
	$Form_Element_IDs = get_post_meta($post_id, 'EWD_UFP_Form_Element_IDs', true);
	if (!is_array($Form_Element_IDs)) {$Form_Element_IDs = array();}

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['EWD_UFP_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['EWD_UFP_meta_box_nonce'], 'EWD_UFP_Save_Meta_Box_Data' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	if (get_post_type($post_id) != 'ufp_form') {return;}

	// Save the form elements.
	$New_Elements = array();
	$Page_ID = 0;
	for ($Counter = 0; $Counter < 200; $Counter++) {
		if (isset($_POST['Element_Type_' . $Counter])) {
			$Element_ID = sanitize_text_field($_POST['Element_ID_' . $Counter]);
			if (!in_array($Element_ID, $Form_Element_IDs)) {
				$Element_ID = wp_insert_post(array('post_title' => sanitize_text_field($_POST['Element_Title_' . $Counter]) . " ", 'post_content' => sanitize_text_field($_POST['Element_Instructions_' . $Counter]) . " ", 'post_type' => 'ufp_form_element'));
			}
			else {
				echo wp_update_post(array('ID' => $Element_ID, 'post_title' => sanitize_text_field($_POST['Element_Title_' . $Counter]) . " ", 'post_content' => sanitize_text_field($_POST['Element_Instructions_' . $Counter]) . " "));
			}
			
			update_post_meta($Element_ID, 'EWD_UFP_Element_Icon', sanitize_text_field($_POST['Element_Icon_' . $Counter]));
			update_post_meta($Element_ID, 'EWD_UFP_Question_Type', sanitize_text_field($_POST['Element_Type_' . $Counter]));
			if ($_POST['Element_Type_' . $Counter] == 'page_break') {
				if ($Page_ID != 0) {update_post_meta($Page_ID, 'EWD_UFP_Last_Element_ID', $Last_Element_ID);}
				$Page_ID = $Element_ID;
			}
			if (isset($_POST['Element_Answer_Options_' . $Counter])) {
				$Sanitized_Answers = array_map('sanitize_text_field', wp_unslash($_POST['Element_Answer_Options_' . $Counter]));
				update_post_meta($Element_ID, 'EWD_UFP_Element_Answer_Options', $Sanitized_Answers);
			}
			else {update_post_meta($Element_ID, 'EWD_UFP_Element_Answer_Options', array());}

			if (isset($_POST['Question_Title_Font_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Title_Font', sanitize_text_field($_POST['Question_Title_Font_' . $Counter]));}
			if (isset($_POST['Question_Title_Font_Size_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Title_Font_Size', sanitize_text_field($_POST['Question_Title_Font_Size_' . $Counter]));}
			if (isset($_POST['Question_Title_Font_Color_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Title_Font_Color', sanitize_text_field($_POST['Question_Title_Font_Color_' . $Counter]));}
			if (isset($_POST['Question_Instructions_Font_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Instructions_Font', sanitize_text_field($_POST['Question_Instructions_Font_' . $Counter]));}
			if (isset($_POST['Question_Instructions_Font_Size_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Instructions_Font_Size', sanitize_text_field($_POST['Question_Instructions_Font_Size_' . $Counter]));}
			if (isset($_POST['Question_Instructions_Font_Color_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Instructions_Font_Color', sanitize_text_field($_POST['Question_Instructions_Font_Color_' . $Counter]));}
			if (isset($_POST['Question_Answers_Font_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Answers_Font', sanitize_text_field($_POST['Question_Answers_Font_' . $Counter]));}
			if (isset($_POST['Question_Answers_Font_Size_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Answers_Font_Size', sanitize_text_field($_POST['Question_Answers_Font_Size_' . $Counter]));}
			if (isset($_POST['Question_Answers_Font_Color_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Answers_Font_Color', sanitize_text_field($_POST['Question_Answers_Font_Color_' . $Counter]));}
			if (isset($_POST['Question_Spacing_Padding_Above_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Spacing_Padding_Above', sanitize_text_field($_POST['Question_Spacing_Padding_Above_' . $Counter]));}
			if (isset($_POST['Question_Spacing_Padding_Below_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Spacing_Padding_Below', sanitize_text_field($_POST['Question_Spacing_Padding_Below_' . $Counter]));}
			if (isset($_POST['Question_Spacing_Padding_Before_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Spacing_Padding_Before', sanitize_text_field($_POST['Question_Spacing_Padding_Before_' . $Counter]));}
			if (isset($_POST['Question_Spacing_Padding_After_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Spacing_Padding_After', sanitize_text_field($_POST['Question_Spacing_Padding_After_' . $Counter]));}
			if (isset($_POST['Question_Spacing_Margin_Between_Elements_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Spacing_Margin_Between_Elements', sanitize_text_field($_POST['Question_Spacing_Margin_Between_Elements_' . $Counter]));}
			if (isset($_POST['Question_Sizing_Question_Area_Width_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Sizing_Question_Area_Width', sanitize_text_field($_POST['Question_Sizing_Question_Area_Width_' . $Counter]));}
			if (isset($_POST['Question_Sizing_Question_Area_Height_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Sizing_Question_Area_Height', sanitize_text_field($_POST['Question_Sizing_Question_Area_Height_' . $Counter]));}
			if (isset($_POST['Question_Sizing_Input_Width_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Sizing_Input_Width', sanitize_text_field($_POST['Question_Sizing_Input_Width_' . $Counter]));}
			if (isset($_POST['Question_Sizing_Input_Height_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Sizing_Input_Height', sanitize_text_field($_POST['Question_Sizing_Input_Height_' . $Counter]));}
			if (isset($_POST['Question_Background_Color_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Background_Color', sanitize_text_field($_POST['Question_Background_Color_' . $Counter]));}
			if (isset($_POST['Question_Background_Input_Color_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Question_Background_Input_Color', sanitize_text_field($_POST['Question_Background_Input_Color_' . $Counter]));}

			if (isset($_POST['Element_Slug_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Element_Slug', sanitize_text_field($_POST['Element_Slug_' . $Counter]));}
			if (isset($_POST['Element_Required_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Element_Required', sanitize_text_field($_POST['Element_Required_' . $Counter]));}
			if (isset($_POST['Element_Regex_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Element_Regex', sanitize_text_field($_POST['Element_Regex_' . $Counter]));}
			if (isset($_POST['Element_Regex_Failed_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Element_Regex_Failed', sanitize_text_field($_POST['Element_Regex_Failed_' . $Counter]));}

			if (isset($_POST['Conditional_Page_Display_Destination_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Conditional_Page_Display_Enabled', sanitize_text_field($_POST['Conditional_Page_Display_Enabled_' . $Counter]));}
			if (isset($_POST['Conditional_Page_Display_Answer_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Conditional_Page_Display_Answer', sanitize_text_field($_POST['Conditional_Page_Display_Answer_' . $Counter]));}
			if (isset($_POST['Conditional_Page_Display_Destination_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Conditional_Page_Display_Destination', sanitize_text_field($_POST['Conditional_Page_Display_Destination_' . $Counter]));}
			if (isset($_POST['Conditional_Question_Display_Logic_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Conditional_Question_Display_Enabled', sanitize_text_field($_POST['Conditional_Question_Display_Enabled_' . $Counter]));}
			if (isset($_POST['Conditional_Question_Display_Questions_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Conditional_Question_Display_Question', sanitize_text_field($_POST['Conditional_Question_Display_Questions_' . $Counter]));}
			if (isset($_POST['Conditional_Question_Display_Logic_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Conditional_Question_Display_Logic', sanitize_text_field($_POST['Conditional_Question_Display_Logic_' . $Counter]));}
			if (isset($_POST['Conditional_Question_Display_Answers_Text_' . $Counter]) and $_POST['Conditional_Question_Display_Answers_Text_' . $Counter] != '') {update_post_meta($Element_ID, 'EWD_UFP_Conditional_Question_Display_Answer', sanitize_text_field($_POST['Conditional_Question_Display_Answers_Text_' . $Counter]));}
			elseif (isset($_POST['Conditional_Question_Display_Answers_Select_' . $Counter])) {update_post_meta($Element_ID, 'EWD_UFP_Conditional_Question_Display_Answer', sanitize_text_field($_POST['Conditional_Question_Display_Answers_Select_' . $Counter]));}

			if ($_POST['Element_Order_' . $Counter] != "") {$New_Elements[sanitize_text_field($_POST['Element_Order_' . $Counter])] = $Element_ID;}
			else {$New_Elements[sanitize_text_field($_POST['Element_Order_' . $Counter]) + 200] = $Element_ID;}

			$Last_Element_ID = $Element_ID;
		}
	}
	update_post_meta($Page_ID, 'EWD_UFP_Last_Element_ID', $Last_Element_ID);
	ksort($New_Elements);
	$Delete_Element_IDs = array_diff($Form_Element_IDs, $New_Elements);
	foreach ($Delete_Element_IDs as $Delete_Element_ID) {wp_delete_post($Delete_Element_ID, true);}

	update_post_meta($post_id, 'EWD_UFP_Form_Element_IDs', $New_Elements);

	$Email_Messages_Array = array();
	for ($Counter = 0; $Counter < 30; $Counter++) {
		if (isset($_POST['Form_Email_' . $Counter])) {
			$Email_Message_Item = array();

			$Email_Message_Item['Send'] = sanitize_text_field($_POST['Form_Email_Send_' . $Counter]);
			$Email_Message_Item['Send_To'] = sanitize_text_field($_POST['Form_Email_Send_To_' . $Counter]);
			$Email_Message_Item['Subject'] = sanitize_text_field($_POST['Form_Email_Subject_' . $Counter]);
			$Email_Message_Item['Message'] = sanitize_text_field($_POST['Form_Email_Message_' . $Counter]);

			$Email_Messages_Array[] = $Email_Message_Item;
		}
	}
	$Form_Settings['Email_Messages_Array'] = $Email_Messages_Array;
	$Form_Settings['Save_Submissions'] = sanitize_text_field($_POST['ewd_ufp_save_submissions']);

	$Form_Settings['Submitted_Successfully_Label'] = sanitize_text_field($_POST['submitted_successfully_label']);
	$Form_Settings['General_Failure_Label'] = sanitize_text_field($_POST['general_failure_label']);
	$Form_Settings['Email_Failure_Label'] = sanitize_text_field($_POST['email_failure_label']);
	$Form_Settings['Save_Failure_Label'] = sanitize_text_field($_POST['save_failure_label']);

	update_post_meta($post_id, 'EWD_UFP_Form_Settings', $Form_Settings);

	$Form_Stylings['Form_Layout'] = sanitize_text_field($_POST['ewd_ufp_form_layout']);
	$Form_Stylings['Form_Text_Color'] = sanitize_text_field($_POST['ewd_ufp_form_text_color']);
	$Form_Stylings['Form_Background_Color'] = sanitize_text_field($_POST['ewd_ufp_form_background_color']);
	$Form_Stylings['Form_Padding_Above'] = sanitize_text_field($_POST['ewd_ufp_form_padding_above']);
	$Form_Stylings['Form_Padding_Below'] = sanitize_text_field($_POST['ewd_ufp_form_padding_below']);
	$Form_Stylings['Form_Padding_Before'] = sanitize_text_field($_POST['ewd_ufp_form_padding_before']);
	$Form_Stylings['Form_Padding_After'] = sanitize_text_field($_POST['ewd_ufp_form_padding_after']);
	update_post_meta($post_id, 'EWD_UFP_Form_Stylings', $Form_Stylings);
}

?>