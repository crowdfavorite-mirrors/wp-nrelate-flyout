<?php
/**
 * nrelate Admin Messages
 *
 * Does system checks and sets messages for admin settings
 *
 * @package nrelate
 * @subpackage Functions
 */


  /* = nrelate service status
 -----------------------------------------------
  * Remove the feed cache so we get the most updated information.
  * Get most recent post from status.nrelate.com.
  * Reset the feed cache to it's default state.
  */
function nr_service_status() {

	// Get WP feed.php
	include_once(ABSPATH . WPINC . '/feed.php');

	// Remove feed cache so we can get the most updated information.
	function nr_filter_handler( $seconds ) {
		if (is_admin()) {
			return 0;
		}
	}
	add_filter( 'wp_feed_cache_transient_lifetime' , 'nr_filter_handler' );

	// Get a SimplePie feed object.
	$rss = fetch_feed('http://status.nrelate.com/feed/');
	if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly 
	// Get the latest item. 
	$maxitems = $rss->get_item_quantity(1); 

	// Build an array of all the items, starting with element 0 (first element).
	$rss_items = $rss->get_items(0, $maxitems); 
	endif;
	?>

	<?php if ($maxitems !== 0) {
			// Loop through each feed item and display each item as a hyperlink.
			foreach ( $rss_items as $item ) : ?>
				<li>
					<div class="info" id="servicecheck">
						<?php _e ('Service Status:','nrelate');?>
						<?php echo $item->get_title(); ?>&nbsp;&nbsp;
						<a href='<?php echo $item->get_permalink(); ?>' title='<?php echo substr($item->get_description(), 0, 200); ?>...'>
						<?php echo $item->get_date('M j G:i T'); ?></a>
					</div>
				</li>
	<?php endforeach;
	}

	// Reset feed cache to default
	remove_filter( 'wp_feed_cache_transient_lifetime' , 'nr_filter_handler' );
}

 
 
 /**
 * nrelate Dashboard Messages
 *
 * Do some checks and load some messages that will help user
 */
function nr_admin_message_set(){
	
	// Let's write some messages
	// Simple create div with id adverify for nrelate to populate the content
	$msg = '<li id="adverify"></li>';
		
	 // Get admin options
	$admin_options = get_option('nrelate_admin_options');
	
	// get admin email address
	$admin_email = isset($admin_options['admin_email_address']) ? $admin_options['admin_email_address'] : null;
	
	// Communication
	if ($admin_email == null) {
		$msg = $msg . '<li><div class="red">It\'s a good idea to provide nrelate with your email address. Check the box under the <a href="#admin_email_address">Communication</a> area below.</div></li>';
		}
	// AJAX call to nrelate server to bring back ad code status
	$msg.='<script type="text/javascript"> checkad(\''.NRELATE_BLOG_ROOT.'\',\''.NRELATE_LATEST_ADMIN_VERSION.'\',\''.get_option('nrelate_key').'\'); </script>';
	echo $msg;
};
add_action ('nrelate_admin_messages','nr_admin_message_set');


 /**
 * nrelate theme compatibility
 *
 * Check active theme and provide messages to user that might be helpful.
 */
function nr_theme_compat() {
	$msg = '';
	
	// Theme Capability for either Related OR Popular
	if (defined('NRELATE_RELATED_ACTIVE') || defined('NRELATE_POPULAR_ACTIVE')) {
		$theme_data = current_theme_info();	
		
		// Woothemes
		if (strlen(strstr($theme_data->author,'woothemes'))>0) { $msg = $msg . '<li><div class="warning">' . sprintf('<strong>Woothemes</strong> are supported, but may require %sconfiguration%s.', '<a href="http://nrelate.com/theblog/theme-compatibility/woothemes/" target="_blank">', '</a>') . '</div></li>'; }

		// Genesis
		if (function_exists('genesis')) { $msg = $msg . '<li><div class="warning">' . sprintf('<strong>Genesis</strong> themes are supported, but may require %sconfiguration%s.', '<a href="http://nrelate.com/theblog/theme-compatibility/genesis/" target="_blank">', '</a>') . '</div></li>'; }

		// Thesis
		if (class_exists('thesis_comments')) { $msg = $msg . '<li><div class="warning">' . sprintf('<strong>Thesis</strong> themes are supported, but may require %sconfiguration%s.', '<a href="http://nrelate.com/theblog/theme-compatibility/thesis/" target="_blank">', '</a>') . '</div></li>'; }

	}

echo $msg;
}
add_action ('nrelate_admin_messages','nr_theme_compat');




 /**
 * nrelate plugin compatibility
 *
 * Check active plugins to see if they are compatable with nrelate
 * If these plugins are active, provide messages to user that might be helpful.
 */
function nr_plugin_compat() {

	// Plugin Capability for either Related OR Popular
	if (defined('NRELATE_RELATED_ACTIVE') || defined('NRELATE_POPULAR_ACTIVE')) {

		//W3 Total Cache
		if (is_plugin_active('w3-total-cache/w3-total-cache.php')) { $msg = $msg . '<li><div class="warning">' . sprintf('<strong>W3 Total Cache</strong> is supported, but may require %sconfiguration%s.', '<a href="http://nrelate.com/theblog/plugin-compatibility/w3-total-cache/" target="_blank">', '</a>') . '</div></li>'; }

		//CDN Tools
		if (is_plugin_active('cdn-tools/cdntools.php')) { $msg = $msg . '<li><div class="warning">' . sprintf('<strong>CDN Tools</strong> option "Google ajax CDN" is not supported. You can learn more %shere%s.', '<a href="http://nrelate.com/theblog/plugin-compatibility/cdn-tools/" target="_blank">', '</a>') . '</div></li>'; }

		//WP Minify
		if (is_plugin_active('wp-minify/wp-minify.php')) { $msg = $msg . '<li><div class="warning">' . sprintf('<strong>WP Minify</strong> is supported, but may require %sconfiguration%s.', '<a href="http://nrelate.com/theblog/plugin-compatibility/wp-minify/" target="_blank">', '</a>') . '</div></li>'; }
		
		// JS & CSS Script Optimizer
		if ( is_plugin_active('js-css-script-optimizer/js-css-script-optimizer.php') ) { $msg = $msg . '<li><div class="warning">' . sprintf('<strong>JS & CSS Script Optimizer</strong> is supported, but may require %sconfiguration%s.', '<a href="http://nrelate.com/theblog/plugin-compatibility/js-css-script-optimizer/" target="_blank">', '</a>') . '</div></li>'; }
		
	}
	
echo isset($msg) ? $msg : '';
	
}
add_action ('nrelate_admin_messages','nr_plugin_compat');
	


		
?>