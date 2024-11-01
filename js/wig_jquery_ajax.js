jQuery(document).ready(function () {
	$('#destination_start_date').datetimepicker({
		timeFormat: 'hh:mm tt',
		useLocalTimezone: true,
		hour: 12
	});
	$('#destination_end_date').datetimepicker({
		timeFormat: 'hh:mm tt',
		useLocalTimezone: true,
		hour: 12
	});

	// Run the Update Posts function
	update_posts();

	// Run the Check Destination Dates function
	check_destination_dates();

	// When the Lesson Selector dropdown changes, process the change
	$('#category_selector').change( update_posts );

	// When the Destination Start & End Date fields change, process the change
	$('#destination_start_date').blur( check_destination_dates );
	$('#destination_end_date').blur( check_destination_dates );
});

// This function gets the updated list of Lessons and populates the page_id select box
function update_posts() {
	// Show the processing image
	$('#wig_loading').show();

	// Get the current value of the selector
	var $category_id = $('#category_selector').val();

	// Get the currently selected post
	var $selected_post_id = $('#selected_post_id').val();

	data = {
		action: 'wig_get_posts',
		category_id: $category_id,
		selected_post_id: $selected_post_id
	};

     	$('#post_id').load( ajaxurl , data , function () {
     		// If the selected category is "Post Category:", set the selector to Miscellaneous
     		if ( $category_id == "pc" ) {
			$('#category_selector').val( 1 );
		}

     		// Hide the processing image
		$('#wig_loading').hide();
     	} );

	return false;
}


// This function checks the date field for overlapping dates and nearby dates
function check_destination_dates() {
	// Get the current values of the Start and End dates
	var $start_date = $('#destination_start_date').val();
	var $end_date = $('#destination_end_date').val();

	if ( ( $start_date != "" ) && ( $end_date != "" ) ) {
		// Show the processing image
		$('#wig_dates_loading').show();

		// Get the Destination ID
		var $destination_id = $('#destination_id').val();

		data = {
			action: 'wig_check_dates',
			start_date: $start_date,
			end_date: $end_date,
			destination_id: $destination_id
		};

		$('#wig_date_overlap').load( ajaxurl , data , function () {
			// Hide the processing image
			$('#wig_dates_loading').hide();
		} );
	}

	return false;
}