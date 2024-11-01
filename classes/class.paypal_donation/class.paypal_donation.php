<?php
// ------------------------------------------------------------------------
// This file declares the PayPal Donation class for use in plugins.
// ------------------------------------------------------------------------
if ( !class_exists ( 'paypal_donation' ) ) {
class paypal_donation {
	/* This function sets defaults for the Paypal Donation class
			EDIT THESE TO YOUR LIKING
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function set_defaults( $atts = '' ) {
		// This is the Heading on the donation form
		if ( trim( $atts['paypal_title'] ) == "" )
			$this->paypal_title = 'Help Support This Free Plugin';
		else
			$this->paypal_title = $atts['paypal_title'];

		// This is the message that is output below the Heading
		if ( trim( $atts['paypal_message'] ) == "" )
			$this->paypal_message = '<p>This plugin is a labor of love created by <a href="http://www.nuttymango.com/" target="_blank">Scott Kustes at Nutty Mango</a>.  Donations help me support and maintain free plugins like this one.</p>';
		else
			$this->paypal_message = $atts['paypal_message'];

		// What email address should payments be sent to?
		if ( ( trim( $atts['paypal_email'] ) == "" ) || ( !filter_var( $atts['paypal_email'] , FILTER_VALIDATE_EMAIL ) ) )
			$this->paypal_email = 'scott.kustes@gmail.com';
		else
			$this->paypal_email = $atts['paypal_email'];

		// What message do you want displayed above the dropdown?
		if ( trim( $atts['paypal_donation_message'] ) == "" )
			$this->paypal_donation_message = 'Make A Small Donation?';
		else
			$this->paypal_donation_message = $atts['paypal_donation_message'];

		// This is an array of options for the dropdown box to select a donation amount
			// Format is array of associative arrays with elements "value", "text", and "selected"
		if ( !is_array( $atts['paypal_donation_options'] ) )
			$this->paypal_donation_options = array(
					array( 'value' => 5 , 'text' => 'Buy Me A Beer' , 'selected' => 'Y' ),
					array( 'value' => 10 , 'text' => 'Buy Me A Burger' , 'selected' => 'N' ),
					array( 'value' => 20 , 'text' => 'Buy My Soul (Or Just A Huge Thank You!)' , 'selected' => 'N' ),
					array( 'value' => '' , 'text' => 'Donate Another Amount' , 'selected' => 'N' )
				);
		else
			$this->paypal_donation_options = $atts['paypal_donation_options'];

		// This is the PayPal button that will show up on the form.  
		// Use buttons from here or custom buttons from elsewhere: https://www.paypal.com/webapps/mpp/logo-center
		if ( !filter_var( $atts['paypal_button_url'] , FILTER_VALIDATE_URL ) )
			$this->paypal_button_url = 'http://www.nuttymango.com/images/paypal.png';
		else
			$this->paypal_button_url = $atts['paypal_button_url'];

		// This is the currency that you'll be paid in
		if ( trim( $atts['paypal_currency'] ) == "" )
			$this->paypal_currency = 'USD';
		else
			$this->paypal_currency = $atts['paypal_currency'];

		// This value will show up on the PayPal screen as the item
		if ( trim( $atts['paypal_item_name'] ) == "" )
			$this->paypal_item_name = 'Donation For Nutty Mango Plugin';
		else
			$this->paypal_item_name = $atts['paypal_item_name'];

		// Where do you want the user sent after they make a donation?
		if ( !filter_var( $atts['return_url'] , FILTER_VALIDATE_URL ) )
			$this->return_url = 'http://www.nuttymango.com/thank-you-donation/';
		else
			$this->return_url = $atts['return_url'];

		/* How many hours between the installation of your plugin and the first appearance of the Donate form?
		(Use 24 for 1 day, 48 for 2 days, etc.) */
		if ( !is_numeric( $atts['first_show_delay'] ) || ( $atts['first_show_delay'] < 0 ) )
			$this->first_show_delay = 168; 		// Wait one week before showing donation form
		else
			$this->first_show_delay = $atts['first_show_delay'];

		// When the user dismisses your donation form, how many hours until it appears again?
		if ( !is_numeric( $atts['interval_between'] ) || ( $atts['interval_between'] < 0 ) )
			$this->interval_between = 120; 		// Wait five days before showing the form again
		else
			$this->interval_between = $atts['interval_between'];

		// How many times should the form appear? (Use 0 for infinite, i.e., user never gets "Never Show Again")
		if ( !is_numeric( $atts['total_dismissals'] ) || ( $atts['total_dismissals'] < 0 ) )
			$this->total_dismissals = 3;		// On the last appearance, the user will get the "Never Show Again" link
		else
			$this->total_dismissals = $atts['total_dismissals'];

		// What do you want to name the entry in the WP Options table?  
		// If you use this class in multiple plugins, you need to change this when instantiating on the chance two of your plugins are used on a site.
		if ( trim( $atts['wp_option_name'] ) == "" )
			$this->wp_option_name = 'ppd_class_options';
		else
			$this->wp_option_name = $atts['wp_option_name'];

		// What do you want to name the AJAX call?  
		// If you use this class in multiple plugins, you need to change this when instantiating on the chance two of your plugins are used on a site.
		if ( trim( $atts['ajax_call'] ) == "" )
			$this->ajax_call = 'dismiss_ppd';
		else
			$this->ajax_call = $atts['ajax_call'];
	}
	/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
			STOP EDITING HERE
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - - - 
Declare Attributes
- - - - - - - - - - - - - - - - - - - - */
	protected $paypal_title;		// The Title to display on the form
	protected $paypal_message;		// The Message to display on the form
	protected $paypal_email;		// The Email Address of the account to send the donation to
	protected $paypal_donation_message;	// The Message that is displayed just above the dropdown
	protected $paypal_donation_options;	// An array of options to go in the Dropdown list
	protected $paypal_button_url;		// The URL of a PayPal button to use
	protected $paypal_currency;		// The Currency you'll get paid in
	protected $paypal_item_name;		// The name of the Item that will show up on PayPal
	protected $allow_dismiss;		// Can the donation form be dismissed?  Default is yes, can be overridden when calling paypal_donation_form()
	protected $return_url;			// Where will the user be redirected after donating?
	protected $last_dismissal;		// Unix timestamp of the last form dismissal
	protected $first_show_delay;		// How many hours until the first appearance of the donation form?
	protected $interval_between;		// After a user dismissed the donation form, how many hours until it appears again?
	protected $total_dismissals;		// How many times do you show the form to the user before offering the "Never Show Again" option?
	protected $show_form;			// Should the form be shown?
	protected $wp_option_name;		// The name of the options in the WP Options table
	protected $ajax_call;			// The name of the AJAX call

/* - - - - - - - - - - - - - - - - - - - 
Constructor & Wordpress Options Functions
- - - - - - - - - - - - - - - - - - - - */
	/* Constructor function to create a new PayPal Donation instance
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function __construct( $atts = '' ) {
		// Include the PayPal Donation CSS & JS
		add_action( 'admin_enqueue_scripts' , array( $this , 'write_paypal_donation_css' ) );
		add_action( 'admin_enqueue_scripts' , array( $this , 'write_paypal_donation_js' ) );

		// Set the default settings if they weren't passed in $atts
		$this->set_defaults( $atts );

		/* Register AJAX Call */
		add_action( 'wp_ajax_' . $this->ajax_call , array( $this , 'dismiss_form' ) );

		// Default to 0
		$this->show_form = 0;
	}

	/* This function runs comparisons against the Wordpress Options that this Class sets.
	These options determine whether the Donation form should be shown or not
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function check_options() {
		global $wpdb;
		$paypal_options = $wpdb->get_row( $wpdb->prepare(
			"SELECT option_value
			FROM " . $wpdb->options . "
			WHERE option_name = %s" , $this->wp_option_name ) );
		$paypal_options = unserialize( $paypal_options->option_value );
		// $paypal_options = get_option( 'paypal_options' );  // THIS DOESN'T WORK FOR SOME STRANGE REASON

		// If the option is an array, do comparisons to determine if the form should be shown
		if ( is_array( $paypal_options ) ) {
			// If next_show = 0 or next_show is in the past, show the form
			if ( ( $paypal_options['next_show'] == 0 ) || ( $paypal_options['next_show'] < strtotime( date( 'Y-m-d H:i:s' ) ) ) )
				$this->show_form = 1;
			else
				$this->show_form = 0;
		}
		// If the option isn't an array (i.e., options don't exist), create the options
		else {
			// If there is no delay on the first appearance of the form, set it to show the form
			if ( $this->first_show_delay == 0 ) {
				$this->show_form = 1;
				$next_show = 0;
			}
			else {
				$this->show_form = 0;

				// Set the time to next show the form to the interval between
				$next_show = $this->calculate_next_show( $this->first_show_delay );
			}

			/* Create array of options */
			$ary_options = array(
				'next_show' => $next_show,
				'total_dismissals' => 0,
				'nonce' => ''
				);

			update_option( $this->wp_option_name , $ary_options );
		}
	}

	/* This function updates the Wordpress options when a form is dismissed
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function dismiss_form() {
		$nonce = $_POST['ppd_nonce'];

		// Make sure Nonce is valid
		if ( !wp_verify_nonce( $nonce , 'ppd-nonce' ) )
			die ( 'Invalid nonce');
 
		// Make sure the user has sufficient permissions
		if ( current_user_can( 'edit_posts' ) ) {
			$paypal_options = get_option( $this->wp_option_name );

			// Increment the total number of dismissals
			$dismissals = $paypal_options['total_dismissals'] + 1;

			// If the form has not been dismissed the number of times allowed or there is no limit on dismissals
			if ( ( $dismissals < $this->total_dismissals ) || ( $this->total_dismissals == 0 ) ) {
				// Set the time to next show the form to the interval between
				$next_show = $this->calculate_next_show( $this->interval_between );
			}
			else {
				$next_show = 9999999999;  // Sat, 20 Nov 2286 17:46:39 GMT
			}

			// Create array of options
			$ary_options = array(
				'next_show' => $next_show,
				'total_dismissals' => $dismissals,
				'nonce' => $nonce
				);

			//update_option( 'paypal_options' , $ary_options );   // THIS DOESN'T WORK FOR SOME STRANGE REASON

			global $wpdb;
			$qry_update_option = $wpdb->update(
						$wpdb->options,
						array( 'option_value' => serialize( $ary_options ) ),
						array( 'option_name' => $this->wp_option_name ),
						array( '%s' ),
						array( '%s' )
						);
		}

		exit;
	}

	/* This function calculates the next_show datetime
	Parameters:
	$interval - The number of hours until showing the form again
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function calculate_next_show( $interval ) {
		// Add the $interval hours to the current datetime
		$next_show = strtotime ( $interval . 'hour' , strtotime ( date ( 'Y-m-d H:i:s' ) ) ) ;

		return $next_show;
	}


/* - - - - - - - - - - - - - - - - - - - 
Form Output Functions
- - - - - - - - - - - - - - - - - - - - */
	/* This function outputs the Donation Form
	Parameters:
	$form - Which form do you want to show? Accepts large (default), small, horizontal, and footer
	$allow_dismiss - Can the form be dismissed?  Default: yes
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function paypal_donation_form( $form = 'large' , $allow_dismiss = 1 ) {
		// Check the Wordpress Options for this instance
		$this->check_options();

		$this->allow_dismiss = $allow_dismiss;

		// If the form should be shown or this form can't be dismissed
		if ( ( $this->show_form == 1 ) || ( is_numeric( $this->allow_dismiss ) && ( $this->allow_dismiss == 0 ) ) ) {
			// Create the Donation form
			$str_donation_form = call_user_func( array( $this , 'paypal_donation_form_' . $form ) );

			return $str_donation_form;
		}
	}

	/* This function creates the dropdown with donation options
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function get_select() {
		$str_donation_select = '<select name="amount">';
			// Loop the Donation Options array
			foreach ( $this->paypal_donation_options AS $donation ) {
				$str_donation_select .= '<option value="' . $donation['value'] . '"' . ( $donation['selected'] == "Y" ? ' SELECTED' : '' ) . '>' . ( $donation['value'] != "" ? '$' . $donation['value'] . ' - ' : '' ) . $donation['text'] . '</option>';
			}
		$str_donation_select .= '</select>';

		return $str_donation_select;
	}

	/* This function creates the "No Thanks" or "Never Show Again" link
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function get_dismiss_link() {
		// Get the Wordpress Options
		$paypal_options = get_option( $this->wp_option_name );

		$dismiss_link = '<a href="#" id="pp_dismiss">' . ( $paypal_options['total_dismissals'] >= ( $this->total_dismissals - 1 ) ? 'Don\'t Show Me This Again' : 'No Thanks' ) . '</a>';
		$dismiss_link .= '<input type="hidden" id="paypal_nonce" name="paypal_nonce" value="' . $this->create_nonce() . '" />';

		return $dismiss_link;
	}

	/* This function creates a random number to include in the link.  This ensures duplicate clicks aren't counted
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function create_nonce() {
		// Combine a random 3 digit number with the current date_time stamp
		$pp_nonce = rand( 100 , 999 ) . strtotime( date( 'Y-m-d H:i:s' ) );

		return $pp_nonce;
	}

	/* This function outputs the name of the AJAX call for this form
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function get_hidden_ajax_call() {
		// Combine a random 3 digit number with the current date_time stamp
		$ajax_call_html = '<input type="hidden" name="ppd_ajax_call" id="ppd_ajax_call" value="' . $this->ajax_call . '" />';

		return $ajax_call_html;
	}


/* - - - - - - - - - - - - - - - - - - - 
Forms
- - - - - - - - - - - - - - - - - - - - */
	/* This function outputs the Large Sidebar Donation Form
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function paypal_donation_form_large() {
		// Create the Donation form
		$str_donation_form = '<div class="paypal_form" id="paypal_wrap"><h2 class="paypal_title">' . $this->paypal_title . '</h2>';
		$str_donation_form .= '<div class="paypal_form_message">' . $this->paypal_message . '</div>';

		$str_donation_form .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="paypal_form">';

		// Create the Select options
		$str_donation_select = '<div class="center"><h3 class="paypal_donation_title">' . $this->paypal_donation_message . '</h3>' . $this->get_select() . '</div>';
		
		$str_donation_form .= $str_donation_select;

		// Output the Image button
		$str_donation_form .= '<div class="paypal_button"><input name="submit" onclick="submit()" src="' . $this->paypal_button_url . '" type="image"/><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></div>';

		// Output Hidden fields
		$str_donation_form .= '<input type="hidden" name="cmd" value="_xclick">';
		$str_donation_form .= '<input name="business" type="hidden" value="' . $this->paypal_email . '" />';
		$str_donation_form .= '<input name="item_name" type="hidden" value="' . $this->paypal_item_name . '" />';
		$str_donation_form .= '<input name="currency_code" type="hidden" value="' . $this->paypal_currency . '" />';
		$str_donation_form .= '<input type="hidden" name="return" value="' . $this->return_url . '">';
		$str_donation_form .= '<input type="hidden" name="no_note" value="0">';
		$str_donation_form .= '<input type="hidden" name="no_shipping" value="1">';
		$str_donation_form .= $this->get_hidden_ajax_call();
		$str_donation_form .= '</form>';

		// If dismissal is allowed, show the proper link
		if ( $this->allow_dismiss == 1 ) {
			$str_donation_form .= '<div class="paypal_dismiss">' . $this->get_dismiss_link() . '</div>';
		}
		$str_donation_form .= '</div>';

		return $str_donation_form;
	}

	/* This function outputs the Small Sidebar Donation Form
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function paypal_donation_form_small() {
		// Create the Donation form
		$str_donation_form = '<div class="paypal_form" id="paypal_wrap"><h2 class="paypal_title">' . $this->paypal_title . '</h2>';

		$str_donation_form .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="paypal_form">';

		// Create the Select options
		$str_donation_select = '<div class="center"><h3 class="paypal_donation_title">' . $this->paypal_donation_message . '</h3>' . $this->get_select() . '</div>';
		
		$str_donation_form .= $str_donation_select;

		// Output the Image button
		$str_donation_form .= '<div class="paypal_button"><input name="submit" onclick="submit()" src="' . $this->paypal_button_url . '" type="image"/><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></div>';

		// Output Hidden fields
		$str_donation_form .= '<input type="hidden" name="cmd" value="_xclick">';
		$str_donation_form .= '<input name="business" type="hidden" value="' . $this->paypal_email . '" />';
		$str_donation_form .= '<input name="item_name" type="hidden" value="' . $this->paypal_item_name . '" />';
		$str_donation_form .= '<input name="currency_code" type="hidden" value="' . $this->paypal_currency . '" />';
		$str_donation_form .= '<input type="hidden" name="return" value="' . $this->return_url . '">';
		$str_donation_form .= '<input type="hidden" name="no_note" value="0">';
		$str_donation_form .= '<input type="hidden" name="no_shipping" value="1">';
		$str_donation_form .= $this->get_hidden_ajax_call();
		$str_donation_form .= '</form>';

		// If dismissal is allowed, show the proper link
		if ( $this->allow_dismiss == 1 ) {
			$str_donation_form .= '<div class="paypal_dismiss">' . $this->get_dismiss_link() . '</div>';
		}
		$str_donation_form .= '</div>';

		return $str_donation_form;
	}

	/* This function outputs the Horizontal Sidebar Donation Form
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function paypal_donation_form_horizontal( $footer = 'no' ) {
		// Create the Donation form
		$str_donation_form = ( $footer == "yes" ? '<div class="ppfooter">' : '' ) . '<div class="paypal_form_horizontal" id="paypal_wrap">';
		$str_donation_form .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="paypal_form">';

		// Create the Select options
		$str_donation_select = '<span class="paypal_title_small">' . $this->paypal_title . '</span>' . $this->get_select();
		
		$str_donation_form .= $str_donation_select;

		// Output the Image button
		$str_donation_form .= '<input type="submit" name="submit" value="Donate with PayPal"/><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">';

		// Output Hidden fields
		$str_donation_form .= '<input type="hidden" name="cmd" value="_xclick">';
		$str_donation_form .= '<input name="business" type="hidden" value="' . $this->paypal_email . '" />';
		$str_donation_form .= '<input name="item_name" type="hidden" value="' . $this->paypal_item_name . '" />';
		$str_donation_form .= '<input name="currency_code" type="hidden" value="' . $this->paypal_currency . '" />';
		$str_donation_form .= '<input type="hidden" name="return" value="' . $this->return_url . '">';
		$str_donation_form .= '<input type="hidden" name="no_note" value="0">';
		$str_donation_form .= '<input type="hidden" name="no_shipping" value="1">';
		$str_donation_form .= $this->get_hidden_ajax_call();

		// If dismissal is allowed, show the proper link
		if ( $this->allow_dismiss == 1 ) {
			$str_donation_form .= '<span class="leftspace">' . $this->get_dismiss_link() . '</span>';
		}
		$str_donation_form .= '</form></div>' . ( $footer == "yes" ? '</div>' : '' );

		return $str_donation_form;
	}

	/* This function outputs the Horizontal Sidebar Donation Form fixed to the footer
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	private function paypal_donation_form_footer() {
		$str_donation_form = $this->paypal_donation_form_horizontal( 'yes' );

		return $str_donation_form;
	}


/* - - - - - - - - - - - - - - - - - - - 
CSS & JS Functions
- - - - - - - - - - - - - - - - - - - - */
	/* This function writes the PayPal Donation CSS to a file and includes it in the Wordpress head
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function write_paypal_donation_css() {
		$css_paypal_donation = '
			.paypal_wrap {
			}
			.paypal_form {
				float: left; 
				width: 350px;
				background-color: #EDEDED;
				border: 1px solid black;
				padding: 20px;
				margin: 10px 0 0 25px;
			}
			.paypal_form_horizontal {
				width: 800px;
				text-align: center;
				background-color: #FFE773;
				border: 1px solid black;
				margin: 5px auto;
				padding: 5px 10px;
			}
			.ppfooter {
				position: fixed;
				bottom: 40px;
				left: 25%;
				text-align: center;
			}
			.paypal_title {
				font-size: 14pt;
				color: #000000;
				text-align: center;
			}
			.paypal_title_small {
				font-size: 11pt;
				color: #000000;
				margin-right: 10px;
			}
			.paypal_form_message {
			}
			.paypal_form select {
				border: 1px solid #dadada;
				border-radius: 4px;
				padding: 3px;
			}
			.paypal_form select:focus { 
				outline: none;
				border-color: #9ecaed;
				box-shadow: 0 0 10px #9ecaed;
			}
			.paypal_button {
				text-align: center;
				padding: 10px;
				clear: both;
			}
			.paypal_button img {
				cursor: pointer;
			}
			.center {
				text-align: center;
			}
			.paypal_dismiss {
				text-align: center;
			}
			.leftspace {
				margin-left: 30px;
			}
		';

		// Remove excess whitespace
		$css_paypal_donation = preg_replace( '/[\s]+/' , ' ' , $css_paypal_donation );

		$write_css = file_put_contents( plugin_dir_path( __FILE__ ) . "paypal_donation.css" , $css_paypal_donation );

		// Register styles for PayPal Donation
		wp_register_style( 'paypal-donation-styles', plugin_dir_url( __FILE__ ) . 'paypal_donation.css' , __FILE__ , '20130226' , 'all' );  
		wp_enqueue_style( 'paypal-donation-styles' );
	}

	/* This function includes the Paypal Donation JS in the Wordpress head
	- - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
	public function write_paypal_donation_js() {
		// Register scripts for PayPal Donation
		wp_register_script( 'ppd-scripts', plugin_dir_url( __FILE__ ) . 'paypal_donation.js' , __FILE__ , '20130404' , true );  
		wp_enqueue_script( 'ppd-scripts' );
		wp_localize_script( 'ppd-scripts', 'ppdajax', 
			array(
				'ppd_nonce' => wp_create_nonce( 'ppd-nonce' )
			) );
	}
}
}

// Create an instance of this class for the Where I'm Going plugin
$atts['paypal_message'] = '<p>The Where I\'m Going plugin was created by <a href="http://www.nuttymango.com/" target="_blank">Scott Kustes at Nutty Mango</a>.  Donations help me support and maintain free plugins like this one.</p><p>If this plugin has helped you keep your travel blog up to date, consider making a small donation to help keep the Wordpress community humming along.</p>';
$atts['paypal_item_name'] = 'Donation For Where I\'m Going Plugin';
$atts['wp_option_name'] = 'ppd_wig_options';
$atts['ajax_call'] = 'dismiss_ppd_wig';

global $wig_paypal;
$wig_paypal = new paypal_donation( $atts );
?>