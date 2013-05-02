<?php
/**
 * nrelate Flyout Box Settings
 *
 * @package nrelate
 * @subpackage Functions
 */

 function options_init_nr_fo_anim_styles(){

	register_setting('nrelate_flyout_anim_options_styles', 'nrelate_flyout_anim_options_styles');
	
	if (isset($_GET['mode']) && in_array($_GET['mode'], array('Fly', 'Fade'))) {
		$options['flyout_animation'] = $type = $_GET['mode'];
	} else {
		$options = get_option('nrelate_flyout_options');
		$type = $options['flyout_animation'];
	}

	
	// Main Section
	add_settings_section('style_section', __('Style Settings for&nbsp;','nrelate') . $type . __('&nbsp;animation','nrelate'), 'section_text_nr_fo_anim_style', __FILE__);
	add_settings_field('flyout_anim_style', '', 'setting_flyout_anim_style_type',__FILE__,'style_section');
	add_settings_field('flyout_anim_save_preview','', 'flyout_anim_save_preview', __FILE__, 'main_section');
}
add_action('admin_init', 'options_init_nr_fo_anim_styles' );


/****************************************************************
 ************************** Admin Sections ********************** 
*****************************************************************/


///////////////////////////
//   Main Settings
//////////////////////////
 
// Section HTML, displayed before the first option
function section_text_nr_fo_anim_style() {
	if (isset($_GET['mode']) && in_array($_GET['mode'], array('Slideout', 'Fade'))) {
		$options['flyout_animation'] = $type = $_GET['mode'];
	} else {
		$options = get_option('nrelate_flyout_options');
		$type = $options['flyout_animation'];
	}

	_e('<p class="section-desc">Choose a style from our gallery, or choose NONE to disable all nrelate CSS.</p>','nrelate');
}


// RADIO - Name: nrelate_flyout_anim_options_styles[flyout_anim_fly_size]
function setting_flyout_anim_style_type(){
	if (isset($_GET['mode']) && in_array($_GET['mode'], array('Slideout', 'Fade'))) {
		$options['flyout_animation'] = $type = $_GET['mode'];
	} else {
		$options = get_option('nrelate_flyout_options');
		$type = $options['flyout_animation'];
	}
	
	//Common header for both styles
	echo '<div id="style-gallery" class="animation-gallery">
			<h4 class="style-select"></h4>
			<h4 class="style-image">Style Type</h4>
			<div class="style-features-info">
				<h4 class="style-features">Features</h4>
				<h4 class="style-info">Information</h4>
			</div>
			';
	
	if($options['flyout_animation']=="Slideout"){
		flyout_anim_slideout_styles();
	} else {
		//flyout_anim_fade_styles(); temporarily removed.
		flyout_anim_slideout_styles();		
	}
}

