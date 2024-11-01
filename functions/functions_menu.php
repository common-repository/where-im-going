<?php
/* Display functions based on menu selection and URL
------------------------------ */
function wig_menu_destinations () {
	if ( trim( $_REQUEST['action'] ) == "" ) {
		$action = "display";
	}
	else {
		$action = $_REQUEST['action'];
	}

	if ( $action == "display" ) { // Display the list of All Destinations
		include( WIG_PLUGIN_DIR . 'views/destination/display_all_destinations.php' );
	}
	else if ($action == "add") {
		include( WIG_PLUGIN_DIR . 'views/destination/add_destination.php' );
	}
	else if ($action == "edit") {
		include( WIG_PLUGIN_DIR . 'views/destination/edit_destination.php' );
	}
	else if ($action == "delete") {
		include( WIG_PLUGIN_DIR . 'views/destination/delete_destination.php' );
	}
	else if ($action == "update") {
		include( WIG_PLUGIN_DIR . 'views/common/update.php' );
	}
}

function wig_menu_settings () {
	if ( trim( $_REQUEST['action'] ) == "" ) {
		$action = "display";
	}
	else {
		$action = $_REQUEST['action'];
	}

	if ( $action == "display" ) { // Display the main Settings page
		include( WIG_PLUGIN_DIR . 'views/common/wig_settings.php' );
	}
}

function wig_menu_inst () {
	if ( trim( $_REQUEST['action'] ) == "" ) {
		$action = "display";
	}
	else {
		$action = $_REQUEST['action'];
	}

	if ( $action == "display" ) { // Display the Instructions page
		include( WIG_PLUGIN_DIR . 'views/common/instructions.php' );
	}
	else if ( $action == "shortcode" ) { // Display the Shortcode Instructions page
		include( WIG_PLUGIN_DIR . 'views/common/instructions_shortcode.php' );
	}
}
?>