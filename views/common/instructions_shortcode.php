<div class="wrap">
	<div class="center"><h2>Using The [wig] Shortcode</h2></div>
	To call the shortcode with default settings, use [wig].  This will output your current destination, if you have one.

	<div class="center"><h2>Additional Parameters</h2></div>
	If you want to custom tailor the output of your Destinations, the [wig] shortcode gives you numerous options.  You can use any or all of these when you call the [wig] shortcode.
	<ul>
		<li><strong>display</strong> - Accepts any one of the following values:
		<ul>
			<li>past - Shows all Destinations that you visited in the past</li>
			<li><em>present (Default)</em> - Shows all Destinations that you're currently in</li>
			<li>future - Shows all Destinations that you'll be visiting in the future</li>
			<li>all - Shows all Destinations</li>
			<li>post - Shows all Destinations that are tied to a Wordpress post or page</li>
			<li>no_post - Shows all Destinations that are not tied to a Wordpress post or page</li>
		</ul>
		</li>
		<li><strong>show</strong> - How many Destinations do you want to show if there are more than 1 that match the parameters?  <em>Default: 1</em> (Set to 0 for "all").  If multiple Destinations are output, a line break will be placed after each.</li>
		<li><strong>display_date_start</strong> - The start of the search period in Unix time.  Overrides display parameter</li>
		<li><strong>display_date_end</strong> - The end of the search period in Unix time.  Overrides display parameter</li>
		<li><strong>has_post</strong> - Accepts: yes, no, <em>all (default)</em></li>
		<li><strong>format</strong> - Accepts the following placeholders (any text is allowed):
		<ul>
			<li>%n% - The Destination name</li>
			<li>%sd% - The Destination start date</li>
			<li>%sdt% - The Destination start datetime</li>
			<li>%st% - The Destination start time</li>
			<li>%ed% - The Destination end date</li>
			<li>%edt% - The Destination end datetime</li>
			<li>%et% - The Destination end time</li>
			<li>%url%...%/url% - If the Destination is tied to a post, the URL will be linked from any text between these.  If a Destination is not tied to a post, a URL will not be output.</li>
			<li><strong>Examples:</strong>
			<ul>
				<li><strong>"%url%%n%%/url%"</strong> outputs:<br/><a href="#">Key West, FL</a></li>
				<li><strong>"%url%%n%%/url% - %sdt% to %edt%"</strong> outputs:<br/><a href="#">Key West, FL</a> - Apr 05, 2013 12:00 to Apr 15, 2013 14:00</li>
				<li><strong>"%n%: %sd% at %st% until %ed% at %et%"</strong> outputs:<br/>Key West, FL: Apr 05, 2013 at 12:00 until Apr 15, 2013 at 14:00</li>
			</ul>
			</li>
		</ul>
		</li>	
		<li><strong>date_format</strong> - Accepts all PHP date options: http://php.net/manual/en/function.date.php. <em>Default: M d, Y (ex: Apr 05, 2013)</em></li>
		<li><strong>time_format</strong> - Accepts all PHP time options: http://php.net/manual/en/function.date.php. <em>Default: H:i (ex: 17:36)</em></li>
		<li><strong>show_error</strong> - Show "No destination(s) found" if there are no matching destinations?  Accepts <em>0 (default)</em> or 1.</li>
	</ul>

	<h2>Examples</h2>
	<ul>
		<li><strong>[wig]</strong>:<br/>Outputs the current Destination without start and end date/time</li>
		<li><strong>[wig display="past" has_post="yes" show=0]</strong>:<br/>Outputs all past Destinations that are assigned to a Wordpress post/page.</li>
		<li><strong>[wig display="future" format="%url%%n%%/url%: %sd% at %st% until %ed% at %et%" time_format="h:i a" show=3]</strong>:<br/>Outputs 3 future Destinations with destination name as a URL (if tied to a post/page), start and end date/time, and time in the format 04:36 pm</li>
	</ul>
</div>