function flyout_anim_slideout_styles() {
	global $nrelate_slideout_anim_styles;
	$options = get_option('nrelate_flyout_anim_options_styles');

	foreach ( $nrelate_slideout_anim_styles as $style_code => $nrelate_slideout_anim_style ) {
		$style_name = $nrelate_slideout_anim_style['name'];	?>
	
		<div class="nrelate-style-images nrelate-style-prev">
<?php		$checked = ($options['flyout_anim_slideout_style']==$style_code) ? 'checked="checked"' : ''; ?>
			<label class="style-select" for="nrelate_style_<?php echo $style_code; ?>">
				<input id="nrelate_style_<?php echo $style_code; ?>" <?php echo $checked; ?> type="radio" name="nrelate_flyout_anim_options_styles[flyout_anim_slideout_style]" value="<?php echo $style_code; ?>" /><br />
				<?php echo $style_name; ?><br />
			</label>
				<img class="style-image" src="<?php echo NRELATE_FLYOUT_IMAGE_DIR; ?>/anim_style_<?php echo $style_code; ?>.png"  alt="<?php echo $style_code; ?>" style="width:380px; height:auto; float:left;" />
			<div id="info-style-<?php echo $style_code;?>" class="style-features-info">
				<div class="style-features"><?php echo $nrelate_slideout_anim_style['features']; ?></div>
				<div class="style-info">
					<p><?php echo $nrelate_slideout_anim_style['info']; ?></p>
					<?php if ($style_code!='none') { ?>
						<a href="<?php echo NRELATE_CSS_URL . 'nrelate-flyout-' . $style_code .'.css';?>?keepThis=true&TB_iframe=true&height=450&width=500" title="CSS for <strong><?php echo $style_name; ?></strong> Style" class="thickbox">View Stylesheet</a>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
	} ?>
		<div style="clear:both;"></div>
    <input type="hidden" name="nrelate_flyout_anim_options_styles[flyout_anim_fade_style]" value="<?php echo htmlentities(isset($options['flyout_anim_fade_style']) ? $options['flyout_anim_fade_style'] : ''); ?>" />
  <?php
  echo '</div>';
}

function flyout_anim_fade_styles() {
	global $nrelate_fade_anim_styles;
	$options = get_option('nrelate_flyout_anim_options_styles');

	foreach ( $nrelate_fade_anim_styles as $style_code => $nrelate_fade_anim_style ) {
		$style_name = $nrelate_fade_anim_style['name'];	?>
	
		<div class="nrelate-style-images nrelate-style-prev">
<?php		$checked = ($options['flyout_anim_fade_style']==$style_code) ? 'checked="checked"' : ''; ?>
			<label class="style-select" for="nrelate_style_<?php echo $style_code; ?>">
				<input id="nrelate_style_<?php echo $style_code; ?>" <?php echo $checked; ?> type="radio" name="nrelate_flyout_anim_options_styles[flyout_anim_fade_style]" value="<?php echo $style_code; ?>" /><br />
				<?php echo $style_name; ?><br />
			</label>
      <?php if ($style_code!='none') { ?>
				<img class="style-image" src="<?php echo NRELATE_FLYOUT_IMAGE_DIR; ?>/anim_style_<?php echo $style_code; ?>.png"  alt="<?php echo $style_code; ?>" />
      <?php } ?>
			<div id="info-style-<?php echo $style_code;?>" class="style-features-info">
				<div class="style-features"><?php echo $nrelate_fade_anim_style['features']; ?></div>
				<div class="style-info"><p><?php echo $nrelate_fade_anim_style['info']; ?></p></div>
			</div>				
		</div>
<?php
	} ?>
		<div style="clear:both;"></div>
    <input type="hidden" name="nrelate_flyout_anim_options_styles[flyout_anim_slideout_style]" value="<?php echo htmlentities(isset($options['flyout_anim_slideout_style']) ? $options['flyout_anim_slideout_style'] : ''); ?>" />
  <?php
  echo '</div>';
}

/****************************************************************
 ******************** Build the Layout Settings Page ********************** 
*****************************************************************/

function nrelate_flyout_anim_styles_do_page() { ?>

	<?php nrelate_flyout_settings_header(); ?>
    <script type="text/javascript">
		//<![CDATA[
		var nr_fo_anim_plugin_settings_url = '<?php echo NRELATE_FLYOUT_SETTINGS_URL; ?>';
		var nr_plugin_domain = '<?php echo NRELATE_BLOG_ROOT; ?>';
		var nr_fo_anim_plugin_version = '<?php echo NRELATE_FLYOUT_PLUGIN_VERSION ?>';
		//]]>
    </script>
		<form name="settings" action="options.php" method="post" enctype="multipart/form-action">
    <?php
		$options = get_option('nrelate_flyout_anim_options');
		$style_options = get_option('nrelate_flyout_anim_options_styles');
		if (isset($_GET['mode']) && in_array($_GET['mode'], array('Fly', 'Fade'))) {
			$options['flyout_anim_fly'] = $_GET['mode'];
		}
    ?>
    <div class="nrelate-hidden">
      <input type="hidden" id="flyout_anim_number_of_posts" value="<?php echo isset($options['flyout_anim_number_of_posts']) ? $options['flyout_anim_number_of_posts'] : ''; ?>" />
      <input type="hidden" id="flyout_anim_title" value="<?php echo isset($options['flyout_anim_title']) ? $options['flyout_anim_title'] : ''; ?>" />
      <input type="checkbox" id="flyout_anim_show_post_title" <?php echo empty($options['flyout_anim_show_post_title']) ? '' : 'checked="checked"'; ?> value="on" />
      <input type="hidden" id="flyout_anim_max_chars_per_line" value="<?php echo isset($options['flyout_anim_max_chars_per_line']) ? $options['flyout_anim_max_chars_per_line'] : ''; ?>" />
      <input type="checkbox" id="flyout_anim_show_post_excerpt" <?php echo empty($options['flyout_anim_show_post_excerpt']) ? '' : 'checked="checked"'; ?> value="on" />
      <input type="hidden" id="flyout_anim_max_chars_post_excerpt" value="<?php echo isset($options['flyout_anim_max_chars_post_excerpt']) ? $options['flyout_anim_max_chars_post_excerpt'] : ''; ?>" />
      <input type="checkbox" id="show_ad" <?php echo empty($options['flyout_anim_display_ad']) ? '' : 'checked="checked"'; ?> value="on" />
      <input type="hidden" id="flyout_anim_number_of_ads" value="<?php echo isset($options['flyout_anim_number_of_ads']) ? $options['flyout_anim_number_of_ads'] : ''; ?>" />
      <input type="hidden" id="flyout_anim_ad_placement" value="<?php echo isset($options['flyout_anim_ad_placement']) ? $options['flyout_anim_ad_placement'] : ''; ?>" />
      <input type="checkbox" id="show_logo" <?php echo empty($options['flyout_anim_display_logo']) ? '' : 'checked="checked"'; ?> value="on" />
      <input type="hidden" id="flyout_anim_fly" value="<?php echo isset($options['flyout_anim_fly']) ? $options['flyout_anim_fly'] : ''; ?>" />
      <input type="hidden" id="flyout_anim_fade_style" value="<?php echo empty($style_options['flyout_anim_fade_style']) ? 'nyt' : $style_options['flyout_anim_fade_style']; ?>" />
      <input type="hidden" id="flyout_anim_slideout_style" value="<?php echo empty($style_options['flyout_anim_slideout_style']) ? 'nyt' : $style_options['flyout_anim_slideout_style']; ?>" />
      <input type="hidden" id="flyout_anim_default_image" value="<?php echo isset($options['flyout_anim_default_image']) ? $options['flyout_anim_default_image'] : ''; ?>" />
      <input type="hidden" id="flyout_anim_max_age_num" value="<?php echo isset($options['flyout_anim_max_age_num']) ? $options['flyout_anim_max_age_num'] : ''; ?>" />
      <input type="hidden" id="flyout_anim_max_age_frame" value="<?php echo isset($options['flyout_anim_max_age_frame']) ? $options['flyout_anim_max_age_frame'] : ''; ?>" />
      <input type="checkbox" class="nrelate-thumb-size" value="<?php echo isset($options['flyout_anim_fly_size']) ? $options['flyout_anim_fly_size'] : ''; ?>" checked="checked" />
      <input type="checkbox" id="ad_animation" value="on" <?php echo empty($options['flyout_anim_ad_animation']) ? '' : ' checked="checked" '; ?> />
    </div>
		<?php settings_fields('nrelate_flyout_anim_options_styles'); ?>
		<?php do_settings_sections(__FILE__);?>
    <!--
		<br>
    <button type="button" class="nrelate_fo_preview_button button-primary" onClick="return nrelate_flyout_anim_popup_preview('<?php echo NRELATE_FLYOUT_SETTINGS_URL; ?>','<?php echo NRELATE_BLOG_ROOT; ?>','<?php echo NRELATE_FLYOUT_PLUGIN_VERSION; ?>');"> <?php _e('Preview','nrelate'); ?> </button>
    -->
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes','nrelate'); ?>" />
		</p>
		</form>

	</div>
<?php }
?>