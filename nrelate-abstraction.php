<?php
/**
 * Temporary file to get past 0.50.0 upgrade
 *
 ******* Backend code only
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
 

 if ( !function_exists( 'nrelate_tooltip' ) ) :
 /* = Located in common
-----------------------------------------------*/
		/**
		 * Common function to load Tooltips
		 * Since v0.50.0
		 */
		function nrelate_tooltip($tip) {
			global $nrelate_tooltips;
			$output = '';
			if ( array_key_exists( $tip, $nrelate_tooltips )) {
				$output = '<span class="nrelate-tooltip"><a class="nrelate-help" title="' . $nrelate_tooltips[$tip] . '">tip</a></span>';
			}
			return $output;
		}

		/**
		 * Common function to load qTip Tooltips
		 * Since v0.50.0
		 */
		function nrelate_qtip() { ?>

		<script type="text/javascript">
		// Create the tooltips only on document load
		jQuery(document).ready(function() // wordpress 
		   {
			jQuery('a[title]').qtip({
				content: 
				{
					text: false
				},
				style:
				{
					classes: 'ui-tooltip-nrelate'
				},
				position:
				{
					viewport: jQuery(window), // Keep the tooltip on-screen at all times
					effect: false // Disable positioning animation
				},
				show:
				{
					event: 'click',
					solo: true // Only show one tooltip at a time
				},
				hide: 'unfocus'
			})
		});
		</script>

		<?php
		}
		add_action('nrelate_admin_page_footer','nrelate_qtip');
		
		
		
		/**
		 * Plugin page header
		 *
		 * Displays name and description for plugin
		 * Does some standard form checking
		 */
		function nrelate_plugin_page_header($name, $description) { ?>
		<script type="text/javascript">
			//<![CDATA[
			/*
			* User warning if switching tabs without saving
			*/
			jQuery(document).ready(function($) {
				$('form')
					// Store initial status
					.each(function(){
						var $this = $(this);
						$this.data( 'init_status', $this.serialize() );
						$this.data( 'is_dirty', false );
					})
					// Disable dirty check when submitting
					.submit(function(){
						$(window).unbind('beforeunload');
					});
				
				// Keep track of changes on form's inputs
				$(':input').live('change keyup', function(){
					$form = $(this).closest('form');
					if ( $form.serialize() != $form.data('init_status') ) {
						$form.data( 'is_dirty', true );
					} else {
						$form.data( 'is_dirty', false );
					}
				});
				
				function is_page_dirty() {
					var is_dirty = false;
					// Iterate through forms checking if there's any dirty
					$('form').each(function(){
						if ( $(this).data( 'is_dirty' ) ) {
							is_dirty = true;
							// If found one dirty, stop iterating returning false
							return false;
						}
					});
					return is_dirty;
				}
				
				$(window).bind('beforeunload', function(){
					if ( is_page_dirty() ) {
						return "You haven't saved your changes. Do you really want to leave?";
					}
				});
			});
			//]]>
		</script>
			<div class="wrap nrelate-settings nrelate-page" style="margin: 10px 0 0 0;">
			<?php do_action ( 'nrelate_page_before_h2');?>
			<img src="<?php echo NRELATE_ADMIN_IMAGES ?>/nrelate-logo.png" alt="nrelate Logo" style="float:left; margin: 0 20px 0 0" />
			<h2 class="nrelate-title">
				<?php echo $name //show plugin name ?>
				<?php nrelate_tos() //display tos ?>
				<p><?php echo $description //show plugin description ?></p>
			</h2>
		<?php
		}
		
		
		/**
		 * Settings Page: MAIN Settings description
		 */
		function nrelate_text_main($name) {
			printf(__('%s Main controls for ' . $name . ' plugin. %s','nrelate'), '<p class="section-desc">', '</p>');
		}
		
		
	
		/**
		 * TEMPORARY UPGRADE NOTICE FOR 0.50.0 ONLY
		 *
		 * This notice (and file) will only load if an older version of nrelate loads first
		 */
		function nrelate_product_check_announce_50() {
			global $current_user;
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
			// Only show message if user can update plugins
			if (!current_user_can('update_plugins') ) return;
			
			$upgrade_url = "plugin-install.php?tab=search&type=term&s=nrelate&plugin-search-input=Search+Plugins";
					

				
				// Show message
				$msg = sprintf(__('Please make sure you are running the latest versions of all nrelate plugins. <a href="%s">Click here to check.</a>', 'nrelate'), $upgrade_url );
				echo '<div class="updated"><p>' . $msg . '</p></div>';
		}
		add_action('admin_notices','nrelate_product_check_announce_50');
		



endif;

?>