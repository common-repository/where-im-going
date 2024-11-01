<?php
	// If $_GET['tab'] isn't set or is set and is 'general', validate the settings
	if ( !isset( $_GET['tab'] ) || ( isset( $_GET['tab'] ) && ( $_GET['tab'] == "general" ) ) ) {
		// Check to see if this is a form submission.  If so, validate and submit the form
		if ( isset( $_POST['wig_hidden_settings']) && ( $_POST['wig_hidden_settings'] == 'Y' ) ) {
			// Validate the POST variables
			$ary_settings_errors = wig_validate_settings_form( $_POST );

			// If there are no errors, update the options and output a success message
			if ( count( $ary_settings_errors ) == 0 ) {
				$wig_options = get_option( 'wig_options' );

				$wig_options['wig_pagination'] = $_POST['wig_pagination'];
				$wig_options['wig_allow_overlap'] = $_POST['wig_allow_overlap'];

				update_option( 'wig_options' , $wig_options );

				echo '<div class="updated"><p><strong>Settings updated</strong></p></div>';
			}
			else {
				global $error_handler;
				echo '<div class="error"><p><strong>' . $error_handler->error_array_to_string( $ary_settings_errors ) . '</strong></p></div>';
			}
		}
	}
?>

<div class="wrap">
	<?php 
		if ( isset ( $_GET['tab'] ) )
			echo wig_output_settings_form( $_GET['tab'] ); 
		else
			echo wig_output_settings_form(); 
	?>
</div>