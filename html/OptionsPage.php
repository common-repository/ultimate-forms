<?php
$Custom_CSS = get_option("EWD_UFP_Custom_CSS");

$Submitted_Successfully_Label = get_option("EWD_UFP_Submitted_Successfully_Label");
$General_Failure_Label = get_option("EWD_UFP_General_Failure_Label");
$Email_Failure_Label = get_option("EWD_UFP_Email_Failure_Label");
$Save_Failure_Label = get_option("EWD_UFP_Save_Failure_Label");

?>

<div class="wrap ufp-options-page-tabbed">
	<div class="ufp-options-submenu-div">
		<ul class="ufp-options-submenu ufp-options-page-tabbed-nav">
			<li><a id="Basic_Menu" class="MenuTab options-subnav-tab <?php if ($Display_Tab == '' or $Display_Tab == 'Basic') {echo 'options-subnav-tab-active';}?>" onclick="ShowOptionTab('Basic');">Basic</a></li>
			<li><a id="Labelling_Menu" class="MenuTab options-subnav-tab <?php if ($Display_Tab == 'Labelling') {echo 'options-subnav-tab-active';}?>" onclick="ShowOptionTab('Labelling');">Labelling</a></li>
			<!--<li><a id="Styling_Menu" class="MenuTab options-subnav-tab <?php if ($Display_Tab == 'Styling') {echo 'options-subnav-tab-active';}?>" onclick="ShowOptionTab('Styling');">Styling</a></li>-->
		</ul>
	</div>


	<div class="ufp-options-page-tabbed-content">
		<form method="post" action="admin.php?page=EWD-UFP-Options&DisplayPage=Options&Action=EWD_UFP_UpdateOptions">
			<div id='Basic' class='ufp-option-set'>
				<h2 id='label-basic-options' class='ufp-options-page-tab-title'>Basic Options</h2>
				<table class="form-table">
					<tr>
						<th scope="row">Custom CSS</th>
						<td>
							<fieldset><legend class="screen-reader-text"><span>Custom CSS</span></legend>
								<label title='Custom CSS'></label><textarea class='ewd-ufp-textarea' name='custom_css'> <?php echo $Custom_CSS; ?></textarea><br />
								<p>You can add custom CSS styles for your reviews in the box above.</p>
							</fieldset>
						</td>
					</tr>
				</table>
			</div>

			<div id='Labelling' class='ufp-option-set ufp-hidden'>
				<h2 id='label-order-options' class='ufp-options-page-tab-title'>Labelling Options</h2>
				<div class="ufp-label-description"> Replace the default text on the Ultimate Forms pages </div>

				<h3>Success/Failure Messages</h3>
				<div id='labelling-view-options' class="ufp-options-div ufp-options-flex">
					<div class='ufp-option ufp-label-option'>
						<?php _e("Form submitted successfully", 'ufp')?>
						<fieldset>
							<input type='text' name='submitted_successfully_label' value='<?php echo $Submitted_Successfully_Label; ?>' />
						</fieldset>
					</div>
					<div class='ufp-option ufp-label-option'>
						<?php _e("There was an error submitting the form", 'ufp')?>
						<fieldset>
							<input type='text' name='general_failure_label' value='<?php echo $General_Failure_Label; ?>' />
						</fieldset>
					</div>
					<!--<div class='ufp-option ufp-label-option'>
						<?php _e("Submission did not send successfully", 'ufp')?>
						<fieldset>
							<input type='text' name='email_failure_label' value='<?php echo $Email_Failure_Label; ?>' />
						</fieldset>
					</div>
					<div class='ufp-option ufp-label-option'>
						<?php _e("Submission did not save successfully", 'ufp')?>
						<fieldset>
							<input type='text' name='save_failure_label' value='<?php echo $Save_Failure_Label; ?>' />
						</fieldset>
					</div>-->
				</div>
			</div>

			<div id='Styling' class='ufp-option-set ufp-hidden'>
				<h2 id='label-styling-options' class='ufp-options-page-tab-title'>Styling Options</h2>
				<div id='ufp-styling-options' class="ufp-options-div ufp-options-flex">
					<div class='ufp-subsection'>
						<div class='ufp-subsection-header'>Review Title</div>
						<div class='ufp-subsection-content'>
							<div class='ufp-option ufp-styling-option'>
								<div class='ufp-option-label'>Font Family</div>
								<div class='ufp-option-input'><input type='text' name='ufp_review_title_font' placeholder='ex: Ariel,Times,etc' value='<?php echo $ufp_Review_Title_Font; ?>' <?php if ($ufp_Full_Version != "Yes") {echo "disabled";} ?> /></div>
							</div>
						</div>
					</div>
				</div>
				</div>

				<p class="submit"><input type="submit" name="Options_Submit" id="submit" class="button button-primary" value="Save Changes"  /></p></form>

			</div>
		</div>
