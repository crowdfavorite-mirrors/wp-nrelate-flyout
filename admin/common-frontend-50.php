<?php
/**
 * Common Frontend Functions added in v0.50.0
 *
 * Load frontend common functions
 *
 * Checks if another nrelate plugin loaded these functions first
 * 
 * @package nrelate
 * @subpackage Functions
 */

 
define( 'NRELATE_COMMON_FRONTEND_50_LOADED', true );

/**
 * Default thumbnail style for IE6
 *
 * @since v.44.0
 * @updated v46.0
 * @moved to common 0.50.0
 */
function nrelate_ie6_thumbnail_style() {
	$nr_css_ie6_url = NRELATE_CSS_URL . "ie6-panels.min.css";
	$nr_ie6_id = 'nrelate-ie6-' . str_replace(".","-",NRELATE_LATEST_ADMIN_VERSION);
	wp_register_style($nr_ie6_id, $nr_css_ie6_url, array(), NRELATE_LATEST_ADMIN_VERSION );
	$GLOBALS['wp_styles']->add_data( $nr_ie6_id, 'conditional', 'IE 6' );
	
	wp_enqueue_style( 'nrelate-ie6-' . str_replace(".","-",NRELATE_LATEST_ADMIN_VERSION) );
}

/**
 * Returns true if currently the_content or the_excerpt
 * filter should be injected with nrelate code
 *
 * @since 0.47.3
 * @moved to common 0.50.0
 */
function nrelate_should_inject($plugin='') {
	global $wp_current_filter;
	
	$should_inject = true;
	
	if ( !nrelate_is_main_loop() ) {
		// Don't inject if out of main loop
		$should_inject = false;
	} elseif ( in_array( 'get_the_excerpt', $wp_current_filter ) ) {
		// Don't inject if calling get_the_excerpt
		$should_inject = false;
	} elseif ( is_single() && in_array( 'the_excerpt', $wp_current_filter ) ) {
		// Don't inject the_excerpt on single post pages
		$should_inject = false;
	}
	
	// Third party widgets
	// For php 5.25 support: debug_backtrace(false);
	$call_stack = debug_backtrace();
	foreach ( $call_stack as $call ) {
		if ( $call['function'] == 'widget' ) {
			$should_inject = false;
			break;
		}
	}
	
	if ($plugin) {
		// Allow fine grained filter for a particular nrelate product
		$should_inject = apply_filters( 'nrelate_'.$plugin.'_should_inject', $should_inject );
	}
	
	// Allow global filter for all nrelate products
	$should_inject = apply_filters( 'nrelate_should_inject', $should_inject );
	
	return $should_inject;
}

/**
 * Get page Title and URL
 *
 * @since 0.50.0
 * @0.50.2 updated to wp_query
 */
function nrelate_title_url () {
	global $wp_query;

	// If page is_404 use the url as the title
	if ( is_404() ) {
		$post_title = urlencode( str_replace('-', ' ', get_query_var('name')) );
		$post_urlencoded = urlencode( "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) ;
	} else {
		$post_title =  urlencode ( get_the_title($wp_query->post->ID) );
		$post_urlencoded = urlencode( get_permalink($wp_query->post->ID) );
	}

$arr = array("post_title" => $post_title, "post_urlencoded" => $post_urlencoded);


return $arr;
}

?>