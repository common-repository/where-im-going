<?php
// ------------------------------------------------------------------------
// This file declares the error_handler class
// This class was downloaded from http://vailo.wordpress.com/2008/07/02/the-php-error-handler-class/
// ------------------------------------------------------------------------
if ( !class_exists ( 'error_handler' ) ) {

class error_handler {
	private $debug = 0;						// Set to 1 to show detailed debugging messages
	
	public function __construct( $debug = 0 ) {
		$this->debug = $debug;
		set_error_handler( array( $this , 'handle_error' ) );
	}
	
	public function handle_error( $error_type , $error_string , $error_file , $error_line ) {	
		switch ( $error_type ) {
			case FATAL:
				switch ( $this->debug ) {
					case 0:
						echo 'Sadly an error has occured!';
						exit;
					case 1:
						echo "<pre><b>FATAL</b> [T: $error_type] [L: $error_line] [F: $error_file]<br/>$error_string<br /></pre>";
						exit;
				}
			case ERROR:
				echo "<pre><b>ERROR</b> [T: $error_type] [L: $error_line] [F: $error_file]<br/>$error_string<br /></pre>";
				break;
			case WARNING:
				echo "<pre><b>WARNING</b> [T: $error_type] [L: $error_line] [F: $error_file]<br/>$error_string<br /></pre>";
				break;
		}
	}

	 /* This function converts an array of errors into a user-readable string
	Parameters:
	$ary_errors - An array of errors to convert to an HTML string

	Returns:
	$str_error_html - An HTML formatted string
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function error_array_to_string( $ary_errors ) {
		// Ensure that an array was passed
		if( !is_array( $ary_errors) ) {
			return "Function error_array_to_string requires an array as the parameter.";
		}

		// Loop the array, adding each error and a line break
		foreach( $ary_errors AS $error ) {
			$str_error_html .= $error . "<br/>";
		}

		// Return the HTML string
		return $str_error_html;
	}
} 

global $error_handler;
$error_handler = new error_handler( 1 );

define( 'FATAL' , E_USER_ERROR );
define( 'ERROR' , E_USER_WARNING );
define( 'WARNING' , E_USER_NOTICE );
}
?>