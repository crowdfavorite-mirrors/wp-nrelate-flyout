=== nrelate Flyout ===
Contributors: nrelate, slipfire, sbruner
Tags: related posts, related content, related, pages, post, posts, fly-out, flyout, slider, fade, thumbnails, animated, animation, box, featured, jquery, new york times, NYTimes, nrelate
Tested up to: 3.4.1
Requires at least: 2.9
Stable tag: 0.51.2


Display related content in a cool flyout box... similarly to NYTimes.com.

== Description ==
Display related content from your site or blogroll, in a cool flyout box... similarly to NYTimes.com.

nrelate is not just another related posts plugin. Our patent-pending technology continuously analyzes your website content and displays other related posts from your website.  This ultimately leads to higher page-views for your site, and a better user experience for your visitors.

Installing this plugin is as simple as activating it, and you can leave the rest to nrelate.  Once activated, the nrelate servers will immediately begin analyzing your website content and associating similar articles.  Of course, we provide an options page so you can fine tune the display.

nrelate's style gallery allows you to customize the look of our plugin by choosing one of our set styles, or designing your own.<br>
<a href="http://wordpress.org/extend/plugins/nrelate-flyout/screenshots/">Check out the screenshots.</a>

Advertising is also possible with the plugin. Ads come with the same display options as the related content and are a great way to earn a little extra income from your blog.

Because all of the processing and analyzing runs on our servers and not yours, nrelate doesn't cause any additional load on your hosting account (especially if you're using shared hosting).

<a href="http://www.nrelate.com" title="nrelate home page">Learn more about nrelate</a> or <a href="http://profiles.wordpress.org/users/nrelate/">View other nrelate plugins.</a>


== Installation ==

1. Activate the nrelate Flyout plugin
2. Head on over to the nrelate settings page and adjust your settings.
3. Sit back and relax... nrelate is analyzing your content and will display related content in a cool flyout, within two hours.

== Frequently Asked Questions ==

= What does this plugin do? =
The nrelate Flyout plugin displays your related content in a Flyout box, similarly to NYTimes.com  Our plugin analyzes your website content, and returns a list of posts that are related to the current story being viewed by your visitor.  If you like, you can also include posts from the websites in your blogroll.

= What makes nrelate different from all the other related content services? =
nrelate started because we believe we can do a better job than the other services out there.  Our patent-pending technology is continuously being improved, and the results are better than the competition.  We're sure you'll be happy with the results... but if you're not, removing nrelate from your website is as easy as deactivating the plugin.

= Does this plugin slow down my website? =
Absolutely not.  Since the nrelate servers are doing all the hard work, your website can focus on what it does best... show content. In fact, if you switch to nrelate from a local related content plugin like YARPP, you may actually see a speed improvement on your site.

= What are my display choices? =
You can show related content as cool image thumbnails (choose from six image sizes), or simple text with bullets. When choosing thumbnails we will look in your post to find the first image and use that. You can also choose a default image to show when your post contains none.  In the plugin options page you can enter your default image url. If your post has no image, and you have not set a default, we will show a random one from our image library.<br>
<a href="http://wordpress.org/extend/plugins/nrelate-related-content/screenshots/">Check out the screenshots.</a>

= Is advertising optional? =
Yes, you always have the option to display or not display ads.

= What ad display options do you offer? =
If you sign up for advertising, you will be able to display up to ten advertisements within the plugin. If you have selected the thumbnail view, then thumbnails will show up. If you have selected text links, then text ads will show up. You can show ads either at the front, end, or mixed within your content links. As of version 0.51.0, you can display ads totally separate from your content links as well.

= Does nrelate offer a revenue share on ads? =
Yes, its your blog, you should be making money on it!

= Where do I sign up for ads? =
After installing the plugin, you can <a href="http://nrelate.com/partners/content-publishers/sign-up-for-advertising/">sign up for advertising here.</a>

= Will it look like the rest of my site? =
Many of your website styles will automatically be used by the plugin so it will blend in nicely with your website.  We do need to set some of our own styles to make it work properly. However, you can makes changes to our styles by including your own CSS in your stylesheet.

= I just activated the plugin and I don't see anything, what's up? =
Once you activate the plugin, the nrelate server will start analyzing your website.  Related content should show up within two hours.

= Can I use your plugin with WordPress Multisite? =
Absolutely. You must activate our plugin on each individual website in your Multi-site install. You cannot use "Network Activate".

= Does plugin support external images, e.g. uploaded on Flickr? =
Absolutely! If you have images in your post, nrelate will find them and auto-create thumbnails.

= Does nrelate work with WordPress "Post Thumbnails"? =
Yes, our plugin automatically detects if you are using post thumbnails.

= Does nrelate work if I use custom fields for my images? =
Yes. Just go to our settings page, and fill in the name of the custom field you use.

