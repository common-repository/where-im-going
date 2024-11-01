<?php
/* This function returns the Posts or Pages to show in the Lessons dropdown box based on POST parameters
$_POST['category_id'] - The selected category or "page"
$_POST['selected_post_id'] - The ID of a Page or Post that has already been chosen
------------------------------ */
function ajax_get_posts() {
	global $wpdb;

	// Retrieve the POST variables into local variables
	if ( $_POST['category_id'] == "pc" )
		$category_id = 1;
	else
		$category_id = $_POST['category_id'];

	if ( $_POST['selected_post_id'] == "" )
		$selected_post_id = "";
	else
		$selected_post_id = $_POST['selected_post_id'];

	// If the $category_id is "page", get the list of Pages
	if ( $category_id == "page" ) {
		// Get the Wordpress Pages
		$ary_pages = get_pages( array( 'hierarchical' => 0 , 'exclude' => $lst_page_ids ) );

		// Insert the first value
		$str_select_options = '<option value=""> - Select A Page - </option>';

		// If $selected_post_id is not blank, get the name of the Page/Post and insert an option with it
		if ( is_numeric( $selected_post_id ) && ( $selected_post_id != 0 ) ) {
			$str_select_options .= '<option value="' . $selected_post_id . '" SELECTED>' . get_the_title( $selected_post_id ) . ' (Currently Selected)</option>';
		}

		// Loop the returned array of Pages
		foreach( $ary_pages AS $page ) {
			$str_select_options .= '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
		}
	}
	else {
		// Get the Wordpress Pages
		$ary_posts = get_posts( array( 'category' => $category_id , 'exclude' => $lst_page_ids , 'numberposts' => -1 ) );

		// Insert the first value
		$str_select_options = '<option value=""> - Select A Post - </option>';

		// If $selected_post_id is not blank, get the name of the Page/Post and insert an option with it
		if ( is_numeric( $selected_post_id ) && ( $selected_post_id != 0 ) ) {
			$str_select_options .= '<option value="' . $selected_post_id . '" SELECTED>' . get_the_title( $selected_post_id ) . ' (Currently Selected)</option>';
		}

		// Loop the returned array of Pages
		foreach( $ary_posts AS $post ) {
			$str_select_options .= '<option value="' . $post->ID . '">' . $post->post_title . '</option>';
		}
	}

	// Echo the Options back to the AJAX caller
	echo $str_select_options;

	exit();
}


/* This function returns the dates around the Destination Start and End dates, flagging any overlaps
$_POST['start_date'] - The Destination Start Date
$_POST['end_date'] - The Destination End Date
------------------------------ */
function ajax_check_dates() {
	global $wpdb;

	// Retrieve the POST variables into local variables
	$start_date = strtotime( $_POST['start_date'] );
	$end_date = strtotime( $_POST['end_date'] );
	$destination_id = $_POST['destination_id'];
	//  bool checkdate ( int $month , int $day , int $year )

	// Calculate 10 days before and 10 days after each date
	$start_date_pre = strtotime( "-10 days" , $start_date );
	$start_date_post = strtotime( "+10 days" , $start_date );
	$end_date_pre = strtotime( "-10 days" , $end_date );
	$end_date_post = strtotime( "+10 days" , $end_date );

	// If both $start_date and $end_date have values, retrieve Destinations around them
	if ( ( $start_date != "" ) && ( $end_date != "" ) ) {
		$qry_destinations = $wpdb->get_results( $wpdb->prepare(
			"SELECT destination_name, destination_start_date, destination_end_date
			FROM " . WIG_TABLE_NAME . " 
			WHERE ( destination_start_date BETWEEN " . $start_date_pre . " AND " . $start_date_post . "
			OR destination_end_date BETWEEN " . $start_date_pre . " AND " . $start_date_post . "
			OR destination_start_date BETWEEN " . $end_date_pre . " AND " . $end_date_post . "
			OR destination_end_date BETWEEN " . $end_date_pre . " AND " . $end_date_post . "
			OR ( "  . $start_date . " < destination_start_date AND " . $end_date . " > destination_end_date ) )" . 
			( $destination_id != "" ? " AND destination_id != " . $destination_id : "" ) . "
			ORDER BY destination_start_date ASC, destination_end_date ASC" , "" ) );

		if ( count( $qry_destinations ) > 0 ) {
			$str_destinations = '<h3>Other Places You\'re Going Around This Time</h3>';
			foreach( $qry_destinations AS $key => $destination ) {
				// If there's a date conflict, flag this Destination on output
				if ( ( ( $start_date >= $destination->destination_start_date ) && ( $start_date <= $destination->destination_end_date ) ) 
					|| ( ( $end_date >= $destination->destination_start_date ) && ( $end_date <= $destination->destination_end_date ) ) 
					|| ( ( $start_date <= $destination->destination_start_date ) && ( $end_date >= $destination->destination_end_date ) ) ) {
					$date_conflict = 1;
					$str_destinations .= '<span class="date_conflict">';
				}

				// Output the Destination data
				$str_destinations .= '<div class="date_check center"><strong>' . $destination->destination_name . '</strong><br/>' . date( "M d, Y h:i a" , $destination->destination_start_date ) . " to " . date( "M d, Y h:i a" , $destination->destination_end_date ) . '</div>';

				// If there was a date conflict, close the span
				if ( $date_conflict == 1 )
					$str_destinations .= '</span>';
			}
		}
	}

	// Echo the Destinations back to the AJAX caller
	echo $str_destinations;

	exit();
}
?>