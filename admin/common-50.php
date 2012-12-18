<?php
/**
 * Common Admin Functions File added in v0.50.0
 *
 * Load Admin common functions
 *
 * Checks if another nrelate plugin loaded these functions first
 * 
 * @package nrelate
 * @subpackage Functions
 */


define( 'NRELATE_COMMON_50_LOADED', true );

/**
 * Show Terms of Service in Thickbox
 */
function get_nrelate_tos() {
	
	$args=array("timeout"=>2);
	$response = wp_remote_post('http://static.nrelate.com/common_wp/'.NRELATE_LATEST_ADMIN_VERSION.'/terms-of-service.html',$args);

	if (!is_wp_error($response) && $response['response']['code']==200 && $response['response']['message']=='OK') {
		$tos = $response['body'];
	} else {
		$tos = sprintf(__('Please <a href="%s" target="_blank">click here</a> to read our Terms of Service on our website.', 'nrelate'), 'http://static.nrelate.com/common_wp/'.NRELATE_LATEST_ADMIN_VERSION.'/terms-of-service.html');
	}
		
	$output = '<div id="nrelate-tos"><div id="nrelate-terms">' . $tos . '</div></div>';
	echo $output;
	
	die();
}
add_action('wp_ajax_get_nrelate_tos', 'get_nrelate_tos');

/**
 * Product check
 *
 * compares nrelate product versions from nrelate_products()
 * if versions do not match, we show a warning, admin_notice and in dashboard
 * user can opt out of messages
 */
function nrelate_product_check(){
	
	$nrelate_products = get_option('nrelate_products');
	
	$nrelate_active_plugins_versions = array();
	
	foreach ( (array) $nrelate_products as $plugin ) {
		// Only check active plugins
		if ($plugin['status'] > 0) {
		// Only check active plugins of which we have the plugin_file path
			if ( isset( $plugin['plugin_file'] ) ) {
				$nrelate_active_plugins_versions[ $plugin['plugin_file'] ] = $plugin['version'];
			} else {
				// At least one nrelate plugin is not v0.50.0+ so show the old warning.
				add_action('admin_notices','nrelate_product_check_announce_50');
			}
		}
	}
	
	// Sort them with the latest version first
	arsort($nrelate_active_plugins_versions);
	
	$nrelate_latest_installed_version = current($nrelate_active_plugins_versions);
	global $nrelate_outdated_plugin_notice;
	
	foreach ($nrelate_active_plugins_versions as $plugin_file => $plugin_version) {
		if ( $nrelate_latest_installed_version >  $plugin_version ) {
			
			// There is a version difference, get data once to check if there are updates
			$update_data = isset($update_data) ? $update_data : get_site_transient( 'update_plugins' );
			$plugins_data = isset($plugins_data) ? $plugins_data : get_plugins();
			
			if ( isset( $update_data->response[$plugin_file] ) ) {
				$plugin_info = $plugins_data[$plugin_file];
				$plugin_update_info = $update_data->response[$plugin_file];
				
				$nrelate_outdated_plugin_notice[] = sprintf(__('<a href="%s">Click here to update %s to %s version automatically</a>', 'nrelate'), wp_nonce_url( self_admin_url('update.php?action=upgrade-plugin&plugin=') . $plugin_file, 'upgrade-plugin_' . $plugin_file), $plugin_info['Name'], $plugin_update_info->new_version );
			}
		}
	}
	
	// Show an admin notice
	function nrelate_product_check_announce() {
		global $current_user, $nrelate_outdated_plugin_notice;
		get_currentuserinfo();
		$user_id = $current_user->ID;
		
		// Only show message if user can update plugins
		if (!current_user_can('update_plugins') ) return;
				
		// If we have a message
		if ( is_array($nrelate_outdated_plugin_notice) && count($nrelate_outdated_plugin_notice) ) {
			// If user wants to disregard notice, update_user_meta
			if ( isset( $_GET['nrelate_disregard'] ) && $_GET['nrelate_disregard'] == 'true' ) {
					update_user_meta($user_id, 'nrelate_product_check', 'none');
			}
			// If user chose not to show message, return
			$user_disregard = get_user_meta( $user_id, 'nrelate_product_check', true); 
			if ($user_disregard == 'none') return;		
			
			// Show message
			$msg = sprintf(__('Running the latest versions of nrelate plugins is highly recommended.<br/>%s<br/><br/>', 'nrelate'), implode('<br/>', $nrelate_outdated_plugin_notice) );
			$disable = sprintf(__('%s Disregard %s', 'nrelate'), '<a class="button" href="admin.php?page=nrelate-main&nrelate_disregard=true">', '</a>');
			echo '<div class="updated"><p>' . $msg . $disable . '</p></div>';
		}
	}
	add_action('admin_notices','nrelate_product_check_announce');

	function nrelate_product_check_announce_50() {
		global $current_user;
		get_currentuserinfo();
		$user_id = $current_user->ID;
		
		// Only show message if user can update plugins
		if (!current_user_can('update_plugins') ) return;
		
		$upgrade_url = "plugin-install.php?tab=search&type=term&s=nrelate&plugin-search-input=Search+Plugins";
				

			
			// Show message
			$msg = sprintf(__('It seems like you are running incompatible versions of nrelate plugins.  Please update to the latest versions. <a href="%s">Click here to check.</a><br>(If you believe this message is incorrect, you can reset it by deactivating and reactivating each nrelate plugin.)', 'nrelate'), $upgrade_url );
			echo '<div class="error"><p>' . $msg . '</p></div>';
	}

	// Show notice on dashboard
	// priority set to 1, so we show up first.
	function nrelate_product_check_dashbord() {
		global $nrelate_outdated_plugin_notice;
		if ( is_array($nrelate_outdated_plugin_notice) && count($nrelate_outdated_plugin_notice) ) {
			$msg = sprintf(__('Running the latest versions of nrelate plugins is highly recommended.&nbsp;%s&nbsp;', 'nrelate'), implode('. ', $nrelate_outdated_plugin_notice) );
			$notice = '<li><div class="warning">' . $msg . '</div></li>';
			echo $notice;
		}
	}
	add_action ('nrelate_admin_messages','nrelate_product_check_dashbord',1);

}
add_action('admin_head','nrelate_product_check');

