<?php
/**
Plugin Name: nrelate Flyout
Plugin URI: http://www.nrelate.com
Description: Easily allow related posts to flyout from the sides of your website. Click on <a href="admin.php?page=nrelate-flyout">nrelate &rarr; Flyout</a> to configure your settings.
Author: <a href="http://www.nrelate.com">nrelate</a> and <a href="http://www.slipfire.com">SlipFire</a>
Version: 0.51.4
Author URI: http://nrelate.com/

/*
 * This plugin was inspired by the:
 * upPrev Previous Post Animated Notification plugin
 * @author: Jason Pelker, Grzegorz Krzyminski
 * @author uri: http://item-9.com/
 * @link: http://wordpress.org/extend/plugins/upprev-nytimes-style-next-post-jquery-animated-fly-in-button/
 */


// Copyright (c) 2011 nrelate, All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// This is a plugin for WordPress
// http://wordpress.org/
//
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************


/**
 * Define Plugin constants
 */
define( 'NRELATE_FLYOUT_PLUGIN_VERSION', '0.51.4' );
define( 'NRELATE_FLYOUT_ADMIN_SETTINGS_PAGE', 'nrelate-flyout' );
define( 'NRELATE_FLYOUT_ADMIN_VERSION', '0.05.3' );
define( 'NRELATE_FLYOUT_NAME' , __('Flyout','nrelate'));
define( 'NRELATE_FLYOUT_DESCRIPTION' , sprintf( __('Display related content in a cool flyout box... similarly to NYTimes.com.','nrelate')));

if(!defined('NRELATE_CSS_URL')) { define( 'NRELATE_CSS_URL', 'http://static.nrelate.com/common_wp/' . NRELATE_FLYOUT_ADMIN_VERSION . '/' ); }
if(!defined('NRELATE_BLOG_ROOT')) { define( 'NRELATE_BLOG_ROOT', urlencode(str_replace(array('http://','https://'), '', get_bloginfo( 'url' )))); }
if(!defined('NRELATE_JS_DEBUG')) { define( 'NRELATE_JS_DEBUG', isset($_REQUEST['nrelate_debug']) ? true : false ); }

/**
 * Define Path constants
 */
