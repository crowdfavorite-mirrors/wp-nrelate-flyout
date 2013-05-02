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
		
		add_action( "wp_print_scripts", "nrelate_init_plugins", 1);
	}
}
add_action ('template_redirect', 'nrelate_jquery_styles');

/**
 * Init JS plugins options
 */
function nrelate_init_plugins() {
	$plugins = array( "related" => false, "popular" => false, "flyout" => false, "nsquared" => false );

	foreach ($plugins as $plugin => $load) {
		if ( function_exists("nrelate_{$plugin}_is_loading") ) {	
			$plugins[$plugin] = call_user_func("nrelate_{$plugin}_is_loading");

			if ( $plugin == "popular" && is_single() ) {
				$plugins[$plugin] = true;				
			}
		}
	}


	if( array_sum($plugins) > 0 ) {
		$domain = esc_js(NRELATE_BLOG_ROOT);
		$loader_url = NRELATE_ADMIN_URL . '/common_frontend'. ( NRELATE_JS_DEBUG ? '' : '.min') .'.js';

		$options = array( "plugins" => array() );
		$async = "async";

		foreach ($plugins as $plugin => $load) {
			if ( $load == true ) {
				$p_opts = get_option("nrelate_{$plugin}_options");
				$ps_opts = get_option("nrelate_{$plugin}_options_styles");
				$pa_opts = get_option("nrelate_{$plugin}_options_ads");

				$style_suffix = $pa_opts["{$plugin}_ad_placement"] == 'Separate' ? "_separate" : "";

				$style_code = $p_opts["{$plugin}_thumbnail"] == "Thumbnails" ? $ps_opts["{$plugin}_thumbnails_style{$style_suffix}"] : $ps_opts["{$plugin}_text_style{$style_suffix}"];
				list($cssstyle, $cols) = explode("-", str_replace("-text", "", $style_code) );

				$options["plugins"][$plugin] = array(
					"cssstyle" 		=> $cssstyle,
					"thumbsize" 	=> (int)$p_opts["{$plugin}_thumbnail_size"],
					"widgetstyle" 	=> ( $p_opts["{$plugin}_thumbnail"] == "Thumbnails" ? 1 : 0 )
				);

				if ( $cols ) {
					$options["plugins"][$plugin]["cols_layout"] = $cols;
				}

				if ( $pa_opts["{$plugin}_display_ad"] == "on" ) {
					$options["plugins"][$plugin]["ad_place"] = $pa_opts["{$plugin}_ad_placement"];
				}

				if ( $plugin == "flyout" ) {
					$pan_opts = get_option("nrelate_{$plugin}_anim_options_styles");

					$options["plugins"][$plugin]["location"] = strtolower($p_opts["{$plugin}_loc"]);
					$options["plugins"][$plugin]["animation"] = strtolower($p_opts["{$plugin}_animation"]);
					$options["plugins"][$plugin]["offset"] = (int)$p_opts["{$plugin}_offset"];
					$options["plugins"][$plugin]["element"] = $p_opts["{$plugin}_offset_element"];
					$options["plugins"][$plugin]["width"] = (float)$p_opts["{$plugin}_anim_width"];
					$options["plugins"][$plugin]["widthtype"] = $p_opts["{$plugin}_anim_width_type"];
					$options["plugins"][$plugin]["frombot"] = (float)$p_opts["{$plugin}_from_bot"];
					$options["plugins"][$plugin]["frombottype"] = $p_opts["{$plugin}_from_bot_type"];
					$options["plugins"][$plugin]["animstyle"] = $pan_opts["{$plugin}_anim_{$options["plugins"][$plugin]["animation"]}_style"];
				}

				if ( $plugin == "popular" ) {
					$p_max_age = $p_opts["{$plugin}_max_age_num"];
					$p_max_frame = $p_opts["{$plugin}_max_age_frame"];

					switch ($p_max_frame){
						case 'Hour(s)':
						  $maxageposts = $p_max_age * 60;
						  break;
						case 'Day(s)':
						  $maxageposts = $p_max_age * 1440;
						  break;
						case 'Week(s)':
						  $maxageposts = $p_max_age * 10080;
						  break;
						case 'Month(s)':
						  $maxageposts = $p_max_age * 44640;
						  break;
						case 'Year(s)':
						  $maxageposts = $p_max_age * 525600;
						  break;
					}

					$options["plugins"][$plugin]["maxageposts"] = $maxageposts;
				}
			}
		}

		if ( $options["plugins"] ) {
			$json_options = esc_attr( json_encode( $options ) );
		}

		$is_home = (string)(int) (is_home() || is_front_page());

		if ( is_single() ) {
			$nr_pageurl_init = ", nr_pageurl = '". get_permalink() ."'";
		} else {
			$nr_pageurl_init = "";
		}

		$markup = <<<EOD
	<script type="text/javascript">var nr_domain = "$domain", nr_is_home = {$is_home}{$nr_pageurl_init};</script>
	<script async type="text/javascript" id="nrelate_loader_script" data-nrelate-options="$json_options" src="$loader_url"></script>
EOD;

		echo $markup;
	}
}


/**
 * Checks if request's user agent identifies a search engine crawler 
 */
function nrelate_is_crawler() {
	$crawlers = 'AdsBot-Google|baidu Transcoder|Baiduspider|bingbot|Bloglines subscriber|Charlotte|DotBot|eCairn Grabber|FeedFetcher-Google|Googlebot|Java VM|LinkWalker|LiteFinder|Mediapartners-Google|msnbot|msnbot-media|QihooBot|Sogou web spider|Sosoimagespider|Sosospider|Speedy Spider|Superdownloads Spiderman|WebAlta Crawler|WukongSpider|Yahoo! Slurp|Yahoo! Slurp China|Yeti|YodaoBot|YodaoBot-Image|YoudaoBot';
	return (preg_match("/$crawlers/", $_SERVER["HTTP_USER_AGENT"]) > 0);
}

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