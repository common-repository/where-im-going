<?php
function wig_create_database_tables() {
	/* Setup the database tables */
	$str_create_wig_table = "CREATE TABLE IF NOT EXISTS " . WIG_TABLE_NAME . " (
		destination_id int(11) NOT NULL AUTO_INCREMENT,
		destination_name varchar(100) NOT NULL,
		destination_start_date int(11) NOT NULL,
		destination_end_date int(11) NOT NULL,
		post_id int(11) NOT NULL,
		PRIMARY KEY (destination_id)
		);";

	// Return an array of the Create Table strings
	$ary_table_sql = array ( $str_create_wig_table );
	return $ary_table_sql;
}

function wig_delete_database_tables() {
	$str_delete_crs_table = "DROP TABLE IF EXISTS " . WIG_TABLE_NAME . ";";

	// Return an array of the Delete Table strings
	$ary_table_sql = array ( $str_delete_crs_table );
	return $ary_table_sql;
}

function wig_insert_default_data() {
	return false;
}
?>