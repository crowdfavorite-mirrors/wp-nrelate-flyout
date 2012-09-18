<?php
/**
 * nrelate Admin Messages
 *
 * Does system checks and sets messages for this particular nrelate plugin
 *
 * @package nrelate
 * @subpackage Functions
 */

function nr_fo_message_set(){

	 // Get flyout options
	$flyout_options = get_option('nrelate_flyout_options');
	
	// Flyout Thumbnail options
	$show_thumbnails = $flyout_options['flyout_thumbnail'];
	$thumbnailurl = $flyout_options['flyout_default_image'];
	// Flyout ad options
	$adcodeopt = isset($flyout_options['flyout_display_ad']) ? $flyout_options['flyout_display_ad'] : null;
	$msg = '';
	// Thumbnail
	if ($show_thumbnails == 'Thumbnails') {
		// Is there a default thumbnail set?
		if ($thumbnailurl == null || $thumbnailurl == '') {
				$msg = $msg . '<li><div class="red">Flyout is set to show thumbnails. It\'s a good idea to add a default image just in case a post does not have images in it. Add your <a href="admin.php?page=nrelate-flyout">default image here</a>.</div></li>';
		} else {
				$msg = $msg . '<li><div class="green">Flyout will show thumbnails, and default thumbnail is set.</div></li>';
		}
	};
	echo $msg;
};
add_action ('nrelate_admin_messages','nr_fo_message_set');


		
?>