jQuery(document).ready(function() {
	jQuery('#add-content-before-form').on('click', function() {
		jQuery(this).addClass('ewd-ufp-hidden');
		jQuery('#postdivrich').addClass('displayed');
	});

	jQuery('.ewd-ufp-build-form-header').on('click', function() {
		jQuery('.ewd-ufp-build-form-body').addClass('ewd-ufp-hidden');

		var Display = jQuery(this).data('box');
		jQuery('.ewd-ufp-build-form-body[data-box="' + Display + '"').removeClass('ewd-ufp-hidden');
	});

	jQuery('.ewd-ufp-add-question-submit').on('click', function() {
		EWD_UFP_Add_Question_To_Form();
	});

	EWD_UFP_Set_Question_Button_Handlers();
	EWD_UFP_Set_Question_Edit_Handlers();
	EWD_UFP_Reorder_Form_Elements();
	EWD_UFP_Set_Submission_Handlers();
	EWD_UFP_Set_Icon_Click_Handlers();
	EWD_UFP_Set_Styling_Title_Click_Handlers();
	EWD_UFP_Set_Conditional_Select_Options();
	EWD_UFP_Handle_Submissions_Table();
	EWD_UFP_Form_Templates();
});

function EWD_UFP_Add_Question_To_Form(Question_Type, Question_Title) {
	Question_Type = Question_Type || jQuery('select[name="Question_Types"]').val();
	var Next_Row = jQuery('.ewd-ufp-add-form-element').data('nextcount');
	var Page_Counter = jQuery('.ewd-ufp-page-container').length + 1;
	jQuery('.ewd-ufp-add-form-element').data('nextcount', Next_Row + 1);

	jQuery('.ewd-ufp-page-table:last tbody').append('<div class="ewd-ufp-form-element-placeholder" data-questioncount="' + Next_Row + '">Adding Element...</div>');

	var data = 'Question_Type=' + Question_Type + '&Question_Title=' + Question_Title + '&Row_Counter=' + Next_Row + '&Page_Counter=' + Page_Counter + '&action=ewd_ufp_add_form_element';
    jQuery.post(ajaxurl, data, function(response) {
    	if (Question_Type != 'page_break') {jQuery('.ewd-ufp-form-element-placeholder[data-questioncount="' + Next_Row + '"]').replaceWith(response);}
    	else {
    		jQuery('.ewd-ufp-form-element-placeholder[data-questioncount="' + Next_Row + '"]').replaceWith('');
    		jQuery('#ewd-ufp-form-elements-table tr:last').before(response);
    	}
    	EWD_UFP_Set_Question_Button_Handlers();
		EWD_UFP_Set_Question_Edit_Handlers();
		EWD_UFP_Setup_Spectrum();
		EWD_UFP_Reorder_Form_Elements();
		EWD_UFP_Set_Icon_Click_Handlers();
		EWD_UFP_Set_Styling_Title_Click_Handlers();
		EWD_UFP_Set_Conditional_Select_Options();
    });
}

function EWD_UFP_Set_Question_Button_Handlers() {
	jQuery('.ewd-ufp-question-section-toggle').off();
	jQuery('.ewd-ufp-question-section-toggle').on('click', function() {
		var Row = jQuery(this).data('row');
		jQuery('.ewd-ufp-question-section[data-row="' + Row + '"]').addClass('ewd-ufp-hidden');
		jQuery('.ewd-ufp-question-section-toggle[data-row="' + Row + '"]').removeClass('ewd-ufp-selected');
		jQuery(this).addClass('ewd-ufp-selected');

		var Display = jQuery(this).data('section');
		jQuery('.ewd-ufp-question-section[data-section="' + Display + '"][data-row="' + Row + '"]').removeClass('ewd-ufp-hidden');
	});

	jQuery('.ewd-ufp-build-form-delete-question').off();
	jQuery('.ewd-ufp-build-form-delete-question').on('click', function() {
		jQuery(this).parent().parent().parent().addClass('ewd-ufp-selected-question');
	}); 

	jQuery(".ewd-ufp-build-form-delete-question").confirm({
	    text: "Are you sure you want to delete this element?",
	    title: "Confirmation required",
	    confirmButton: "Yes I am",
	    cancelButton: "No",
	    confirm: function() {
	    	jQuery('.ewd-ufp-selected-question').remove();
	    },
	    cancel: function() {
	    	jQuery('.ewd-ufp-selected-question').removeClass('ewd-ufp-selected-question');
	    }
	});
}

