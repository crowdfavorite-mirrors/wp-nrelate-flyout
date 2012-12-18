<?php
/**
 * Temporary file to get past 0.50.0 upgrade
 *
 ******* Frontend code only
 *
 * Version 0.50.0 has major code changes to improve performance.
 * Many functions were moved from individual plugins to the /admin/ folder.
 * If upgrading ONE nrelate plugin to v0.50.0, but not another
 * the user would receive errors that would disable access to their site.
 *
 * The functions listed below are duplicate functions from 
 * the /admin/ folder in v0.50.0.
 *
 * This file temporarily gets users past this upgrade.
 * It will be removed in a future version
 * 
 * @package nrelate
 * @subpackage Functions
 */
 
 if ( !function_exists( 'nrelate_should_inject' ) ) :

 /* = Located in common-frontend.php
-----------------------------------------------*/
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
		 * Default thumbnail style for IE6
		 *
		 * @since v.44.0
		 * @updated v46.0
		 * @moved to common 0.50.0
		 */
		function nrelate_ie6_thumbnail_style() {
			$nr_css_ie6_url = NRELATE_CSS_URL . "ie6-panels.min.css";
			$nr_ie6_id = 'nrelate-ie6-' . str_replace(".","-",NRELATE_FLYOUT_ADMIN_VERSION);
			wp_register_style($nr_ie6_id, $nr_css_ie6_url, array(), NRELATE_LATEST_ADMIN_VERSION );
			$GLOBALS['wp_styles']->add_data( $nr_ie6_id, 'conditional', 'IE 6' );
			
			wp_enqueue_style( 'nrelate-ie6-' . str_replace(".","-",NRELATE_FLYOUT_ADMIN_VERSION) );
		}

endif;

?>