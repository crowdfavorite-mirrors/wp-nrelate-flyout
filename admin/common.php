<?php
/**
 * Common Admin Functions File
 *
 * Load Admin common functions
 *
 * Checks if another nrelate plugin loaded these functions first
 * 
 * @package nrelate
 * @subpackage Functions
 */

define( 'NRELATE_COMMON_LOADED', true );

/**
 * nrelate admin menu hook
 *
 * add hook to top of page
 * add hook to footer
 *
 * Since v0.49.0
 * Footer added v0.50.0
 */
function nrelate_admin_menu() {

	if ( isset( $_GET['page'] ) && ( substr($_GET['page'], 0, 7) == 'nrelate' ) ) { 
		// Initial page hook
		do_action ('nrelate_admin_page');
		
		// Footer hook
		function nrelate_admin_menu_footer() {
			do_action ('nrelate_admin_page_footer');
		}
		add_action('admin_footer','nrelate_admin_menu_footer');
	}
}
add_action('admin_menu','nrelate_admin_menu');


/**
 * Save settings hook
 *
 * Since v0.49.0
 */
function nrelate_save_settings() {

	function nrelate_admin_notices() {
	
		if ( (isset($_GET['updated']) && $_GET['updated'] == 'true') || (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') ) {
			
			$msg = __('nrelate settings updated');
			
			$msg = apply_filters( 'nrelate_flush_cache', $msg );
			
			echo '<div class="updated"><p><strong>'.$msg.'.</strong></p></div>';
			
			do_action ('nrelate_settings_updated');
		}
	}
	add_action('admin_notices','nrelate_admin_notices');
}
add_action('nrelate_admin_page','nrelate_save_settings');


/**
 * Clean third party plugins cache
 *
 * Since v0.50.0
 */
function nrelate_flush_w3tc_cache( $msg = '' ) {
	// Flush Total Cache
	// @cred: http://wordpress.org/extend/plugins/wordpress-seo/
	if ( function_exists('w3tc_pgcache_flush') ) {
		w3tc_pgcache_flush();
		$msg .= __(' &amp; W3 Total Cache Page Cache flushed');
	}
	return $msg;
}
add_filter('nrelate_flush_cache', 'nrelate_flush_w3tc_cache');


function nrelate_flush_wpsc_cache( $msg = '' ) {
	// Flush Super Cache
	// @cred: http://wordpress.org/extend/plugins/wordpress-seo/
	if (function_exists('wp_cache_clear_cache')) {
		wp_cache_clear_cache();
		$msg .= __(' &amp; WP Super Cache flushed');
	}
	return $msg;
}
add_filter('nrelate_flush_cache', 'nrelate_flush_wpsc_cache');


/**
 * Nrelate Products Array
 * 
 * Holds information about all of nrelate products that are installed
 * 
 * $status = 
 * 		-1: uninstalled, 0:deactivated, 1:activated
 * 
 * return values = 
 * 		<0: all are uninstalled 0: all deactivated, 1: at least one activated
 */
function nrelate_products($product,$version,$admin_version,$status, $plugin_file=false){
	$nrelate_products = get_option('nrelate_products');
	if($status==-1){
		unset($nrelate_products[$product]);
	}
	else{
		$nrelate_products[$product]["status"]=$status;
		$nrelate_products[$product]["version"]=$version;
		$nrelate_products[$product]["admin_version"]=$admin_version;
		$nrelate_products[$product]["time_stamp"]=time();		
		if ($plugin_file) {
			$nrelate_products[$product]["plugin_file"]=$plugin_file;
		}
		if ($status) {
			// Flush cache if activating plugin
			apply_filters( 'nrelate_flush_cache', '' );
		}
	}
	update_option('nrelate_products', $nrelate_products);
	if(count($nrelate_products)==0)
		return -1;
	foreach($nrelate_products as $productname => $productinfo){
		if($productinfo["status"]==1)
			return 1;
	}
	return 0;
}




/**
 * System check
 *
 * verifies whether the current system meets our minimum requirements
 */
function nrelate_system_check(){
	$plugin = NRELATE_PLUGIN_BASENAME;
	$warning = "<p><strong>".__('nrelate Warning(s):', 'nrelate')."</strong></p>";
	
	// WordPress version check
	if (!version_compare(NRELATE_MIN_WP, get_bloginfo('version'), '<=')) {
		$message .= "<li>".sprintf(__('You\'re running WordPress version %1$s. nrelate requires WordPress version %2$s.<br/>Please upgrade to WordPress version %2$s.', 'nrelate' ), get_bloginfo('version'), NRELATE_MIN_WP ) . "</li>";
	}
	
	// PHP version check
	if (!version_compare(NRELATE_MIN_PHP, PHP_VERSION, '<')) {
		$message .= "<li>".sprintf(__('You\'re server is running PHP version %1$s. nrelate requires PHP version %2$s.<br/>Please contact your web host and request PHP version %2$s.', 'nrelate' ), PHP_VERSION, NRELATE_MIN_PHP ) . "</li>";
	}
	
	$closing = "<p>".__('The nrelate plugin has been deactivated.','nrelate')."<br/><br/><a href=\"/wp-admin\">".__('Click here to return to your WordPress dashboard.','nrelate')."</a></p>";
		
	if (!empty($message)) {
		deactivate_plugins($plugin);
		wp_die( $warning . "<ol>" . $message . "<ol>" . $closing );
	}
}

/**
 * Show Terms of Service in Thickbox
 */
function nrelate_tos($dummy='') {

	$args=array("timeout"=>2);
	$result = wp_remote_post('http://static.nrelate.com/common_wp/'.NRELATE_LATEST_ADMIN_VERSION.'/terms-of-service.html',$args);

	if (!is_wp_error($response) && $response['response']['code']==200 && $response['response']['message']=='OK') {
		$tos = $response['body'];
	} else {
		$tos = sprintf(__('Please <a href="%s" target="_blank">click here</a> to read our Terms of Service on our website.', 'nrelate'), 'http://static.nrelate.com/common_wp/'.NRELATE_LATEST_ADMIN_VERSION.'/terms-of-service.html');
	}
		
	$output = '<div id="nrelate-tos" style="display:none"><div id="nrelate-terms">' . $tos . '</div></div><a class="thickbox button add-new-h2" title = "nrelate Terms Of Service" href="#TB_inline?height=385&amp;width=640&amp;inlineId=nrelate-tos">Terms Of Service</a>';
	echo $output;
}


/**
 * Setup Dashboard menu and menu page
 */
function nrelate_setup_dashboard() {
	require_once NRELATE_ADMIN_DIR . '/nrelate-admin-settings.php';
	require_once NRELATE_ADMIN_DIR . '/nrelate-main-menu.php';
	require_once NRELATE_ADMIN_DIR . '/admin-messages.php';
	global $dashboardpage,$mainsectionpage;
	$mainsectionpage = add_menu_page(__('Dashboard','nrelate'), __('nrelate','nrelate'), 'manage_options', 'nrelate-main', 'nrelate_main_section', NRELATE_ADMIN_IMAGES . '/menu-logo.png');
	$dashboardpage = add_submenu_page('nrelate-main', __('Dashboard','nrelate'), __('Dashboard','nrelate'), 'manage_options', 'nrelate-main', 'nrelate_main_section');
};
add_action('admin_menu', 'nrelate_setup_dashboard');
 

/**
 * Load Admin Scripts
 *
 * @since 0.47.3
 */
function nrelate_load_admin_scripts() {
	wp_enqueue_script('nrelate_admin_js', NRELATE_ADMIN_URL.'/nrelate_admin_jsfunctions'. ( NRELATE_JS_DEBUG ? '' : '.min') .'.js', array('jquery'));
	wp_enqueue_script('qtip', NRELATE_ADMIN_URL.'/qtip/jquery.qtip.min.js', array('jquery'));
	wp_enqueue_script('thickbox'); //used for help videos
}
add_action('nrelate_admin_page','nrelate_load_admin_scripts');


/**
 * Load Admin Styles
 *
 * @since 0.47.3
 */
function nrelate_load_admin_styles() {
	wp_enqueue_style( 'nrelate-admin', NRELATE_ADMIN_URL . '/nrelate-admin.css', array(),NRELATE_LATEST_ADMIN_VERSION );
	wp_enqueue_style( 'qtip-style', NRELATE_ADMIN_URL . '/qtip/jquery.qtip.min.css');
	wp_enqueue_style('thickbox');
}
add_action('nrelate_admin_page','nrelate_load_admin_styles');







/**
 * Common function to load YouTube videos into our admin
 * $youtube_id = youtube id, not full url
 * $div_id = unique div id for each thickbox instance
 */
function nrelate_thickbox_youtube($youtube_id, $div_id) {

$output = '
<div id="' . $div_id .'" style="display:none">
	<div class="nrelate_help_video">
		<object width="640" height="385">
			<param name="movie" value="http://www.youtube.com/v/' . $youtube_id . '&autoplay=1?fs=1&amp;hl=en_US"></param>
			<param name="allowFullScreen" value="true"></param>
			<param name="allowscriptaccess" value="always"></param>
			<embed src="http://www.youtube.com/v/' . $youtube_id . '&autoplay=1?fs=1&amp;hl=en_US" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="640" height="385"></embed>
		</object>
	</div>
</div>
<a class="thickbox" href="#TB_inline?height=385&amp;width=640&amp;inlineId=' . $div_id . '">
	<img class="nrelate-help" src=' . NRELATE_ADMIN_IMAGES . '/help.png />
</a>';

return $output;
}


/**
 * Re-index function
 *
 * Since v0.45.0
 */
function nrelate_reindex() {
	$action = "REINDEX";
	$rss_mode = isset($rss_mode) ? $rss_mode : '';
	$rssurl = isset($rssurl) ? $rssurl : '';
	
	$body=array(
		'DOMAIN'=>NRELATE_BLOG_ROOT,
		'ACTION'=>$action,
		'RSSMODE'=>$rss_mode,
		'KEY'=>get_option('nrelate_key'),
		'ADMINVERSION'=>NRELATE_LATEST_ADMIN_VERSION,
		'RSSURL'=>$rssurl
	);
	$url = 'http://api.nrelate.com/common_wp/'.NRELATE_LATEST_ADMIN_VERSION.'/reindex.php';

	$result = wp_remote_post($url, array(
		'method'=>'POST',
		'body'=>$body,
		'blocking'=>false,
		'timeout'=>15
    	)
	);
	
	delete_option("nrelate_reindex_required");
}

/**
 * Check index status
 *
 * Since v0.45.0
 */
function nrelate_index_check() {

	$result = wp_remote_get("http://api.nrelate.com/common_wp/".NRELATE_LATEST_ADMIN_VERSION."/indexcheck.php?domain=".NRELATE_BLOG_ROOT."&getrequest=0",array("timeout"=>10));

	if (!is_wp_error($result)){
		echo '<li class="nolist"><div id="indexcheck" class="info">'.$result['body'].'</div></li>';
	}
}

/**
 * Get blogroll
 *
 * Takes user's bookmarks with category name 'blogroll'
 * Returns a string with all of the blogroll link urls separated by the less than character (<).
 */

function nrelate_get_blogroll(){
	$option = get_option('nrelate_related_options');
	
	$bm = array();
	
	if ( is_array($option['related_blogoption']) && count($option['related_blogoption']) ) {
		$categories_id = implode(',', $option['related_blogoption']);
		
		$bm = get_bookmarks( array(
			'category'  => $categories_id,
			'hide_invisible' => 1,
			'show_updated'   => 0, 
			'include'        => null,
			'exclude'        => null,
			'search'         => '.'));	
	}
	
	$counter=0;
	$tmp = '';
	foreach ($bm as $bookmark){
		if($counter<25)
			$tmp.=$bookmark->link_url.'<';
		$counter+=1;
	}
	return $tmp;
}

/**
 * Layout conditionals
 *
 * Define's conditionals for automatic placement
 * @ moved to common.php 0.49.3
 */

$nrelate_cond_tags = array(
	(object) array(
		"term_id" => 1,
		"check_val" => "is_front_page",
		"name" => "Front Page  (is_front_page)", 
		"parent" => 0
	),
	(object) array(
		"term_id" => 2,
		"check_val" => "is_home",
		"name" => "Main Page  (is_home)", 
		"parent" => 0
	),
	(object) array(
		"term_id" => 3,
		"check_val" => "is_single",
		"name" => "Single Posts  (is_single)", 
		"parent" => 0
	),
	(object) array(
		"term_id" => 4,
		"check_val" => "is_page",
		"name" => "Pages  (is_page)", 
		"parent" => 0
	),
	(object) array(
		"term_id" => 5,
		"check_val" => "is_archive",
		"name" => "All Archives  (is_archive)", 
		"parent" => 0
	),
			(object) array(
				"term_id" => 8,
				"check_val" => "is_category",
				"name" => "Category Archives  (is_category)", 
				"parent" => 5
			),
			(object) array(
				"term_id" => 9,
				"check_val" => "is_tag",
				"name" => "Tag Archives  (is_tag)", 
				"parent" => 5
			),
			(object) array(
				"term_id" => 10,
				"check_val" => "is_author",
				"name" => "Author Archives  (is_author)", 
				"parent" => 5
			),
			(object) array(
				"term_id" => 11,
				"check_val" => "is_date",
				"name" => "Date Archives  (is_date)", 
				"parent" => 5
			),
	(object) array(
		"term_id" => 6,
		"check_val" => "is_search",
		"name" => "Search Results  (is_search)", 
		"parent" => 0
	),
	(object) array(
		"term_id" => 7,
		"check_val" => "is_attachment",
		"name" => "Attachment Pages  (is_attachment)", 
		"parent" => 0
	),
	(object) array(
		"term_id" => 12,
		"check_val" => "is_404",
		"name" => "404 Page  (is_404)", 
		"parent" => 0
	)
);

/**
 * Add nrelate dropdown help
 *
 * add contextual help page to all nrelate pages
 * Since v0.44.0
 * Updated v0.46.0 for all pages
 */
function nrelate_dashboard_help($contextual_help, $screen_id) {
	$string = "nrelate";
	if (strstr($screen_id, $string)) {
		$contextual_help = nrelate_site_inventory();
	}
	return $contextual_help;
}
add_action('contextual_help', 'nrelate_dashboard_help', 10, 2);


/**
 * Website inventory for support
 *
 * used in dashboard help page
 * Since v0.44.0
 * @credits http://wordpress.org/extend/plugins/wphelpcenter/
 */
function nrelate_site_inventory(){
	$theme = get_theme(get_current_theme());
	$themename = $theme['Name'];
	$themeversion = $theme['Version'];
	$themeauthor = strip_tags($theme['Author']);
	$url = get_option('siteurl');
	$wp_version = get_bloginfo('version');
	global $wpmu_version, $wp_version;
		is_null($wpmu_version) ? $wp_type = __('WordPress (single user)', 'nrelate') : $wp_type = __('WordPress MU', 'nrelate');
	$phpversion = phpversion();
	
	//get active plugins
	$all_plugins = get_plugins();
	$active_plugins = array();
	$inactive_plugins = array();
	foreach ( (array)$all_plugins as $plugin_file => $plugin_data) {
		if ( is_plugin_active($plugin_file) ) {
			$active_plugins[ $plugin_file ] = $plugin_data;
		} else {
			$inactive_plugins[ $plugin_file ] = $plugin_data;
		}
	}
	$plugins = '';
	foreach ( (array)$active_plugins as $plugin_file => $plugin_data) {
		$plugins .= strip_tags($plugin_data['Title']). " " .strip_tags($plugin_data['Version']). " " . __('by:', 'nrelate') .  " " . strip_tags($plugin_data['Author']).'&#10;' ;
	}

$message = <<<EOD
<strong>If you are having trouble with our plugin please copy the information below and email it to: support@nrelate.com<br>
** This is sensitive information about your website and should not be posted publicly **</strong>
<textarea style="width:90%; height:200px;">
URL: $url 
WordPress Version: $wp_version
WordPress Type: $wp_type
PHP Version: $phpversion
Active Theme: $themename $themeversion by $themeauthor

Active Plugins:
$plugins
</textarea>
EOD;

return $message;
}



/**
 * Permalink warning
 *
 * User must reindex if changing permalinks
 * Since v0.60.0
 */
function nrelate_admin_notice_options_permalink(){
	global $pagenow;
    if ( $pagenow == 'options-permalink.php' ) {
	$message .= "<li>".sprintf(__('You\'re running WordPress version %1$s. nrelate requires WordPress version %2$s.<br/>Please upgrade to WordPress version %2$s.', 'nrelate' ), get_bloginfo('version'), NRELATE_MIN_WP ) . "</li>";
	
         echo "<div class=\"error\"><p>"
		 .sprintf(__('<strong>nRelate message:</strong> If you change your permalink settings, you must visit the <a href="%s">nRelate dashboard</a> and press the REINDEX WEBSITE button.', 'nrelate'), 'admin.php?page=nrelate-main')
		 ."</div>";
    }
}
add_action('admin_notices', 'nrelate_admin_notice_options_permalink');







/**
 * Old to New Options for all plugins
 *
 * @param string $old_option - Old Option name
 * @param string $old_option_key - old Option key name
 * @param string $new_option - new Option name
 * @param string $new_option_key - new Option key name
 * 
 * Since v0.42.2
 */
function nrelate_upgrade_option($old_option, $old_option_key, $new_option, $new_option_key) {
    $old_value = get_option($old_option);
	$old_value = isset($old_value[$old_option_key]) ? $old_value[$old_option_key] : false;
	$old_value = ($old_value == false) ? array() : $old_value;
    if ($old_value != false) {
        $new_value = get_option($new_option);
        $new_value = ($new_value == false) ? array() : $new_value;

        $new_value[$new_option_key] = $old_value;
        update_option($new_option, $new_value);
    }
}


?>