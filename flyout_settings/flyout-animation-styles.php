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

 
 
$nrelate_slideout_anim_styles = array(
'nyt' => array(
					"stylesheet" => "nrelate-flyout-nyt.css",
					"name"=>__('NY Times','nrelate'),
					"features"=>__('<ul>
										<li>Easy to read</li>
									</ul>','nrelate'),
					"info"=>__('This style is based upon the original flyout at the NYTimes.com.','nrelate'),
				),
'simple' => array(
					"stylesheet" => "nrelate-flyout-simple.css",
					"name"=>__('Simple','nrelate'),
					"features"=>__('<ul>
										<li>Clean</li>
										<li>Round corners (CSS3 browsers only)</li>
									</ul>','nrelate'),
					"info"=>__('A clean, simple style.','nrelate'),
				),
'simplydk' => array(
					"stylesheet" => "nrelate-flyout-simply-dark.css",
					"name"=>__('Simply Dark','nrelate'),
					"features"=>__('<ul>
										<li>Clean</li>
										<li>Semi-transparent background (Certain browsers only)</li>
										<li>Round corners (CSS3 browsers only)</li>
									</ul>','nrelate'),
					"info"=>__('A dark alternative to our "Simple" style.','nrelate'),
				),
'centered' => array(
					"stylesheet" => "nrelate-flyout-centered.css",
					"name"=>__('Centered','nrelate'),
					"features"=>__('<ul>
										<li>Centered content</li>
										<li>Works well with thumbnails</li>
									</ul>','nrelate'),
					"info"=>__('Centers your content, instead of flush left.
								<ul>
									<strong>Ideas</strong>
										<li>Set width to 100%, and style to "Fade", to create a nice popup.</li>
								</ul>','nrelate'),
				),
'centereddk' => array(
					"stylesheet" => "nrelate-flyout-centereddk.css",
					"name"=>__('Centered Dark','nrelate'),
					"features"=>__('<ul>
										<li>Centered content</li>
										<li>Works well with thumbnails</li>
										<li>Semi-transparent background (Certain browsers only)</li>
									</ul>','nrelate'),
					"info"=>__('Centers your content, instead of flush left.
								<ul>
									<strong>Ideas</strong>
										<li>Set width to 100%, and style to "Fade", to create a nice popup.</li>
								</ul>','nrelate'),
				),
'none' => array(
					"name"=>__('none'),
					"features"=>__('<ul>
										<li>Allows you to create your own css</li>
									</ul>','nrelate'),
					"info"=>__('Selecting this option will disable all nrelate animation styles, allowing you to create your own.<br>
								Use one of the styles above as a starting point.','nrelate'),
				
				)
);

// Currently not used
$nrelate_fade_anim_styles = array(
'nyt' => array(
					"stylesheet" => "nrelate-flyout-nyt.css",
					"name"=>__('NY Times','nrelate'),
					"features"=>__('<ul>
										<li>Easy to read</li>
									</ul>','nrelate'),
					"info"=>__('This style is based upon the original flyout at the NYTimes.com.','nrelate'),
				)
);

?>