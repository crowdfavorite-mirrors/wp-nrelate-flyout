<?php
/**
 * nrelate Plugin Status
 *
 * Activation, Deactivation and Upgrade functions
 *
 * @package nrelate
 * @subpackage Functions
 */
 
 

global $nr_fo_std_options, $nr_fo_ad_options, $nr_fo_layout_options, $nr_fo_anim_options;

// Default Options
// ALL options must be listed
$nr_fo_std_options = array(
		"flyout_title" => "You may also like-",
		"flyout_version" => NRELATE_FLYOUT_PLUGIN_VERSION,
		"flyout_bar" => "Low",
		"flyout_max_age_num" => "10",
		"flyout_max_age_frame" => "Year(s)",
		"flyout_animation" => "Slideout",
		"flyout_loc" => "Right",
		"flyout_anim_width" => "360",
		"flyout_anim_width_type" => "px",
		"flyout_display_logo" => false,
		"flyout_reset" => "",
		"flyout_show_post_title" => 'on',
		"flyout_max_chars_per_line" => 100,
		"flyout_show_post_excerpt" => "",
		"flyout_max_chars_post_excerpt" => 25,		
		"flyout_thumbnail" => "Thumbnails",
		"flyout_thumbnail_size" => 90,
		"flyout_default_image" => NULL,
		"flyout_number_of_posts" => 1,
		"flyout_offset"=>2,
		"flyout_offset_element"=>"#comments",
		"flyout_where_to_show" => array( "is_single" ),
		"flyout_nonjs" => 0,
		"flyout_from_bot" => "0",
		"flyout_from_bot_type" => "px"
	);

$nr_fo_ad_options = array(
		"flyout_display_ad" => false,
		"flyout_ad_animation" => "on",
		"flyout_validate_ad" => NULL,
		"flyout_number_of_ads" => 1,
		"flyout_ad_placement" => "Last",
		"flyout_ad_title" => "More from the Web -"
	);
		
$nr_fo_layout_options = array(		
		"flyout_thumbnails_style" => "huf",
		"flyout_thumbnails_style_separate" => "huf-2row",
		"flyout_text_style" => "default",
		"flyout_text_style_separate" => "default-text-2col"
);

$nr_fo_anim_options = array(		
		"flyout_anim_slideout_style" => "nyt",
		"flyout_anim_fade_style" => "nyt"
);


/**
 * Upgrade function
 *
 * @since 0.46.0
 */
add_action('admin_init','nr_fo_upgrade');
function nr_fo_upgrade() {
	$flyout_settings = get_option('nrelate_flyout_options');
	$flyout_ad_settings = get_option('nrelate_flyout_options_ads');
	$flyout_layout_settings = get_option('nrelate_flyout_options_styles');
	$flyout_anim_settings = get_option('nrelate_flyout_anim_options_styles');
	$current_version = $flyout_settings['flyout_version'];
	
	// If settings exist and we're running on old version (or version doesn't exist), then this is an upgrade
	if ( ( !empty( $flyout_settings ) ) && ( $current_version < NRELATE_FLYOUT_PLUGIN_VERSION ) )  {
	
		nrelate_system_check(); // run system check
		
		global $nr_fo_std_options, $nr_fo_ad_options, $nr_fo_layout_options, $nr_fo_anim_options, $nr_fo_old_checkbox_options;
			
			
			// move all ad settings code from flyout settings to advertising settings: v0.50.0
			nrelate_upgrade_option('nrelate_flyout_options', 'flyout_display_ad', 'nrelate_flyout_options_ads', 'flyout_display_ad');
			nrelate_upgrade_option('nrelate_flyout_options', 'flyout_number_of_ads', 'nrelate_flyout_options_ads', 'flyout_number_of_ads');
			nrelate_upgrade_option('nrelate_flyout_options', 'flyout_ad_placement', 'nrelate_flyout_options_ads', 'flyout_ad_placement');
			nrelate_upgrade_option('nrelate_flyout_options', 'flyout_ad_animation', 'nrelate_flyout_options_ads', 'flyout_ad_animation');
			
			// re-get the latest since we just made changes
			$flyout_settings = get_option('nrelate_flyout_options');
			$flyout_ad_settings = get_option('nrelate_flyout_options_ads');
			$flyout_layout_settings = get_option('nrelate_flyout_options_styles');
			$flyout_anim_settings = get_option('nrelate_flyout_anim_options_styles');

			// Update new options if they don't exist
			$flyout_settings = wp_parse_args( $flyout_settings, $nr_fo_std_options );
			$flyout_ad_settings = wp_parse_args( $flyout_ad_settings, $nr_fo_ad_options );
			$flyout_layout_settings = wp_parse_args( $flyout_layout_settings, $nr_fo_layout_options );
			$flyout_anim_settings = wp_parse_args( $flyout_anim_settings, $nr_fo_anim_options );
			
			// now update again
			update_option('nrelate_flyout_options', $flyout_settings);
			update_option('nrelate_flyout_options_ads', $flyout_ad_settings);
			update_option('nrelate_flyout_options_styles', $flyout_layout_settings);
			update_option('nrelate_flyout_anim_options_styles', $flyout_anim_settings);
			
			// Update version number in DB
			$flyout_settings = get_option('nrelate_flyout_options');
			$flyout_settings['flyout_version'] = NRELATE_FLYOUT_PLUGIN_VERSION;
			update_option('nrelate_flyout_options', $flyout_settings);
			
			// Ping nrelate servers about the upgrade
			$body=array(
				'DOMAIN'=>NRELATE_BLOG_ROOT,
				'VERSION'=>NRELATE_FLYOUT_PLUGIN_VERSION,
				'KEY'=>get_option('nrelate_key'),
				'PLUGIN'=>"flyout"
			);
			$url = 'http://api.nrelate.com/common_wp/'.NRELATE_LATEST_ADMIN_VERSION.'/versionupdate.php';

			$result=wp_remote_post($url,array('body'=>$body,'blocking'=>false, 'timeout'=>15));
					
			// Calculate plugin file path
			$dir = substr( realpath(dirname(__FILE__) . '/..'), strlen(WP_PLUGIN_DIR) );
			$file = key( get_plugins( $dir ) );
			$plugin_file = substr($dir, 1) . '/' . $file;
			// Update the WP database with the new version number and additional info about this plugin
			nrelate_products("flyout",NRELATE_FLYOUT_PLUGIN_VERSION,NRELATE_FLYOUT_ADMIN_VERSION,1, $plugin_file);
	}
}


  
 /**
 * Define default options for settings
 *
 * @since 0.1
 */
 
 
