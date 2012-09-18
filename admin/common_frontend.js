if (typeof(nRelate)=='undefined') {
	var nRelate = window.nRelate = {
		//domain where the plugin is installed
		domain : '',
		//holds the URL of the nrelate post clicked
		clicked_link : null,
		//flag to redirect the page when click on a nrelate post
		load_link : false,
		//flag that indicates if tracking function has been enabled. Since it uses jQuery.live function it can be called only once
		//YK 060911: track_enabled is now an array where [0] is rc and [1] is mp
		track_enabled : [false,false,false],
		//flag to redirect the page when click on a related post
		domready : false,
		//flag used in bindDomReady function
		domready_bound : false,
		//array of functions to be called on DOMReady event
		domready_list : [],
		//var that determines if flyout stuff should show or not
		flyout_show : false,
		//flag that indicates the current browser is IE
		ie_browser : navigator.appName=='Microsoft Internet Explorer',
		//redirects the page when click on a nrelate post (old function nr_loadframe)
		loadFrame : function() {
			if (nRelate.load_link) {
				nRelate.load_link = false;
				window.location.href = nRelate.clicked_link;
			}
		},
		
		//Sets the click tracking function:
		//YK: var plugin holds either mp or rc
		//YK: var plugin can also be fo
		tracking : function (plugin) {
			if (typeof(jQuery)=='undefined') { setTimeout(arguments.callee,0); return; }
			
			var nr = nRelate;
			
			// YK 060911: track_enabled now checks rc and mp
			if(plugin=="rc" && nr.track_enabled[0]) return;
			if(plugin=="mp" && nr.track_enabled[1]) return;
			if(plugin=="fo" && nr.track_enabled[2]) return;
			
			if(plugin=="rc")
				nr.track_enabled[0]=true;
			else if(plugin=="mp")
				nr.track_enabled[1]=true;
			else
				nr.track_enabled[2]=true;
			
			jQuery('.nr_'+plugin+'_link').live('click', function(event){
				if (jQuery(this).hasClass('nr_partner')) {
					return true;
				}
				var src_url = window.location.href;
				var iframe_src = "http://t.nrelate.com/tracking/";
				if (jQuery(this).hasClass('nr_avid')) {
					nr_type = 'avid';
				} else if (jQuery(this).hasClass('nr_external')) {
					nr_type = 'external';
				} else {
					nr_type = 'internal';
				}
				iframe_src += "?plugin="+escape(plugin)+"&type=" + escape(nr_type) + "&domain=" + nr.domain + "&src_url=" + escape(src_url) + "&dest_url=" + escape(jQuery(this).attr('href'));
				if(nr_type != 'avid'){
					nr.load_link = true;
					nr.clicked_link = jQuery(this).attr('href');
				}
				on_load_function = event.ctrlKey ? "void(0)" : "nRelate.loadFrame()";
				jQuery('<iframe id="nr_clickthrough_frame_'+Math.ceil(100*Math.random())+'" height="0" width="0" style="border-width: 0px; display:none;" src="'+iframe_src+'" onload="javascript:'+on_load_function+'"></iframe>').appendTo('body');
				return ( event.ctrlKey ? true : false );
			});
		},

		//Fix for height in nrelate posts (old function nr_rc_fix_css)
		fixHeight : function (id) {
			if (typeof(jQuery)=='undefined') {
				var r = arguments.callee;
				setTimeout(function(){ r(id); },0); return;
			}
			
			var sel = '#' + id + ' a';
			
			if ( jQuery(sel + ':first').length == 0 ) return;
			
			var currentTallest = 0,
				 currentRowStart = 0,
				 rowDivs = new Array(),
				 $el,
				 topPosition = 0,
				 num_cols = 0,
				 num_rows = 0,
				 row_counter = 0,
				 id_string = '';
			
			currentRowStart = jQuery(sel + ':first').position().top;
			
			//var id_string = id ? '#' + id + ' ' : '';
			
			jQuery(sel).each(function() {
				$el = jQuery(this);
				$el.append('<div class="nr_clear" style="height:1px;"></div>');
				topPostion = $el.position().top;
				if (currentRowStart != topPostion) {
					for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
						rowDivs[currentDiv].height(currentTallest);
					}
					rowDivs.length = 0;
					currentRowStart = topPostion;
					currentTallest = $el.innerHeight();
					rowDivs.push($el);
				} else {
					rowDivs.push($el);
					currentTallest = (currentTallest < $el.innerHeight()) ? ($el.innerHeight()) : (currentTallest);
				}
				
				for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
					rowDivs[currentDiv].height(currentTallest);
				}
			});
			
			//CSS: code that ads extra CSS classes like nr_even_row-->
			
			topPosition = jQuery(sel + ':first').position().top;
			var _elements = jQuery(sel);
			for (var i=0; i<_elements.length; i++) { if (jQuery(_elements[i]).position().top!=topPosition) break; num_cols++; }
			num_rows = Math.ceil(_elements.length/num_cols);
			
			var _row_counter = 1, _col_counter = 1, row_even_odd, col_even_odd, nr_first_col, nr_last_col, nr_first_row, nr_last_row;
			for(var i=0; i<_elements.length; i++) {
				$el = jQuery(_elements[i]);
				
				row_even_odd = (_row_counter%2==0) ? ' nr_even_row' : ' nr_odd_row';
				col_even_odd = (_col_counter%2==0) ? ' nr_even_col' : ' nr_odd_col';
				nr_first_col = (_col_counter==1) ? ' nr_first_col' : '';
				nr_last_col = (_col_counter==num_cols && nr_first_col=='') ? ' nr_last_col' : '';
				nr_first_row = (_row_counter==1) ? ' nr_first_row' : '';
				nr_last_row = (_row_counter==num_rows && nr_first_row=='') ? ' nr_last_row' : '';
				$el.addClass('nr_row_' + _row_counter + ' nr_col_' + _col_counter + row_even_odd + col_even_odd + nr_first_col + nr_last_col + nr_first_row + nr_last_row);
				_col_counter++;
				if (_col_counter>num_cols) {
					_col_counter = 1;
					_row_counter++;
				}
			}
			jQuery('#'+id+'.nrelate_pol').addClass('rotate');
			//<--CSS: code that ads extra CSS classes like nr_even_row
		},
		
		//Enables ad animation
		adAnimation : function (id) {
			if (typeof(jQuery)=='undefined') {
				var r = arguments.callee;
				setTimeout(function(){ r(id); },0); return;
			}
			
			jQuery('#' + id + ' .nr_panel .nr_sponsored').hover(
				function(){
					jQuery(this).stop();
					jQuery(this).animate(
						{'left' : '0px'},
						200
					);
				},
				function(){
					jQuery(this).stop();
					jQuery(this).animate(
						{'left' : (jQuery(this).parent().width() - 18) + 'px'},
						200
					);
				}
			);
		},
		
		//Attaches a script tag to head.
		getScript : function(url, async) {
			var sc = document.createElement('script');
			sc.type = 'text/javascript';
			sc.src = url;
			if (async) sc.async = true;
			document.getElementsByTagName('head')[0].appendChild(sc);
		},
		/**
		* Enqueues a function to be executed when DOMReady is readhed. If the event has been already reached then simply executes the function fn
		* 
		* @param Object[function] fn
		*/
		bindDomReady : function (fn) {
			var nr = nRelate;
			
			if (nr.domready) { fn(); return; }
			
			nr.domready_list.push(fn);
			
			if (nr.domready_bound) return;
			
			nr.domready_bound = true;
			
			if (document.addEventListener) {
				document.addEventListener("DOMContentLoaded", nr.domReadyReached, false);
			} else if(document.attachEvent) {//IE event model
				document.attachEvent("onreadystatechange", nr.readyStateChange);
				
				var toplevel = false;
				try {
					toplevel = window.frameElement == null;
				} catch(e) {}
	
				if ( document.documentElement.doScroll && toplevel ) {
					nr.scrollCheck();
				}
			}
		},
		/**
		* In IE. Listener that listen changes in document.readyState
		*/
		readyStateChange : function () {
			var d = document,
					r = arguments.callee;
			if ( d.readyState === "complete" ) {
				d.detachEvent( "onreadystatechange", r );
				nRelate.domReadyReached();
			}
		},
		/**
		* In IE document.documentElement.doScroll can indicates also the event DOMReady has been reached
		*/
		scrollCheck : function () {
			var nr = nRelate;
			if (nr.domready) {
				nr.domReadyReached(); return;
			}
			try {
				// If IE is used, use the trick by Diego Perini
				// http://javascript.nwbox.com/IEContentLoaded/
				document.documentElement.doScroll("left");
			} catch(e) {
				setTimeout( nr.scrollCheck, 0 ); return;
			}
			nr.domReadyReached();
		},
		/**
		* Function called on DOMReady event. This function calls all queued functions in array domready_list
		*/
		domReadyReached : function () {
			var nr = nRelate;
			if (nr.domready) return;
			
			nr.domready = true;
			
			if (document.addEventListener) document.removeEventListener( "DOMContentLoaded", nr.domReadyReached, false );
			
			//Once DOMReady is reached we call all binded function
			for (var i=0; i<nr.domready_list.length; i++) {
				nr.domready_list[i]();
			}
			
			nr.domready_list = [];
		},
		
		//Get nrelate posts using JS Iframe method
		getNrelatePosts : function (url) {
			var nr = nRelate;
			
			if (nr.ie_browser && !nr.domready) {
				nr.bindDomReady(function(){
					nr.getNrelatePosts( url );
				});
				return;
			}
			
			if (nr.ie_browser) {//in IE broser (specially in IE7) we must wait until DOMReady event is reached
				nr.getScript(url, true);
			} else {
				nr.jsIframe(url, 'nRelate=window.parent.nRelate;');
			}
		},
		
		//Shows content for the specified widget
		sw : function (content, id) {
			
			
			var nr = nRelate;
			var plugin='';
			
			if(id.match('popular'))
				plugin='mp';
			if(id.match('related'))
				plugin='rc';
			if(id.match('flyout'))
				plugin='fo';
			if (document.getElementById(id)) {
				document.getElementById(id).innerHTML = content;
				nr.fixHeight(id);
				nr.adAnimation(id);
				nr.tracking(plugin);
			}
		},
		/**
		* Functions that performs the JS Iframe technique to download in parallel another JS resource
		*
		* @param string resource_url url for the javascript to request
		* @param string custom_js js code to be added in iframe src
		*/
		jsIframe : function (resource_url, custom_js) {
			var doc = document;
			nrDiv = doc.createElement('div');
			nrDiv.style.display = 'none';
			
			ifr = doc.createElement('iframe');
			ifr.frameBorder = '0';
			ifr.allowTransparency = 'true';
			
			nrDiv.appendChild(ifr);
			doc.body.appendChild(nrDiv);
			
			domainSrc = "javascript:var d=document.open(); d.domain='" + doc.domain + "';";
			
			try{
				ifr.contentWindow.document.open();
			}catch(e){
				iframe.src = domainSrc + "void(0);";
			}
			
			var self_script = '';
			custom_js = typeof(custom_js)=='string' ? custom_js : '';

			resource_url = resource_url.replace(/\'/g, "\\'");
			
			iframe_html = '<body onload="d=document; ' + custom_js + 'd.getElementsByTagName(\'head\')[0].appendChild(d.createElement(\'script\')).src=\'' + resource_url + '\';"></body>';
			
			try{
				var d = ifr.contentWindow.document;
				d.write(iframe_html);
				d.close();
			} catch(e) {
				ifr.src = domainSrc + 'd.write("' + iframe_html.replace(/"/g, '\\"') + '");d.close();';
			}
		},
		/**
		 * Function to fix the height of flyout div when fade out is selected.
		 * This function is called at the bottom of a call if fade out is selected.
		 */
		flyout_fix_height : function(){
			jQuery(".nrelate_flyout").css( {"right":"0px","display":"none"} );
		}
	};
}