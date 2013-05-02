<?php
/**
 * nrelate styles
 *
 * Common styles for nrelate
 *
 * Checks if another nrelate plugin loaded these functions first
 * 
 * @package nrelate
 * @subpackage Functions
 */

 
 
/* = Thumbnail Styles
-----------------------------------------------
 */ 
$nrelate_thumbnail_styles = array(
'default' => array(
					"stylesheet" => "nrelate-panels-default",
					"styleclass" => "default",
					"layout" => "1col",
					"name"=>__('Default','nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>Border.</li>
										<li>Left aligned text.</li>
									</ul>','nrelate'),
					"info"=>__('We developed this style as a simple way to place nrelate content on any site. We wanted to keep everything simple, so that the focus is on your site\'s content.','nrelate'),
				),
'bty' => array(
					"stylesheet" => "nrelate-panels-bty",
					"styleclass" => "bty",
					"layout" => "1col",
					"name"=>__('Bloginity.com', 'nrelate'),
					"features"=>__('<ul>
										<li>No Border.</li>
										<li>Centered text.</li>
										<li>No Hover.</li>
									</ul>','nrelate'),
					"info"=>__('This style is based upon the custom version developed at <a href="http://bloginity.com" target="_blank">Bloginity.com</a>.<br>Bloginity is a a young, vibrant online magazine, fashion blog, and source of culture entertainment news.','nrelate'),
				),
'dhot' => array(
					"stylesheet" => "nrelate-panels-dhot",
					"styleclass" => "dhot",
					"layout" => "1col",
					"name"=>__('LinkWithin', 'nrelate'),
					"features"=>__('<ul>
										<li>All Capital text.</li>
										<li>Hover Effects.</li>
										<li>Stylized thumbnail dividing line.</li>
									</ul>', 'nrelate'),
					"info"=>__('This style is based upon the related thumbnail widget from <a href="http://linkwithin.com" target="_blank">Linkwithin.com</a>. LinkWithin is a blog widget that appears under each post.','nrelate'),
				
				
				),
'huf' => array(
					"stylesheet" => "nrelate-panels-huf",
					"styleclass" => "huf",
					"layout" => "1col",
					"name"=>__('Huffingtonpost', 'nrelate'),
					"features"=>__('<ul>
										<li>Side Layout.</li>
										<li>No Hover Effect.</li>
										<li>Built for Showing Excerpt.</li>
									</ul>', 'nrelate'),
					"info"=>__('This style is based upon the related content area on <a href="http://huffingtonpost.com" target="_blank">huffingtonpost.com</a>. The Huffington Post is an American news website and aggregated blog.','nrelate'),
				
				),
'tre' => array(
					"stylesheet" => "nrelate-panels-tre",
					"styleclass" => "tre",
					"layout" => "1col",
					"name"=>__('Trendland.net', 'nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>Border.</li>
										<li>Left aligned text.</li>
										<li>Text over thumbnail.</li>
										<li>Semi-transparent text background.</li>
									</ul>', 'nrelate'),
					"info"=>__('This style is based upon the custom version developed at <a href="http://trendland.net" target="_blank">Trendland.net</a>. Trendland is an online magazine that redefines trend forecasting through a rich visual journey.','nrelate'),
				
				),
'toon' => array(
					"stylesheet" => "nrelate-panels-toon",
					"styleclass" => "toon",
					"layout" => "1col",
					"name"=>__('ToonBarn', 'nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>Border.</li>
										<li>Left aligned text.</li>
										<li>Text over thumbnail.</li>
										<li>Semi-transparent text background.</li>
									</ul>', 'nrelate'),
					"info"=>__('** Best with Thumbnail size of 100px or more ** This style is based upon the custom version developed at <a href="http://toonbarn.com" target="_blank">ToonBarn.com</a>. ToonBarn is the ultimate hang out for cartoon fans.','nrelate'),
				
				),
'pol' => array(
					"stylesheet" => "nrelate-panels-pol",
					"styleclass" => "pol",
					"layout" => "1col",
					"name"=>__('Polaroid', 'nrelate'),
					"features"=>__('<ul>
										<li>Turns your images into Polaroid pictures.</li>
										<li>Rotated randomly.</li>
										<li>**Only works well in modern browsers.</li>
									</ul>', 'nrelate'),
					"info"=>__('Polaroid style images. Inspired by <a href="http://www.zurb.com/playground/css3-polaroids" target="_blank">this article</a> at Zurb.com.','nrelate'),
				
				),
'loud' => array(
					"stylesheet" => "nrelate-panels-loud",
					"styleclass" => "loud",
					"layout" => "1col",
					"name"=>__('LoudFeed', 'nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>Rounded corners</li>
									</ul>','nrelate'),
					"info"=>__('** "Animated sponsored text in ads" are disable in this style, if you have ads turned on.<br/>This style was designed for nRelate by the team at <a href="http://LoudFeed.com" target="_blank">LoudFeed</a>.','nrelate'),
				),
'nsq' => array(
          "name"=>__('nSquared Classic'),
          "stylesheet" => "nrelate-panels-nsq",
          "styleclass" => "nsq",
          "layout" => "1col",
          "features"=>__('<ul>
                    <li>Hover effects with title transition.</li>
                    <li>No border.</li>
                    <li>Left aligned text.</li>
                  </ul>','nrelate'),
          "info"=>__('Created to be the default style for nSquared. Show off your pictures with a minimal aesthetic in mind.','nrelate'),
        ),
'til' => array(
          "name"=>__('Tiles'),
          "stylesheet" => "nrelate-panels-til",
          "styleclass" => "til",
          "layout" => "1col",
          "features"=>__('<ul>
                    <li>Hover effect with translucent title.</li>
                    <li>No border.</li>
                    <li>Left aligned text.</li>
                  </ul>','nrelate'),
          "info"=>__('A more static version of nSquared Classic.','nrelate'),
        ),
'none' => array(
					"name"=>__('none'),
					"stylesheet" => "",
					"styleclass" => "none",
					"layout" => "1col",
					"features"=>__('<ul>
										<li>Allows you to create your own css.</li>
									</ul>','nrelate'),
					"info"=>__('Selecting this option will disable all nrelate styles, allowing you to create your own.','nrelate'),
				
				)

);


/* = Thumbnail / Ad Styles
-----------------------------------------------
 */ 
$nrelate_thumbnail_styles_separate = array(
'til-2col' => array(
					"stylesheet" => "nrelate-panels-til",
					"styleclass" => "til",
					"layout" => "2col",
					"name"=>__('Default:<br/><em>2 Columns</em>','nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>No border.</li>
										<li>Left aligned text.</li>
									</ul>','nrelate'),
					"info"=>__('Child of nSquared Classic. More static and ','nrelate'),
				),
'til-2row' => array(
					"stylesheet" => "nrelate-panels-til",
					"styleclass" => "til",
					"layout" => "2row",
					"name"=>__('Default:<br/><em>2 Columns</em>','nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>No border.</li>
										<li>Left aligned text.</li>
									</ul>','nrelate'),
					"info"=>__('Child of nSquared Classic. More static and ','nrelate'),
				),
'default-2col' => array(
					"stylesheet" => "nrelate-panels-default",
					"styleclass" => "default",
					"layout" => "2col",
					"name"=>__('Default:<br/><em>2 Columns</em>','nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>Border.</li>
										<li>Left aligned text.</li>
									</ul>','nrelate'),
					"info"=>__('We developed this style as a simple way to place nrelate content on any site. We wanted to keep everything simple, so that the focus is on your site\'s content.','nrelate'),
				),
'default-2row' => array(
					"stylesheet" => "nrelate-panels-default",
					"styleclass" => "default",
					"layout" => "2row",
					"name"=>__('Default:<br/><em>2 Rows</em>','nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>Border.</li>
										<li>Left aligned text.</li>
									</ul>','nrelate'),
					"info"=>__('We developed this style as a simple way to place nrelate content on any site. We wanted to keep everything simple, so that the focus is on your site\'s content.','nrelate'),
				),
'bty-2col' => array(
					"stylesheet" => "nrelate-panels-bty",
					"styleclass" => "bty",
					"layout" => "2col",
					"name"=>__('Bloginity.com:<br/><em>2 Columns</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>No Border.</li>
										<li>Centered text.</li>
										<li>No Hover.</li>
									</ul>','nrelate'),
					"info"=>__('This style is based upon the custom version developed at <a href="http://bloginity.com" target="_blank">Bloginity.com</a>.<br>Bloginity is a a young, vibrant online magazine, fashion blog, and source of culture entertainment news.','nrelate'),
				),
'bty-2row' => array(
					"stylesheet" => "nrelate-panels-bty",
					"styleclass" => "bty",
					"layout" => "2row",
					"name"=>__('Bloginity.com:<br/><em>2 Rows</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>No Border.</li>
										<li>Centered text.</li>
										<li>No Hover.</li>
									</ul>','nrelate'),
					"info"=>__('This style is based upon the custom version developed at <a href="http://bloginity.com" target="_blank">Bloginity.com</a>.<br>Bloginity is a a young, vibrant online magazine, fashion blog, and source of culture entertainment news.','nrelate'),
				),
'dhot-2col' => array(
					"stylesheet" => "nrelate-panels-dhot",
					"styleclass" => "dhot",
					"layout" => "2col",
					"name"=>__('LinkWithin:<br/><em>2 Columns</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>All Capital text.</li>
										<li>Hover Effects.</li>
										<li>Stylized thumbnail dividing line.</li>
									</ul>', 'nrelate'),
					"info"=>__('This style is based upon the related thumbnail widget from <a href="http://linkwithin.com" target="_blank">Linkwithin.com</a>. LinkWithin is a blog widget that appears under each post.','nrelate'),
				
				
				),
'dhot-2row' => array(
					"stylesheet" => "nrelate-panels-dhot",
					"styleclass" => "dhot",
					"layout" => "2row",
					"name"=>__('LinkWithin:<br/><em>2 Rows</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>All Capital text.</li>
										<li>Hover Effects.</li>
										<li>Stylized thumbnail dividing line.</li>
									</ul>', 'nrelate'),
					"info"=>__('This style is based upon the related thumbnail widget from <a href="http://linkwithin.com" target="_blank">Linkwithin.com</a>. LinkWithin is a blog widget that appears under each post.','nrelate'),
				
				
				),
'huf-2col' => array(
					"stylesheet" => "nrelate-panels-huf",
					"styleclass" => "huf",
					"layout" => "2col",
					"name"=>__('Huffingtonpost:<br/><em>2 Columns</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>Side Layout.</li>
										<li>No Hover Effect.</li>
										<li>Built for Showing Excerpt.</li>
									</ul>', 'nrelate'),
					"info"=>__('This style is based upon the related content area on <a href="http://huffingtonpost.com" target="_blank">huffingtonpost.com</a>. The Huffington Post is an American news website and aggregated blog.','nrelate'),
				
				),
'huf-2row' => array(
					"stylesheet" => "nrelate-panels-huf",
					"styleclass" => "huf",
					"layout" => "2row",
					"name"=>__('Huffingtonpost:<br/><em>2 Rows</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>Side Layout.</li>
										<li>No Hover Effect.</li>
										<li>Built for Showing Excerpt.</li>
									</ul>', 'nrelate'),
					"info"=>__('This style is based upon the related content area on <a href="http://huffingtonpost.com" target="_blank">huffingtonpost.com</a>. The Huffington Post is an American news website and aggregated blog.','nrelate'),
				
				),
'tre-2col' => array(
					"stylesheet" => "nrelate-panels-tre",
					"styleclass" => "tre",
					"layout" => "2col",
					"name"=>__('Trendland.net:<br/><em>2 Columns</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>Border.</li>
										<li>Left aligned text.</li>
										<li>Text over thumbnail.</li>
										<li>Semi-transparent text background.</li>
									</ul>', 'nrelate'),
					"info"=>__('This style is based upon the custom version developed at <a href="http://trendland.net" target="_blank">Trendland.net</a>. Trendland is an online magazine that redefines trend forecasting through a rich visual journey.','nrelate'),
				
				),
'tre-2row' => array(
					"stylesheet" => "nrelate-panels-tre",
					"styleclass" => "tre",
					"layout" => "2row",
					"name"=>__('Trendland.net:<br/><em>2 Rows</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>Border.</li>
										<li>Left aligned text.</li>
										<li>Text over thumbnail.</li>
										<li>Semi-transparent text background.</li>
									</ul>', 'nrelate'),
					"info"=>__('This style is based upon the custom version developed at <a href="http://trendland.net" target="_blank">Trendland.net</a>. Trendland is an online magazine that redefines trend forecasting through a rich visual journey.','nrelate'),
				
				),
'toon-2col' => array(
					"stylesheet" => "nrelate-panels-toon",
					"styleclass" => "toon",
					"layout" => "2col",
					"name"=>__('ToonBarn:<br/><em>2 Columns</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>Border.</li>
										<li>Left aligned text.</li>
										<li>Text over thumbnail.</li>
										<li>Semi-transparent text background.</li>
									</ul>', 'nrelate'),
					"info"=>__('** Best with Thumbnail size of 100px or more ** This style is based upon the custom version developed at <a href="http://toonbarn.com" target="_blank">ToonBarn.com</a>. ToonBarn is the ultimate hang out for cartoon fans.','nrelate'),
				
				),
'toon-2row' => array(
					"stylesheet" => "nrelate-panels-toon",
					"styleclass" => "toon",
					"layout" => "2row",
					"name"=>__('ToonBarn:<br/><em>2 Rows</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>Border.</li>
										<li>Left aligned text.</li>
										<li>Text over thumbnail.</li>
										<li>Semi-transparent text background.</li>
									</ul>', 'nrelate'),
					"info"=>__('** Best with Thumbnail size of 100px or more ** This style is based upon the custom version developed at <a href="http://toonbarn.com" target="_blank">ToonBarn.com</a>. ToonBarn is the ultimate hang out for cartoon fans.','nrelate'),
				
				),
'pol-2col' => array(
					"stylesheet" => "nrelate-panels-pol",
					"styleclass" => "pol",
					"layout" => "2col",
					"name"=>__('Polaroid:<br/><em>2 Columns</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>Turns your images into Polaroid pictures.</li>
										<li>Rotated randomly.</li>
										<li>**Only works well in modern browsers.</li>
									</ul>', 'nrelate'),
					"info"=>__('Polaroid style images. Inspired by <a href="http://www.zurb.com/playground/css3-polaroids" target="_blank">this article</a> at Zurb.com.','nrelate'),
				
				),
'pol-2row' => array(
					"stylesheet" => "nrelate-panels-pol",
					"styleclass" => "pol",
					"layout" => "2row",
					"name"=>__('Polaroid:<br/><em>2 Rows</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>Turns your images into Polaroid pictures.</li>
										<li>Rotated randomly.</li>
										<li>**Only works well in modern browsers.</li>
									</ul>', 'nrelate'),
					"info"=>__('Polaroid style images. Inspired by <a href="http://www.zurb.com/playground/css3-polaroids" target="_blank">this article</a> at Zurb.com.','nrelate'),
				
				),
'loud-2col' => array(
					"stylesheet" => "nrelate-panels-loud",
					"styleclass" => "loud",
					"layout" => "2col",
					"name"=>__('LoudFeed:<br/><em>2 Columns</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>Rounded corners</li>
									</ul>','nrelate'),
					"info"=>__('** "Animated sponsored text in ads" are disable in this style, if you have ads turned on.<br/>This style was designed for nRelate by the team at <a href="http://LoudFeed.com" target="_blank">LoudFeed</a>.','nrelate'),
				),
'loud-2row' => array(
					"stylesheet" => "nrelate-panels-loud",
					"styleclass" => "loud",
					"layout" => "2row",
					"name"=>__('LoudFeed:<br/><em>2 Rows</em>', 'nrelate'),
					"features"=>__('<ul>
										<li>Hover effects.</li>
										<li>Rounded corners</li>
									</ul>','nrelate'),
					"info"=>__('** "Animated sponsored text in ads" are disable in this style, if you have ads turned on.<br/>This style was designed for nRelate by the team at <a href="http://LoudFeed.com" target="_blank">LoudFeed</a>.','nrelate'),
				),
'none' => array(
					"name"=>__('none'),
					"stylesheet" => "",
					"styleclass" => "none",
					"layout" => "2row",
					"features"=>__('<ul>
										<li>Allows you to create your own css.</li>
									</ul>','nrelate'),
					"info"=>__('Selecting this option will disable all nrelate styles, allowing you to create your own.','nrelate'),
				
				)
);


/* = Text Styles
-----------------------------------------------
 */ 
$nrelate_text_styles = array(
'default' => array(
					"stylesheet" => "nrelate-text-default",
					"styleclass" => "default",
					"layout" => "1col",
					"name"=>__('Default','nrelate'),
					"features"=>__('<ul>
										<li>Simple.</li>
										<li>Left aligned text.</li>
									</ul>','nrelate'),
					"info"=>__('We developed this style as a simple way to place nrelate content on any site. We wanted to keep everything simple, so that the focus is on your site\'s content.','nrelate'),
				),
'engadget' => array(
					"stylesheet" => "nrelate-text-engadget",
					"styleclass" => "engadget",
					"layout" => "1col",
					"name"=>__('Engadget','nrelate'),
					"features"=>__('<ul>
										<li>Colorful Bars</li>
										<li>Bars reduce in size.</li>
										<li>Highlights number of Views. ( nRelate\'s Most Popular plugin, only )</li>
									</ul>','nrelate'),
					"info"=>__('Bright, Colorful Bars. Inspired by the Comments sidebar at <a href="http://www.engadget.com/" target="_blank">Engadget.com</a>.<br>','nrelate'),
				),
'none' => array(
					"name"=>__('none'),
					"stylesheet" => "",
					"styleclass" => "none",
					"layout" => "1col",
					"features"=>__('<ul>
										<li>Allows you to create your own css.</li>
									</ul>','nrelate'),
					"info"=>__('Selecting this option will disable all nrelate styles, allowing you to create your own.','nrelate'),
				
				)
);

/* = Text/Ad Styles
-----------------------------------------------
 */ 
$nrelate_text_styles_separate = array(
'default-text-2col' => array(
					"stylesheet" => "nrelate-text-default",
					"styleclass" => "default",
					"layout" => "2col",
					"name"=>__('Default:<br/><em>2 Columns</em>','nrelate'),
					"features"=>__('<ul>
										<li>Simple.</li>
										<li>Left aligned text.</li>
									</ul>','nrelate'),
					"info"=>__('We developed this style as a simple way to place nrelate content on any site. We wanted to keep everything simple, so that the focus is on your site\'s content.','nrelate'),
				),
'default-text-2row' => array(
					"stylesheet" => "nrelate-panels-text",
					"styleclass" => "default",
					"layout" => "2row",
					"name"=>__('Default:<br/><em>2 Rows</em>','nrelate'),
					"features"=>__('<ul>
										<li>Simple.</li>
										<li>Left aligned text.</li>
									</ul>','nrelate'),
					"info"=>__('We developed this style as a simple way to place nrelate content on any site. We wanted to keep everything simple, so that the focus is on your site\'s content.','nrelate'),
				),
'engadget-text-2col' => array(
					"stylesheet" => "nrelate-text-engadget",
					"styleclass" => "engadget",
					"layout" => "2col",
					"name"=>__('Engadget:<br/><em>2 Columns</em>','nrelate'),
					"features"=>__('<ul>
										<li>Colorful Bars</li>
										<li>Bars reduce in size.</li>
										<li>Highlights number of Views. ( nRelate\'s Most Popular plugin, only )</li>
									</ul>','nrelate'),
					"info"=>__('Bright, Colorful Bars. Inspired by the Comments sidebar at <a href="http://www.engadget.com/" target="_blank">Engadget.com</a>.<br>','nrelate'),
				),
'engadget-text-2row' => array(
					"stylesheet" => "nrelate-text-engadget",
					"styleclass" => "engadget",
					"layout" => "2row",
					"name"=>__('Engadget:<br/><em>2 Rows</em>','nrelate'),
					"features"=>__('<ul>
										<li>Colorful Bars</li>
										<li>Bars reduce in size.</li>
										<li>Highlights number of Views. ( nRelate\'s Most Popular plugin, only )</li>
									</ul>','nrelate'),
					"info"=>__('Bright, Colorful Bars. Inspired by the Comments sidebar at <a href="http://www.engadget.com/" target="_blank">Engadget.com</a>.<br>','nrelate'),
				),
'none' => array(
					"name"=>__('none'),
					"stylesheet" => "",
					"styleclass" => "none",
					"layout" => "2row",
					"features"=>__('<ul>
										<li>Allows you to create your own css.</li>
									</ul>','nrelate'),
					"info"=>__('Selecting this option will disable all nrelate styles, allowing you to create your own.','nrelate'),
				
				)
);

?>