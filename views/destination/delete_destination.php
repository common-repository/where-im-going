<?php
	// Check to see if this is a Delete form submission.  If so, delete the Destination
	if( $_POST['wig_hidden_delete'] == 'Y' ) {
		// Delete the Destination
		$obj_destination_update_result = wig_destination::delete_destination( $_POST['num_destination_id'] );

		// Send the user back to the Destinations main page
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=admin.php?page=wig_destinations&message=destination-deleted">';
		exit();
	}
	// If this is not a form submission, get the Destination by $_REQUEST['destination_id']
	else {
		$atts['destination_id'] = $_REQUEST['destination_id'];
		$delete_destination = new wig_destination( $atts );
	}
?>
<div class="wrap">
	<?php echo '<div id="icon-tools" class="icon32"></div><h2>' . __( 'Delete Destination: ', 'wpcam_trdom' ) . $delete_destination->get_destination_name() . '</h2>'; ?>
	<p><strong>Are you sure you want to delete "<?php echo $delete_destination->get_destination_name(); ?>"?</strong></p>
	<p><div class="warning-red">This action cannot be undone.</div></p>
	<div class="form-div">
		<form enctype="multipart/form-data" name="wig_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<input type="hidden" name="wig_hidden_delete" value="Y">
			<input type="hidden" name="num_destination_id" value="<?php echo $delete_destination->get_destination_id(); ?>">
			<div class="form-submit"><input type="submit" class="button-red" name="Delete Destination" value="<?php _e('Delete Destination', 'wpcam_trdom' ) ?>" /> | <a href="admin.php?page=wig_destinations">Return To Destinations</a></div>
		</form>
	</div>
</div>