// Add default values to nrelate_flyout_options in wordpress db
// After conversion, send default values to nrelate server with user's home url and rss url
// UPDATE (v.0.2.2): add nrelate ping host to ping list and enable xml-rpc ping
// UPDATE (v.0.2.2): notify nrelate server when this plugin is activated
// UPDATE (v.0.3): send the plugin version info to nrelate server
function nr_fo_add_defaults() {

	nrelate_system_check(); // run system check
	
	// Calculate plugin file path
	$dir = substr( realpath(dirname(__FILE__) . '/..'), strlen(WP_PLUGIN_DIR) );
	$file = key( get_plugins( $dir ) );
	$plugin_file = substr($dir, 1) . '/' . $file;

	nrelate_products("flyout",NRELATE_FLYOUT_PLUGIN_VERSION,NRELATE_FLYOUT_ADMIN_VERSION,1, $plugin_file); // add this product to the nrelate_products array
	
	global $nr_fo_std_options, $nr_fo_ad_options, $nr_fo_layout_options, $nr_fo_anim_options;

	$tmp = get_option('nrelate_flyout_options');
	// If flyout_reset value is on or if nrelate_flyout_options was never created, insert default values
    if(($tmp['flyout_reset']=='on')||(!is_array($tmp))) {
		
		update_option('nrelate_flyout_options', $nr_fo_std_options);
		update_option('nrelate_flyout_options_ads', $nr_fo_ad_options);		
		update_option('nrelate_flyout_options_styles', $nr_fo_layout_options);
		update_option('nrelate_flyout_anim_options_styles', $nr_fo_anim_options);
		
		// Convert some values to send to nrelate server
		$r_bar = "Low";
		$r_title = "You may also like -";
		$r_max_age = 10;
		$r_max_frame = "Year(s)";
		$r_display_post_title = true;
		$r_max_char_per_line = 100;
		$r_max_char_post_excerpt = 100;
		$r_display_ad = false;
		$r_display_logo = false;
		$r_flyout_reset = "";
		$flyout_thumbnail = "Thumbnails";
		$r_validate_ad = NULL;
		$backfillimage = NULL;
		$flyout_thumbnail_size=90;
		$flyout_ad_animation="on";
		$flyout_animation="Fly";
		$flyout_loc="right";
		$noflyoutposts=1;
		$flyout_offset=2;
		$flyout_offset_element="#comments";
		$r_number_of_ads = 1;
		$r_ad_placement = "Last";
		$r_ad_title = "More from the Web -";
		$r_nonjs = 0;
		$flyout_from_bot=0;
		$flyout_from_bot_type="px";
		$r_anim_width = 360;
		$r_anim_width_type = "px";

		// Convert max age time frame to minutes
		switch ($r_max_frame)
		{
		case 'Hour(s)':
		  $maxageposts = $r_max_age * 60;
		  break;
		case 'Day(s)':
		  $maxageposts = $r_max_age * 1440;
		  break;
		case 'Week(s)':
		  $maxageposts = $r_max_age * 10080;
		  break;
		case 'Month(s)':
		  $maxageposts = $r_max_age * 44640;
		  break;
		case 'Year(s)':
		  $maxageposts = $r_max_age * 525600;
		  break;
		}

		// Convert ad parameter
		switch ($r_display_ad)
		{
		case true:
			$ad = 1;
			break;
		default:
			$ad = 0;
		}

		// Convert display post title parameter
		switch ($r_display_post_title)
		{
		case 'on':
		  $r_display_post_title = 1;
		  break;
		default:
		 $r_display_post_title = 0;
		}
		
		// Convert logo parameter
		switch ($r_display_logo)
		{
		case 'on':
		  $logo = 1;
		  break;
		default:
		 $logo = 0;
		}

		// Convert thumbnail option parameter
		switch ($flyout_thumbnail)
		{
		case 'Thumbnails':
			$thumb = 1;
			break;
		default:
			$thumb = 0;
		}
		$r_show_post_title = isset($r_show_post_title) ? $r_show_post_title : null;
		$backfill = isset($backfill) ? $backfill : null;
		
		$body=array(
			'DOMAIN'=>NRELATE_BLOG_ROOT,
			'VERSION'=>NRELATE_FLYOUT_PLUGIN_VERSION,
			'KEY'=>get_option('nrelate_key'),
			'NUM'=>$noflyoutposts,
			'R_BAR'=>$r_bar,
			'HDR'=>$r_title,
			'MAXPOST'=>$maxageposts,
			'SHOWPOSTTITLE'=>$r_show_post_title,
			'MAXCHAR'=>$r_max_char_per_line,
			'ADOPT'=>$ad,
			'THUMB'=>$thumb,
			'ADCODE'=>$r_validate_ad,
			'LOGO'=>$logo,
			'IMAGEURL'=>$backfill,
			'THUMBSIZE'=>$flyout_thumbnail_size,
			'ANIMATION'=>$flyout_animation,
			'LOCATION'=>$flyout_loc,
			'OFFSET'=>$flyout_offset,
			'ELEMENT'=>$flyout_offset_element,
			'ADNUM'=>$r_number_of_ads,
			'ADPLACE'=>$r_ad_placement,
			'ADTITLE'=>$r_ad_title,
			'NONJS'=>$r_nonjs,
			'FROMBOT'=>$flyout_from_bot,
			'FROMBOTTYPE'=>urlencode($flyout_from_bot_type),
			'WIDTH'=>$r_anim_width,
			'WIDTHTYPE'=>urlencode($r_anim_width_type)
		);
		$url = 'http://api.nrelate.com/fow_wp/'.NRELATE_FLYOUT_PLUGIN_VERSION.'/processWPflyoutAll.php';
		
		$result=wp_remote_post($url,array('body'=>$body,'blocking'=>false, 'timeout'=>15));
	}

	// RSS mode is sent again just incase if the user already had nrelate_flyout_options in their wordpress db
	// and doesn't get sent above
	$excerptset = get_option('rss_use_excerpt');
	$rss_mode = "FULL";
	if ($excerptset != '0') { // are RSS feeds set to excerpt
		update_option('nrelate_admin_msg', 'yes');
		$rss_mode = "SUMMARY";
	}

	$rssurl = get_bloginfo('rss2_url');

	// Add our ping host to the ping list
	$current_ping_sites = get_option('ping_sites');
	$pingexist = strpos($current_ping_sites, "http://api.nrelate.com/rpcpinghost/");
	if($pingexist == false){
	$pinglist = <<<EOD
$current_ping_sites
http://api.nrelate.com/rpcpinghost/
EOD;
	update_option('ping_sites',$pinglist);
	}
	// Enable xmlrpc for the user
	update_option('enable_xmlrpc',1);


	//Set up a unique nrelate key, for secure feed access
	$key = get_option( 'nrelate_key' );
	if ( empty( $key ) ) {
		$key = wp_generate_password( 24, false, false );
		update_option( 'nrelate_key', $key );
	}
	
	// Send notification to nrelate server of activation and send us rss feed mode information
	$action = "ACTIVATE";
	$body=array(
		'DOMAIN'=>NRELATE_BLOG_ROOT,
		'ACTION'=>$action,
		'RSSMODE'=>$rss_mode,
		'VERSION'=>NRELATE_FLYOUT_PLUGIN_VERSION,
		'KEY'=>get_option('nrelate_key'),
		'ADMINVERSION'=>NRELATE_FLYOUT_ADMIN_VERSION,
		'PLUGIN'=>'flyout',
		'RSSURL'=>$rssurl
	);
	$url = 'http://api.nrelate.com/common_wp/'.NRELATE_FLYOUT_ADMIN_VERSION.'/wordpressnotify_activation.php';
	
	$result=wp_remote_post($url,array('body'=>$body,'blocking'=>false, 'timeout'=>15));

}
 
 
// Deactivation hook callback
function nr_fo_deactivate(){

	$nrelate_active=nrelate_products("flyout",NRELATE_FLYOUT_PLUGIN_VERSION,NRELATE_FLYOUT_ADMIN_VERSION,0);
	
	if($nrelate_active==0){
		// Remove our ping link from ping_sites
		$current_ping_sites = get_option('ping_sites');
		$new_ping_sites = str_replace(array("\nhttp://api.nrelate.com/rpcpinghost/","http://api.nrelate.com/rpcpinghost/"), "", $current_ping_sites);
		update_option('ping_sites',$new_ping_sites);
	}
	
	// RSS mode is sent again just incase if the user already had nrelate_flyout_options in their wordpress db
	// and doesn't get sent above
	$excerptset = get_option('rss_use_excerpt');
	$rss_mode = "FULL";
	if ($excerptset != '0') { // are RSS feeds set to excerpt
		update_option('nrelate_admin_msg', 'yes');
		$rss_mode = "SUMMARY";
	}

	$rssurl = get_bloginfo('rss2_url');

	// Send notification to nrelate server of deactivation
	$action = "DEACTIVATE";
	$body=array(
		'DOMAIN'=>NRELATE_BLOG_ROOT,
		'ACTION'=>$action,
		'RSSMODE'=>$rss_mode,
		'VERSION'=>NRELATE_FLYOUT_PLUGIN_VERSION,
		'KEY'=>get_option('nrelate_key'),
		'ADMINVERSION'=>NRELATE_FLYOUT_ADMIN_VERSION,
		'PLUGIN'=>'flyout',
		'RSSURL'=>$rssurl
	);
	$url = 'http://api.nrelate.com/common_wp/'.NRELATE_FLYOUT_ADMIN_VERSION.'/wordpressnotify_activation.php';

	$result=wp_remote_post($url,array('body'=>$body,'blocking'=>false, 'timeout'=>15));
}

