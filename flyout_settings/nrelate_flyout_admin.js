// Takes values from input fields, makes appropriate conversions
// Opens a pop up window with preview url with these parameters
function nrelate_flyout_popup_preview(NRELATE_FLYOUT_SETTINGS_URL,wp_root_nr, NRELATE_FLYOUT_PLUGIN_VERSION){
	if (!window.focus)return true;
	var nr_thumbsize, nr_maxageposts, nr_age_num,age_frame, nr_href, nr_imageurl, nr_title, nr_numberflyout, nr_r_title, nr_r_show_post_title, nr_r_max_char_perline, nr_r_show_post_excerpt, nr_r_max_char_post_excerpt, nr_ad, nr_logo, nr_thumb, nr_adval, nr_logoval, nr_thumbval;
	nr_title = "Nrelate_Preview";
	nr_href = 'http://api.nrelate.com/fow_wp/' + NRELATE_FLYOUT_PLUGIN_VERSION + '/nrelate_popup_content.php';
	nr_numberflyout = document.getElementById("flyout_number_of_posts").value;
	nr_r_title = document.getElementById("flyout_title").value;
	nr_r_show_post_title = document.getElementById("flyout_show_post_title").checked;
	nr_r_max_char_perline = document.getElementById("flyout_max_chars_per_line").value;
	nr_r_show_post_excerpt = document.getElementById("flyout_show_post_excerpt").checked;
	nr_r_max_char_post_excerpt = document.getElementById("flyout_max_chars_post_excerpt").value;
	nr_adval = document.getElementById("show_ad").checked;
	nr_num_ads = document.getElementById("flyout_number_of_ads").value;
	nr_ads_placement = document.getElementById("flyout_ad_placement").value;
	nr_logoval = document.getElementById("show_logo").checked;
	nr_thumbval = document.getElementById("flyout_thumbnail").value;
	nr_thumbstyle = document.getElementById('flyout_imagestyle').value;
	nr_textstyle = document.getElementById('flyout_textstyle').value;
	nr_imageurl = document.getElementById("flyout_default_image").value;
	nr_age_num = document.getElementById("flyout_max_age_num").value;
	nr_age_frame = document.getElementById("flyout_max_age_frame").value;
	nr_thumbsize = document.getElementById("flyout_thumbnail_size").value;
	nr_r_title = escape(nr_r_title);
	
	switch (nr_age_frame){
		case 'Hour(s)':
			nr_maxageposts = nr_age_num * 60;
			break;
		case 'Day(s)':
			nr_maxageposts = nr_age_num * 1440;
			break;
		case 'Week(s)':
			nr_maxageposts = nr_age_num * 10080;
			break;
		case 'Month(s)':
			nr_maxageposts = nr_age_num * 44640;
			break;
		case 'Year(s)':
			nr_maxageposts = nr_age_num * 525600;
			break;
		}
		
	// Convert show post title parameter
	switch (nr_r_show_post_title){
	case true:
		nr_r_show_post_title = 1;
		break;
	default:
		nr_r_show_post_title = 0;
	}
	
	// Convert show post excerpt parameter
	switch (nr_r_show_post_excerpt){
	case true:
		nr_r_show_post_excerpt = 1;
		break;
	default:
		nr_r_show_post_excerpt = 0;
	}
		
	
	// Convert ad parameter
	switch (nr_adval){
	case true:
		nr_ad = 1;
		break;
	default:
		nr_ad = 0;
	}
	
	// Convert logo parameter
	switch (nr_logoval){
	case true:
		nr_logo = 1;
		break;
	default:
		nr_logo = 0;
	}
	
	// Convert thumbnail parameter
	switch (nr_thumbval){
	case 'Thumbnails':
		nr_thumb = 1;
		break;
	default:
		nr_thumb = 0;
	}
																														 
	nr_tag = "?NUM="+nr_numberflyout+"&DOMAIN="+wp_root_nr+"&IMAGEURL="+escape(nr_imageurl)+"&TITLE="+escape(nr_r_title)+"&SHOWPOSTTITLE="+nr_r_show_post_title+"&MAXCHAR="+nr_r_max_char_perline+"&SHOWEXCERPT="+nr_r_show_post_excerpt+"&MAXCHAREXCERPT="+nr_r_max_char_post_excerpt+"&AD="+nr_ad+"&LOGO="+nr_logo+"&THUMB="+nr_thumb+"&MAXAGE="+nr_maxageposts+"&THUMBSIZE="+nr_thumbsize+"&FLYOUT_VERSION="+NRELATE_FLYOUT_PLUGIN_VERSION;
	nr_tag += '&NUMADS=' + nr_num_ads + '&ADSPLACE=' + nr_ads_placement + '&THUMBSTYLE=' + nr_thumbstyle + '&TEXTSTYLE=' + nr_textstyle;
	
	if (jQuery('#ad_animation').is(':checked')) nr_tag += '&AD_ANIMATION=1';
	
	nr_link = nr_href + nr_tag;
	//window.open(nr_link,nr_title,'width=600,height=400,scrollbars=yes');
	//return false;
	return nr_link;
}

function nr_iframe_reload(){
	document.getElementById('TB_iframeContent').src = nrelate_flyout_popup_preview(nr_fo_plugin_settings_url, nr_plugin_domain, nr_fo_plugin_version)+'&TB_iframe=1&width=822&height=372';
	jQuery('#TB_iframeContent').unbind('load');
}

jQuery(document).ready(function($){
	$('.nrelate-thumbnail-style-prev').click(function(){
		$('#flyout_imagestyle').val( $(this).parents('div:first').find('input:first').val() );
	});

	$('.nrelate-text-style-prev').click(function(){
		$('#flyout_textstyle').val( $(this).parents('div:first').find('input:first').val() );
	});
	
	$('.nrelate_preview_button').click(function(event){
		event.preventDefault();
		
		if ($('#flyout_thumbnail').val() == 'Thumbnails') {
			if ($('#flyout_imagestyle').val() == 'none') return;
		} else {
			if ($('#flyout_textstyle').val() == 'none') return;
		}
		
		_url = nrelate_flyout_popup_preview(nr_fo_plugin_settings_url, nr_plugin_domain, nr_fo_plugin_version)+'&TB_iframe=1&width=822&height=372';
		tb_show('nRelate - preview', _url, false);
		$('#TB_iframeContent').load(function(){
			nr_iframe_reload();
		});
	});
	
	$('#show_ad').click(function(){
		$('#ads_warning').slideDown('fast');
	});
	
});