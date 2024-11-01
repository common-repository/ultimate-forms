<?php 
/* The function that creates the HTML on the front-end, based on the parameters
* supplied in the product-catalog shortcode */
function EWD_UFP_Insert_Contact_Form($atts) {
		
	// Get the attributes passed by the shortcode, and store them in new variables for processing
	extract( shortcode_atts( array(
		 		'form_id' => 0
		 		),
		$atts
		)
	);

	$ReturnString = "";

	if ($form_id != 0) {
		$ReturnString .=  EWD_UFP_Contact_Form_HTML($form_id);
	}
		
	return $ReturnString;
}
add_shortcode("ultimate-forms", "EWD_UFP_Insert_Contact_Form");
