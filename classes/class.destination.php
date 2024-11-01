<?php
// ------------------------------------------------------------------------
// This file declares the wig_destination class
// ------------------------------------------------------------------------

class wig_destination {
	// Declare Attributes
	protected $destination_id;		// The ID of the Destination
	protected $destination_name;		// The name of the Destination
	protected $destination_start_date;	// The start datetime for this Destination
	protected $destination_end_date;	// The end datetime for this Destination
	protected $destination_post_url;	// The URL of the post for this Destination
	protected $post_id;			// The ID of the post tied to this Destination
	protected $destination_checked;		// Has the data in the Destination been checked for validity before database insert?


/* - - - - - - - - - - - - - - - - - - - 
Constructor & Retrieval Functions
- - - - - - - - - - - - - - - - - - - - */
	/* Constructor function to create a new Destination instance
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function __construct( $atts = '' ) {
		// If there is a Destination ID, get the Destination information from the database
		if ( ( trim( $atts['destination_id'] ) != "" ) && is_numeric( $atts['destination_id'] ) && ( trim( $atts['destination_name'] ) == "" ) ) {
			global $wpdb;

			// Get the record matching $atts['destination_id']
			$qry_get_destination = $wpdb->get_row( $wpdb->prepare( 
				"SELECT *
				FROM " . WIG_TABLE_NAME . "
				WHERE destination_id = %d" , $atts['destination_id'] ) );

			// If an Ad was found, set it into $this
			if ( $qry_get_destination != NULL ) {
				$this->destination_id = $qry_get_destination->destination_id;
				$this->destination_name = $qry_get_destination->destination_name;
				$this->destination_start_date = $qry_get_destination->destination_start_date;
				$this->destination_end_date = $qry_get_destination->destination_end_date;
				$this->post_id = $qry_get_destination->post_id;
				$this->destination_checked = 0;
			}
			else {
				echo "The Destination ID is invalid";
			}
		}
		// If $atts were passed, but Destination ID wasn't passed, create a Destination with the passed values
		else if ( is_array( $atts ) ) {
			$this->destination_id = $atts['destination_id'];
			$this->destination_name = $atts['destination_name'];
			$this->destination_start_date = strtotime( $atts['destination_start_date'] );
			$this->destination_end_date = strtotime( $atts['destination_end_date'] );
			$this->post_id = $atts['post_id'];
			$this->destination_checked = 0;
		}
		// Otherwise, create a Destinationwith defaults
		else {
			$this->destination_id = "";
			$this->destination_name = "";
			$this->destination_start_date = "";
			$this->destination_end_date = "";
			$this->post_id = "";
			$this->destination_checked = 0;
		}
	}


	/* This function returns all Destination IDs
	Parameters:
	$atts - Array of parameters
		$date_search_start - OPTIONAL.  Datetime for beginning of custom date range. Overrides $date_search
		$date_search_end - OPTIONAL.  Datetime for end of custom date range.  Overrides $date_search
		$date_search - OPTIONAL.  Accepts "past","present","future","all".  Defaults to "all"
		$has_post - OPTIONAL. Accepts "yes","no","all".  Defaults to "all"
		$limit - OPTIONAL. How many destinations to show if there are more than 1 matching the query?  Default: 0 (Set to 0 for "all")	

	Returns:
	An array of Destination IDs
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public static function get_destination_ids( $atts = '' ) {
		global $wpdb;

		// Get the parameters
		extract( shortcode_atts( array(
			'date_search_start' => 0,
			'date_search_end' => 9999999999,
			'date_search' => 'all',
			'has_post' => 'all',
			'limit' => 0
		), $atts ) );

		// Default the WHERE clause
		$str_where_clause = ' WHERE 1 = 1';

		// If date_search_start and date_search_end haven't been set, check date_search
		if ( ( $date_search_start == 0 ) && ( $date_search_end == 9999999999 ) ) {
			// If date_search is "past", set the WHERE clause to get Destinations that are in the past
			if ( $date_search == "past" ) {
				$str_where_clause .= ' AND destination_end_date < ' . time();
			}
			// If date_search is "future", set the WHERE clause to get Destinations that are in the future
			else if ( $date_search == "future" ) {
				$str_where_clause .= ' AND destination_start_date > ' . time();
			}
			// If date_search is "present", set the WHERE clause to get Destinations that are happening now
			else if ( $date_search == "present" ) {
				$str_where_clause .= ' AND destination_start_date < ' . time() . ' AND destination_end_date > ' . time();
			}
			// Otherwise, no WHERE clause needed
			else {
				$str_where_clause .= '';
			}
		}
		// Set the WHERE clause to find Destinations between the start and end search date
		else {
			$str_where_clause .= ' AND destination_start_date < ' . $date_search_start . ' AND destination_end_date > ' . $date_search_end;
		}

		// If has_post is yes or no, append the WHERE clause
		if ( $has_post == "yes" )
			$str_where_clause .= ' AND ( post_id IS NOT NULL AND post_id != "" )';
		else if ( $has_post == "no" )
			$str_where_clause .= ' AND ( post_id IS NULL OR post_id == "" )';

		// If a $limit has been set, create the LIMIT clause
		if ( $limit != 0 ) 
			$str_limit_clause = ' LIMIT ' . $limit;
		else
			$str_limit_clause = '';

		// Get all Destination IDs matching WHERE clause
		$qry_get_destinations = $wpdb->get_results( $wpdb->prepare( 
			"SELECT destination_id
			FROM " . WIG_TABLE_NAME . $str_where_clause . "
			ORDER BY destination_end_date DESC" . $str_limit_clause , "" ) );

		// If Destination IDs were found, push them into an array and return the array
		if ( count( $qry_get_destinations ) > 0 ) {
			$ary_destination_ids = array();
			foreach( $qry_get_destinations AS $destination ) {
				array_push( $ary_destination_ids , $destination->destination_id );
			}

			// Return the array
			return $ary_destination_ids;
		}
		// If no Destination IDs were found, return false
		else {
			return false;
		}
	}


/* - - - - - - - - - - - - - - - - - - - 
Add, Update, & Delete Destination Functions
- - - - - - - - - - - - - - - - - - - - */
	/* This function inserts $this Destinationinto the database
	Returns:
	$num_destination_id - The DestinationID from the database insert
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function add_destination_to_database() {
		// Make sure check_destination_data has been run successfully
		if ( $this->destination_checked != 1 ) {
			return "Please run the check_destination_data function before adding this Destination to the database.";
		}
		else {
			// Add $this to the database
			global $wpdb;

			// Find any rows with the same Destination Name
			$qry_insert_destination = $wpdb->insert(
							WIG_TABLE_NAME,
							array(
								'destination_name' => stripslashes_deep( trim( $this->destination_name ) ),
								'destination_start_date' => $this->destination_start_date,
								'destination_end_date' => $this->destination_end_date,
								'post_id' => $this->post_id
							),
							array(
								'%s',
								'%d',
								'%d',
								'%d'
							)
						);
			$this->destination_id = $wpdb->insert_id;

			// Return Destination ID
			return $this->destination_id;
		}
	}

	/* This function updates $this Destinationafter running check_destination_data to ensure data is valid
	Parameters:
	No parameters

	Returns:
	True if update was successful, false if not.
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function update_destination() {
		// Make sure check_destination_data has been run successfully
		if ( $this->destination_checked != 1 ) {
			return "Please run the check_destination_data function before attemping to update this Destination.";
		}
		// Otherwise, there are no errors
		else {
			global $wpdb;

			// Update the Destination
			$qry_update_destination = $wpdb->update(
							WIG_TABLE_NAME,
							array(
								'destination_name' => stripslashes_deep( trim( $this->destination_name ) ),
								'destination_start_date' => $this->destination_start_date,
								'destination_end_date' => $this->destination_end_date,
								'post_id' => $this->post_id
							),
							array( 'destination_id' => $this->destination_id ),
							array(
								'%s',
								'%d',
								'%d',
								'%d'
							),
							array( '%d' )
						);

			// If the Destination was successfully updated, return true
			if( $qry_update_destination > 0 ) {
				return true;
			}
			else {
				return false;
			}
		}
	}

	/* This function deletes a Destination(and associated DestinationKeys and Checks) based on a DestinationID
	Parameters:
	$num_destination_id - Numeric destination ID

	Returns:
	True
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public static function delete_destination( $num_destination_id ) {
		// Ensure DestinationID is numeric
		if( !is_numeric( $num_destination_id ) ) {
			return "Destination ID must be numeric.";
		}

		global $wpdb;

		// Delete the Destination
		$qry_delete_destination = $wpdb->query( $wpdb->prepare( 
			"DELETE FROM " . WIG_TABLE_NAME . "
			WHERE destination_id = %d" , $num_destination_id ) );

		return true;
	}

/* - - - - - - - - - - - - - - - - - - - 
Data Check & Correction Functions
- - - - - - - - - - - - - - - - - - - - */
	/* This function checks the values in $this Attributes and returns errors, if there are any
	Returns:
	$ary_data_errors - Array of errors, one error per element
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function check_destination_data() {
		$ary_data_errors = array();

		// Ensure Destination Name has been entered
		if ( trim( $this->destination_name ) == "" ) {
			$ary_data_errors[] = "Please enter the name of your Destination.";
		}

		// Verify dates are entered and end date is after start date
		if ( !is_numeric( $this->destination_start_date ) || ( $this->destination_start_date == "" ) ) {
			$ary_data_errors[] = "Please enter the date and time you'll arrive.";
		}
		else if ( !is_numeric( $this->destination_end_date ) || ( $this->destination_end_date == "" ) ) {
			$ary_data_errors[] = "Please enter the date and time you'll leave.";
		}
		else if ( $this->destination_start_date > $this->destination_end_date ) {
			$ary_data_errors[] = "You can't leave before you arrive. Your departure date must be after your arrival date.";
		}

		// If WIG_ALLOW_OVERLAP is No, ensure there is no date overlap
		if ( WIG_ALLOW_OVERLAP == 0 ) {
			$check_date_overlap = $this->check_date_overlap();

			if ( $check_date_overlap !== true ) {
				foreach ( $check_date_overlap AS $overlap ) {
					$ary_data_errors[] = $overlap;
				}
			}
		}

		if ( count( $ary_data_errors ) > 0 ) {
			$this->destination_checked = 0;
		}
		else {
			$this->destination_checked = 1;
		}

		return $ary_data_errors;
	}


	/* This function ensures there is no date overlap for a $this start and end date
	Returns:
	True or array detailing overlap
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function check_date_overlap() {
		global $wpdb;

		// Get any Destinations with an overlapping start or end date
		$qry_date_overlap = $wpdb->get_results( $wpdb->prepare(
			"SELECT destination_id
			FROM " . WIG_TABLE_NAME . "
			WHERE " . ( $this->destination_id != "" ? ' destination_id != ' . $this->destination_id : ' destination_id IS NOT NULL ' ) . " AND ( " . 
				$this->destination_start_date . " BETWEEN destination_start_date AND destination_end_date 
				OR " . 
				$this->destination_end_date . " BETWEEN destination_start_date AND destination_end_date  
				OR ( " . 
					$this->destination_start_date . " < destination_start_date 
					AND " . 
					$this->destination_end_date . " > destination_end_date 
				) 
			)" , "" ) );

		// If there are results, we have a date overlap
		if ( $wpdb->num_rows ) {
			$ary_data_errors = array();

			foreach( $qry_date_overlap AS $overlap_destination_id ) {
				$atts['destination_id'] = $overlap_destination_id->destination_id;
				$overlap_destination = new wig_destination( $atts );

				$ary_data_errors[] = "You'll be in " . $overlap_destination->get_destination_name() . " from " . date( "D M d, Y h:i a" , $overlap_destination->get_destination_start_date() ) . " to " . date( "D M d, Y h:i a" , $overlap_destination->get_destination_end_date() ) . ".";
			}

			return $ary_data_errors;
		}
		else {
			return true;
		}
	}


/* - - - - - - - - - - - - - - - - - - - 
Attribute Access Functions
- - - - - - - - - - - - - - - - - - - - */
	/* These functions return a single Attribute for $this Destination
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function get_destination_id() {
		return $this->destination_id;
	}
	public static function get_destination_name_by_id( $destination_id ) {
		// Ensure Destination ID is numeric
		if ( !is_numeric( $destination_id ) ) {
			return "Destination ID must be numeric";
		}
		else {
			global $wpdb;
			$qry_get_destination_name = $wpdb->get_row( $wpdb->prepare( 
				"SELECT destination_name
				FROM " . WIG_TABLE_NAME . "
				WHERE destination_id = %d" , $destination_id ) );

			if ( $qry_get_destination_name != NULL ) {
				return $qry_get_destination_name->destination_name;
			}
			else {
				return "There is no Destination matching that ID.";
			}
		}
	}
	public function get_destination_name() {
		return $this->destination_name;
	}
	public function get_destination_start_date() {
		return $this->destination_start_date;
	}
	public function get_destination_end_date() {
		return $this->destination_end_date;
	}
	public function get_post_id() {
		return $this->post_id;
	}
	/* This function returns the entire link HTML for the associated Post URL (or just the URL based on $display)
	Parameters:
	$display - What do you want displayed to the user on the screen? Accepts: url, title (default - the post title), short (the post slug), long (the entire URL), or custom text
	$display_length - How many characters to display.  0 = all, Default = 25
	$display_from - Which end of the text string to display the characters from. Accepts: front, back, or split (half from front, half from back)
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function get_post_url( $display = 'title' , $display_length = '25' , $display_from = 'split' ) {
		// If there is a Post ID tied to this Destination, continue
		if ( is_numeric( $this->post_id ) && ( $this->post_id != 0 ) ) {
			// Validate parameters for valid input
			if ( !is_numeric( $display_length ) )
				$display_length = 25;
			if ( ( $display_from != "front" ) && ( $display_from != "back" ) )
				$display_from = 'split';

			global $wpdb;

			// Get the Post Permalink
			$post_url = get_permalink( $this->post_id );
			// If $display is "url", return the URL
			if ( $display == "url" )
				return $post_url;
			else {
				// Build the HTML
				$destination_link = '<a href="' . $post_url . '" target="_blank">';
	
				// If $display is "short", get just the URL slug
				if ( $display == "short" ) {
					$qry_get_post_slug = $wpdb->get_row( $wpdb->prepare(
						"SELECT post_name
						FROM " . $wpdb->posts . "
						WHERE ID = %d" , $this->post_id ) );
		
					$display_string = $qry_get_post_slug->post_name;
				}
				// If $display is "long", output the entire URL
				else if ( $display == "long" ) {
					$display_string = $post_url;		
				}
				// If $display is "title", get the Post title and output it
				else if ( $display == "title" ) {
					$qry_get_post_title = $wpdb->get_row( $wpdb->prepare(
						"SELECT post_title
						FROM " . $wpdb->posts . "
						WHERE ID = %d" , $this->post_id ) );
		
					$display_string = $qry_get_post_title->post_title;
				}
				// Otherwise, output whatever was passed in $display
				else {
					$display_string = $display;
				}
	
				// If the string is longer than the $display_length, shorten it based on $display_length and $display_from
				if ( strlen( $display_string ) > $display_length ) {
					// If $display_from = "split", get characters from front and back of string
					if ( $display_from == "split" ) {
						$characters_from_front = ceil( $display_length / 2 );
						$characters_from_back = floor( $display_length / 2 );
		
						$display_string = substr( $display_string , 0 , $characters_from_front ) . '...' . substr( $display_string , -$characters_from_back );
					}
					// If $display_from = "front", get characters from front and back of string
					else if ( $display_from == "front" ) {
						$display_string = substr( $display_string , 0 , $display_length ) . '...';
					}
					else {
						$display_string = '...' . substr( $display_string , -$display_length );
					}
				}
	
				$destination_link .= $display_string . '</a>';
	
				return $destination_link;
			}
		}
		else {
			return false;
		}
	}
}
?>