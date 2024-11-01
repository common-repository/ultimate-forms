jQuery(function(){ //DOM Ready

	jQuery('.ewd-ufp-form form').on('submit', function(event) {
		jQuery('.ewd-ufp-element-error').remove();
		jQuery(this).find('.ewd-ufp-question').each(function(index, el) {
			var Element_ID = jQuery(this).data('elementid');
			
			if (ewd_ufp_form_data.Regex[Element_ID] != "") {
				var Pattern = new RegExp(ewd_ufp_form_data.Regex[Element_ID]);

				if (jQuery(this).data('questiontype') == 'textarea') {var Value = jQuery(this).find('textarea').val();}
				else {var Value = jQuery(this).find('input[type!="hidden"]').val();}

				if (!Pattern.test(Value)) {
					event.preventDefault();
					jQuery(this).append('<span class="ewd-ufp-element-error">' + ewd_ufp_form_data.Regex_Failed[Element_ID] + '</span>');
					jQuery('html, body').animate({
    				    scrollTop: jQuery(this).offset().top - 100
    				}, 500);
				}
			}
		});
	});

	jQuery('.ewd-ufp-page-navigation').on('click', function() {
		var button = jQuery(this);
		var Page_Number = undefined;
		var Previous_Page = undefined;
		if (button.data('previouspage') != undefined) {Previous_Page = button.data('previouspage');}
		else {var Page_ID = button.data('pageid');}
		var Question_Counters = String(button.data('questioncounters')).split(',');

		var All_Valid = true;
		jQuery(Question_Counters).each(function(index, el) {
			if (jQuery('[name="Answer_' + el + '"]')[0] && !jQuery('[name="Answer_' + el + '"]')[0].checkValidity()) {All_Valid = false;}
		});

		if (!All_Valid) {
			jQuery(this).parent().parent().find(':submit').click();
		}
		else {
			if (button.hasClass('ewd-ufp-next-page')) {
				button.parent().find('.ewd-ufp-question').each(function(index, el) {
					var Question_Number = jQuery(this).data('row');
					if (jQuery(this).data('conditionalpageanswer') != '' && jQuery(this).data('conditionalpageanswer') == jQuery('[name="Answer_' + Question_Number + '"]').val()) {
						Page_Number = jQuery(this).data('conditionalpagedestination');
					}
				});
			}

			jQuery('.ewd-ufp-form-page').addClass('ewd-ufp-hidden');
			if (Previous_Page) {
				jQuery('.ewd-ufp-form-page[data-pageid="' + Previous_Page +'"]').removeClass('ewd-ufp-hidden');
			}
			else if (Page_Number != undefined) {
				jQuery('.ewd-ufp-form-page:nth-of-type(' + Page_Number +')').removeClass('ewd-ufp-hidden');
				if (button.hasClass('ewd-ufp-next-page')) {jQuery('.ewd-ufp-form-page:nth-of-type(' + Page_Number +')').find('.ewd-ufp-page-navigation.ewd-ufp-previous-page').data('previouspage', button.parent().data('pageid'));}
			}
			else {
				jQuery('.ewd-ufp-form-page[data-pageid="' + Page_ID + '"]').removeClass('ewd-ufp-hidden');
				if (button.hasClass('ewd-ufp-next-page')) {console.log("Erasing"); jQuery('.ewd-ufp-form-page[data-pageid="' + Page_ID + '"]').find('.ewd-ufp-page-navigation.ewd-ufp-previous-page').removeData('previouspage');
				console.log(jQuery('.ewd-ufp-form-page[data-pageid="' + Page_ID + '"]').find('.ewd-ufp-page-navigation.ewd-ufp-previous-page').data('previouspage'));}
			}
		}
	});

	jQuery('.ewd-ufp-question-input').on('change input', function() {
		EWD_UFP_Hide_Unhide_Questions(jQuery(this).closest('.ewd-ufp-question'));
	});

	jQuery('.ewd-ufp-question').each(function(index, el) {
		EWD_UFP_Hide_Unhide_Questions(jQuery(this));	
	});
});

function EWD_UFP_Hide_Unhide_Questions(Question) {
	var Element_ID = Question.data('elementid');
	var Question_Type = Question.data('questiontype');
	var Row = Question.data('row');
	if (Question_Type == 'checkbox') {var Value = jQuery('input[name="Answer_' + Row + '"]:checked').val();}
	else if (Question_Type == 'radio') {var Value = jQuery('input[name="Answer_' + Row + '"]:checked').val();}
	else {var Value = jQuery('[name="Answer_' + Row + '"]').val();}
	jQuery('.ewd-ufp-question').each(function(index, el) {
		if (Element_ID == jQuery(this).data('conditionalquestionquestion')) {
			if ((Value == jQuery(this).data('conditionalquestionanswer') && jQuery(this).data('conditionalquestionlogic') == 'Equal') || 
				(Value != jQuery(this).data('conditionalquestionanswer') && jQuery(this).data('conditionalquestionlogic') == 'NotEqual')) {
				jQuery(this).addClass('ewd-ufp-hidden');
			}
			else {
				jQuery(this).removeClass('ewd-ufp-hidden');
			}
		}
	});
}