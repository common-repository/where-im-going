/* NO CHANGES ARE NEEDED IN THIS FILE!! */

jQuery(document).ready(function () {
	if ( jQuery('#pp_dismiss').length != 0 ) {
		// When the Paypal dismiss link is clicked, process and close the form
		jQuery('#pp_dismiss').click( function() {
			// Hide the form
			jQuery('#paypal_wrap').hide();

			// Get the name of the AJAX Action call
			$action_call = jQuery('#ppd_ajax_call').val();

			jQuery.post( ajaxurl, {
					action: $action_call,
					ppd_nonce: ppdajax.ppd_nonce
				}
			);

			return false;
		} );
	}
});