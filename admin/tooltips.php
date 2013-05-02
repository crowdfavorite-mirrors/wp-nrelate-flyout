<?php
/**
 * nrelate tooltips
 *
 * Common tooltips for nrelate
 * Checks if another nrelate plugin loaded these functions first
 * 
 * @package nrelate
 * @subpackage Functions
 */

 
$nrelate_tooltips = array(
	"_thumbnail" =>	__("Just like it says, this is where you decide whether to show thumbnail links, or text only links. If you have images in most of your posts, try out thumbnails. If you don't have pictures in your posts, it is probably best to go with text only. Try out both and see what each looks like on your site. Switching back and forth is easy.","nrelate"),
	
	"_thumbnail_size" => __("This was one of the first user suggested settings we added. Every site is different, and this setting helps you fit the plugin perfectly within your post width. For example, if you want to fit a certain number of thumbnails across the bottom of the post, you will need to play with this setting and the <strong>Maximum number of posts to display</strong> setting.","nrelate"),
	
	"_default_image" => __("If a post doesn't have an image, your default image will appear for that post whenever it is linked to. If you do not set a default image, for posts with no image we will display a random license free image from our database. Setting a default image is highly recommended.","nrelate"),
	
	"_title" => __("This is where you control the title of the plugin box. Common English titles include &#34;You May Also Like&#34;, &#34;More  Reading&#34;, &#34;Also Check Out&#34;, &#34;Related Articles&#34; and so on.","nrelate"),
	
	"_number_of_posts" => __("This setting allows you to specify the number of links you want to show from your own site. You can use this setting along with thumbnail size to make the plugin fit perfectly within the width of your posts. If you want to show more than one row of thumbnail links, just select a higher number here. Note: If you are showing links to partner sites/and or advertisers, these links will be included in this number. For example, if you select 10 posts, 2 Partner links, and 2 Ad links, your posts will show a total of 10 links (6 Post links, 2 Partner links and 2 Ad links).","nrelate"),
	
	"_max_age" => __("Here you can specify the maximum age of posts to display. If you write more &#34;evergreen&#34; content, it is best to make this setting go back at least as many years as your site has been around. If your content is more time sensitive, you can adjust this based on however any minutes, hours, days, or weeks you want to.","nrelate"),
	
	"_mp_max_age" => __("Select the time period for post views. For example: If you choose ONE MONTH, then nRelate will show the most popular posts viewed in the last month. This is not the date when the post was published. This is the timeframe that posts were viewed.  In this example, a post published a year ago could show up if your visitors started reading it and make it popular.","nrelate"),
	
	"_exclude_cats" => __("This allows to make sure certain categories do not show up in the plugin. To do this, you will need to go to the nRelate dashboard, select the categories you want to block, and press &#34;Save Settings&#34;. The nRelate servers will immediately reindex your website. Most people who use this have a timely/personal/or other types of posts that they do not want to always direct visitors to.","nrelate"),
	
	"_show_post_title" => __("Leave this box selected to display the post title. You spent a lot of time writing awesome titles, so why not grab your reader's attention by displaying them? The most common types of sites that turn title &#34;off&#34; are heavily focused on photos. We think its best to leave the title on, no matter what.","nrelate"),
	
	"_max_chars_per_line" => __("Set the maximum character length of the title. If some of your posts have really long titles, it might be a good idea to set this to around 50.","nrelate"),
	
	"_show_post_excerpt" => __("You can also display an excerpt of your post by selecting this setting. This setting is highly recommended to be used along with the Huffington Post style (located in the Style Gallery). Be sure to set a maximum excerpt length or else you may stretch out the plugin on the page.","nrelate"),
	
	"_max_chars_post_excerpt" => __("Set the maximum word length of the post excerpt. It's usually fine to set this at 20.","nrelate"),

	"_where_to_show" => __("This setting allows you to control which pages nrelate will display on. Even if you <a href='http://nrelate.com/theblog/2011/12/22/manually-adding-nrelate-code-to-your-wordpress-template/' target='_blank'>manually</a> add our code to  your theme template, you still need to check the appropriate boxes here.","nrelate"),

	"_loc_top" => __("nrelate will automatically display <em>before</em> your post content. If you want to control where nrelate displays, you should uncheck this box, and <em>'Bottom of post (Automatic)'</em> and <a href='http://nrelate.com/theblog/2011/12/22/manually-adding-nrelate-code-to-your-wordpress-template/'>follow these instructions on manually adding our code.</a>.","nrelate"),

	"_loc_bottom" => __("nrelate will automatically display <em>after</em> your post content. If you want to control where nrelate displays, you should uncheck this box, and <em>'Top of post (Automatic)'</em> and <a href='http://nrelate.com/theblog/2011/12/22/manually-adding-nrelate-code-to-your-wordpress-template/' target='_blank'>follow these instructions on manually adding our code.</a>.","nrelate"),

	"_is_404" => __("Requires <a href='#loc_manual'>manual installation</a> in your theme's 404.php file. You may also use the <a href='widgets.php' target='_blank'>nrelate widget</a>, if your 404 page is widgetized.","nrelate"),
	
	
	// ADVERTISING
	"_adplacement" => __("When choosing to display ads, you can decide where you want them to display:
							<ul>
								<li>MIXED: ads will be randomly distributed in nrelate content.
								<li>FIRST: ads will show before nrelate content.
								<li>LAST: ads will show after nrelate content.
								<li>SEPARATE: ads will display in a totally separate area than nrelate content, and you will have different styles to choose from in the Style Gallery. 
							</ul>"
						,"nrelate"),

	);
					
?>