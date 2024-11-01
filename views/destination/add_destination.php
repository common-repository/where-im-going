<?php
	// Check to see if this is a form submission.  If so, validate and submit the form
	if ( isset( $_POST['wig_hidden_add'] ) && ( $_POST['wig_hidden_add'] == 'Y' ) ) {
		// Create a Destination with the form entry
		$add_destination_form = new wig_destination_form( $_POST );

		// Check to make sure the Attachment is valid
		$ary_errors = $add_destination_form->check_destination_data();

		// If there are errors, output them
		if ( count( $ary_errors ) > 0 ) {
			global $error_handler;
			echo '<div class="error"><p><strong>' . $error_handler->error_array_to_string( $ary_errors ) . '</strong></p></div>';
		}
		else {
			$obj_destination_insert_result = $add_destination_form->add_destination_to_database();

			// If the result isn't an array, the Destination was added to the database
			if ( !is_array( $obj_destination_insert_result ) ) {
				// Output a Success message
				echo '<div class="updated"><p><strong>"' . $add_destination_form->get_destination_name() . '" was added successfully.</strong> <input type="button" class="button-primary" name="Edit This Destination" value="' . __('Edit This Destination', 'wig_trdom' ) . '" onClick="parent.location=\'admin.php?page=wig_destinations&action=edit&destination_id=' . $add_destination_form->get_destination_id() . '\'" /></p></div>';

				// Create an instance of an input form
				$add_destination_form = new wig_destination_form();
			}
			// Otherwise, output an error message
			else {
				echo '<div class="error"><p><strong>' . error_array_to_string( $obj_destination_insert_result ) . '</strong></p></div>';
			}
		}
	}
	// If this is not a form submission, create a default Destination
	else {
		// Create an instance of an input form
		$add_destination_form = new wig_destination_form();
	}
?>

<div class="wrap">
	<?php echo '<div id="icon-tools" class="icon32"></div><h2>' . __( 'Add A Destination', 'wig_trdom' ) . '</h2>'; ?>

	<div class="form-div">
		<form enctype="multipart/form-data" name="wig_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<input type="hidden" name="wig_hidden_add" value="Y">
			<?php echo $add_destination_form->output_form_fields( 'add' ); ?>
			<div class="form-submit"><input type="submit" class="button-primary" name="Add Destination" value="<?php _e('Add Destination', 'wig_trdom' ) ?>" /> <input type="reset" class="button-secondary" name="Reset Form" value="<?php _e('Reset Form', 'wig_trdom' ) ?>" /> | <a href="admin.php?page=wig_destinations">Return To Destinations</a></div>
		</form>
	</div>
</div>