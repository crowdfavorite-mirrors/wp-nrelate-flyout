<?php
/**
 * nrelate Admin Settings
 *
 * Admin settings for all nrelate plugins
 *
 * @package nrelate
 * @subpackage Functions
 */

// Register our settings. Add the settings section, and settings fields

function options_admin_init_nr(){
	
	register_setting('nrelate_admin_options', 'nrelate_admin_options', 'admin_options_validate' );
	
	// Ad Section
	add_settings_section('ad_section', __('Advertising','nrelate'), 'section_text_nr_ad', __FILE__);
	add_settings_field('admin_validate_ad', __('Partner ID','nrelate').'<br>(<a href="' . NRELATE_WEBSITE_AD_SIGNUP .'" target="_blank">'.__('Sign up to earn money.','nrelate') . '</a>)', 'setting_admin_validate_ad', __FILE__, 'ad_section');
	add_settings_field('admin_paypal_email', __('Paypal Email (so we can pay you)','nrelate'), 'setting_admin_paypal_email', __FILE__, 'ad_section');

	// Communication Section
	add_settings_section('comm_section', __('Communication','nrelate'), 'section_text_nr_comm', __FILE__);
	add_settings_field('admin_email_address', '<span id="admin_email_address">'.__('Send email address','nrelate').'</span>', 'setting_admin_email', __FILE__, 'comm_section');	
	
	// Custom Fields
	add_settings_section('customfield_section', __('Custom Field for Images','nrelate'), 'section_text_nr_customfield', __FILE__);
	add_settings_field('admin_custom_field', __('Enter your <b>Custom Field</b> for images:','nrelate'), 'setting_admin_custom_field',__FILE__,'customfield_section');
  add_settings_field('admin_custom_field_path', __('If your custom field does not include the path to your images, please enter the path here:','nrelate'), 'setting_admin_custom_field_path',__FILE__,'customfield_section');
	
	// Exclude categories
	add_settings_section('excludecat_section', __('Exclude Categories','nrelate')  . nrelate_tooltip('_exclude_categories'), 'section_text_nr_excludecat', __FILE__);
	add_settings_field('admin_exclude_categories', __('Categories:','nrelate'), 'setting_admin_exclude_categories',__FILE__,'excludecat_section');
	
	// Include custom post types
	add_settings_section('includecpt_section', __('Include Custom Post Types','nrelate'), 'section_text_nr_includecpt', __FILE__);
	add_settings_field('admin_include_cpt', __('Post Types:','nrelate'), 'setting_admin_include_cpt',__FILE__,'includecpt_section');

}
add_action('admin_init', 'options_admin_init_nr' );


/****************************************************************
 ************************** Admin Sections ********************** 
*****************************************************************/

///////////////////////////
//   Advertising Settings
//////////////////////////

// Section HTML: Advertising
function section_text_nr_ad() {
		_e('<p>Become a part of the nrelate advertising network and earn some extra money on your blog. Click on the ADVERTISING tab of a participating nRelate product settings page.</p>','nrelate');
}


// TEXTBOX - Validate ads
function setting_admin_validate_ad() {
	$options = get_option('nrelate_admin_options');
	echo '<div id="getnrcode"></div>';
	echo '<input id="admin_validate_ad" name="nrelate_admin_options[admin_validate_ad]" size="10" type="hidden" value="" />';
}

// TEXTBOX - paypal email address field
function setting_admin_paypal_email(){
	$options = get_option('nrelate_admin_options');
	echo '<input id="admin_paypal_email" name="nrelate_admin_options[admin_paypal_email]" size="30" type="text" value="'.$options['admin_paypal_email'].'" />';
}


///////////////////////////
//   Communciation Settings
//////////////////////////

// Section HTML: Communication
function section_text_nr_comm() {
		_e('<p>nrelate may need to communicate with you when we release new features or have a problem accessing your website.</br>  Check the box, below, to send nrelate the admin email address (under "General Settings").  We promise not to overwhelm you with email.<p/>','nrelate');
}

// CHECKBOX - Admin email address
function setting_admin_email() {
	$options = get_option('nrelate_admin_options');
	$checked = (isset($options['admin_email_address']) && $options['admin_email_address']=='on') ? ' checked="checked" ' : '';
	echo "<input ".$checked." name='nrelate_admin_options[admin_email_address]' type='checkbox' />";
}

