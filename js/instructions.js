function show_hide_inst ( div_name ) {
	the_div = document.getElementById( div_name );

	if ( the_div.style.display != "block" ) {
		the_div.style.display = "block";
	}
	else { 
		the_div.style.display = "none";
	}
}