function EWD_UFP_Set_Question_Edit_Handlers() {
	EWD_UFP_Set_Icon_Handlers();
	EWD_UFP_Set_Title_Handlers();
	EWD_UFP_Set_Instructions_Handlers();
	EWD_UFP_Set_Answers_Handlers();
	EWD_UFP_Set_Add_Answer_Handlers();
}

function EWD_UFP_Set_Icon_Handlers() {
	jQuery('.ewd-ufp-build-form-question-icon').off();
	jQuery('.ewd-ufp-build-form-question-icon').on('click', function() {
		var Row = jQuery(this).data('row');

		jQuery('#ewd-ufp-dashicon-selector').data('selectedrow', Row);
		jQuery('#ewd-ufp-dashicon-selector').removeClass('ewd-ufp-hidden');
	});

	jQuery('.ewd-ufp-dashicon').off();
	jQuery('.ewd-ufp-dashicon').on('click', function() {
		var Row = jQuery('#ewd-ufp-dashicon-selector').data('selectedrow');
		var Classes = jQuery('.ewd-ufp-build-form-question-icon[data-row="' + Row + '"]').attr('class').split(' ');

		jQuery(Classes).each(function(index, el) {
			if (el.indexOf('dashicons-') !== -1) {jQuery('.ewd-ufp-build-form-question-icon[data-row="' + Row + '"]').removeClass(el);}
		});

		jQuery('input[name="Element_Icon_' + Row +'"]').val(jQuery(this).data('dashiconclass'));
		jQuery('.ewd-ufp-build-form-question-icon[data-row="' + Row + '"]').addClass(jQuery(this).data('dashiconclass'));
		jQuery('#ewd-ufp-dashicon-selector').data('selectedrow', -1);
		jQuery('#ewd-ufp-dashicon-selector').addClass('ewd-ufp-hidden');
	});
}

