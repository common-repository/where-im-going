<?php
/* This function creates the Select box options for outputting a category hierarchy from the root.
Parameters:
$num_parent = The parent ID used search for Children
$num_depth = Current depth in the hierarchy
$lst_exclude - The list of IDs to be excluded from the Select box.
$str_delimeter - The delimiter to insert before sub-categories
$selected - The selected option
*/
if ( !function_exists ( 'create_category_select_options' ) ) {
function create_category_select_options ( $num_parent = '0' , $num_depth = '0' , $lst_exclude = '' , $str_delimiter = '&nbsp;&nbsp;&nbsp;' , $selected = '' ) {
	$str_options_html = "";

	// Turn the list of excluded values into an array
	$aryExclude = explode( "," , $lst_exclude);

	// Create array of arguments to pass to get_categories
	$args = array(
		'type'                     => 'post',
		'child_of'                 => '',
		'parent'                   => $num_parent,
		'orderby'                  => 'id',
		'order'                    => 'ASC',
		'hide_empty'               => 0,
		'hierarchical'             => 0,
		'exclude'                  => '',
		'include'                  => '',
		'number'                   => '',
		'taxonomy'                 => 'category',
		'pad_counts'               => false );

	// Call the get_categories function
	$ary_categories = get_categories( $args );

	// Loop the array of categories
	foreach ( $ary_categories AS $category ) {
		// Call the function recursively, checking for children of this category
		$str_children_html = create_category_select_options ( $category->term_id , $num_depth + 1 , $lst_exclude , $str_delimiter , $selected );

		$str_options_html .= '<option value="' . $category->term_id . '"' . ( $category->term_id == $selected ? ' SELECTED' : '' ) . '>';

		// Add the delimiter before the category name
		$i = 1;
		while ( $i <= $num_depth ) {
			$str_options_html .= $str_delimiter;
			$i++;
		}

		$str_options_html .= $category->name . '</option>';

		// Add the HTML for the Children
		$str_options_html .= $str_children_html;
	}

	return $str_options_html;
}
}
?>