///////////////////////////
//   customfield Settings
//////////////////////////

// Section HTML: customfield
function section_text_nr_customfield() {
		_e('<p>If you use a Custom Field to show images in your posts, you can have nrelate show those images.</p>','nrelate');
}

// TEXTBOX - Name: nrelate_admin_options[admin_custom_field]
function setting_admin_custom_field() {
	$options = get_option('nrelate_admin_options');
	$customfield = $options['admin_custom_field'];
	echo '<div id="imagecustomfield"><input id="admin_custom_field" name="nrelate_admin_options[admin_custom_field]" size="40" type="text" value="'.$customfield.'" /></div>';
}

// TEXTBOX - Name: nrelate_admin_options[admin_custom_field_path]
function setting_admin_custom_field_path() {
  $options = get_option('nrelate_admin_options');
  $customfield_path = $options['admin_custom_field_path'];
  echo '<div id="imagecustomfield_path">
          <input id="admin_custom_field_path" name="nrelate_admin_options[admin_custom_field_path]" size="40" type="text" value="'.$customfield_path.'" />
          <p class="description">i.e. http://www.mysite.com/path-to-images/</p>
          </div>';
}

///////////////////////////
//   Exclude Categories Settings
//////////////////////////

// Section HTML: customfield
function section_text_nr_excludecat() {
	_e('<p id="exclude-cats">Select the categories you want to <b>exclude</b> from ALL nrelate products. If a post is in multiple categories, and one of those categories is excluded, it will not show up.<p/>EXAMPLE: You published a Post in categories "A" and "B", and you only exclude category "B", the post will not show up, even though category "A" is to be included. Since the post is in an excluded category it will not show up.</p>', 'nrelate');
}

// CHECKBOX LIST - Name: nrelate_admin_options[admin_exclude_categories]
function setting_admin_exclude_categories() {
	$options = get_option('nrelate_admin_options');
	
	echo '<div id="nrelate-exclude-cats" class="categorydiv"><ul id="categorychecklist" class="list:category categorychecklist form-no-clear">';
	
	$taxonomy = 'category';
	$args = array('taxonomy' => $taxonomy);
	$tax = get_taxonomy($taxonomy);
	$args['disabled'] = !current_user_can($tax->cap->assign_terms);
	$args['selected_cats'] = (isset($options['admin_exclude_categories']) && is_array($options['admin_exclude_categories'])) ? $options['admin_exclude_categories'] : array();
	$categories = (array) get_terms($taxonomy, array('get' => 'all'));
	$walker = new nrelate_Walker_Category_Checklist();
	echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
	
	echo '</ul></div>';
	
	$javascript = <<< JAVA_SCRIPT
jQuery(document).ready(function(){
	var nrel_excluded_cats_changed = false;
	
	jQuery('#nrelate-exclude-cats :checkbox').change(function(){
		var me= jQuery(this);
		if (!nrel_excluded_cats_changed) {
			if (confirm("Any changes to this section will require a site reindex. Are you sure you want to continue?\u000AIf Yes, press OK, SAVE CHANGES and press the REINDEX button (Reindexing may take a while, please be patient)."))
			{
				nrel_excluded_cats_changed = true;
			} 
			else 
			{
				me.attr('checked', !me.is(':checked'));
			}
		}
		
		if ( nrel_excluded_cats_changed ) {
			me.parent().siblings('.children').find(':checkbox').attr('checked', me.is(':checked'));
			
			if ( me.closest('#nrelate-exclude-cats').find(':checkbox').size() == me.closest('#nrelate-exclude-cats').find(':checkbox:checked').size() ) {
				alert("WARNING: You have marked all your categories for exclusion. Nothing will show up in nrelate. Please uncheck at least one category.");
			}
		}
	});								
});
JAVA_SCRIPT;

	echo "<script type='text/javascript'>{$javascript}</script>";
}