function EWD_UFP_Set_Title_Handlers() {
	jQuery('.ewd-ufp-build-form-question-title').off();
	jQuery('.ewd-ufp-build-form-question-title').on('click', function() {
		if (jQuery(this).find('input').length > 0) {return;}
		var Row = jQuery(this).data('row');
		var Value = jQuery('input[name="Element_Title_' + Row + '"]').val();

		if (Value == "Question Title " || Value == "Question Title") {Value = "";}

		jQuery(this).addClass('ewd-ufp-question-edit');
		jQuery(this).html('<input type="text" placeholder="Question" class="ewd-ufp-edit-title" value="' + Value + '">');
		jQuery(this).find('input').focus();
		EWD_UFP_Set_Title_Handlers();
	});

	jQuery('.ewd-ufp-build-form-question-title input').off();
	jQuery('.ewd-ufp-build-form-question-title input').on('focusout', function() {
		var Text = jQuery(this).val();
		var Row = jQuery(this).parent().data('row');

		jQuery(this).parent().removeClass('ewd-ufp-question-edit');
		jQuery(this).parent().html(Text);
		jQuery('input[name="Element_Title_' + Row + '"]').val(Text);

		if (jQuery('input[name="Element_Slug_' + Row + '"]').val() == "") {
			var Space_Less = Text.replace(/ /g, '-');
			var Lower_Case = Space_Less.toLowerCase();
			var Slug = Lower_Case.replace(/[\/\\\[\]|&;$%@"<>()+,^#*{}'!=:?]/g, "");
			jQuery('input[name="Element_Slug_' + Row + '"]').val(Slug);
		}
	});
}

function EWD_UFP_Set_Instructions_Handlers() {
	jQuery('.ewd-ufp-build-form-question-instructions').off();
	jQuery('.ewd-ufp-build-form-question-instructions').on('click', function() {
		if (jQuery(this).find('input').length > 0) {return;}
		var Row = jQuery(this).data('row');
		var Value = jQuery('input[name="Element_Instructions_' + Row + '"]').val();
		
		if (Value == "Question Content " || Value == "Question Content") {Value = "";}

		jQuery(this).addClass('ewd-ufp-question-edit');
		jQuery(this).html('<input type="text" placeholder="Instructions" class="ewd-ufp-edit-title" value="' + Value + '">');
		jQuery(this).find('input').focus();
		EWD_UFP_Set_Instructions_Handlers();
	});

	jQuery('.ewd-ufp-build-form-question-instructions input').off();
	jQuery('.ewd-ufp-build-form-question-instructions input').on('focusout', function() {
		var Text = jQuery(this).val();
		var Row = jQuery(this).parent().data('row');

		jQuery(this).parent().removeClass('ewd-ufp-question-edit');
		jQuery(this).parent().html(Text);
		jQuery('input[name="Element_Instructions_' + Row + '"]').val(Text);
	});
}

function EWD_UFP_Set_Answers_Handlers() {
	/*jQuery('.ewd-ufp-build-form-answer-options').off();
	jQuery('.ewd-ufp-build-form-answer-options').on('click', function() {
		if (jQuery(this).find('input').length > 0) {return;}
		var Row = jQuery(this).data('row');
		var Value = jQuery('input[name="Element_Answer_Options_' + Row + '"]').val();

		jQuery(this).addClass('ewd-ufp-question-edit');
		jQuery(this).html('<input type="text" placeholder="Answers" class="ewd-ufp-edit-title" value="' + Value + '">');
		jQuery(this).find('input').focus();
		EWD_UFP_Set_Answers_Handlers();
	});

	jQuery('.ewd-ufp-build-form-answer-options input').off();
	jQuery('.ewd-ufp-build-form-answer-options input').on('focusout', function() {
		var Text = jQuery(this).val();
		var Row = jQuery(this).parent().data('row');

		jQuery(this).parent().removeClass('ewd-ufp-question-edit');
		jQuery(this).parent().html(Text);
		jQuery('input[name="Element_Answer_Options_' + Row + '"]').val(Text);
	});*/
}

function EWD_UFP_Set_Add_Answer_Handlers() {
	jQuery('.ewd-ufp-add-answer-option').off();
	jQuery('.ewd-ufp-add-answer-option').on('click', function() {
		var Row = jQuery(this).parent().data('row');

		jQuery(this).before("<input type='text' name='Element_Answer_Options_" + Row + "[]' placeholder='Another Option' class='ewd-ufp-edit-answer' /><div class='ewd-ufp-clear'></div>");
	});
}

function EWD_UFP_Reorder_Form_Elements() {
    jQuery("#ewd-ufp-form-elements-table").sortable({
    	items: 'tr.ewd-ufp-page-container',
		opacity: 0.6,
		cursor: 'move',
		axis: 'y',
    	stop: function( event, ui ) {EWD_UFP_Update_Element_Order(); }
    }).disableSelection();

    jQuery("#ewd-ufp-form-elements-table").sortable({
    	items: 'tr.ewd-ufp-form-element',
		opacity: 0.6,
		cursor: 'move',
		axis: 'y',
    	stop: function( event, ui ) {EWD_UFP_Update_Element_Order(); }
    }).disableSelection();
}

function EWD_UFP_Update_Element_Order() {
	jQuery('#ewd-ufp-form-elements-table tr').each(function(index, el) {
		var Counter = jQuery(this).data('questioncount');
		jQuery('input[name="Element_Order_' + Counter + '"]').val(index);
	});
}

function EWD_UFP_Set_Icon_Click_Handlers() {
	jQuery('#ewd-ufp-dashicon-selector').click(function(e){
		if(e.target != this) return;
		jQuery('#ewd-ufp-dashicon-selector').addClass('ewd-ufp-hidden');
	});
}

function EWD_UFP_Set_Styling_Title_Click_Handlers() {
	jQuery('.ewd-ufp-form-question-style-heading').on('click', function(){
		var Question_Count = jQuery(this).data('questioncount');
		var Section = jQuery(this).data('section');
		jQuery('.ewd-ufp-form-question-style-heading[data-questioncount="' + Question_Count + '"][data-section="' + Section + '"]').toggleClass('ewd-ufp-down-caret').toggleClass('ewd-ufp-up-caret');
		jQuery('.ewd-ufp-form-question-style-section[data-questioncount="' + Question_Count + '"][data-section="' + Section + '"]').toggleClass('ewd-ufp-hidden');
	});
}

function EWD_UFP_Set_Conditional_Select_Options() {
	jQuery('.ewd-ufp-page-container').each(function (i, page) {
	    jQuery('.ewd-ufp-page-conditional-pages').append(jQuery('<option>', { 
	        value: i+1,
	        text : i+1 
	    }));
	});

	jQuery(".ewd-ufp-page-conditional-pages option").val(function(idx, val) {
	  jQuery(this).siblings("[value='"+ val +"']").remove(); return val;
	});

	jQuery('.ewd-ufp-conditional-questions').off();
	jQuery('.ewd-ufp-conditional-questions').on('change',function() {
		var Selected_ID = jQuery(this).val();
		var Current_Row = jQuery(this).data('row');
		var Selected_Row = jQuery('.ewd-ufp-question-section[data-elementid="' + Selected_ID + '"]').data('row');
		var Question_Type = jQuery('.ewd-ufp-question-section[data-row="' + Selected_Row + '"]').find('input[name="Element_Type_' + Selected_Row + '"]').val();

		if (Question_Type == 'radio' || Question_Type == 'checkbox' || Question_Type == 'select') {
			jQuery('.ewd-ufp-question-conditional-answers-text[data-row="' + Current_Row + '"]').addClass('ewd-ufp-hidden').val('');
			var Current_Value = jQuery('.ewd-ufp-question-conditional-answers-select[data-row="' + Current_Row + '"]').val();
			jQuery('.ewd-ufp-question-conditional-answers-select[data-row="' + Current_Row + '"]').removeClass('ewd-ufp-hidden').find('option').remove();

			jQuery('.ewd-ufp-question-section[data-row="' + Selected_Row + '"]').find('.ewd-ufp-edit-answers').each(function(index, el) {
				jQuery('.ewd-ufp-question-conditional-answers-select[data-row="' + Current_Row + '"]').append(jQuery('<option>', { 
	    		    value: jQuery(el).val(),
	    		    text : jQuery(el).val() 
	    		}));
			});
			jQuery('.ewd-ufp-question-conditional-answers-select[data-row="' + Current_Row + '"]').val(Current_Value);
		}
		else {
			jQuery('.ewd-ufp-question-conditional-answers-select[data-row="' + Current_Row + '"]').addClass('ewd-ufp-hidden').find('option').remove();
			jQuery('.ewd-ufp-question-conditional-answers-text[data-row="' + Current_Row + '"]').removeClass('ewd-ufp-hidden');
		}
	});

	jQuery('.ewd-ufp-build-form-question-title').each(function (i, title_element) {
	    var row = jQuery(title_element).data('row');
	    var Element_ID = jQuery('input[name="Element_ID_' + row + '"]').val();
	    if (Element_ID != '') {
	    	jQuery('.ewd-ufp-conditional-questions.ewd-ufp-fill-select').append(jQuery('<option>', { 
	    	    value: jQuery('input[name="Element_ID_' + row + '"]').val(),
	    	    text : jQuery(title_element).html() 
	    	}));
	    }
	}); 
	jQuery('.ewd-ufp-conditional-questions.ewd-ufp-fill-select').each(function(index, el) {
		var Selected_Option = '';
		jQuery(this).find('option').each(function(index, el) {
			if (jQuery(el).html() == jQuery(el).attr('value') && jQuery(el).attr('value') != '') {
				Selected_Option = jQuery(el).html();
				jQuery(el).remove();
			}
		});
		if (Selected_Option != '') {
			jQuery(this).val(Selected_Option);
			jQuery(this).trigger('change');
		}
	});
	jQuery('.ewd-ufp-conditional-questions.ewd-ufp-fill-select').removeClass('ewd-ufp-fill-select');

}

jQuery(document).ready(function() {
	SetMessageDeleteHandlers();

	jQuery('.ewd-ufp-add-email').on('click', function(event) {
		var Counter = jQuery(this).data('nextcounter');

		var HTML = "<tr id='ewd-ufp-email-message-" + Counter + "'>";
		HTML += "<td><input type='hidden' name='Form_Email_" + Counter + "' value='Set' /><a class='ewd-ufp-delete-message' data-messagecounter='" + Counter + "'>Delete</a></td>";
		HTML += "<td><input type='checkbox' name='Form_Email_Send_" + Counter + "' value='Yes' checked></td>";
		HTML += "<td><input type='text' name='Form_Email_Send_To_" + Counter + "'></td>";
		HTML += "<td><input type='text' name='Form_Email_Subject_" + Counter + "'></td>";
		HTML += "<td><textarea name='Form_Email_Message_" + Counter + "'></textarea></td>";
		HTML += "</tr>";

		//jQuery('table > tr#ewd-ufp-add-reminder').before(HTML);
		jQuery('#ewd-ufp-email-messages-table tr:last').before(HTML);

		Counter++;
		jQuery(this).data('nextcounter', Counter); //updates but doesn't show in DOM

		SetMessageDeleteHandlers();

		event.preventDefault();
	});
});

function SetMessageDeleteHandlers() {
	jQuery('.ewd-ufp-delete-message').on('click', function(event) {
		var ID = jQuery(this).data('messagecounter');
		var tr = jQuery('#ewd-ufp-email-message-'+ID);

		tr.fadeOut(400, function(){
            tr.remove();
        });

		event.preventDefault();
	});
}

function EWD_UFP_Set_Submission_Handlers() {
	jQuery('.ewd-ufp-download-submissions').on('click', function(event) {
		event.preventDefault();

		var Form_ID = jQuery(this).data('formid');

		window.location='admin.php?Action=EWD_UFP_Export_To_Excel&Form_ID=' + Form_ID;
	});

	jQuery('.ewd-ufp-clear-submissions').on('click', function(event) {
		event.preventDefault();

		var Form_ID = jQuery(this).data('formid');

		var data = 'Form_ID=' + Form_ID + '&action=ewd_ufp_clear_submissions';
    	jQuery.post(ajaxurl, data, function(response) {
    		jQuery('.ewd-ufp-clear-submissions').parent().append('<span class="ewd-ufp-notice">Submissions have been deleted</span>');
    	});
	});
}

function EWD_UFP_Handle_Submissions_Table() {
	jQuery('.ewd-ufp-pagination-first-page').on('click', function() {
		jQuery('.ewd-ufp-submissions-table').data('submissioncounter', 0);

		EWD_UFP_Update_Submissions_Table();
	});

	jQuery('.ewd-ufp-previous-page').on('click', function() {
		var Per_Page = jQuery('.ewd-ufp-submissions-table').data('perpage');
		var Current_Counter = jQuery('.ewd-ufp-submissions-table').data('submissioncounter');
		jQuery('.ewd-ufp-submissions-table').data('submissioncounter', Math.max(0, Current_Counter - Per_Page));

		EWD_UFP_Update_Submissions_Table();
	});

	jQuery('.ewd-ufp-next-page').on('click', function() {
		var Per_Page = jQuery('.ewd-ufp-submissions-table').data('perpage');
		var Current_Counter = jQuery('.ewd-ufp-submissions-table').data('submissioncounter');
		var Max_Page = jQuery('.ewd-ufp-submissions-table').data('maxpage');
		jQuery('.ewd-ufp-submissions-table').data('submissioncounter', Math.min((Max_Page - 1) * Per_Page, Current_Counter + Per_Page));

		EWD_UFP_Update_Submissions_Table();
	});

	jQuery('.ewd-ufp-pagination-max-page').on('click', function() {
		var Per_Page = jQuery('.ewd-ufp-submissions-table').data('perpage');
		var Max_Page = jQuery('.ewd-ufp-submissions-table').data('maxpage');
		jQuery('.ewd-ufp-submissions-table').data('submissioncounter', Per_Page * (Max_Page - 1));

		EWD_UFP_Update_Submissions_Table();
	});

	EWD_UFP_Update_Submissions_Table();
}

function EWD_UFP_Update_Submissions_Table() {
	var Submission_Counter = jQuery('.ewd-ufp-submissions-table').data('submissioncounter');
	var Form_ID = jQuery('.ewd-ufp-submissions-table').data('formid');
	var Per_Page = jQuery('.ewd-ufp-submissions-table').data('perpage');
	var Column_Element_IDs = jQuery('.ewd-ufp-submissions-table tbody').data('columnelementids');
	var Max_Page = jQuery('.ewd-ufp-submissions-table').data('maxpage');

	Page_Count = Math.ceil((Submission_Counter + 1) / Per_Page);

	jQuery('.ewd-ufp-submissions-table tbody').html('');

	var data = 'Form_ID=' + Form_ID + '&Submission_Counter=' + Submission_Counter + '&Per_Page=' + Per_Page + '&Column_Element_IDs=' + Column_Element_IDs + '&action=ewd_ufp_get_submissions';
    jQuery.post(ajaxurl, data, function(response) {
    	jQuery('.ewd-ufp-submissions-table tbody').html(response);
    	jQuery('.ewd-ufp-pagination-page-count').html("Page " + Page_Count + " of " + Max_Page)
    });
}

function EWD_UFP_Form_Templates() {
	jQuery('.ewd-ufp-template').on('click', function() {
		jQuery('.ewd-ufp-form-templates').addClass('ewd-ufp-hidden');
		var Form_Type = jQuery(this).data('template');

		var Elements = EWD_UFP_Get_Template_Elements(Form_Type);
		jQuery(Elements).each(function(index, el) {
			EWD_UFP_Add_Question_To_Form(el.type, el.title);
		});
	});
}

function EWD_UFP_Get_Template_Elements(Form_Type) {
	switch(Form_Type) {
		case 'contact_us':
			var Elements = [
				{type: 'text', title: 'Name'},
				{type: 'email', title: 'Email'},
				{type: 'text', title: 'Subject'},
				{type: 'textarea', title: 'Message'}
			];
			break;
		case 'email_sign_up':
			var Elements = [
				{type: 'text', title: 'Name'},
				{type: 'email', title: 'Email'}
			];
			break;
		case 'service_request':
			var Elements = [
				{type: 'text', title: 'Name'},
				{type: 'email', title: 'Email'},
				{type: 'tel', title: 'Phone Number'},
				{type: 'text', title: 'Service Requested'},
				{type: 'textarea', title: 'Problem'}
			];
			break;
		case 'contact_info':
			var Elements = [
				{type: 'text', title: 'Name'},
				{type: 'email', title: 'Email'},
				{type: 'tel', title: 'Phone Number'},
				{type: 'text', title: 'Address Line One'},
				{type: 'text', title: 'Address Line Two'},
				{type: 'text', title: 'City'},
				{type: 'text', title: 'Country'}
			];
			break;
		case 'reservation':
			var Elements = [
				{type: 'text', title: 'Name'},
				{type: 'email', title: 'Email'},
				{type: 'tel', title: 'Phone Number'},
				{type: 'text', title: 'Location'},
				{type: 'text', title: 'Date'},
				{type: 'text', title: 'Time'}
			];
			break;
		case 'product_inquiry':
			var Elements = [
				{type: 'text', title: 'Name'},
				{type: 'email', title: 'Email'},
				{type: 'tel', title: 'Phone Number'},
				{type: 'text', title: 'Product Name'},
				{type: 'number', title: 'Quantity Requested'}
			];
			break;
		default:
			var Elements = [];
			break;
	}

	return Elements
}

/* Used to show and hide the admin tabs for UFP */
function ShowTab(TabName) {
		jQuery(".OptionTab").each(function() {
				jQuery(this).addClass("HiddenTab");
				jQuery(this).removeClass("ActiveTab");
		});
		jQuery("#"+TabName).removeClass("HiddenTab");
		jQuery("#"+TabName).addClass("ActiveTab");
		
		jQuery(".nav-tab").each(function() {
				jQuery(this).removeClass("nav-tab-active");
		});
		jQuery("#"+TabName+"_Menu").addClass("nav-tab-active");
}


function ShowOptionTab(TabName) {
	jQuery(".ufp-option-set").each(function() {
		jQuery(this).addClass("ufp-hidden");
	});
	jQuery("#"+TabName).removeClass("ufp-hidden");

	jQuery(".options-subnav-tab").each(function() {
		jQuery(this).removeClass("options-subnav-tab-active");
	});
	jQuery("#"+TabName+"_Menu").addClass("options-subnav-tab-active");
}

jQuery(document).ready(function() {
	EWD_UFP_Setup_Spectrum();

	jQuery('.ufp-spectrum').each(function() {
		if (jQuery(this).val() != "") {
			jQuery(this).css('background', jQuery(this).val());
			var rgb = EWD_UFP_hexToRgb(jQuery(this).val());
			var Brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
			if (Brightness < 100) {jQuery(this).css('color', '#ffffff');}
			else {jQuery(this).css('color', '#000000');}
		}
	});
});

function EWD_UFP_Setup_Spectrum() {
	jQuery('.ufp-spectrum').spectrum({
		showInput: true,
		showInitial: true,
		preferredFormat: "hex",
		allowEmpty: true
	});

	jQuery('.ufp-spectrum').css('display', 'inline');

	jQuery('.ufp-spectrum').on('change', function() {
		if (jQuery(this).val() != "") {
			jQuery(this).css('background', jQuery(this).val());
			var rgb = EWD_UFP_hexToRgb(jQuery(this).val());
			var Brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
			if (Brightness < 100) {jQuery(this).css('color', '#ffffff');}
			else {jQuery(this).css('color', '#000000');}
		}
		else {
			jQuery(this).css('background', 'none');
		}
	});
}

function EWD_UFP_hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}