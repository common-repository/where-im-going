<div class="wrap">
	<div id="main_instructions">
		<?php /* - - - - - - - - - - - - Video Tutorials - - - - - - - - - - - - */ ?>
		<div class="center"><h2>Video Tutorials</h2></div>
		<div class="center">Video tutorials for using the "Where I'm Going" plugin can be found here:</div>
		<div class="center"><a href="http://www.nuttymango.com/tutorials/where-im-going/" target="_blank"><h2>Where I'm Going Tutorial</h2></a></div>

		<div class="hr"></div>

		<?php /* - - - - - - - - - - - - Usage Instructions - - - - - - - - - - - - */ ?>
		<div class="center"><h2>Using The Where I'm Going Plugin</h2></div>
		<div class="inst_step">
			<div class="inst_title" onclick="show_hide_inst( 'step-1' );">Step 1: Add Your First Destination</div>
			<div class="inst_content" id="step-1">
				<p><a href="admin.php?page=whereimgoing&action=add">Tell your readers where you're going!</a></p>
			</div>
		</div>
		<div class="inst_step">
			<div class="inst_title" onclick="show_hide_inst( 'step-2' );">Step 2: Display Your Destination(s)</div>
			<div class="inst_content" id="step-2">
				<p>Use the shortcode [wig] wherever you want to display your destinations.  By default, [wig] will show the destination that you're currently in.</p>
				<p>This shortcode is powerful, accepting numerous parameters that allow you to tailor the output to your blog and your audience.  For a complete list of shortcode parameters, <a href="admin.php?page=whereimgoing&action=shortcode">go to this Shortcode instructions page</a> or visit the <a href="http://www.nuttymango.com/tutorials/where-im-going/" target="_blank">Where I'm Going plugin tutorial</a>.</p>
			</div>
		</div>
		<div class="inst_step">
			<div class="inst_title" onclick="show_hide_inst( 'step-3' );">Step 3: Go Travel!</div>
			<div class="inst_content" id="step-3">
				<p>No, really...go travel!  I'll take care of keeping your readers updated on your whereabouts.  (Well, as long as you be sure to keep your <a href="admin.php?page=whereimgoing">destinations</a> updated.)</p>
			</div>
		</div>

		<div class="hr"></div>

		<?php /* - - - - - - - - - - - - Contact Me Instructions - - - - - - - - - - - - */ ?>
		<div class="center"><h2>Contact Me With Issues</h2></div>
		<p>If you're having trouble getting "Where I'm Going" to work properly with your system, please email me at scott@nuttymango.com and I will help.</p>
	</div>
	<?php 
		global $wig_paypal;
		echo $wig_paypal->paypal_donation_form();
	?>
</div>