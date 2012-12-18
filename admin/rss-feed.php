<?php
/**
 * nrelate custom RSS feed
 *
 * @package nrelate
 * @subpackage Functions
 */
 
  /**
 * Retrieve Post ID, formatted for use in feeds.
 *
 * @credit WordPress /includes/rss-feed.php
 *
 * Since 0.50.2
 */
function nrelate_get_the_post_ID() {
	global $post;
	$post_id = $post->ID;
	
	$the_post_id = "<postID><![CDATA[" . $post_id . "]]></postID>\n";

	return $the_post_id;
}

 /**
 * Retrieve Post Type, formatted for use in feeds.
 *
 * @credit WordPress /includes/rss-feed.php
 *
 * Since 0.50.2
 */
function nrelate_get_the_post_type_rss() {
	global $post;
	$post_type = get_post_type( $post );
	
	$the_post_type = "\t\t<postType><![CDATA[" . @html_entity_decode( $post_type, ENT_COMPAT, get_option('blog_charset') ) . "]]></postType>\n";

	return $the_post_type;
}


 /**
 * Retrieve all of the post categories, formatted for use in feeds.
 *
 * @credit WordPress /includes/rss-feed.php
 *
 * Since 0.44.0
 */
function nrelate_get_the_category_rss() {
	$categories = get_the_category();
	
	$filter = 'rss';

	$cat_names = array();
	if ( !empty($categories) ) {
		foreach ( (array) $categories as $category ) {
			$cat_names[ $category->term_id ] = sanitize_term_field('name', $category->name, $category->term_id, 'category', $filter);
		}
	}

	$cat_names = array_unique($cat_names);
	
	$the_cats = '';
	foreach ( $cat_names as $cat_id => $cat_name ) {
		$the_cats .= "\t\t<category><![CDATA[" . @html_entity_decode( $cat_name, ENT_COMPAT, get_option('blog_charset') ) . "]]></category><categoryID>" . $cat_id . "</categoryID>\n";
	}

	return $the_cats;
}

 /**
 * Retrieve all of the post tags, formatted for use in feeds.
 *
 * @credit WordPress /includes/rss-feed.php
 *
 * Since 0.44.0
 */
function nrelate_get_the_tags_rss() {
	$tags = get_the_tags();
	$tag_names = array();

	$filter = 'rss';

	if ( !empty($tags) ) {
		foreach ( (array) $tags as $tag ) {
			$tag_names[$tag->term_id] = sanitize_term_field('name', $tag->name, $tag->term_id, 'post_tag', $filter);
		}
	}
	
	$tag_names = array_unique($tag_names);
	
	$the_tags = '';
	foreach ( $tag_names as $tag_id => $tag_name ) {
		$the_tags .= "\t\t<tag><![CDATA[" . @html_entity_decode( $tag_name, ENT_COMPAT, get_option('blog_charset') ) . "]]></tag><tagID>" . $tag_id . "</tagID>\n";
	}

	return $the_tags;
}

 /**
 * Display Post ID, Post Type, Categories and Tags in feed
 *
 * @cred WordPress /includes/rss-feed.php
 *
 * Since 0.44.0
 * Update 0.50.2
 */

function nrelate_cdata() {
	echo nrelate_get_the_post_ID();
	echo nrelate_get_the_post_type_rss();
	echo nrelate_get_the_category_rss();
	echo nrelate_get_the_tags_rss();
}

/**
 * Get published post count
 *
 * Updated 0.45.0
 */
function nrelate_post_count() {
	global $wp_query;
	echo "\t<count>" . $wp_query->found_posts . "</count>\n";
}