// Walker class to customize Checkbox List input names
class nrelate_Walker_Category_Checklist extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

	function start_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children' style='margin-left:18px;'>\n";
	}

	function end_lvl(&$output, $depth, $args) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function end_el(&$output, $category, $depth, $args) {
		$output .= "</li>\n";
	}
	
	function start_el(&$output, $category, $depth, $args) {
		extract($args);
		if ( empty($taxonomy) )
			$taxonomy = 'category';

		$name = isset($name) ? $name : 'nrelate_admin_options[admin_exclude_categories]';
		$value_field = isset($value_field) ? $value_field : 'term_id';

		$css_classes = !$category->parent ? ' top-level-category' : '';
		$css_classes .= $has_children ? ' parent-category' : '';
		$css_classes .= in_array( $category->$value_field, $selected_cats ) ? ' excluded-category' : '';
		
		$class =  $css_classes ? "class='{$css_classes}'" : '';
		
		// Supports WP v2.9
		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit"><input value="' . $category->$value_field . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->$value_field, $selected_cats ), true, false ) . ' /> ' . esc_html( apply_filters('the_category', $category->name )) . nrelate_tooltip("_{$category->$value_field}") . '</label>';
	
	}
}

///////////////////////////
//   Include Custom Post Types Settings
//////////////////////////

// Section HTML: customfield
function section_text_nr_includecpt() {
	_e('<p id="include-cpt">Select the Post Types you want to <b>include</b> in ALL nrelate products.</p>', 'nrelate');
}

// CHECKBOX LIST - Name: nrelate_admin_options[admin_exclude_categories]
function setting_admin_include_cpt() {
	$options = get_option('nrelate_admin_options');
	
	$selected = isset( $options['admin_include_post_types'] ) ? $options['admin_include_post_types'] : array( 'post' );
	
	$post_types = get_post_types( 
		array(
			'public'=>true, 
			//'publicly_queryable'=>true,
			//'capability_type'=> array('post','page'),
			'show_ui'=>true
		), 'object' 
	);
	
	echo "<div id='nrelate-include-cpts' class='cptdiv'><ul id='posttypeschecklist' class='list:posttypes categorychecklist form-no-clear'>";
	
	foreach ( $post_types as $id => $post_type ) {
		$checked = in_array( $id, $selected ) ? "checked='checked'" : "";
		echo "<li id='post-type-{$id}'><label class='selectit'><input type='checkbox' value='{$id}' name='nrelate_admin_options[admin_include_post_types][]' {$checked} id='in-post-type-{$id}' /> {$post_type->name}</label></li>";
	}
	
	echo '</ul></div>';


$javascript_cpt = <<< JAVA_SCRIPT
jQuery(document).ready(function(){
	var nrel_include_cpts_changed = false;
	
	jQuery('#nrelate-include-cpts :checkbox').change(function(){
		var me= jQuery(this);
		if (!nrel_include_cpts_changed) {
			if (confirm("Any changes to this section will require a site reindex. Are you sure you want to continue?\u000AIf Yes, press OK, SAVE CHANGES and press the REINDEX button (Reindexing may take a while, please be patient)."))
			{
				nrel_include_cpts_changed = true;
			} 
			else 
			{
				me.attr('checked', !me.is(':checked'));
			}
		}
	});								
});
JAVA_SCRIPT;

	echo "<script type='text/javascript'>{$javascript_cpt}</script>";
}

/*
*	Executes when nrelate_admin_options changes so it can ask the user to reindex
*	if one of the changes requires complete site re-indexation
*/
function nrelate_admin_check_options_change($new_value) {
	$old_value = (array) get_option('nrelate_admin_options');
	$reindex = array();
	
  // Fields from dashboard we DON'T want to trigger reindex
	$ignore_fields = array(
    'admin_email_address'
    ,'admin_paypal_email'
    ); 
	
	$fields_to_check = array_merge( array_keys( (array)$old_value ), array_keys( (array)$new_value ) );
	$fields_to_check = array_unique( $fields_to_check );
	
	foreach ( $fields_to_check as $field ) {
		if ( in_array($field, $ignore_fields) ) continue;
		
		if ( isset($new_value[$field]) != isset($old_value[$field]) ) {
			$reindex[] = $field;
			break;
		}
		
		if ( isset($new_value[$field]) && $new_value[$field] !== $old_value[$field] ) {
			$reindex[] = $field;
			break;
		}
	}
	
	if ( $reindex ) {
		update_option("nrelate_reindex_required", $reindex );
	}
	
	return $new_value;
}

add_filter('pre_update_option_nrelate_admin_options', 'nrelate_admin_check_options_change');


