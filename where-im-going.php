<?php
/*
Plugin Name: Where I'm Going
Plugin URI: http://www.realfooduniversity.com/
Description: Where are you now?  Where are you going?  Where have you been?  Keep your readers up to date on your current, past, and future travels.
Version: 1.1
Author: Scott Kustes
Author URI: http://www.realfooduniversity.com/
License: GPL2
THIS PLUG-IN IS NO LONGER SUPPORTED BY THE DEVELOPER
*/

/* - - - - - - - - - - - - - - - - - - - - - - - - - -
		Define global variables
- - - - - - - - - - - - - - - - - - - - - - - - - - */
// Call WIG classes
include( 'classes/class_includes.php' );
// Include PPD
include( 'classes/class.paypal_donation/class.paypal_donation.php' );

date_default_timezone_set("America/New_York");

global $wpdb;

global $ary_wig_ajax_screens;
$ary_wig_ajax_screens = array ( 'add' , 'edit' );

// Constants for WIG definition
if ( !defined( 'WIG_DB_VERS' ) )
	define( 'WIG_DB_VERS' , '1.1' );
if ( !defined( 'WIG_PLUGIN_URL' ) )
	define( 'WIG_PLUGIN_URL' , plugin_dir_url( __FILE__ ) );
if ( !defined( 'WIG_PLUGIN_DIR' ) )
	define( 'WIG_PLUGIN_DIR' , plugin_dir_path( __FILE__ ) );
if ( !defined( 'WIG_IMAGES_URL' ) )
	define( 'WIG_IMAGES_URL' , WIG_PLUGIN_URL . 'images/' );
if ( !defined( 'WIG_IMAGES_DIR' ) )
	define( 'WIG_IMAGES_DIR' , WIG_PLUGIN_DIR . 'images/' );
// Constants for database table names
if ( !defined( 'WIG_TABLE_NAME' ) )
	define( 'WIG_TABLE_NAME' , $wpdb->prefix . "where_im_going" );

/* Runs when plugin is activated */
register_activation_hook( __FILE__ , 'wig_install' );

/* Runs when plugin is deactivated AND deleted */
register_uninstall_hook( __FILE__ , 'wig_uninstall' );

/* Register Options */
add_action( 'admin_init' , 'wig_admin_init' );

/* Register shortcodes */
add_action( 'init' , 'register_wig_shortcodes');
function register_wig_shortcodes () {
	/* Register Shortcode for outputting Destinations */
	add_shortcode( 'wig' , 'wig_output' );
}


/* Installation function:
Sets up database tables
Sets up Wordpress options
------------------------------ */
function wig_install () {
	global $wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	// Get the SQL for creating the database tables
	$ary_create_table_sql = wig_create_database_tables();

	// Loop the array, creating tables using Wordpress function dbDelta
	foreach ( $ary_create_table_sql AS $table_sql ) {
		dbDelta( $table_sql );
	}

	wig_add_default_options();
}


/* Uninstall function
------------------------------ */
function wig_uninstall () {
	global $wpdb;

	// Get the SQL for creating the database tables
	$ary_delete_table_sql = wig_delete_database_tables();

	// Loop the array, deleting tables
	foreach ( $ary_delete_table_sql AS $table_sql ) {
		$qry_delete_table = $wpdb->query( $wpdb->prepare( $table_sql , "" ) );
	}

	delete_option('wig_options');
}

/* Function to create admin menu
------------------------------ */
add_action( 'admin_menu' , 'wig_add_admin_menu' );
function wig_add_admin_menu () {
	global $wig_home;
	global $wig_main;
	global $wig_inst;
	global $wig_settings;

	/* Create Menu */
	$wig_home = add_menu_page( 'Where I\'m Going', 'Where I\'m Going', 'administrator', 'whereimgoing', 'wig_menu_inst' );
	$wig_inst = add_submenu_page( 'whereimgoing', 'Instructions', 'Instructions', 'administrator', 'whereimgoing', 'wig_menu_inst' );
	$wig_main = add_submenu_page( 'whereimgoing', 'My Destinations', 'My Destinations', 'administrator', 'wig_destinations', 'wig_menu_destinations' );
	$wig_settings = add_submenu_page( 'whereimgoing', 'Settings', 'Settings', 'administrator', 'wig_settings', 'wig_menu_settings' );
}