/**
 * Get Custom Images
 *
 * Attempts to retrieve any custom images
 * set by user or themes/plugins
 *
 * If no custom image is found, grab the first image in the post
 * @updated 0.47.8
 */
    function nrelate_get_custom_images($content) {
        global $post;
		$thumb_found = false;
		
		// Get custom field images if user set a custom field
		$options = get_option('nrelate_admin_options');
        $customfield = $options['admin_custom_field'];
		if ($customfield != null) {
			$customfieldvalue = get_post_meta($post->ID, $customfield, $single = true);
			if ($customfieldvalue != null) {
        $customfield_path = $options['admin_custom_field_path'];
        if ($customfield_path != null) {
          $customfieldvalue = $customfield_path . $customfieldvalue;
        }
				$content = '<p><img class="nrelate-image custom-field-image" src="' . $customfieldvalue . '" /></p>' . $content;
				$thumb_found = true;
			}
		}

		// Simple Post Thumbnails
		// http://wordpress.org/extend/plugins/simple-post-thumbnails/
		// Since 0.45.0
		// Updated 0.47.8
		if (!$thumb_found && function_exists('p75HasThumbnail')){
			$p75image = p75GetOriginalImage($post->ID);
			$p75default = get_option('p75_default_thumbnail');
			$imageurl = get_bloginfo('wpurl') . $p75image;

			if (empty($p75image)) $imageurl = $p75default;
			
			if ($imageurl) {
				$content = sprintf('<p><img class="nrelate-image p75-thumbnail" src="%s" /></p>%s', $imageurl, $content);
				$thumb_found = true;
			}
		}
		
		// Thumbshots
		// http://wordpress.org/extend/plugins/thumbshots/
		// Since 0.49.3
		if (!$thumb_found && class_exists('ThumbshotsPlugin')){
			preg_match('#<img[^>]+src=[\"\']{1}(http:\/\/(www\.)?(open\.thumbshots.org/image\.aspx|robothumb\.com/src)[^\"\']*)[\"\']{1}[^>]+>#i', $content, $images);
			$imageurl = isset($images[1]) ? $images[1] : null;
			if ( $imageurl ) {
				$content = sprintf('<p><img class="nrelate-image thumbshot-image" src="%s" alt="post thumbnail" /></p>%s', $imageurl, $content);
				$thumb_found = true;
			}
		}

		// WordPress Featured Image (Post Thumbnails)
		if (!$thumb_found && function_exists('has_post_thumbnail') && has_post_thumbnail( $post->ID )) {
			$default_attr = array( 'class' => "nrelate-image featured-image" );
			$content = '<p>' . get_the_post_thumbnail( $post->ID, 'large', $default_attr ) . '</p>' . $content;
			$thumb_found = true;
		}
		
		// Last resort for custom images, search through all custom fields for image URL and grab the first
		// url must start with http and file must be a gif, png or jpg.
		// Since 0.45.0
		if (!$thumb_found) {
			foreach ( get_post_custom($post->ID) as $key => $values ) {
				$meta = get_post_meta($post->ID, $key);
				
				// Extract metadata
				if (is_array($meta)) {
					$meta = current($meta);
				}
				
				foreach( (array)$meta as $key => $imageurl ) {

					if ( $imageurl = nrelate_get_img_url($imageurl) ) {
						$content = sprintf('<p><img class="nrelate-image auto-custom-field-image" src="%s" alt="post thumbnail" /></p>%s', $imageurl, $content);
						$thumb_found = true;
						break;
					}
				}
				
				if ($thumb_found) {
					break;
				}
			}
		}
		
		// If no images are found yet, grab the first image in the post.
		if (!$thumb_found) {
			preg_match('#<img[^>]+src=[\"\']{1}(http:\/\/.*\.(gif|png|jpg|jpeg|tif|tiff|bmp){1})[\"\']{1}[^>]+>#i', $content, $images);
			$imageurl = isset($images[1]) ? $images[1] : null;
			if ( $imageurl = nrelate_get_img_url($imageurl) ) {
				$content = sprintf('<p><img class="nrelate-image auto-content-image" src="%s" alt="post thumbnail" /></p>%s', $imageurl, $content);
				$thumb_found = true;
			}
		}
		
		
		// Last resort.  If no images in post, let's check post attachments.
		if (!$thumb_found) {
			$attachments = get_posts( array('post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post->ID, 'order' => 'ASC', 'orderby' => 'menu_order ID') );
			foreach ($attachments as $attach) {
				if($img = wp_get_attachment_image_src($attach->ID) ) {
					$content = sprintf('<p><img class="nrelate-image post-attachment" src="%s" alt="post thumbnail" /></p>%s', $img[0], $content);
					$thumb_found = true;
					break;
				}
			}
		}
		
		return $content;
	}

/**
 * Helper to get the inner image URL from a string
 * returns false if no img URL found
 */
function nrelate_get_img_url( $url ) {
	if ( !$url = trim( (string) $url ) ) return false;
	$parsed = parse_url( html_entity_decode($url) );
	
	if( isset($parsed['query']) ) {
		$params = explode( '&', $parsed['query']);
		foreach ($params as $pair) {
			if ( trim($pair) == '' ) continue;
			list( $param, $value) = explode( '=', $pair );
			if ( preg_match('#^http:\/\/(.*)\.(gif|png|jpg|jpeg|tif|tiff|bmp)$#i', $value) ) {
				// found a valid image URL in a parameter of the original URL
				return $value;
			}
		}
	}
	
	if ( preg_match('#^http:\/\/(.*)\.(gif|png|jpg|jpeg|tif|tiff|bmp)$#i', $url) ) {
		return $url;
	}
	
	return false;
}
	
/**
 * Remove Javascript from our feed
 *
 */
function nrelate_remove_script($content) {
	$content = preg_replace('#(\n?<script[^>]*?>.*?</script[^>]*?>)|(\n?<script[^>]*?/>)#is', '', $content);
	return $content;
}

/**
 * Parse oEmbed objects in our feed
 *
 */
function nrelate_parse_oembed($content) {
	global $wp_embed;
	return $wp_embed->autoembed( $content );
}


 /**
 * Execute Shortcodes in our feed
 *
 */
function nrelate_execute_shortcode($content) {
	return do_shortcode( $content );
}

/**
 * Debug mode
 * Since v45.1
 */
function nrelate_debug() {
	
	$admin_options = get_option('nrelate_admin_options');
	
	// Make category exclusion human-readable
	if ( isset( $admin_options['admin_exclude_categories'] ) ) {
		$readable_cats = array();
		foreach ( (array) $admin_options['admin_exclude_categories'] as $cat_id  ) {
			$readable_cats[ $cat_id ] = get_cat_name( $cat_id );
		}
		$admin_options['admin_exclude_categories'] = $readable_cats;
	}
	
	$options = print_r($admin_options,true);
	
	// Reindex required information
	if ( $reindex = get_option("nrelate_reindex_required") ) {
		$options .= print_r( array("nrelate_reindex_required" => $reindex), true );
	}
	
	//Get related options
	if (function_exists('nrelate_related')) {
		$options .= print_r(get_option('nrelate_related_options'),true);
		$options .= print_r(get_option('nrelate_related_options_styles'),true);
		$options .= print_r(get_option('nrelate_related_options_ads'),true);
	}

	//Get most popular options
	if (function_exists('nrelate_popular')) {
		$options .= print_r(get_option('nrelate_popular_options'),true);
		$options .= print_r(get_option('nrelate_popular_options_styles'), true);
		$options .= print_r(get_option('nrelate_popular_options_ads'), true);
	}
	
	//Get flyout options
	if (function_exists('nrelate_flyout')) {
		$options .= print_r(get_option('nrelate_flyout_options'),true);
		$options .= print_r(get_option('nrelate_flyout_options_styles'),true);
		$options .= print_r(get_option('nrelate_flyout_box_options_styles') ,true);
		$options .= print_r(get_option('nrelate_flyout_options_ads'),true);
	}

	//Get nsquared options
	if (function_exists('nrelate_nsquared')) {
		$options .= print_r(get_option('nrelate_nsquared_options'),true);
		$options .= print_r(get_option('nrelate_nsquared_options_styles'),true);
		$options .= print_r(get_option('nrelate_nsquared_box_options_styles') ,true);
		$options .= print_r(get_option('nrelate_nsquared_options_ads'),true);
	}
	
	echo '<pre>';
	print_r($options);
	echo '</pre>';
}

function nrelate_noindex() {
	echo "\t<xhtml:meta xmlns:xhtml=\"http://www.w3.org/1999/xhtml\" name=\"robots\" content=\"noindex\" />\n";
}

/**
 * MAIN NRELATE FEED
 * Serve a custom full-text feed. Thwarts FeedBurner plugins
 *
 * @credits: Originally written for nrelate by Mark Jaquith http://coveredwebservices.com/
 */
function nrelate_custom_feed() {
	if ( isset( $_GET['nrelate_feed'] ) && $_GET['nrelate_feed'] == get_option( 'nrelate_key' ) ) {
		if (isset($_GET['debug'])) {
			nrelate_debug();
			exit();
		}

		// Posts per page		
		if ( isset( $_GET['posts_per_page'] ) )
			set_query_var( 'posts_per_page', $_GET['posts_per_page'] );
		else
			set_query_var( 'posts_per_page', 50 );
			
		// Specific Posts
		if(isset($_GET['p'])){
			$p = $_GET['p'];
		}else{
			$p = null;
		}

		// Get Admin options
		$options = get_option('nrelate_admin_options');

		// Fix for pagination
		if(isset($_GET['paged'])){
			$paged = $_GET['paged'];
		}else{
			$paged = 1;
		}

		// Sticky post backwards compatibility
		global $wp_version;
		$ignore_sticky = ($wp_version >= '3.1' ? 'ignore_sticky_posts' : 'caller_get_posts');
		
		$query_posts_args = array(
			'posts_per_page' => get_query_var( 'posts_per_page' ),
			'p' => $p,
			'paged' => $paged,
			'post_status' => 'publish',
			$ignore_sticky => 1,
			'post_type' => isset($options['admin_include_post_types']) ? $options['admin_include_post_types'] : array()
		);
		
		if ( isset($options['admin_exclude_categories']) ) {
			foreach( $options['admin_exclude_categories'] as &$cat ) {
				$cat *= -1;
			}
			$query_posts_args['cat'] = implode(',', $options['admin_exclude_categories']);
		}
		
		query_posts($query_posts_args);

		// WP Super Cache: disable
		if ( function_exists('add_cacheaction') ) { define('DONOTCACHEPAGE', true); }

		//W3 Total Cache: disable
		if ( function_exists('w3tc_add_action') ) {
			define('DONOTCACHEPAGE', true);
			define('DONOTCACHEDB', true);
			define('DONOTMINIFY', true);
			define('DONOTCACHCEOBJECT', true);
		}

		// Show full content even if MORE tag is present
		global $more;
		$more = 1;

		// Force the feed to return full content
		add_filter( 'pre_option_rss_use_excerpt', create_function( '', 'return 0;' ), 0 );

		// Remove all filters that might effect feed
		remove_all_filters ('wp_title');
		remove_all_filters ('wp_title_rss');
		remove_all_filters ('the_title_rss');
		remove_all_filters ('the_title');
		remove_all_filters ('the_permalink_rss');
		remove_all_filters ('the_permalink');
		remove_all_filters ('the_content_feed');
		remove_all_filters ('the_content');
		remove_all_filters ('the_content_rss');
		remove_all_filters ('the_excerpt_rss');
		remove_all_filters ('the_excerpt');
		remove_all_filters ('comment_author_rss');
		remove_all_filters ('comment_text_rss');
		remove_all_filters ('bloginfo_rss');
		remove_all_filters ('the_author');
		remove_all_filters ('the_category_rss');

		// Bring back standard WP RSS filters
		add_filter( 'the_title_rss',      'strip_tags',		0 );
		add_filter( 'the_title_rss',      'ent2ncr',      	0 );
		add_filter( 'the_title_rss',      'esc_html',		0 );
		add_filter( 'the_content_rss',    'ent2ncr',      	0 );
		add_filter( 'the_content_feed',   'ent2ncr',		0 );
		add_filter( 'the_excerpt_rss',    'convert_chars',	0 );
		add_filter( 'the_excerpt_rss',    'ent2ncr',      	0 );
		add_filter( 'comment_author_rss', 'ent2ncr',      	0 );
		add_filter( 'comment_text_rss',   'ent2ncr',      	0 );
		add_filter( 'comment_text_rss',   'esc_html',		0 );
		add_filter( 'bloginfo_rss',       'ent2ncr',      	0 );
		add_filter( 'the_author',         'ent2ncr',      	0 );

		// Execute Shortcodes
		add_filter('the_excerpt_rss', 'nrelate_execute_shortcode', 5);
        add_filter('the_content_feed', 'nrelate_execute_shortcode', 5);
		
		// Support oEmbed objects
		add_filter('the_excerpt_rss', 'nrelate_parse_oembed', 7);
        add_filter('the_content_feed', 'nrelate_parse_oembed', 7);
		
        // Remove Javascript
		add_filter('the_excerpt_rss', 'nrelate_remove_script', 10);
        add_filter('the_content_feed', 'nrelate_remove_script', 10);
		
		// Get custom images
        add_filter('the_excerpt_rss', 'nrelate_get_custom_images', 20);
        add_filter('the_content_feed', 'nrelate_get_custom_images', 20);
		
		
		/**
		 * Support for other plugins
		 */
		
		// Smart YouTube
		if ( class_exists('SmartYouTube') ) {
			$smrtYtb = new SmartYouTube();
			add_filter('the_content_feed', array($smrtYtb, 'check'), 30);
		}
		
		
		// Add post count
		add_action ('rss2_head', 'nrelate_post_count');

		//Show nrelate custom CDATA
		add_filter('the_category_rss', 'nrelate_cdata', 0);

		// Prevent search engine indexing
		header("X-Robots-Tag: noindex", true);
		add_action('rss2_head', 'nrelate_noindex');
		
		// Use WP's feed template
		do_feed_rss2( false );
		exit();
	}
}
add_action( 'template_redirect', 'nrelate_custom_feed', 0 );
?>