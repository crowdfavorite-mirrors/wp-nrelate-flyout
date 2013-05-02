//hide and show advanced thumbnail options
function nrelate_showhide_thumbnail(idname){
	var nr_thumbval = document.getElementById(idname).value;
	switch (nr_thumbval){
	case 'Thumbnails':
		jQuery('.nr_image_option').show('slow');
		break;
	default:
		jQuery('.nr_image_option').hide('slow');
	}
}

//Ajax call to checkad.php to check ad validation
function checkad(nr_domain,nr_admin_version,nr_key){
	jQuery.getScript("http://api.nrelate.com/common_wp/"+nr_admin_version+"/adcheck.php?domain="+nr_domain+"&nr_key="+nr_key+"&getrequest=1");
}

//Ajax call to getnrcode.php to get the adcode for signing up in partners.nrelate.com
function getnrcode(nr_domain,nr_admin_version,nr_key){
	jQuery.getScript("http://api.nrelate.com/common_wp/"+nr_admin_version+"/getnrcode.php?domain="+nr_domain+"&nr_key="+nr_key);
}