/* Function to add Wordpress options
------------------------------ */
function wig_add_default_options() {
	$wig_options = get_option( 'wig_options' );

	// If the option isn't an array (i.e., options don't exist), create the options
	if ( !is_array( $wig_options ) || ( $wig_options['wig_version'] != WIG_DB_VERS ) ) {
		/* Create array of options */
		$ary_options = array(
			'wig_version' => WIG_DB_VERS,
			'wig_allow_overlap' => 0,
			'wig_pagination' => 20
			);

		update_option( 'wig_options' , $ary_options );
	}
}


add_action( 'admin_print_styles' , 'wig_admin_styles');
function wig_admin_styles () {
	global $wig_main;
	global $wig_inst;
	global $wig_settings;
	global $ary_wig_ajax_screens;

	$screen = get_current_screen();

	if ( ( $screen->id == $wig_main ) || ( $screen->id == $wig_settings ) || ( $screen->id == $wig_inst ) ) {
		// Admin styles for Destination Manager
		wp_register_style( 'wig-styles', WIG_PLUGIN_URL . 'css/wig_style.css' , __FILE__ , '2013403' , 'all' );
		wp_enqueue_style( 'wig-styles' );

		// Styles for Date Picker
		wp_register_style( 'anytime-styles', WIG_PLUGIN_URL . 'anytime/anytime.css' , __FILE__ , '2013403' , 'all' );
		wp_enqueue_style( 'anytime-styles' );
	}
}

/* Register JS */
add_action( 'admin_print_scripts' , 'wig_admin_scripts');
function wig_admin_scripts () {
	global $wig_main;
	global $wig_inst;
	global $ary_wig_ajax_screens;

	$screen = get_current_screen();

	if ( $screen->id == $wig_main ) {
		// If this is a screen that needs AJAX functions, load them
		if ( isset( $_REQUEST['action'] ) && is_numeric( array_search( $_REQUEST['action'] , $ary_wig_ajax_screens ) ) ) {
			// Include jQuery
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js' );
			wp_enqueue_script( 'jquery' );
			wp_register_script( 'jquery-migrate', 'http://code.jquery.com/jquery-migrate-1.0.0.js' );
			wp_enqueue_script( 'jquery-migrate' );

			// AJAX for WIG
			wp_register_script( 'wig-ajax', WIG_PLUGIN_URL . 'js/wig_jquery_ajax.js' , __FILE__ );
			wp_enqueue_script( 'wig-ajax' );

			wp_register_script( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js' );
			wp_enqueue_script( 'jquery-ui' );
			wp_register_script( 'timepicker', WIG_PLUGIN_URL . 'js/timepicker.js' , __FILE__ );
			wp_enqueue_script( 'timepicker' );
		}
	}

	if ( $screen->id == $wig_inst ) {
		// Javascript for Instructions page
		wp_register_script( 'wig-inst', WIG_PLUGIN_URL . 'js/instructions.js' , __FILE__ );
		wp_enqueue_script( 'wig-inst' );
	}
}

/* Register AJAX */
add_action( 'admin_init' , 'register_wig_ajax');
function register_wig_ajax () {
	global $wig_main;
	$screen = get_current_screen();

	//add_action( 'wp_ajax_ppd_wig' , 'ppd_dismiss_form' );

	if ( $screen->id == $wig_main ) {
		/* Register AJAX for retrieving Lessons to populate dropdown */
		add_action( 'wp_ajax_wig_get_posts' , 'ajax_get_posts' );

		/* Register AJAX for comparing Start and End dates */
		add_action( 'wp_ajax_wig_check_dates' , 'ajax_check_dates' );
	}
}


/* Function to initialize Admin
------------------------------ */
function wig_admin_init() {
	// Set the default pagination and extension settings into a constant
	$wig_options = get_option( 'wig_options' );

	if ( !defined( 'WIG_ALLOW_OVERLAP' ) )
		define( 'WIG_ALLOW_OVERLAP' , $wig_options['wig_allow_overlap'] );
	if ( !defined( 'WIG_PAGINATION' ) )
		define( 'WIG_PAGINATION' , $wig_options['wig_pagination'] );
}

/* Include other Wordpress specific function files to tie WIG into WP */
include( 'functions/functions_shortcodes.php' );

/* Include files specific to WIG functionality*/
include( 'functions/functions_menu.php' );
include( 'functions/functions_ajax.php' );
include( 'functions/functions_output.php' );
include( 'functions/functions_settings.php' );
include( 'models/database.php' );
?>