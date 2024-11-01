<?php
/* Callback function for shortcode [wig]
Parameters:
$atts - An array of attributes, which can contain: 
	display - Accepts: past, present (default), future, all
	show - How many destinations to show if there are more than 1 matching the query?  Default: 1 (Set to 0 for "all")
	display_date_start - The start of the search period (default: 0).  Overrides display_type
	display_date_end - The end of the search period (default: 9999999999)  Overrides display_type
	has_post - Accepts: yes, no, all (default)
	format - Accepts the following placeholders (any text is allowed)
		%n% - The Destination name
		%sd% - The Destination start date
		%sdt% - The Destination start datetime
		%st% - The Destination start time
		%ed% - The Destination end date
		%edt% - The Destination end datetime
		%et% - The Destination end time
		%url%...%/url% - If the Destination is tied to a post, the URL will be linked from any text between these

		Defaults: (URL will not be included if has_post = no)
			%url%%n%%/url% - for display_type = "present" and has_post = yes or all
			%url%%n%%/url% - %sdt% to %edt% - for display_type = "past", "future", or "all" (or a search by date) and has_post = yes or all
				If multiple Destinations are output, a <br/> will be placed after each
	date_format - Accepts all PHP date options: http://php.net/manual/en/function.date.php. Default: M d, Y (ex: Apr 05, 2013)
	time_format - Accepts all PHP time options: http://php.net/manual/en/function.date.php. Default: H:i (ex: 17:36)
	show_error - Show "No destination(s) found." if there are no matching destinations?  Accept 0 (default) or 1.

Return:
This code returns the output HTML for the display options
------------------------------ */
function wig_output ( $atts ) {
	// Array of allowed values for text parameters
	$ary_allowed_values = array(
		'display' => array( 'past' , 'present' , 'future' , 'all' ),
		'has_post' => array( 'yes' , 'no' , 'all' )
		);

	// Get the passed parameters
	extract( shortcode_atts( array(
		'display' => 'present',
		'show' => 1,
		'display_date_start' => 0,
		'display_date_end' => 9999999999,
		'has_post' => 'all',
		'format' => '',
		'date_format' => 'M d, Y',
		'time_format' => 'H:i',
		'show_error' => 0
	), $atts ) );

	// Set defaults if proper format wasn't passed
	if ( array_search( $display , $ary_allowed_values['display'] ) === false )
		$display = 'present';
	if ( !is_numeric( $show ) || ( $show < 0 ) )
		$show = 1;
	if ( !is_numeric( $display_date_start ) || ( $display_date_start < 0 ) )
		$display_date_start = 0;
	if ( !is_numeric( $display_date_end ) || ( $display_date_end < 0 ) )
		$display_date_end = 9999999999;
	if ( array_search( $has_post , $ary_allowed_values['has_post'] ) === false )
		$display = 'all';
	if ( $show_error != 1 )
		$show_error = 0;

	// Ensure formats were passed for the returned string, date, and time
	if ( trim( $format ) == "" ) {
		// If displaying the present destination 
		if ( $display == "present" )
			$format = '%url%%n%%/url%';
		else
			$format = '%url%%n%%/url% - %sdt% to %edt%';
	}
	if ( trim( $date_format ) == "" )
		$date_format = 'M d, Y';
	if ( trim( $time_format ) == "" )
		$time_format = 'H:i';

	// Put the date and time format together for a datetime format
	$date_time_format = $date_format . " " . $time_format;

	// If has_post is "no", remove the %url% and %/url% placeholders
	if ( $has_post == "no" ) {
		$format = str_replace( $format , "%url%" , "" );
		$format = str_replace( $format , "%/url%" , "" );
	}

	// Get the Destination IDs matching the inputs
	$atts['date_search_start'] = $display_date_start;
	$atts['date_search_end'] = $display_date_end;
	$atts['date_search'] = $display;
	$atts['has_post'] = $has_post;
	$atts['limit'] = $show;
	$ary_destination_ids = wig_destination::get_destination_ids( $atts );

	// If an array of Destination IDs was returned
	if ( $ary_destination_ids !== false ) {
		$str_destination_html = '';
		$num_total_destinations = count( $ary_destination_ids );

		// Loop the returned Destinations
		foreach( $ary_destination_ids AS $key => $destination_id ) {
			$destination = new wig_destination( array( 'destination_id' => $destination_id ) );

			// Pull the format into a variable to fill in the placeholders with data
			$destination_html = $format;

			// Replace the placeholders with proper values
			$destination_html = str_replace( '%n%' , $destination->get_destination_name() , $destination_html );
			$destination_html = str_replace( '%sd%' , date( $date_format , $destination->get_destination_start_date() ) , $destination_html );
			$destination_html = str_replace( '%sdt%' , date( $date_time_format , $destination->get_destination_start_date() ) , $destination_html );
			$destination_html = str_replace( '%st%' , date( $time_format , $destination->get_destination_start_date() ) , $destination_html );
			$destination_html = str_replace( '%ed%' , date( $date_format , $destination->get_destination_end_date() ) , $destination_html );
			$destination_html = str_replace( '%edt%' , date( $date_time_format , $destination->get_destination_end_date() ) , $destination_html );
			$destination_html = str_replace( '%et%' , date( $time_format , $destination->get_destination_end_date() ) , $destination_html );

			// If this Destination has no URL, strip the placeholders for URL
			if ( $destination->get_post_url() != "" ) {
				$destination_html = str_replace( '%url%' , '<a href="' . $destination->get_post_url( 'url' ) . '" target="_blank">' , $destination_html );
				$destination_html = str_replace( '%/url%' , '</a>' , $destination_html );
			}
			else {
				$destination_html = str_replace( '%url%' , '' , $destination_html );
				$destination_html = str_replace( '%/url%' , '' , $destination_html );
			}
			
			// Add the updated string to the returned string
			$str_destination_html .= $destination_html . ( $key < ( $num_total_destinations - 1 ) ? '<br/>' : '' );
		}

		return $str_destination_html;
	}
	else if ( $show_error == 1 ) {
		return "No destination(s) found.";
	}
	else {
		return false;
	}
}
?>