/**
 * Common function to load Tooltips
 * Since v0.50.0
 */
function nrelate_tooltip($tip) {
	global $nrelate_tooltips;
	$output = '';
	if ( array_key_exists( $tip, $nrelate_tooltips )) {
		$output = '<span class="nrelate-tooltip"><a class="nrelate-help" title="' . $nrelate_tooltips[$tip] . '">tip</a></span>';
	}
	return $output;
}

/**
 * Common function to load qTip Tooltips
 * Since v0.50.0
 */
function nrelate_qtip() { ?>

<script type="text/javascript">
// Create the tooltips only on document load
jQuery(document).ready(function() // wordpress 
   {
	jQuery('a.nrelate-help[title]').qtip({
		content: 
		{
			text: false
		},
		style:
		{
			classes: 'ui-tooltip-nrelate'
		},
		position:
		{
			viewport: jQuery(window), // Keep the tooltip on-screen at all times
			effect: false // Disable positioning animation
		},
		show:
		{
			event: 'click',
			solo: true // Only show one tooltip at a time
		},
		hide: 'unfocus'
	})
});
</script>

<?php
}
add_action('nrelate_admin_page_footer','nrelate_qtip');


/**
 * Plugin page header
 *
 * Displays name and description for plugin
 * Does some standard form checking
 * Since v0.44.0
 */