// Uninstallation hook callback
function nr_fo_uninstall(){
	
	// Delete nrelate flyout options from user's wordpress db
	delete_option('nrelate_flyout_options');
	delete_option('nrelate_flyout_options_ads');
	delete_option('nrelate_flyout_options_styles');
	delete_option('nrelate_flyout_anim_options_styles');
	
	$nrelate_active=nrelate_products("flyout",NRELATE_FLYOUT_PLUGIN_VERSION,NRELATE_FLYOUT_ADMIN_VERSION,-1);
	
	if ($nrelate_active<0){
		// This occurs if the user is deleting all of nrelate's products
		
		// Remove our ping link from ping_sites
		$current_ping_sites = get_option('ping_sites');
		$new_ping_sites = str_replace(array("\nhttp://api.nrelate.com/rpcpinghost/","http://api.nrelate.com/rpcpinghost/"), "", $current_ping_sites);
		update_option('ping_sites',$new_ping_sites);
		
		// Delete nrelate admin options from users wordpress db
		delete_option('nrelate_products');
		delete_option('nrelate_admin_msg');
		delete_option('nrelate_admin_options');
		$current_ping_sites = get_option('ping_sites');
		$new_ping_sites = str_replace("\nhttp://api.nrelate.com/rpcpinghost/", "", $current_ping_sites);
		update_option('ping_sites',$new_ping_sites);
	}
	
	// RSS mode is sent again just incase if the user already had nrelate_flyout_options in their wordpress db
	// and doesn't get sent above
	$excerptset = get_option('rss_use_excerpt');
	$rss_mode = "FULL";
	if ($excerptset != '0') { // are RSS feeds set to excerpt
		update_option('nrelate_admin_msg', 'yes');
		$rss_mode = "SUMMARY";
	}
	
	$rssurl = get_bloginfo('rss2_url');
	
	// Send notification to nrelate server of uninstallation
	$action = "UNINSTALL";
	$body=array(
		'DOMAIN'=>NRELATE_BLOG_ROOT,
		'ACTION'=>$action,
		'RSSMODE'=>$rss_mode,
		'VERSION'=>NRELATE_FLYOUT_PLUGIN_VERSION,
		'KEY'=>get_option('nrelate_key'),
		'ADMINVERSION'=>NRELATE_FLYOUT_ADMIN_VERSION,
		'PLUGIN'=>'flyout',
		'RSSURL'=>$rssurl
	);
	$url = 'http://api.nrelate.com/common_wp/'.NRELATE_FLYOUT_ADMIN_VERSION.'/wordpressnotify_activation.php';

	$result=wp_remote_post($url,array('body'=>$body,'blocking'=>false, 'timeout'=>15));
}

?>