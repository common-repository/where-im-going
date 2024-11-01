=== Where I'm Going ===
Contributors: skustes
Donate link: http://www.nuttymango.com/donate
Tags: travel, location, destination, where, adventure, journey, route, trip
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plug-in allows you to input your travel destinations and the dates you'll be there, then easily output those dates to your readers.

THIS PLUG-IN IS NO LONGER SUPPORTED BY THE DEVELOPER.

== Description ==

This plug-in allows you to input your travel destinations and the dates you'll be there, then easily output those dates to your readers.  Destinations can be linked to Wordpress posts and pages to direct your readers to your content.  Output is fully customizable to show destinations and dates in any format.

THIS PLUG-IN IS NO LONGER SUPPORTED BY THE DEVELOPER.

== Installation ==

1. Upload 'where-im-going' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to the Where I'm Going menu and input your destinations.
4. Use the [wig] shortcode to output your destinations in theme files, posts, and pages.  Full details of this shortcode are available at the bottom of this file, from the Where I'm Going instructions page, or here: http://www.nuttymango.com/tutorials/where-im-going/

== Frequently asked questions ==
N/A

== Screenshots ==

1. http://www.nuttymango.com/wp-content/uploads/2013/04/wig-main-page.png
2. http://www.nuttymango.com/wp-content/uploads/2013/04/wig-add-destination.png

== Changelog ==

= 1.1 = 
* Fixed navigation broken by last change

= 1.0 = 
* Menu clean-up
* CSS clean-up

= 0.5 =
* Initial release

== Upgrade notice ==

== [wig] Shortcode Usage ==
The current Destination can be output simply by inserting [wig] where you want to output the Destination.  If you want to tailor the output to show past or future dates, a link to a post, the start and/or end date, etc, use the following parameters:

* display - Accepts any one of the following values:
	- past - Shows all Destinations that you visited in the past
	- present (Default) - Shows all Destinations that you're currently in
	- future - Shows all Destinations that you'll be visiting in the future
	- all - Shows all Destinations
	- post - Shows all Destinations that are tied to a Wordpress post or page
	- no_post - Shows all Destinations that are not tied to a Wordpress post or page
* show - How many Destinations do you want to show if there are more than 1 that match the parameters?  (Default: 1) (Set to 0 for "all").  If multiple Destinations are output, a line break will be placed after each.
* display_date_start - The start of the search period in Unix time.  Overrides display parameter
* display_date_end - The end of the search period in Unix time.  Overrides display parameter
* has_post - Accepts: yes, no, all (default)
* format - Accepts the following placeholders (any text is allowed):
	- %n% - The Destination name
	- %sd% - The Destination start date
	- %sdt% - The Destination start datetime
	- %st% - The Destination start time
	- %ed% - The Destination end date
	- %edt% - The Destination end datetime
	- %et% - The Destination end time
	- %url%...%/url% - If the Destination is tied to a post, the URL will be linked from any text between these.  If a Destination is not tied to a post, a URL will not be output.
	- Examples:
		= "%url%%n%%/url%" outputs: <a href="{link}">Key West, FL</a>
		= "%url%%n%%/url% - %sdt% to %edt%" outputs: <a href="{link}">Key West, FL</a> - Apr 05, 2013 12:00 to Apr 15, 2013 14:00
		= "%n%: %sd% at %st% until %ed% at %et%" outputs: Key West, FL: Apr 05, 2013 at 12:00 until Apr 15, 2013 at 14:00
* date_format - Accepts all PHP date options: http://php.net/manual/en/function.date.php. <em>Default: M d, Y (ex: Apr 05, 2013)</em>
* time_format - Accepts all PHP time options: http://php.net/manual/en/function.date.php. <em>Default: H:i (ex: 17:36)</em>
* show_error - Show "No destination(s) found" if there are no matching destinations?  Accepts <em>0 (default)</em> or 1.

== Shortcode Examples ==
* [wig] Outputs the current Destination without start and end date/time
* [wig display="past" has_post="yes" show=0] Outputs all past Destinations that are assigned to a Wordpress post/page.
* [wig display="future" format="%url%%n%%/url%: %sd% at %st% until %ed% at %et%" time_format="h:i a" show=3] Outputs 3 future Destinations with destination name as a URL (if tied to a post/page), start and end date/time, and time in the format 04:36 pm