function nrelate_plugin_page_header($name, $description) { ?>
<script type="text/javascript">
	//<![CDATA[
	/*
	* User warning if switching tabs without saving
	*/
	jQuery(document).ready(function($) {
		$('form')
			// Store initial status
			.each(function(){
				var $this = $(this);
				$this.data( 'init_status', $this.serialize() );
				$this.data( 'is_dirty', false );
			})
			// Disable dirty check when submitting
			.submit(function(){
				$(window).unbind('beforeunload');
			});
		
		// Keep track of changes on form's inputs
		$(':input').live('change keyup', function(){
			$form = $(this).closest('form');
			if ( $form.serialize() != $form.data('init_status') ) {
				$form.data( 'is_dirty', true );
			} else {
				$form.data( 'is_dirty', false );
			}
		});
		
		function is_page_dirty() {
			var is_dirty = false;
			// Iterate through forms checking if there's any dirty
			$('form').each(function(){
				if ( $(this).data( 'is_dirty' ) ) {
					is_dirty = true;
					// If found one dirty, stop iterating returning false
					return false;
				}
			});
			return is_dirty;
		}
		
		$(window).bind('beforeunload', function(){
			if ( is_page_dirty() ) {
				return "You haven't saved your changes. Do you really want to leave?";
			}
		});
	});
	//]]>
</script>
	<div class="wrap nrelate-settings nrelate-page" style="margin: 10px 0 0 0;">
	<?php do_action ( 'nrelate_page_before_h2');?>
	<img src="<?php echo NRELATE_ADMIN_IMAGES ?>/nrelate-logo.png" alt="nrelate Logo" style="float:left; margin: 0 20px 0 0" />
	<h2 class="nrelate-title">
		<?php echo $name //show plugin name ?>
		<a class="thickbox button add-new-h2" title="nrelate Terms Of Service" href="admin-ajax.php?action=get_nrelate_tos&amp;height=385&amp;width=640">Terms Of Service</a>
		<p><?php echo $description //show plugin description ?></p>
	</h2>
<?php
}


/**
 * Settings Page: MAIN Settings description
 */
function nrelate_text_main($name) {
	printf(__('%s Main controls for ' . $name . ' plugin. %s','nrelate'), '<p class="section-desc">', '</p>');
}

/**
 * Settings Page: CUSTOM FIELDS link
 */
function nrelate_text_custom_fields( $divstyle ) {

	echo '<div class="nr_image_option" id="imagecustomfield" '.$divstyle.'>';
	printf(__('%s Click Here, to enter your custom field on the nrelate dashboard, under CUSTOM FIELD FOR IMAGES settings. > %s','nrelate'), '<a href="admin.php?page=nrelate-main#admin_custom_field">', '</a>');
	echo '</div>';
}		
		
/**
 * Settings Page: EXCLUDE CATEGORIES link
 */
function nrelate_text_exclude_categories() {
	printf(__('%s Click Here, to select categories to exclude under the EXCLUDE CATEGORIES settings. > %s','nrelate'), '<a href="admin.php?page=nrelate-main#exclude-cats">', '</a>');
}

/**
 * Settings Page: PARTNER Settings description
 */
function nrelate_text_partner($name) {
	printf(__('%s' . $name . ' can be brought in from your blogroll. %s','nrelate'), '<p class="section-desc">', '</p>');
}

/**
 * Settings Page: LAYOUT Settings description
 */
function nrelate_text_layout($name) {
	printf(__('%sWhere do you want your ' . $name . ' to display?%s','nrelate'), '<p class="section-desc">', '</p>');
}

/**
 * Settings Page: WIDGET PAGE link
 */
function nrelate_text_widget_page() {
	printf(__('To use our widget, visit %syour widget page.%s','nrelate'), '<a href="widgets.php">', '</a>');
}
	
/**
 * Settings Page: ADVERTISING Settings description
 */
function nrelate_text_advertising() {
	
	$ad_signup = '<a href="' . NRELATE_WEBSITE_AD_SIGNUP . '" target="_blank">';
	$ad_signup_close = '</a>';
	$admin_page = '<a href="admin.php?page=nrelate-main">';
	$admin_page_close = '</a>';
	
	echo '<p class="section-desc">';
	printf(__('nrelate can display content ads within the plugin and you can earn money. To signup, please %sregister at nrelate Partners and create an account%s.','nrelate'), $ad_signup, $ad_signup_close);
	echo '</p>';
	do_action ( 'nrelate_before_ad_settings' );
}

/**
 * Settings Page: ADVERTISING: Show money image
 */
