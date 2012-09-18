<?php
/**
 * Plugin Admin File
 *
 * Settings for this plugin
 *
 * @package nrelate
 * @subpackage Functions
 */


// Check dashboard messages if on dashboard page in admin
require_once NRELATE_FLYOUT_SETTINGS_DIR . '/flyout-messages.php';
 
/**
 * Add sub menu
 */
function nrelate_flyout_setup_admin() {

    // Add our submenu to the custom top-level menu:
	require_once NRELATE_FLYOUT_SETTINGS_DIR . '/nrelate-flyout-settings.php';
	require_once NRELATE_FLYOUT_SETTINGS_DIR . '/nrelate-flyout-styles-settings.php';
	require_once NRELATE_FLYOUT_SETTINGS_DIR . '/nrelate-flyout-advertising-settings.php';
	require_once NRELATE_FLYOUT_SETTINGS_DIR . '/nrelate-flyout-animation-styles-settings.php';
    $flyoutmenu = add_submenu_page('nrelate-main', __('Flyout','nrelate'), __('Flyout','nrelate'), 'manage_options', NRELATE_FLYOUT_ADMIN_SETTINGS_PAGE, 'nrelate_flyout_settings_page');
	add_action('load-'.$flyoutmenu,'nrelate_flyout_load_admin_scripts');
};
add_action('admin_menu', 'nrelate_flyout_setup_admin');


/**
 * Load plugin specific JS
 *
 * Only loads on plugin specific page
 */
function nrelate_flyout_load_admin_scripts() {
	wp_enqueue_script('nrelate_flyout_js', NRELATE_FLYOUT_SETTINGS_URL.'/nrelate_flyout_admin'. ( NRELATE_JS_DEBUG ? '' : '.min') .'.js', array('jquery'));
}


/**
 * Main Flyout Settings
 *
 * Generates all settings pages
 * since v0.46.0
 */
function nrelate_flyout_settings_page() {
	global $pagenow;
	
	if ( $pagenow == 'admin.php' && $_GET['page'] == 'nrelate-flyout' ) : 
    if ( isset ( $_GET['tab'] ) ) : 
        $tab = $_GET['tab']; 
    else: 
        $tab = 'general'; 
    endif; 
    switch ( $tab ) : 
        case 'general' : 
            nrelate_flyout_do_page(); 
            break; 
        case 'styles' : 
            nrelate_flyout_styles_do_page(); 
            break; 
        case 'anim-styles' : 
            nrelate_flyout_anim_styles_do_page(); 
            break;
        case 'advertising' : 
            nrelate_flyout_ads_do_page(); 
            break;	
    endswitch; 
	endif;
}

/**
 * Tabs for flyout settings
 *
 * since v0.46.0
 */
function nrelate_flyout_tabs($current = 0) { 

	// Animation type
	$options = get_option('nrelate_flyout_options');
	$anitype = $options['flyout_animation'];

	// Text or Thumbnails?	
	$options = get_option('nrelate_flyout_options');
	$styletype = $options['flyout_thumbnail'];
		
	// What type of ads?
	$flyout_ad_type = get_option('nrelate_flyout_options_ads');
	
	// If Ads == Separate, then overwrite $styletype
	if ($flyout_ad_type['flyout_ad_placement']=="Separate"){
		$styletype = $styletype . " | " . _('Ads');
	}
	
	
	

    $tabs = array( 'general' =>  __(' General','nrelate'), 'advertising' => __(' Advertising','nrelate'), 'styles' => $styletype . __(' Gallery','nrelate'), 'anim-styles' => __('Animation Styles','nrelate') ); 	
    $links = array();
	
		if ( $current == 0 ) {
		if ( isset( $_GET[ 'tab' ] ) ) {
			$current = $_GET[ 'tab' ];
		} else {
			$current = 'general';
		}
	}
		
    foreach( $tabs as $tab => $name ) : 
        if ( $tab == $current ) : 
            $links[] = "<a class='nav-tab nav-tab-active' href='?page=nrelate-flyout&tab=$tab'>$name</a>"; 
        else : 
            $links[] = "<a class='nav-tab' href='?page=nrelate-flyout&tab=$tab'>$name</a>"; 
        endif; 
    endforeach; 
    echo '<h2>'; 
    foreach ( $links as $link ) 
        echo $link; 
    echo '</h2>'; 
}

/**
 * Header for flyout settings
 *
 * Common for all settings pages
 * @since v0.46.0
 * @updated 0.50.0
 */
function nrelate_flyout_settings_header() {
	nrelate_plugin_page_header ( NRELATE_FLYOUT_NAME, NRELATE_FLYOUT_DESCRIPTION );
	nrelate_index_check();
	nrelate_flyout_tabs();
}

// Check dashboard messages if on dashboard page in admin
require_once NRELATE_FLYOUT_SETTINGS_DIR . '/flyout-messages.php';

/**
 * Tells the dashboard that we're active
 * Shows icon and link to settings page
 */
function nr_fo_plugin_active(){ ?>
	<li class="active-plugins">
		<?php echo '<img src="'. NRELATE_FLYOUT_IMAGE_DIR .'/flyout.png" style="float:left;" alt="" />'?>
		<a href="admin.php?page=<?php echo NRELATE_FLYOUT_ADMIN_SETTINGS_PAGE ?>">
		<?php echo NRELATE_FLYOUT_NAME;?> &raquo;</a>
	</li>
<?php
};
add_action ('nrelate_active_plugin_notice','nr_fo_plugin_active');



/**
 * Add settings link on plugin page
 *
 * @since 0.40.3
 */
function nrelate_flyout_add_plugin_links( $links, $file) {
	if( $file == NRELATE_FLYOUT_PLUGIN_BASENAME ){
		return array_merge( array(
			'<a href="admin.php?page='.NRELATE_FLYOUT_ADMIN_SETTINGS_PAGE.'">'.__('Settings', 'nrelate').'</a>',
			'<a href="admin.php?page=nrelate-main">'.__('Dashboard', 'nrelate').'</a>'
		),$links );
	}
	return $links;
}
add_filter('plugin_action_links', 'nrelate_flyout_add_plugin_links', 10, 2);

/**
 * Add plugin row meta on plugin page
 *
 * @since 0.40.3
 */

function nrelate_flyout_set_plugin_meta($links, $file) {
	// create link
	if ($file == NRELATE_FLYOUT_PLUGIN_BASENAME) {
		return array_merge( $links, array(
			'<a href="admin.php?page='.NRELATE_FLYOUT_ADMIN_SETTINGS_PAGE.'">'.__('Settings', 'nrelate').'</a>',
			'<a href="admin.php?page=nrelate-main">'.__('Dashboard', 'nrelate').'</a>',
			'<a href="'.NRELATE_WEBSITE_FORUM_URL.'">' . __('Support Forum', 'nrelate') . '</a>'
		));
	}
	return $links;
}
add_filter('plugin_row_meta', 'nrelate_flyout_set_plugin_meta', 10, 2 );

?>