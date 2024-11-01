<?php
/* This function creates the Settings tabs
Return:
$ary_settings_errors - Array of errors in Settings updates
------------------------------ */
function wig_settings_tabs( $current = 'general' ) {
	$tabs = array( 'general' => 'General Settings' , 
			'donate' => 'Donate' );
	echo '<div id="icon-themes" class="icon32"><br/></div>';
	echo '<h2 class="nav-tab-wrapper">';

	foreach( $tabs as $tab => $name ) {
		$class = ( $tab == $current ? ' nav-tab-active' : '' );
		echo '<a class="nav-tab' . $class . '" href="?page=wig_settings&tab=' . $tab . '">' . $name . '</a>';
	}

	echo '</h2>';
}


/* This function creates the settings form
Parameters:
No parameters

Return:
$str_settings_form - The HTML for the Settings form
------------------------------ */
function wig_output_settings_form( $tab = 'general' ) {
	wig_settings_tabs( $tab );

	switch( $tab ) {
		case "general":
			$str_settings_form = wig_general_settings_form();
			break;
		case "donate":
			global $wig_paypal;
			$str_settings_form = $wig_paypal->paypal_donation_form( 'large' , 0 );
			break;
		default:
			$str_settings_form = wig_general_settings_form();
			break;
	}

	return $str_settings_form;
}


/* This function creates the General Settings form
------------------------------ */
function wig_general_settings_form() {
	$wig_options = get_option( 'wig_options' );
	$str_settings_form = '<div class="form-div"><form name="wig_form" method="post" action="' . str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . '"><input type="hidden" name="wig_hidden_settings" value="Y">';

// Pagination field
	$str_settings_form .= '<div class="form-fieldset"><div class="form-fieldset-title">How Many Items Do You Want To Show On Admin Pages?</div>';
	$str_settings_form .= '<div class="form-fieldset-input">';
	$str_settings_form .= '<input type="text" name="wig_pagination" value="' . $wig_options['wig_pagination'] . '" maxlength="10" size="10" />';
	$str_settings_form .= '</div></div>';


// Show Navigation Only From Start field
	$str_settings_form .= '<div class="form-fieldset"><div class="form-fieldset-title">Do You Want To Allow Date Overlap On Destinations?</div>';
	$str_settings_form .= '<div class="form-fieldset-input">';
	$str_settings_form .= '<select name="wig_allow_overlap" onfocus="show_hide_instruction_div(\'inst_navigation\',\'show\');" onblur="show_hide_instruction_div(\'inst_navigation\',\'hide\');">';
		$str_settings_form .= '<option value="0"' . ( $wig_options['wig_allow_overlap'] == 0 ? ' SELECTED' : '' ) . '>No</option>';
		$str_settings_form .= '<option value="1"' . ( $wig_options['wig_allow_overlap'] == 1 ? ' SELECTED' : '' ) . '>Yes</option>';
	$str_settings_form .= '</select>';
	$str_settings_form .= '</div></div>';


// Submit buttons and close form and divs
	$str_settings_form .= '<div class="form-submit"><input type="submit" class="button-primary" name="Update Settings" value="' . __('Update Settings', 'wig_trdom' ) . '" /> <input type="reset" class="button-secondary" name="Reset Form" value="' . __('Reset Form', 'wig_trdom' ) . '" /></div>';
	$str_settings_form .= '</form></div>';

	return $str_settings_form;
}


/* This function validates the settings form
Return:
$ary_settings_errors - Array of errors in Settings updates
------------------------------ */
function wig_validate_settings_form( $ary_settings ) {
	$ary_settings_errors = array();

	// Validate Default Pagination setting
	if ( !is_numeric( $_POST['wig_pagination'] ) || ( $_POST['wig_pagination'] < 1 ) ) {
		array_push( $ary_settings_errors , "The number of items per page must be greater than zero." );
	}

	return $ary_settings_errors;
}
?>