function nrelate_wanna_make_money() {
	echo '<a title="Register to start making money" href="' . NRELATE_WEBSITE_AD_SIGNUP . '" target="_blank"><img src="' . NRELATE_ADMIN_IMAGES . '/nrelate_advertising.png" /></a>';
	}
	

/**
 * Settings Page: LABS Settings description
 */
function nrelate_text_labs() {
	printf(__('%sTry out some new nrelate features, by selecting the BETA VERSION.%s','nrelate'), '<p class="section-desc">', '</p>');
}

/**
 * Settings Page: RESET Settings description
 */
function nrelate_text_reset() {
	printf(__('%sTo reset the plugin to defaults, check the box below, deactivate the plugin, and then reactivate it.%s','nrelate'), '<p class="section-desc">', '</p>');
}


/**
 * Save / Preview button
 *
 * Includes error messages
 * since v0.46.0
 */
function nrelate_save_preview() { ?>
	<span class="nrelate_disabled_preview">
		<span class="nrelate-hidden thumbnails_message nr_error"><p><?php echo __('No CSS Stylesheet is selected for Thumbnails mode. Please change this setting.', 'nrelate'); ?></p></span>
		<span class="nrelate-hidden text_message nr_error"><p><?php echo __('No CSS Stylesheet is selected for Text mode. Please change this setting.', 'nrelate'); ?></p></span>
		<span class="nrelate-hidden text-warning-message nr_error"><p><?php echo __('When selecting TEXT mode you must show either <a href="#nrelate_show_post_title">Post Title</a> or <a href="#nrelate_show_post_excerpt">Post Excerpt</a>.'); ?></p></span>
	</span>
	<span class="nrelate-submit-preview">
		<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes','nrelate'); ?>" <?php echo NRELATE_API_ONLINE ? '' : 'disabled="disabled" title="Sorry nrelate\'s api server is not available. Please try again later"'; ?> />
		<button type="button" class="nrelate_preview_button button-primary" <?php echo NRELATE_API_ONLINE ? '' : 'disabled="disabled" title="Sorry nrelate\'s api server is not available. Please try again later"'; ?>> <?php _e('Preview','nrelate'); ?> </button>
	</span>
<?php
}

/**
 * Save button
 *
 * since v0.50.0
 */
function nrelate_save() { ?>
	<p class="submit">
		<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes','nrelate'); ?>" <?php echo NRELATE_API_ONLINE ? '' : 'disabled="disabled" title="Sorry nrelate\'s api server is not available. Please try again later"'; ?> />
	</p>
<?php
}

/**
 * nrelate check for: where to show options
 *
 * shows warnings to user
 *
 * @moved to common 0.50.0
 */
function nrelate_where_to_show_check() {

$warning = <<< JAVA_SCRIPT
jQuery(document).ready(function(){
	var nrelate_where_to_show_check = jQuery('#nrelate-where-to-show :checkbox');
	
	nrelate_where_to_show_check.change(function(){
		var me = jQuery(this);
		
		if ( !me.is(':checked') && me.val() == 'is_single' ) {
			if ( !confirm("Are you sure you do not want to show related content on single posts?") ) {
				me.attr('checked', true );
			}
		}
		
		if ( nrelate_where_to_show_check.filter(':checked').size() == 0 ) {
			if ( !confirm("The plugin will not be shown anywhere. Please confirm.") ) {
				me.attr('checked', true );
			}
		}
		
		// Check children
		me.parent().siblings('.children').find(':checkbox').attr('checked', me.is(':checked'));
		
		// If a child, check/uncheck parent according to group
		var siblings = me.closest('.children').find(':checkbox');
		if ( siblings.size() ) {
			if ( siblings.size() == siblings.filter(':checked').size() ) {
				me.closest('.parent-category').children('.selectit').find(':checkbox').attr('checked', true);	
			} else {
				me.closest('.parent-category').children('.selectit').find(':checkbox').attr('checked', false);
			}
		}
		
	});								
});
JAVA_SCRIPT;

	echo "<script type='text/javascript'>{$warning}</script>";
}

?>