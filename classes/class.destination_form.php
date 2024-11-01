<?php
// ------------------------------------------------------------------------
// This file declares the wig_destination_form class
// ------------------------------------------------------------------------

class wig_destination_form extends wig_destination {
/* - - - - - - - - - - - - - - - - - - - 
Attributes
- - - - - - - - - - - - - - - - - - - - */


/* - - - - - - - - - - - - - - - - - - - 
Constructor & Retrieval Functions
- - - - - - - - - - - - - - - - - - - - */
	/* Constructor function to create a new Destination Form instance
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function __construct( $atts = '' ) {
		parent::__construct( $atts );
	}


/* - - - - - - - - - - - - - - - - - - - 
Form Field Output Functions
- - - - - - - - - - - - - - - - - - - - */
	/* This function creates the HTML for the form fields for $this
	Returns:
	$str_form_html - HTML for Course form
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function output_form_fields( $form_type = 'add' ) {
		$str_form_html = '<div class="manage-column-left">';
		if ( $form_type == "edit" ) {
			$str_form_html .= $this->form_field_destination_id();
		}
		$str_form_html .= $this->form_field_destination_name();
		$str_form_html .= $this->form_field_destination_start_date();
		$str_form_html .= $this->form_field_destination_end_date();
		$str_form_html .= $this->form_field_category_selector();
		$str_form_html .= $this->form_field_page_id( $form_type );
		$str_form_html .= '</div>';
		$str_form_html .= $this->form_field_overlap_div();
		
		return $str_form_html;
	}

			

	/* This function creates the hidden input for Destination ID
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function form_field_destination_id() {
		$str_form_field_html = '<input type="hidden" name="destination_id" id="destination_id" value="' . $this->get_destination_id() . '">';

		return $str_form_field_html;
	}


	/* This function creates the text box for inputting Destination Name
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function form_field_destination_name() {
		$str_form_field_html = '<div class="form-fieldset"><div class="form-fieldset-title">Where Are You Going? <span class="required-field">*</span></div>';
		$str_form_field_html .= '<div class="form-fieldset-input">';
		$str_form_field_html .= '<input type="text" name="destination_name" value="' . stripslashes_deep( $this->get_destination_name() ) . '" maxlength="100" size="75" tabindex="1"/>';

		$str_form_field_html .= '</div></div>';
		return $str_form_field_html;
	}


	/* This function creates the input field for the Start Date
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function form_field_destination_start_date() {
		$str_form_field_html = '<div class="form-fieldset"><div class="form-fieldset-title">When Are You Getting There? <span class="required-field">*</span></div>';
		$str_form_field_html .= '<div class="form-fieldset-input">';
		$str_form_field_html .= '<input type="text" name="destination_start_date" id="destination_start_date" value="' . ( $this->get_destination_start_date() != 0 ? date( 'm/d/Y h:i a' , $this->get_destination_start_date() ) : '' ) . '" size="40" tabindex="2" />';

		$str_form_field_html .= '</div></div>';
		return $str_form_field_html;
	}


	/* This function creates the input field for the End Date
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function form_field_destination_end_date() {
		$str_form_field_html = '<div class="form-fieldset"><div class="form-fieldset-title">When Are You Leaving? <span class="required-field">*</span></div>';
		$str_form_field_html .= '<div class="form-fieldset-input">';
		$str_form_field_html .= '<input type="text" name="destination_end_date" id="destination_end_date" value="' . ( $this->get_destination_end_date() != 0 ? date( 'm/d/Y h:i a' , $this->get_destination_end_date() ) : '' ) . '" size="40" tabindex="3" />';

		$str_form_field_html .= '</div></div>';
		return $str_form_field_html;
	}


	/* This function creates the select box for picking to load Pages or a Post Category in the Post ID box
	Returns:
	$str_form_field_html
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function form_field_category_selector() {
		$str_form_field_html = '<div class="form-fieldset"><div class="form-fieldset-title">Is This Destination Detailed On A Page Or A Post?  Select the category:</div>';
		$str_form_field_html .= '<div class="form-fieldset-input">';
		$str_form_field_html .= '<select name="category_selector" id="category_selector" onfocus="show_hide_instruction_div(\'inst_page_id\',\'show\');" onblur="show_hide_instruction_div(\'inst_page_id\',\'hide\');">';
		$str_form_field_html .= '<option value="page"' . ( $this->category_selector == "page" ? ' SELECTED' : '' ) . '>Page</option>';
		$str_form_field_html .= '<option value="pc">Post Category:</option>';
		$str_form_field_html .= create_category_select_options ( 0 , 1 , '' , '&nbsp;&nbsp;&nbsp;' , $this->category_selector );
		$str_form_field_html .= '</select>';
		$str_form_field_html .= '<img src="' . admin_url('/images/wpspin_light.gif') . '" class="waiting" id="wig_loading" style="display:none;" />';

		// Output a div with instructions for this field
		$str_form_field_html .=  '<div class="instruction-bubble" id="inst_page_id">If you have a page with details or pictures of this destination, you can link to it here.<div class="instruction-bubble-arrow-border"></div><div class="instruction-bubble-arrow"></div></div>';

		$str_form_field_html .= '</div></div>';
		return $str_form_field_html;
	}


	/* This function creates the select box for picking the Page that is the Course Overview
	Returns:
	$str_form_field_html
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function form_field_page_id( $form_type ) {
		$str_form_field_html = '<div class="form-fieldset"><div class="form-fieldset-title">Which Page or Post Has The Details Of This Destination?</div>';
		$str_form_field_html .= '<div class="form-fieldset-input">';

		// If this is an Edit form, output the current selected page_id in a hidden field
		if ( $form_type == "edit" ) {
			$str_form_field_html .= '<input type="hidden" id="selected_post_id" value="' . $this->get_post_id() . '" />';
		}

		$str_form_field_html .= '<select name="post_id" id="post_id" width="400" style="width: 400px;">';
		$str_form_field_html .= '</select>';

		$str_form_field_html .= '</div></div>';
		return $str_form_field_html;
	}


	/* This function creates the div for outputting date/destination overlap information
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function form_field_overlap_div() {
		$str_form_field_html = '<div class="manage-column-right"><div id="wig_date_overlap"></div>';
		$str_form_field_html .= '<div class="center"><img src="' . WIG_IMAGES_URL . 'ajax-loader.gif" class="waiting" id="wig_dates_loading" style="display:none;" /></div></div>';

		return $str_form_field_html;
	}
}
?>