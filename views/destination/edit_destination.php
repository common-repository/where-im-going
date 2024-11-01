<?php
	// Check to see if this is a form submission.  If so, validate and submit the form
	if ( isset( $_POST['wig_hidden_edit'] ) && ( $_POST['wig_hidden_edit'] == 'Y' ) ) {
		// Create a Destination with the form entry
		$edit_destination_form = new wig_destination_form( $_POST );

		// Check to make sure the Destination is valid
		$ary_errors = $edit_destination_form->check_destination_data();

		// If there are errors, output them
		if ( count( $ary_errors ) > 0 ) {
			global $error_handler;
			echo '<div class="error"><p><strong>' . $error_handler->error_array_to_string( $ary_errors ) . '</strong></p></div>';
		}
		else {
			$obj_destination_update_result = $edit_destination_form->update_destination();

			// If the result isn't an array, the Destination was successfully updated in the database
			if ( !is_array( $obj_destination_update_result ) ) {
				// Output a Success message
				echo '<div class="updated"><p><strong>"' . $edit_destination_form->get_destination_name() . '" was updated successfully.</strong></p></div>';
			}
			// Otherwise, output an error message
			else {
				echo '<div class="error"><p><strong>' . error_array_to_string( $obj_destination_update_result ) . '</strong></p></div>';
			}
		}
	}
	// If this is not a form submission, create a default Destination
	else {
		// Create an instance of an input form
		$atts['destination_id'] = $_REQUEST['destination_id'];
		$edit_destination_form = new wig_destination_form( $atts );
	}
?>

<div class="wrap">
	<?php echo '<div id="icon-tools" class="icon32"></div><h2>' . __( 'Update Destination: "', 'wpcam_trdom' ) . $edit_destination_form->get_destination_name() . '"</h2>'; ?>

	<div class="form-div">
		<form enctype="multipart/form-data" name="wig_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<input type="hidden" name="wig_hidden_edit" value="Y">
			<?php echo $edit_destination_form->output_form_fields( 'edit' ); ?>
			<div class="form-submit"><input type="submit" class="button-primary" name="Update Destination" value="<?php _e('Update Destination', 'wpcam_trdom' ) ?>" /> <input type="reset" class="button-secondary" name="Reset Form" value="<?php _e('Reset Form', 'wpcam_trdom' ) ?>" /> | <a href="admin.php?page=wig_destinations">Return To Destinations</a></div>
		</form>
	</div>
</div>