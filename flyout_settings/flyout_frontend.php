<?php
function nrelate_flyout_makejs(){
	$nrelate_flyout_options = get_option('nrelate_flyout_options');
	$offset = $nrelate_flyout_options['flyout_offset'] == "" ? 2 : $nrelate_flyout_options['flyout_offset'];
	$offset_element = $nrelate_flyout_options['flyout_offset_element'];
	$animation = $nrelate_flyout_options['flyout_animation'] == "Fade" ? "Fade" : "Slideout";
	$position = $nrelate_flyout_options['flyout_loc'] == "Right" ? "right" : "left";
	$anim_width = isset($nrelate_flyout_options['flyout_anim_width']) ? $nrelate_flyout_options['flyout_anim_width']: 360;
	$anim_width_type = isset($nrelate_flyout_options['flyout_anim_width_type']) ? $nrelate_flyout_options['flyout_anim_width_type']: "px";
	if($anim_width_type=="px"){
		$anim_hide_width=-($anim_width+40);
	}else{
		$anim_hide_width=-($anim_width+4);
	}
	$nr_domain=urldecode(NRELATE_BLOG_ROOT);
/*
 * function getScrollY()
 *
 * Major credit for this function goes to:
 * @author: Jason Pelker, Grzegorz Krzyminski
 * @author uri: http://item-9.com/
 * @link: http://wordpress.org/extend/plugins/upprev-nytimes-style-next-post-jquery-animated-fly-in-button/
 */

$flyout_js_str= <<<EOD
	function getScrollY() {
		    scrOfY = 0;
		    if( typeof( window.pageYOffset ) == "number" ) {
		        scrOfY = window.pageYOffset;
		    } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
		        scrOfY = document.body.scrollTop;
		    } else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
		        scrOfY = document.documentElement.scrollTop;
		    }
		    return scrOfY;
	}
	
	function nr_fo_get_closed_cookie(){ 
		var NameOfCookie="nr_fo_closed";
		if (document.cookie.length > 0) { 
			begin = document.cookie.indexOf(NameOfCookie+"="); 
			if (begin != -1) { 
				begin += NameOfCookie.length+1; 
				end = document.cookie.indexOf(";", begin);
				if (end == -1) 
					end = document.cookie.length;
				return unescape(document.cookie.substring(begin, end)); 
			}
		}
		return "false"; 
	}
	
	function nr_fo_set_closed_cookie(value,domain) { 
		var NameOfCookie="nr_fo_closed";
		var ExpireDate = new Date ();
		ExpireDate.setTime(ExpireDate.getTime() + (7*24*60*60*1000));
		document.cookie = NameOfCookie + "=" + value + "; expires=" + ExpireDate.toGMTString()+"; path=/" + "; domain="+domain ;
	}
	
EOD;
		if($offset==4){
			if(strlen($offset_element)>0)
				$offset=4;
			else
				$offset=2;
		}
		
/*
 * Much of the logic for the following code was inspired by:
 * @author: Jason Pelker, Grzegorz Krzyminski
 * @author uri: http://item-9.com/
 * @link: http://wordpress.org/extend/plugins/upprev-nytimes-style-next-post-jquery-animated-fly-in-button/
 */
$flyout_js_str.= <<<EOD
		value=nr_fo_get_closed_cookie();
		if(value=="false")
			nr_fo_closed=false;
		else
	  		nr_fo_closed=true;
		
		var nr_fo_hidden = true;
		