// Generic: will be assigned to the first nrelate plugin that loads
if (!defined( 'NRELATE_PLUGIN_BASENAME')) { define( 'NRELATE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); }
if (!defined( 'NRELATE_PLUGIN_NAME')) { define( 'NRELATE_PLUGIN_NAME', trim( dirname( NRELATE_PLUGIN_BASENAME ), '/' ) ); }
if (!defined( 'NRELATE_PLUGIN_DIR')) { define( 'NRELATE_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . NRELATE_PLUGIN_NAME ); }
if (!defined('NRELATE_ADMIN_DIR')) { define( 'NRELATE_ADMIN_DIR', NRELATE_PLUGIN_DIR .'/admin'); }
if (!defined('NRELATE_ADMIN_URL')) { define( 'NRELATE_ADMIN_URL', WP_PLUGIN_URL . '/' . NRELATE_PLUGIN_NAME .'/admin'); }
if (!defined('NRELATE_API_URL')) { define ('NRELATE_API_URL', is_ssl() ? 'https://api.nrelate.com' : 'http://api.nrelate.com'); }
if (!defined('NRELATE_EXTENSIONS')) { define ('NRELATE_EXTENSIONS', NRELATE_ADMIN_DIR . '/extensions' ); }

// Plugin specific
define( 'NRELATE_FLYOUT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'NRELATE_FLYOUT_PLUGIN_NAME', trim( dirname( NRELATE_FLYOUT_PLUGIN_BASENAME ), '/' ) );
define( 'NRELATE_FLYOUT_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . NRELATE_FLYOUT_PLUGIN_NAME );
define( 'NRELATE_FLYOUT_PLUGIN_URL', WP_PLUGIN_URL . '/' . NRELATE_FLYOUT_PLUGIN_NAME );
define( 'NRELATE_FLYOUT_SETTINGS_DIR', NRELATE_FLYOUT_PLUGIN_DIR . '/flyout_settings' );
define( 'NRELATE_FLYOUT_SETTINGS_URL', NRELATE_FLYOUT_PLUGIN_URL . '/flyout_settings' );
define( 'NRELATE_FLYOUT_ADMIN_DIR', NRELATE_FLYOUT_PLUGIN_DIR . '/admin' );
define( 'NRELATE_FLYOUT_IMAGE_DIR', NRELATE_FLYOUT_PLUGIN_URL . '/images' );

// Load WP_Http
if( !class_exists( 'WP_Http' ) )
	include_once( ABSPATH . WPINC. '/class-http.php' );
	
// Load Language
load_plugin_textdomain('nrelate-flyout', false, NRELATE_FLYOUT_PLUGIN_DIR . '/language');

/**
 * Get the product status of all nrelate products.
 *
 * @since 0.49.0
 */
if ( !defined( 'NRELATE_PRODUCT_STATUS' ) ) { require_once ( NRELATE_FLYOUT_ADMIN_DIR . '/product-status.php' ); }

/**
 * Load common styles if another nrelate plugin has not loaded it yet.
 *
 * @since 0.46.0
 */
if (!isset($nrelate_thumbnail_styles)) { require_once ( NRELATE_FLYOUT_ADMIN_DIR . '/styles.php' ); }
require_once ( NRELATE_FLYOUT_SETTINGS_DIR . '/flyout-animation-styles.php' );

/**
 * Check related version to make sure it is compatible with FO
 */
$related_settings = get_option('nrelate_related_options');
$related_version = $related_settings['related_version'];
if($related_version!='' &&version_compare("0.47.4", $related_version)>0){
	$plugin = NRELATE_FLYOUT_PLUGIN_BASENAME;
	$warning = "<p><strong>".__('nrelate Warning(s):', 'nrelate')."</strong></p>";
	$message .= "<li>".sprintf(__('You\'re running Related Content plugin version %1$s. The Flyout plugin requires Related Content version to be 0.47.4 or higher.<br/>Please upgrade to the latest release of Related Content plugin before installing the Flyout plugin.', 'nrelate' ), $related_version ) . "</li>";
	$closing = "<p>".__('The nrelate Flyout plugin has been deactivated.','nrelate')."<br/><br/><a href=\"/wp-admin\">".__('Click here to return to your WordPress dashboard.','nrelate')."</a></p>";
	deactivate_plugins($plugin);
	wp_die( $warning . "<ol>" . $message . "<ol>" . $closing );
	return;
}

/**
 * Initializes the plugin and it's features.
 *
 * @since 0.1
 */
if (is_admin()) {

		//load common admin files if not already loaded from another nrelate plugin
		if ( ! defined( 'NRELATE_COMMON_LOADED' ) ) { require_once ( NRELATE_FLYOUT_ADMIN_DIR . '/common.php' ); }
		if ( ! defined( 'NRELATE_COMMON_50_LOADED' ) ) { require_once ( NRELATE_FLYOUT_ADMIN_DIR . '/common-50.php' ); }
		
		//load plugin status
		require_once ( NRELATE_FLYOUT_SETTINGS_DIR . '/flyout-plugin-status.php' );
		
		//load flyout menu
		require_once ( NRELATE_FLYOUT_SETTINGS_DIR . '/flyout-menu.php' );
		
		// Load Tooltips
		if (!isset($nrelate_tooltips)) { require_once ( NRELATE_FLYOUT_ADMIN_DIR . '/tooltips.php' ); }
		
		// temporary file for 0.50.0 upgrades
		require_once ( 'nrelate-abstraction.php' );
}



/** Load common frontend functions **/
if ( ! defined( 'NRELATE_COMMON_FRONTEND_LOADED' ) ) { require_once ( NRELATE_FLYOUT_ADMIN_DIR . '/common-frontend.php' ); }
if ( ! defined( 'NRELATE_COMMON_FRONTEND_50_LOADED' ) ) { require_once ( NRELATE_FLYOUT_ADMIN_DIR . '/common-frontend-50.php' ); }

// temporary file for 0.50.0 upgrades
require_once ( 'nrelate-abstraction-frontend.php' );


/*
 * Load flyout styles
 *
 * since v.44.0
 * updated v46.0
 */
function nrelate_flyout_styles() {
	if ( nrelate_flyout_is_loading() ) {
	
		global $nrelate_thumbnail_styles, $nrelate_thumbnail_styles_separate, $nrelate_text_styles, $nrelate_text_styles_separate, $fo_styleclass, $fo_layout;
		$options = get_option('nrelate_flyout_options');
		$style_options = get_option('nrelate_flyout_options_styles');
		$ad_options = get_option('nrelate_flyout_options_ads');

		// Are we loading separate ads?
		if ($ad_options['flyout_ad_placement']=='Separate') {
			$style_suffix = '_separate';
		} else {
			$style_suffix = '';
		}

		// Thumbnails or Text?
		if ($options['flyout_thumbnail']=='Thumbnails') {
			$style_type = 'flyout_thumbnails_style' . $style_suffix;
			$style_array = 'nrelate_thumbnail_styles' . $style_suffix;
		} else {
			$style_type = 'flyout_text_style' . $style_suffix;
			$style_array = 'nrelate_text_styles' . $style_suffix;
		}
			
			
		//Identify Animation type and stylesheet
		$options = get_option('nrelate_flyout_options');
		$animstyle_options = get_option('nrelate_flyout_anim_options_styles');
			if ($options['flyout_animation']=='Slideout') {
				//Slideout Animation
				if ('none'==$animstyle_options['flyout_anim_slideout_style']) return;
				$anim_style_type = 'flyout-' . $animstyle_options['flyout_anim_slideout_style'];
				//$anim_style_type = 'slideout-' . $animstyle_options['flyout_anim_slideout_style']; use for two different styles
				$anim_stylesheet = 'nrelate-' . $anim_style_type .'.min.css';		
			} else {
			//Fade Animation
				if ('none'==$animstyle_options['flyout_anim_fade_style']) return;
				$anim_style_type = 'flyout-' . $animstyle_options['flyout_anim_slideout_style'];
				//$anim_style_type = 'fade-' . $animstyle_options['flyout_anim_fade_style'];  use for two different styles
				$anim_stylesheet = 'nrelate-'.$anim_style_type .'.min.css';
			}
		
		// Get style name (i.e. Default)
		$style_name = $style_options [$style_type];
				
		// Get the style sheet and class from STYLES.PHP
		$style_array_convert = ${$style_array};
		$stylesheet = $style_array_convert[$style_name]['stylesheet'] ? $style_array_convert[$style_name]['stylesheet'] : "nrelate-panels-default";
		$fo_styleclass = $style_array_convert[$style_name]['styleclass'];
		$fo_layout = $style_array_convert[$style_name]['layout'];

		// Get full stylesheet url
		$fo_css_url = NRELATE_CSS_URL . $stylesheet . '.min.css';
		
		$fo_anim_css_url = NRELATE_CSS_URL . $anim_stylesheet;
		// For local development
		//$fo_anim_css_url= NRELATE_FLYOUT_PLUGIN_URL . '/' . $anim_stylesheet;
		
		// Only load if style not set to NONE
		if ('none'!=$style_options[$style_type]) {
			nrelate_ie6_thumbnail_style();
			wp_register_style('nrelate-style-'. $style_name . "-" . str_replace(".","-",NRELATE_FLYOUT_ADMIN_VERSION), $fo_css_url, array(), NRELATE_LATEST_ADMIN_VERSION );
			wp_enqueue_style( 'nrelate-style-'. $style_name . "-" . str_replace(".","-",NRELATE_FLYOUT_ADMIN_VERSION) );
		}
		
		// Load animation style
		wp_register_style('nrelate-style-'. $anim_style_type . "-" . str_replace(".","-",NRELATE_FLYOUT_ADMIN_VERSION), $fo_anim_css_url, array(), NRELATE_LATEST_ADMIN_VERSION );
		wp_enqueue_style( 'nrelate-style-'. $anim_style_type . "-" . str_replace(".","-",NRELATE_FLYOUT_ADMIN_VERSION) );
	}
}
add_action('wp_enqueue_scripts', 'nrelate_flyout_styles');

/*
 * Check if nrelate is loading (frontend only)
 *
 * @since 0.47.0
 */
function nrelate_flyout_is_loading() {
 	// Temporary added YK: flyout will only work for is_single for beta version
 	// Don't care about the where_to_show field, just show on is_single
    // Probably will change in the future
	$is_loading = false;
   
  /*  if ( !is_admin() ) {   
        $options = get_option('nrelate_flyout_options');
       
        if ( isset($options['flyout_where_to_show']) ) {
            foreach ( (array)$options['flyout_where_to_show'] as $cond_tag ) {
                if ( function_exists( $cond_tag ) && call_user_func( $cond_tag ) ) {
                    $is_loading = true;
                    break;
                }
            }
        }
    }*/
	if(is_single()){

		// Load Mobile Detect
		if (!class_exists('Mobile_Detect')) {
			require_once ( NRELATE_EXTENSIONS . '/Mobile_Detect/Mobile_Detect.php');
		}
		
		$detect = new Mobile_Detect();
		
		if ($detect->isMobile()) {
			$is_loading=false;
		} else {
			$is_loading=true;			
		}

	}
	
	return apply_filters( 'nrelate_flyout_is_loading', $is_loading);
}



/**
 * Inject flyout posts into the footer
 *
 * @since 0.50.4
 */
function nrelate_flyout_inject($content) {
	global $post;
	
	if ( nrelate_should_inject('flyout') ) {

		return $content . nrelate_flyout(true);
		
	}
	
	return $content;
}
add_filter( 'wp_footer', 'nrelate_flyout_inject', 10 );

/**
 * Inject top and bottom post indicators to calculate flyout position
 *
 * @since 0.50.3
 */
function nrelate_flyout_wrap_post($content) {
	global $post;
	
	if ( nrelate_should_inject('flyout') ) {
		$original = $content;

		$content  = "<div id='nr_fo_top_of_post'></div>";
		$content .= $original;
		$content .= "<div id='nr_fo_bot_of_post'></div> ";
	}
	
	return $content;
}
add_filter( 'the_content', 'nrelate_flyout_wrap_post', 10 );
//Since we only show on is_single, we can remove the_excerpt filter
//add_filter( 'the_excerpt', 'nrelate_flyout_inject', 10 );


/**
 * Returns true if currently the_content or the_excerpt
 * filter should be injected with nrelate code
 *
 * @since 0.47.3
 */
function nrelate_flyout_should_inject_filter($should_inject) {
	global $nr_fo_counter, $wp_current_filter;
	
	// Force one instance on single pages
	if ( is_single() && $nr_fo_counter == 0 && array_intersect($wp_current_filter, array('wp_footer', 'the_content')) ) {
		return true;
	}
	
	// Otherwise, don't inject
	return false;
}
add_filter('nrelate_flyout_should_inject', 'nrelate_flyout_should_inject_filter', 0);


// FLYOUT: this function will build the js for flyout
require_once ( NRELATE_FLYOUT_SETTINGS_DIR . '/flyout_frontend.php' );

/**
 * Primary function
 *
 * Gets options and passes to nrelate via Javascript
 *
 * @since 0.1
 */
 
$nr_fo_counter = 0;


function nrelate_flyout() {
	global $post, $nr_fo_counter, $fo_styleclass, $fo_layout;
	
	$animation_fix = $nr_fo_nonjsbody = $nr_fo_nonjsfix = $nr_fo_js_str = $flyout_js_str= '';
	
	if ( nrelate_flyout_is_loading() )  {	
		$nr_fo_counter++;
		$nrelate_flyout_options = get_option('nrelate_flyout_options');
		$fo_style_options = get_option('nrelate_flyout_options_styles');
		$fo_style_code = 'nrelate_' . ($fo_styleclass ? $fo_styleclass : "default");
		$fo_layout_code = 'nr_' . ($fo_layout ? $fo_layout : "1col");
		$fo_anim_style_options = get_option('nrelate_flyout_anim_options_styles');
		//$fo_anim_style_code = 'nrelate_animate_style_' . (($nrelate_flyout_options['flyout_animation']=='Slideout') ? $fo_anim_style_options['flyout_anim_slideout_style'] : $fo_anim_style_options['flyout_anim_fade_style']); // use for two styles
		$fo_anim_style_code = 'nrelate_animate_style_' . $fo_anim_style_options['flyout_anim_slideout_style'];
		
		$nr_fo_width_class = 'nr_' . (($nrelate_flyout_options['flyout_thumbnail']=='Thumbnails') ? $nrelate_flyout_options['flyout_thumbnail_size'] : "text");
		
		// Get the page title and url array
		$nrelate_title_url = nrelate_title_url();
	
		$nonjs=$nrelate_flyout_options['flyout_nonjs'];
		
		$nr_url = "http://api.nrelate.com/fow_wp/" . NRELATE_FLYOUT_PLUGIN_VERSION . "/?tag=nrelate_flyout";
		$nr_url .= "&keywords=$nrelate_title_url[post_title]&domain=" . NRELATE_BLOG_ROOT . "&url=$nrelate_title_url[post_urlencoded]&nr_div_number=".$nr_fo_counter;
		$nr_url .= is_home() ? '&source=hp' : '';
		
		$nr_url = apply_filters('nrelate_api_url', $nr_url, $post->ID);
		
		//is loaded only once per page
		if (!defined('NRELATE_FLYOUT_HOME')) {
			define('NRELATE_FLYOUT_HOME', true);
		    
			$animation_fix = '<style type="text/css">.nrelate_flyout .nr_sponsored{ left:0px !important; }</style>';
			
			$nrelate_flyout_options_ads = get_option('nrelate_flyout_options_ads');
			if (!empty($nrelate_flyout_options_ads['flyout_ad_animation'])) {
				$animation_fix = '';
			}
			//FLY OUT ANIMATION FIX
			$frombot = $nrelate_flyout_options['flyout_from_bot'];
			$frombottype = $nrelate_flyout_options['flyout_from_bot_type'];
			$flyout_width = $nrelate_flyout_options['flyout_anim_width'];
			$flyout_width_type = $nrelate_flyout_options['flyout_anim_width_type'];
			$position = $nrelate_flyout_options['flyout_loc'] == "Right" ? "right" : "left";
			$flyout_hide_width = 0;
			if($flyout_width_type=="px"){
				$flyout_hide_width=-($flyout_width+40);
			}else{
				$flyout_hide_width=-($flyout_width+4);
			}
			if ($nrelate_flyout_options['flyout_animation'] == "Fade") {
				$flyout_animation_type = 'fade';
		        $flyout_animation= "<style type='text/css'>.nrelate_flyout {display:hidden; ".$position.": 0px; bottom: ".$frombot.$frombottype."; width:".$flyout_width.$flyout_width_type.";} #nrelate_flyout_open{display:none; ".$position.":0px; bottom: ".$frombot.$frombottype.";}";
		    } else {
				$flyout_animation_type = 'slideout';
		        $flyout_animation= "<style type='text/css'>.nrelate_flyout {display:block; ".$position.": ".$flyout_hide_width.$flyout_width_type."; bottom: ".$frombot.$frombottype."; width:".$flyout_width.$flyout_width_type.";} #nrelate_flyout_open{display:block; ".$position.": -80px;bottom: ".$frombot.$frombottype.";}";
		    }
		    $flyout_animation.=" #nrelate_flyout_close{background: #fff url(".NRELATE_FLYOUT_PLUGIN_URL."/images/close_window.gif) no-repeat 0 0} </style>";
			$flyout_type_position = strtolower( $flyout_animation_type . '_' . $position );
			
		    //This call makes the js for flyout
			$flyout_js_str=nrelate_flyout_makejs();
		}
		if (!defined('NRELATE_HOME')) {
			define('NRELATE_HOME', true);
			$domain = addslashes(NRELATE_BLOG_ROOT);
			$nr_domain_init = "nRelate.domain = \"{$domain}\";";
		} else {
			$nr_domain_init = '';
		}
		
		if($nonjs){
		    $args=array("timeout"=>2);
			$response=wp_remote_get($nr_url."&nonjs=1",$args);

		    if( !is_wp_error( $response ) ){
			    if($response['response']['code']==200 && $response['response']['message']=='OK'){
				    $nr_fo_nonjsbody=$response['body'];
			   		$nr_fo_nonjsfix='<script type="text/javascript">'.$nr_domain_init.'nRelate.fixHeight("nrelate_flyout_'.$nr_fo_counter.'"); ';
			   		$nr_fo_nonjsfix.='nRelate.adAnimation("nrelate_flyout_'.$nr_fo_counter.'"); ';
					$nr_fo_nonjsfix.='nRelate.tracking("fo"); ';
					$nr_fo_nonjsfix.=$flyout_js_str.'</script>';
			    }else{
			    	$nr_fo_nonjsbody="<!-- nrelate error: nrelate server not 200. -->";
			    }
		    }else{
		    	$nr_fo_nonjsbody="<!-- nrelate error: WP-request to nrelate server failed. -->";
		    }
		}
		else{
			$nr_fo_js_str= <<<EOD
	<script type="text/javascript">
	/*<![CDATA[*/
		$flyout_js_str
		$nr_domain_init
		var entity_decoded_nr_url = jQuery('<div/>').html("$nr_url").text();
		nRelate.getNrelatePosts(entity_decoded_nr_url);
	/*]]>*/
	</script>
EOD;
		}
		$markup = <<<EOD
$animation_fix
$flyout_animation
	<div id="nrelate_flyout_{$nr_fo_counter}" class="nrelate nrelate_flyout nr_$flyout_type_position nr_animate_type_$flyout_animation_type $fo_anim_style_code $fo_style_code $fo_layout_code $nr_fo_width_class">$nr_fo_nonjsbody</div>
	<!--[if IE 6]>
		<script type="text/javascript">jQuery('.$fo_style_code').removeClass('$fo_style_code');</script>
	<![endif]-->
	$nr_fo_nonjsfix
	$nr_fo_js_str
    <div id='nrelate_flyout_open' class='$fo_anim_style_code nr_$flyout_type_position'></div>
EOD;

echo $markup;

	}
}


//Activation and Deactivation functions
//Since 0.47.4, added uninstall hook
register_activation_hook(__FILE__, 'nr_fo_add_defaults');
register_deactivation_hook(__FILE__, 'nr_fo_deactivate');
register_uninstall_hook(__FILE__, 'nr_fo_uninstall');
?>