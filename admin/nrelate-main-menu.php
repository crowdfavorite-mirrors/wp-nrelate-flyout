<?php
/**
 * nrelate Main Menu
 *
 * @package nrelate
 * @subpackage Functions
 */
 
function nrelate_main_section() { ?>

<div id="nrelate-dashboard" class="wrap nrelate-page" style="margin: 10px 15px 0 5px">

<?php	

do_action ( 'nrelate_page_before_h2');
echo '<img src="'. NRELATE_ADMIN_IMAGES .'/nrelate-logo.png" alt="nrelate Logo" style="float:left; margin: 0 20px 0 0;" />';
echo '<h2 class="nrelate-title">';
_e('nrelate Dashboard','nrelate' );
?><a class="thickbox button add-new-h2" title="nrelate Terms Of Service" href="admin-ajax.php?action=get_nrelate_tos&amp;height=385&amp;width=640">Terms Of Service</a><?php
echo '</h2>';

?>

<div class="metabox-holder has-right-sidebar" id="poststuff">

	<div class="inner-sidebar" id="side-info-column">

		<div class="meta-box-sortables ui-sortable" id="side-sortables">
		
				<!-- Plugins Installed -->
				<div id="nr_installed_plugins" class="postbox sidebar-list">
					<h3 class="hndle"><span><?php _e('Configure Installed Plugins:')?></span></h3>
					<div class="inside">
						<ul>
							<!-- Hook to let us know which plugins are active -->
							<?php do_action('nrelate_active_plugin_notice');?>
						</ul>
					</div><!-- .inside -->
				</div><!-- #nr_installed_plugins -->

				<!-- Support Forums -->
				<div id="nr_support_forums" class="postbox sidebar-list">
					<h3 class="hndle"><span><?php _e('Need Support?')?></span></h3>
					<div class="inside">
						<?php printf('If you are having issues with any nRelate plugin, post a question on our %sSupport Forum%s.','<a href="http://nrelate.com/forum">','</a>');?>
					</div><!-- .inside -->
				</div><!-- #nr_installed_plugins -->

				<!-- RSS Feeds -->
				<div id="nr_rss_feeds" class="postbox">
					<h3 class="hndle"><span><?php _e('From Our Blog:')?></span></h3>
					<div class="inside">
						<?php // Get RSS Feed(s)
						include_once(ABSPATH . WPINC . '/feed.php');

						// Get a SimplePie feed object from the specified feed source.
						$rss = fetch_feed('http://nrelate.com/theblog/feed');
						if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly 
						// Figure out how many total items there are, but limit it to 5. 
						$maxitems = $rss->get_item_quantity(5); 

						// Build an array of all the items, starting with element 0 (first element).
						$rss_items = $rss->get_items(0, $maxitems); 
						endif;
						?>

					<ul>
						<?php if ($maxitems == 0) printf('%s Sorry, there seems to be an issue with our blog. We\'re hard at working getting it fixed. %s','<p>','</p>');
								else
								// Loop through each feed item and display each item as a hyperlink.
								foreach ( $rss_items as $item ) : ?>
									<li>
										<a href='<?php echo $item->get_permalink(); ?>'title='<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>'>
										<?php echo $item->get_title(); ?></a>
									</li>
						<?php endforeach; ?>
					</ul>
					</div><!-- .inside -->
				</div><!-- #nr_rss_feeds -->			
		
				<!-- About nrelate -->
				<div id="nr_about" class="postbox">
					<h3 class="hndle"><span><?php _e('About nrelate:')?></span></h3>
					<div class="inside">
						<ul>
							<li class="nrelate"><a href="http://www.nrelate.com"><?php _e('Visit us')?></a></li>
							<li class="forums"><a href="http://www.nrelate.com/forum"><?php _e('Ask us')?></a></li>
							<li class="twitter"><a href="http://www.twitter.com/nrelate"><?php _e('Follow us')?></a></li>
							<li class="facebook">
								<iframe src="http://www.facebook.com/plugins/like.php?app_id=124076657681566&amp;href=http%3A%2F%2Fnrelate.com&amp;send=false&amp;layout=button_count&amp;width=110&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:110px; height:21px;" allowTransparency="true"></iframe>
							</li>
						</ul>
					</div><!-- .inside -->
				</div><!-- #nr_about -->
				
				<!-- Re-Index -->
				<div id="nr_reindex" class="postbox">
					<h3 class="hndle"><span><?php _e('Re-Index Your Site:')?></span></h3>
					<div class="inside">
						<p><?php _e('Use the button below to have nrelate reindex your website.')?></p>
						<?php if(isset($_POST['reindex'])) { nrelate_reindex(); } ?>
						<form action="" method="post">
							<input id="nrelate_reindex_button" type="submit" class="reindex <?php echo NRELATE_API_ONLINE ? '' : 'disabled'; ?>" name="reindex" value="Re-Index Website" <?php echo NRELATE_API_ONLINE ? '' : 'disabled="disabled" title="Sorry nrelate\'s api server is not available. Please try again later"'; ?>/>
							<script type="text/javascript">
							//<![CDATA[
							jQuery(function($){
								if( $('#indexresponse').html().indexOf("nRelate plugin is ready to go") == -1) {
									$("#nrelate_reindex_button").addClass('disabled').attr('disabled', 'disabled');
								}
							});
							//]]>
							</script>
						</form>
						<p><strong><?php _e('IMPORTANT: All nrelate content will be temporarily removed from your website while we reindex.<br/><center>Only use when neccessary</center>','nrelate')?></strong></p>
					</div><!-- .inside -->
				</div><!-- #nr_reindex -->

			</div><!-- #side-sortables -->
		</div><!-- #side-info-column -->

		
		<div id="post-body">
			<div id="post-body-content">

				<!-- Message -->
				<div id="nr-messages" class="postbox">
					<h3 class="hndle"><span><?php _e('Messages:')?></span></h3>
					<ul class="inside">

          <!-- Hook for PRIORITY admin messages from all nrelate plugins -->
          <?php do_action('nrelate_priority_admin_messages');?>
					
					<!-- Show index status -->
					<?php nrelate_index_check();?>	
					
					<!-- Show service status -->
					<?php nr_service_status();?>		
					
					<!-- Hook for admin messages from all nrelate plugins -->
					<?php do_action('nrelate_admin_messages');?>
					<li>
					<div class="info" id="extra_message">
						<?php 
						// Call to nrelate server (sends home url)
						// Nrelate server returns any message to be displayed in the nrelate dashboard
						$body=array( 'DOMAIN'=>NRELATE_BLOG_ROOT );

						$url = 'http://api.nrelate.com/common_wp/'.NRELATE_LATEST_ADMIN_VERSION.'/wordpressnotify_adminmessage.php';
						
						$result = wp_remote_post($url, array(
							'method'=>'POST',
							'body'=>$body,
							'timeout'=>2
					    	)
						);
						
						echo !is_wp_error($result) ? $result['body'] : null ;?>
					</div><!-- #extra_message -->
					</li>
					</ul><!-- .inside -->
					
				</div><!-- #nr-messages -->
				
				<?php nrelate_admin_do_page(); 	// Get Admin settings from nrelate-admin-settings.php ?>
				
			</div><!-- #post-body-content -->
				
		</div><!-- #post-body -->
		<br class="clear">


		</div><!-- #side-sortables -->

	</div><!-- #side-info-column -->


<?php }
?>