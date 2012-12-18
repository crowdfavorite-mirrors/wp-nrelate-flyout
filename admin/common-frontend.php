<?php
/**
 * Common Frontend Functions 
 *
 * Load frontend common functions
 *
 * Checks if another nrelate plugin loaded these functions first
 * 
 * @package nrelate
 * @subpackage Functions
 */

 
define( 'NRELATE_COMMON_FRONTEND_LOADED', true );

/**
 * Load common jquery and styles
 */
function nrelate_jquery_styles() {
	$popular_load=0;
	$related_load=0;
	$flyout_load=0;
	$nsquared_load=0;
	if (function_exists("nrelate_popular_is_loading"))
		$popular_load=((nrelate_popular_is_loading() || is_single())? 1:0);
	if (function_exists("nrelate_related_is_loading"))	
		$related_load=(nrelate_related_is_loading()? 1:0);
	if (function_exists("nrelate_flyout_is_loading"))	
		$flyout_load=(nrelate_flyout_is_loading()? 1:0);
	if (function_exists("nrelate_nsquared_is_loading")) 
		$nsquared_load=(nrelate_nsquared_is_loading()? 1:0);
		
	if ($related_load || $popular_load || $flyout_load || $nsquared_load) {

	// Load Common CSS
	wp_register_style('nrelate-style-common-' . str_replace(".","-",NRELATE_LATEST_ADMIN_VERSION), NRELATE_CSS_URL . 'nrelate-panels-common.min.css', array(), NRELATE_LATEST_ADMIN_VERSION );
	wp_enqueue_style( 'nrelate-style-common-' . str_replace(".","-",NRELATE_LATEST_ADMIN_VERSION) );
			
	$options=get_option('nrelate_products');
	if(isset($options["related"]["status"]) && $options["related"]["status"]){
		$rc_options=get_option('nrelate_related_options_ads');
		if(isset($rc_options['related_display_ad']) && $rc_options['related_display_ad']==true && $rc_options['related_number_of_ads']>0){
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
		}
	}
	if(isset($options["popular"]["status"]) && $options["popular"]["status"]){
		$mp_options=get_option('nrelate_popular_options_ads');
		if(isset($mp_options['popular_display_ad']) && $mp_options['popular_display_ad']==true && $mp_options['popular_number_of_ads']>0){
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
		}
	}
	wp_enqueue_script('jquery');
		
		wp_register_script( 'nrelate_js', NRELATE_ADMIN_URL . '/common_frontend'. ( NRELATE_JS_DEBUG ? '' : '.min') .'.js', array(), NRELATE_LATEST_ADMIN_VERSION, false);
		wp_enqueue_script('nrelate_js', array('jquery'));
	}
}
add_action ('template_redirect', 'nrelate_jquery_styles');


/**
 * Load feed only when called
 * and if another nrelate plugin has not loaded it yet.
 *
 * @since 0.42.7
 */
if(isset($_GET['nrelate_feed'])&& !function_exists('nrelate_custom_feed')) { require_once 'rss-feed.php'; }
 

/**
 * Detects if called inside main loop
 * @cred http://alexking.org/blog/2011/06/01/wordpress-code-snippet-to-detect-main-loop
 *
 * @since 0.47.3
 */
function nrelate_is_main_loop($query = null) {
	global $wp_the_query, $nr_is_main_loop;
	
	if (is_null($query)) {
		return $nr_is_main_loop ? true : false;
	}
	
	if ($query === $wp_the_query) {
		$nr_is_main_loop = true;
	} else {
		$nr_is_main_loop = false;
	}
	
	return $nr_is_main_loop;
}
add_action('loop_start', 'nrelate_is_main_loop');


?>