EOD;
$flyout_js_str.= 'jQuery(function($){$(window).scroll(function() {var lastScreen;';
				// Middle of Article
				if ($offset==1) {
			 		$flyout_js_str.='lastScreen = getScrollY() + $(window).height() < ($("#nr_fo_bot_of_post").offset().top) - ($("#nr_fo_top_of_post").offset().top) ? false : true;';
				} else if ($offset==2){
					$flyout_js_str.='lastScreen = getScrollY() + $(window).height() < ($("#nr_fo_bot_of_post").offset().top) ? false : true;';
				} else if ($offset==3) {
					// BOTTOM OF PAGE
				    $flyout_js_str.='lastScreen = getScrollY() + $(window).height() < $(document).height() ? false : true;';
				} else if ($offset==4){
$flyout_js_str.=<<<EOD
				        if ($("$offset_element").length > 0)
				            lastScreen = getScrollY() + $(window).height() < $("$offset_element").offset().top ? false : true;
				        else
				        	lastScreen = getScrollY() + $(window).height() < $(document).height() ? false : true;
EOD;
				}
				// If this user has not pressed the close button and passes beyond limit (show flyout stuff)
				$flyout_js_str.='if (lastScreen && !nr_fo_closed && getScrollY()!=0 && nRelate.flyout_show) {';
					if ($animation == "Fade"){
						$flyout_js_str.='$(".nrelate_flyout").fadeIn("slow");';
					}else{
						$flyout_js_str.='$(".nrelate_flyout").stop().animate({"'.$position.'":"0'.$anim_width_type.'"});';
					}
				    $flyout_js_str.='nr_fo_hidden = false}';
				// If close button has been pressed and passes beyond limit (show arrow)
				$flyout_js_str.='else if (nr_fo_closed && lastScreen && getScrollY()!=0 && nRelate.flyout_show) {';
					if ($animation == "Fade"){
						$flyout_js_str.='$("#nrelate_flyout_open").fadeIn("slow");';
					}else{
						$flyout_js_str.='$("#nrelate_flyout_open").stop().animate({"'.$position.'":"0px"});';
					}
					$flyout_js_str.='nr_fo_hidden=false;}';
				// if close button not pushed and is already showing and got this far, we should hide (hide flyout stuff)
				// ADDED: or if scroll bar is maxed to the top
				$flyout_js_str.='else if (!nr_fo_hidden && !nr_fo_closed) {';
					if ($animation == "Fade"){
						$flyout_js_str.='$(".nrelate_flyout").fadeOut("slow");';
					}else{
						$flyout_js_str.='$(".nrelate_flyout").stop().animate({"'.$position.'":"'.$anim_hide_width.$anim_width_type.'"});';
					}
					$flyout_js_str.='nr_fo_hidden = true;}';
				// if close button is pushed and is already showing and got this far, we should hide (hide arrow)
				// ADDED: or if scroll bar is maxed to the top
				$flyout_js_str.='else if (!nr_fo_hidden && nr_fo_closed) {';
					if ($animation == "Fade"){
					    $flyout_js_str.='$("#nrelate_flyout_open").fadeOut("slow");';
					}else{
					    $flyout_js_str.='$("#nrelate_flyout_open").stop().animate({"'.$position.'":"-80px"});';
					}
					$flyout_js_str.='nr_fo_hidden = true;}});';
		 	
		$flyout_js_str.='$("#nrelate_flyout_close").live("click",function() {';
				if ($animation == "Fade"){
				    $flyout_js_str.=' $(".nrelate_flyout").fadeOut("slow"); $("#nrelate_flyout_open").fadeIn("slow");';
				}else{	
				    $flyout_js_str.=' $(".nrelate_flyout").stop().animate({"'.$position.'":"'.$anim_hide_width.$anim_width_type.'"}); $("#nrelate_flyout_open").stop().animate({"'.$position.'":"0px"});';
				}
				$flyout_js_str.=' nr_fo_closed = true; nr_fo_hidden = false; nr_fo_set_closed_cookie(true,"'.$nr_domain.'");});';
		$flyout_js_str.='$("#nrelate_flyout_open").live("click",function() {';
				if ($animation == "Fade"){
					$flyout_js_str.=' $("#nrelate_flyout_open").fadeOut("slow"); $(".nrelate_flyout").fadeIn("slow");';
				}else{	
					$flyout_js_str.=' $("#nrelate_flyout_open").stop().animate({"'.$position.'":"-80px"}); $(".nrelate_flyout").stop().animate({"'.$position.'":"0'.$anim_width_type.'"});';
				}
				$flyout_js_str.='nr_fo_closed = false; nr_fo_hidden = false; nr_fo_set_closed_cookie(false,"'.$nr_domain.'");});';
		$flyout_js_str.='});';
	return $flyout_js_str;
}