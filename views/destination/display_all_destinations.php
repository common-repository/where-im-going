<?php
	// If "message" was passed on the query string, output the correct message
	if ( isset( $_REQUEST['message'] ) && ( $_REQUEST['message'] == "destination-deleted" ) ) {
		echo '<div class="updated"><p><strong>Destination deleted</strong></p></div>';
	}

	$ary_all_destination_ids = wig_destination::get_destination_ids();
	$pagination = new pagination( array( 'items_per_page' => WIG_PAGINATION ) );
	$ary_destination_ids = $pagination->paginate_results( $ary_all_destination_ids );
?>

<div class="wrap">
	<?php echo '<div id="icon-tools" class="icon32"></div><h2>' . __( 'All Destinations', 'wig_trdom' ) . '</h2>'; ?>

	<?php echo $pagination->pagination_html( ( $ary_all_destination_ids !== false ? count( $ary_all_destination_ids ) : 0 ) ); ?>

	<table class="widefat">
		<thead>
			<tr>
				<th width="15%" class="column-title">Destination</th>
				<th width="15%" class="column-title">Start Date</th>
				<th width="15%" class="column-title">End Date</th>
				<th width="25%" class="column-title">URL</th>
				<th width="10%" class="column-title">Status</th>
				<th width="20%" class="column-title">Actions</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th width="15%" class="column-title">Destination</th>
				<th width="15%" class="column-title">Start Date</th>
				<th width="15%" class="column-title">End Date</th>
				<th width="25%" class="column-title">URL</th>
				<th width="10%" class="column-title">Status</th>
				<th width="20%" class="column-title">Actions</th>
			</tr>
		</tfoot>
		<tbody>
	<?php
		// If there are records, output them
		if ( is_array( $ary_destination_ids ) ) {
			foreach ( $ary_destination_ids AS $key => $destination_id ) {
				$atts['destination_id'] = $destination_id;
				$current_destination = new wig_destination( $atts ); ?>

				<tr<?php echo ( $key%2 == 1 ? ' class="even-row"' : '' ); ?>>
				<td><?php echo $current_destination->get_destination_name(); ?></td>
				<td class="center-column"><?php echo date( "D M d, Y H:i a" , $current_destination->get_destination_start_date() ); ?></td>
				<td class="center-column"><?php echo date( "D M d, Y H:i a" , $current_destination->get_destination_end_date() ); ?></td>
				<td class="center-column"><?php echo ( $current_destination->get_post_url() !== false ? $current_destination->get_post_url() : '&nbsp;' ); ?></td>
				
				<?php // Is this in the future, present, or past
				if ( strtotime( date( "Y-m-d H:i:s" ) ) > $current_destination->get_destination_end_date() ) { ?>
					<td class="center-column">Past</td>
				<?php } else if ( strtotime( date( "Y-m-d H:i:s" ) ) < $current_destination->get_destination_start_date() ) { ?>
					<td class="center-column">Future</td>
				<?php } else { ?>
					<td class="center-column">Present</td>
				<?php } ?>
				
				<td class="center-column"><a href="admin.php?page=wig_destinations&action=edit&destination_id=<?php echo $current_destination->get_destination_id(); ?>">Edit</a> | <a href="admin.php?page=wig_destinations&action=delete&destination_id=<?php echo $current_destination->get_destination_id(); ?>">Delete</a></td>
				</tr>
		<?php
			}
		}
		else { ?>
			<tr><td colspan="6">You have not added any destinations yet</td></tr>
		<?php } ?>
		</tbody>
	</table>
	<p class="submit"><input type="button" class="button-primary" name="Add A New Destination" value="<?php _e('Add A New Destination', 'skcm_trdom' ) ?>" onClick="parent.location='admin.php?page=wig_destinations&action=add'" /></p>
	<?php
		global $wig_paypal;
		echo $wig_paypal->paypal_donation_form( 'footer' );
	?>
</div>