/****************************************************************
 ******************** Build the Admin Page ********************** 
*****************************************************************/
function nrelate_admin_do_page() { ?> 

		<div id="nr-admin-settings" class="postbox">
			<h3 class="hndle"><span><?php _e('Common settings for all nrelate products:')?></span></h3>
				<ul class="inside">
					<?php $connectionstatus = update_nrelate_admin_data();
					if($connectionstatus !="Success"){
						echo "<h1 style='color:red;font-size:16px;'>".$connectionstatus."</h1>";
					} ?>
					<form name="settings" action="options.php" method="post" enctype="multipart/form-action">
						<?php settings_fields('nrelate_admin_options'); ?>
						<?php do_settings_sections(__FILE__);?>
						<p class="submit">
							<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes','nrelate'); ?>" <?php echo NRELATE_API_ONLINE ? '' : 'disabled="disabled" title="Sorry nrelate\'s api server is not available. Please try again later"'; ?> />
						</p>
					</form>
				</ul><!-- .inside -->
		</div><!-- #nr-admin-settings -->
		<script type="text/javascript">getnrcode("<?php echo NRELATE_BLOG_ROOT; ?>","<?php echo NRELATE_LATEST_ADMIN_VERSION; ?>","<?php echo get_option('nrelate_key');?>");</script>
<?php
	
	update_nrelate_admin_data();
}

// Loads all of the nrelate_admin_options from wp database
// Makes necessary conversion for some parameters.
// Sends nrelate_admin_options entries, rss feed mode, and wordpress home url to the nrelate server
// Returns Success if connection status is "200". Returns error if not "200"
function update_nrelate_admin_data(){
	
	// Get nrelate_admin options from wordpress database
	$option = get_option('nrelate_admin_options');
	$send_email = isset($option['admin_email_address']) ? $option['admin_email_address'] : null;
	$custom_field = $option['admin_custom_field'];
	$paypal_email = $option['admin_paypal_email'];
	
	if($send_email){
		$send_email = 1;
		$user_email = trim(get_option('admin_email'));
	}else{
		$send_email = 0;
		$user_email = null;
	}
	
	// Get Rssmode from rss_use_excerpt option
	$excerptset = get_option('rss_use_excerpt');
	$rss_mode = "FULL"; 					
	if ($excerptset != '0') { // are RSS feeds set to excerpt
		update_option('nrelate_admin_msg', 'yes');
		$rss_mode = "SUMMARY";
	}
	
	$rssurl = get_bloginfo('rss2_url');
	
	// Get the wordpress root url and the wordpress rss url.
	$wp_root_nr=get_bloginfo( 'url' );
	$wp_root_nr = str_replace(array('http://','https://'), '', $wp_root_nr);
	$rssurl = get_bloginfo('rss2_url');
	// Write the parameters to be sent
	$body=array(
		'DOMAIN'=>$wp_root_nr,
		'EMAIL'=>$user_email,
		'RSSMODE'=>$rss_mode,
		'RSSURL'=>$rssurl,
		'KEY'=>get_option('nrelate_key'),
		'CUSTOM'=>$custom_field,
		'EMAILOPT'=>$send_email,
		'PPEMAIL'=>$paypal_email
	);
	$url = 'http://api.nrelate.com/common_wp/'.NRELATE_LATEST_ADMIN_VERSION.'/processWPadmin.php';
	
	$result = wp_remote_post($url, array(
		'method'=>'POST',
		'body'=>$body,
		'blocking'=>false,
		'timeout'=>15
    	)
	);
}


// Validate user data for some/all of our input fields
function admin_options_validate($input) {
	
	// Like escape all text fields
	$input['admin_validate_ad'] = like_escape($input['admin_validate_ad']);
	// Add slashes to all text fields
	$input['admin_validate_ad'] = esc_sql($input['admin_validate_ad']);

	/**
	 * Make sure that unchecked checkboxes are stored as empty strings
	 *
	 * nrelate_admin_options doesn't have a "defaults" global array
	 * so let's keep an array of checkbox settings here, the only place
	 * it's required by now
	 */
	$empty_settings_array = array( 
		'admin_email_address' => ''
	);
	
	$input = wp_parse_args( $input, $empty_settings_array );
	
	
	return $input; // return validated input
}
?>