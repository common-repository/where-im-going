<?php
// ------------------------------------------------------------------------
// This file declares the pagination class
// ------------------------------------------------------------------------
if ( !class_exists ( 'pagination' ) ) {

class pagination {
	// Declare Attributes - All Attributes are private and use public accessor methods to access/change data
	private $current_page;			// The current page in the pagination
	private $query_string;			// The variable to use in the query string
	private $items_per_page;		// How many items per page?


/* - - - - - - - - - - - - - - - - - - - 
Constructor & Retrieval Functions
- - - - - - - - - - - - - - - - - - - - */
	/* Constructor function to create a new Product instance
	Parameters:
	$atts: Array of parameters.  Accepts the following:
	query_string - OPTIONAL. The string to use in the query string for pagination. Defaults to "pg"
	items_per_page - OPTIONAL. How many items to show per page? Defaults to 20

	Returns:
	No return value
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function __construct( $atts = '' ) {
		// If $atts['query_string'] was passed, set it into $this->query_string
		if ( trim( $atts['query_string'] ) != "" ) {
			$this->query_string = $atts['query_string'];
		}
		// Otherwise, set it to default
		else {
			$this->query_string = "pg";
		}

		// If $atts['items_per_page'] was passed, set it into $this->items_per_page
		if ( is_numeric( $atts['items_per_page'] ) ) {
			$this->items_per_page = $atts['items_per_page'];
		}
		// Otherwise, set it to default
		else {
			$this->items_per_page = 20;
		}

		// Check if $this->query_string isset and is_numeric
		if ( isset( $_REQUEST[$this->query_string] ) && is_numeric( $_REQUEST[$this->query_string] ) ) {
			$this->current_page = $_REQUEST[$this->query_string];
		}
		else {
			$this->current_page = 1;
		}
	}


/* - - - - - - - - - - - - - - - - - - - 
Data Functions
- - - - - - - - - - - - - - - - - - - - */
	/* This function paginates an array of query results, returning the correct set of results based on the current page
	Parameters:
	$ary_results - The array to paginate

	Returns:
	$ary_paginated_results - The subset of $ary_results corresponding to $this->current_page
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function paginate_results( $ary_results ) {
		// If $ary_results has fewer results than $this->items_per_page, return the array as is
		if ( count( $ary_results ) <= $this->items_per_page ) {
			$ary_paginated_results = $ary_results;
		}
		// Otherwise, select the subset for $this->current_page
		else {
			// Get the start of the range
			$num_range_start = ( $this->current_page - 1 ) * $this->items_per_page;

			// Get the end of the range
			$num_range_end = ( $this->current_page * $this->items_per_page );

			// Get the range length
			$num_range_length = $num_range_end - $num_range_start;

			// Get the slice of $ary_results between range start and end
			$ary_paginated_results = array_slice( $ary_results , $num_range_start , $num_range_length );
		}

		return $ary_paginated_results;
	}


	/* This function creates the pagination HTML for Admin display screens.  Uses WP tablenav classes
	Parameters:
	$total_items - The total number of items in the set
	$target_page - OPTIONAL. What page should be loaded when a new page is clicked?  Defaults to the current page, including query variables
	$adjacents - OPTIONAL. How many adjacent pages to current page to show?

	Return:
	$pagination - The HTML string for pagination
	------------------------------ */
	public function pagination_html( $total_items , $target_page = '' , $adjacents = 1 ) {
		// Set Defaults
		if( !$adjacents ) $adjacents = 1;

		// If target page wasn't passed, set $target_page to the current page, including query variables (removing $this->query_string if it exists)
		if( $target_page == "" ) {
			// Remove $this->query_string from the current query string, including &, =, and data
			$reg_ex_pattern = '/&' . $this->query_string . '(\=[^&]*)?(?=&|$)|^' . $this->query_string . '(\=[^&]*)?(&|$)/';
			$query_string = preg_replace( $reg_ex_pattern , '' , $_SERVER['QUERY_STRING'] );

			// Append the query string to the current page
			$target_page = end( explode( "/" , $_SERVER['PHP_SELF'] ) ) . "?" . $query_string;
		}

		$query_var = "&" . $this->query_string . "=";

		//other vars
		$prev = $this->current_page - 1;						//previous page is page - 1
		$next = $this->current_page + 1;						//next page is page + 1
		$lastpage = ceil( $total_items / $this->items_per_page );			//lastpage is = total items / items per page, rounded up.
		$lpm1 = $lastpage - 1;								//last page minus 1

		$range_start = ( ( $this->current_page - 1 ) * $this->items_per_page ) + 1;	// The start of the range for "Showing X to Y of Z"

		// If ( $this->current_page * $this->items_per_page ) is beyond the $total_items, set range end to $total_items
		if ( ( $this->current_page * $this->items_per_page ) > $total_items ) {
			$range_end = $total_items;					// The end of the range for "Showing X to Y of Z"
		}
		// Otherwise, set it normally
		else {
			$range_end = $this->current_page * $this->items_per_page;	// The end of the range for "Showing X to Y of Z"
		}

		/*
			Now we apply our rules and draw the pagination object.
		*/
		$pagination = '';

		if ( $total_items != 0 ) {
			$pagination .= '<div class="tablenav"><div class="tablenav-pages">';
			$pagination .= '<strong>Showing ' . $range_start . ' to ' . $range_end . ' of ' . $total_items . '</strong> &nbsp;&nbsp; ';

			if( $lastpage > 1 ) {
				//previous button
				if ( $this->current_page > 1 )
					$pagination .= '<a href="' . $target_page . $query_var . $prev . '"><< prev</a>';

				//pages
				if ( $lastpage < 7 + ( $adjacents * 2 ) ) {	//not enough pages to bother breaking it up
					for ( $counter = 1; $counter <= $lastpage; $counter++ ) {
						if ( $counter == $this->current_page )
							$pagination .= '<span class="current">' . $counter . '</span>';
						else
							$pagination .= '<a href="' . $target_page . $query_var . $counter . '">' . $counter . '</a>';
					}
				}
				else if( $lastpage >= 7 + ( $adjacents * 2 ) ) {	//enough pages to hide some
					//close to beginning; only hide later pages
					if( $this->current_page < 1 + ( $adjacents * 3 ) ) {
						for ( $counter = 1; $counter < 4 + ( $adjacents * 2 ); $counter++ ) {
							if ( $counter == $this->current_page )
								$pagination .= '<span class="current">' . $counter . '</span>';
							else
								$pagination .= '<a href="' . $target_page . $query_var . $counter . '">' . $counter . '</a>';
						}
						$pagination .= '<span class="elipses">...</span>';
						$pagination .= '<a href="' . $target_page . $query_var . $lpm1 . '">' . $lpm1 . '</a>';
						$pagination .= '<a href="' . $target_page . $query_var . $lastpage . '">' . $lastpage . '</a>';
					}
					//in middle; hide some front and some back
					else if( ( $lastpage - ( $adjacents * 2 ) > $this->current_page ) && ( $this->current_page > ( $adjacents * 2 ) ) ) {
						$pagination .= '<a href="' . $target_page . $query_var . '1">1</a>';
						$pagination .= '<a href="' . $target_page . $query_var . '2">2</a>';
						$pagination .= '<span class="elipses">...</span>';
						for ( $counter = $this->current_page - $adjacents; $counter <= $this->current_page + $adjacents; $counter++ ) {
							if ( $counter == $this->current_page )
								$pagination .= '<span class="current">' . $counter . '</span>';
							else
								$pagination .= '<a href="' . $target_page . $query_var . $counter . '">' . $counter . '</a>';
						}
						$pagination .= '...';
						$pagination .= '<a href="' . $target_page . $query_var . $lpm1 . '">' . $lpm1 . '</a>';
						$pagination .= '<a href="' . $target_page . $query_var . $lastpage . '">' . $lastpage . '</a>';
					}
					//close to end; only hide early pages
					else {
						$pagination .= '<a href="' . $target_page . $query_var . '1">1</a>';
						$pagination .= '<a href="' . $target_page . $query_var . '2">2</a>';
						$pagination .= '<span class="elipses">...</span>';
						for ( $counter = $lastpage - ( 1 + ( $adjacents * 3 ) ); $counter <= $lastpage; $counter++ ) {
							if ( $counter == $this->current_page )
								$pagination .= '<span class="current">' . $counter . '</span>';
							else
								$pagination .= '<a href="' . $target_page . $query_var . $counter . '">' . $counter . '</a>';
						}
					}
				}

				//next button
				if ( $this->current_page < $counter - 1 )
					$pagination .= '<a href="' . $target_page . $query_var . $next . '">next >></a>';
			}
	
			$pagination .= '</div></div>';
		}
		else {
			$pagination .= '<div class="tablenav"><div class="tablenav-pages">';
			$pagination .= '<strong>No items to show</strong> &nbsp;&nbsp; </div></div>';
		}

		return $pagination;
	}
}

}
?>