= How does the nrelate plugin get my website content? =
Our plugin creates an additional nrelate specific RSS feed.  We use this feed so that we don't run into issues if your regular RSS feed is set to "Summary" or if you use a service like Feedburner.

= What is in the nrelate specific RSS feed and how is it used? =
The nrelate specific RSS feed is very similar to your standard RSS feed if you set it to full feed.  Since we had some users that had their feed to just show a summary and others that used Feedburner, we set this up.  The nrelate specific feed can only be accessed by using a random key that is generated upon install.  To make sure this feed is not used for other purposes, we hired WordPress lead developer and security expert, Mark Jaquith, to build it for us.

= How does nrelate know when new content is published? =
When you activate an nrelate plugin, our Pinghost is automatically added to your list of Update Services, so we are automatically notified when you publish a new post. This allows us to index your new content quickly. You can learn more about the WordPress Update Services at the <a href="http://codex.wordpress.org/Update_Services">WordPress Codex</a>.

= My website is not in English, will nrelate work? =
Our plugin will work on websites in the following languages: Dutch, English, French, German, Indonesian, Italian, Polish, Portuguese, Russian, Spanish, Swedish and Turkish.  If you do not see your language on the list or you think that we could improve the relevancy of our plugin in your language, please <a href="http://nrelate.com/forum/">contact us</a> and we will work with you to configure the plugin accordingly.

== Screenshots ==

1. "NY Times" animation style with "Huffington Post" content style.
2. "Simple" animation style with "Huffington Post" content style.
3. "Simply Dark" animation style with "Huffington Post" content style.
4. "Centered" animation style with "Trendland" content style.
5. "Centered Dark" animation style with "Trendland" content style.
6. "nrelate Default" style
7. "Bloginity" style
8. "LinkWithin" style
9. "Huffington Post" style
10. "Trendland" style
11. "Polaroid" style
12. "Text" style
13. Advertising mixed into content
14. Hovering on an advertisement


== Changelog ==

= 0.51.2 =
* New Style: LoudFeed.
* Thesis information message on dashboard.
* Bug: issue with service status message in dashboard.
* Bug: Only Published posts should show up in nrelate feed.
* Flyout does not show if there's no content to show.

= 0.51.1 =
* Fixed CSS path
* Bug fix: issue with NONE style

= 0.51.0 =
* Allow for advertising to appear separately from content.
* Eighteen(18) new styles for separate advertising.
* Disable Flyout on all mobile devices.

= 0.50.6 =
* Update wp_Http calls to WordPress HTTP functions.
* Switch to plugins_url() function.
* Fixed category exclusion bug in nrelate feed.
* Fixed Thumbshots plugin support in nrelate feed.
* Support oEmbed in nrelate feed.

= 0.50.5 =
* Fixed bug with Flyout injection.

= 0.50.4 =
* Fixed bug with Flyout injection.

= 0.50.3 =
* Fixed bug on nrelate dashboard and TOS.
* Remove two div's that are not needed anymore.
* Fixed error with nrelate debug.


= 0.50.2 =
* Removed the_excerpt filter since we only run on is_single.
* Fixed clickthrough iframe bug.
* Include/Exclude Post types in data pool.
* Post Type added to nrelate custom feed.
* Change wp_print_styles to wp_enqueue_scripts for WordPress 3.3 compatibility.
* Changed get_permalink($post->ID) to get_permalink($wp_query->post->ID), so we can accurately pull the correct url.

= 0.50.1 =
* Fixed file_get_contents error.

= 0.50.0 =
* The most efficient version yet. Tons of functions are now common to all nrelate plugins!
* New Engadget style!
* 404 Page support!
* Better explaination of advertising opportunities for publishers.
* Add more CSS classes to Text.
* nrelate product check notice.
* nrelate product array now holds the timestamp.
* Fix bug with Text stylesheet handle is incorrect.
* Elimnated reindexing trigger for non-index option changes.
* Fixed some PHP warning errors.
* Ad animation fix. Animation now on a per plugin basis.
* JS & CSS Script Optimizer compatibility warning message
* load css and jquery only when required.
* Fix nrelate_title_url not getting post ID.
* Fixed issue with WP Super Cache flush not working properly.
* Flush cache on plugin activation.
* Avoid feed search engine indexation.

= 0.49.4 =
* Javascript change to open ads in a new tab/window
* Bug fix for Thumbshots plugin

= 0.49.3 =
* New Polaroid style
* Added is_home as a display option.
* Grab proper image for Thumbshots plugin.
* Bug fix for sticky posts.

= 0.49.2 =
* Initial release

== Upgrade Notice ==

= 0.51.0 =
18 new styles! Ads can be displayed separately.

= 0.50.0 =
Two new styles added to style Gallery: Polaroid and Bold Numbers.