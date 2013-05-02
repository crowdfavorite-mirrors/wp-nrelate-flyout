if ( typeof nRelate == 'undefined' ) {
	var nr_load_time = new Date().getTime();
	nRelate = function() {		
		var _nrelate = {
			//**********************************************
			//
			//		nRelate private methods
			//
			//**********************************************
			
			/**
			 * Initializes options and sets up the widget singleton object
			 * 
			 * @since 0.52.0
			 */
			init : function( args ) {
				var p, plugin,
					sanitized_plugins = null;
				
				// singleton check
				if ( this.is_initialized ) {
					this.debug("nRelate already setup");
					return false;
				}
				this.is_initialized = true;
				
				this.extend( this.options, args, this.constants );
				
				// Sanitize user selected plugin list
				if ( this.is_object( this.options.plugins ) ) {
					for ( p in this.options.plugins ) {
						plugin = this.options.plugins[ p ];
						if ( this.is_defined( this.plugin_helpers[ p ] ) ) {
							sanitized_plugins = sanitized_plugins || {};
							sanitized_plugins[ p ] = this.extend( {}, this.plugin_helpers[ p ]._defaults, plugin, this.plugin_helpers[ p ] );
						}
					}
				}
				
				// Force loading at least related
				this.options.plugins = sanitized_plugins;
				this.plugins_sanitized = true;
				
				
				// Sanitize parameters
				for ( p in this.options.plugins ) {
					this.snt( p );
				}
				
				// Load custom scripts
				this.lcs();
				
				this.debug("Options initialized %o", this.options);
			},
			
			/**
			 * Sanitizes plugin settings
			 */
			snt : function( p ) {
				var i, l, v,
					plugin = this.options.plugins[ p ],
					settings = ['cssstyle', 'thumbsize', 'ad_place', 'widgetstyle'],
					// Compatibility with user-friendly setting names
					translations = { style: 'cssstyle', thumb_size: 'thumbsize' };
				
				for ( i in translations ) {
					if ( this.is_defined( plugin[i] ) ) {
						plugin[ translations[i] ] = plugin[i];
						delete plugin[i];
					}
				}
				
				for ( i=0, l=settings.length ; i<l ; i++ ){
					v = "supported_" + settings[i] + "s";
					if ( this.in_array( plugin[ settings[i] ], plugin[ v ] ) < 0 ) {
						plugin[ settings[i] ] = plugin[ v ][0];
					}
				}
				
				// Custom plugin params sanitation
				if ( this.is_function( plugin.csnt ) ) {
					plugin.csnt.call( this, plugin );
				}
			},
			
			/**
			 * Load custom scripts for selected plugins
			 *
			 * @since 0.52.0
			 */
			lcs : function() {
				// create the API URL to request custom scripts
				var url = this.options.debug_mode ? this.options.debug_cs_api_url : this.options.cs_api_url;
				url += this.to_slug( this.options.domain ) + ".js";
				
				this.debug("Loading custom scripts from %o",url);
				
				this.lr( url, 'nrelate-widget-cs' );
				
				var self = this;
				setTimeout(function() { self.acs( {} ); }, 5000);
			},
			
			/**
			 * Applies custom scripts to plugins
			 *
			 * @since 0.52.0
			 */
			acs : function( cs ) {
				if ( !this.plugins_sanitized || this.custom_scripts_loaded ) return;
				this.custom_scripts_loaded = true;
				
				var plugin;
				
				this.debug("Applying custom scripts");
				for( plugin in this.options.plugins ) {
					this.extend( this.options.plugins[ plugin ], cs[ plugin ] );
				}
				
				this.nr_cs_load_time = new Date().getTime();

				if ( !this.options.browser.msie ) {
					try {
						this.pd();
					} catch(e) { }
				}
				
				this.bdr( this.pd, "pd()" );
			},
			
			/**
			 * Parses the document looking for placeholders and
			 * permalinks to make the API calls
			 *
			 * @since 0.52.0
			 */
			pd : function() {
				var plugin, fph, fpl, fkw, i, l, prev_found, unique_pls;
				
				for ( p in this.options.plugins ) {
					plugin = this.options.plugins[ p ];
					
					if ( this.is_function( plugin.fph ) ) {
						fph = plugin.fph;
						this.debug("Use custom fph for %s", p);
					} else {
						fph = this.dfph;
						this.debug("Use default fph for %s", p);
					}
					
					if ( this.is_function( plugin.fpl ) ) {
						fpl = plugin.fpl;
						this.debug("Use custom fpl for %s", p);
					} else {
						fpl = this.dfpl;
						this.debug("Use default fpl for %s", p);
					}
					
					if ( this.is_function( plugin.fkw ) ) {
						fkw = plugin.fkw;
						this.debug("Use custom fkw for %s", p);
					} else {
						fkw = this.dfkw;
						this.debug("Use default fkw for %s", p);
					}
					
					this.debug("Parsing document for %s", p);
					prev_found = plugin.cts.length; // previously found placeholders
					plugin.phs = fph.call(this, p);
					plugin.pls = fpl.call(this, p);
					
					// Detect home page based on found permalinks
					unique_pls = {};
					for ( i=0, l=plugin.pls.length; i < l; i++ ) {
						unique_pls[ plugin.pls[i] ] = 1;
					}
					this.is_home_page = Boolean( this.is_defined( window["nr_is_home"] ) ? window["nr_is_home"] : (this.get_keys(unique_pls).length > 1) );

					plugin.kws = fkw.call(this, p);
					
					for ( i = prev_found, l = plugin.phs.length; i < l && i < (l <= 1 ? 1 : 15); ++i) {
						this.act( i, p );
					}
					
					this.mac();
				}
			},
			
			/**
			 * Default function to fetch possible placeholders
			 *
			 * @param string p Plugin name 
			 *
			 * @since 0.52.0
			 */
			dfph : function( p ) {
				var i, j, k, l, m, n, candidates,
					phs = [],
					
					// CSS class selectors to search for
					class_selectors = ['post-body', 'article-content', 'entry-content', 'entry', 'post-entry', 'post-inner', 'postmeta2', 'post_content', 'text', 'postcontent', 'single', 'post-header', 'content', 'post', 'art-PostContent', 'jump-link', 'entrytitle', 'wrap', 'blogPost', 'post-footer', 'postfooter', 'commentpage', 'post-footer-line'],
				
					// Selectors that are intended to add the placeholder on the previousSibling
					prev_sibling = ['post-footer', 'postfooter', 'commentpage', 'post-footer-line'],
				
					// Exceptions
					exclude = ['format_teaser', 'sideContent', 'ngg-widget'];
				
				
				// Backwards compatibility, use #nrelate_[plugin]_placeholder
				if ( phs = this.xgebi( 'nrelate_' + p + '_placeholder' ) ) {
					this.debug("Backwards compatibility: using #nrelate_%s_placeholder: %o", p, phs);
					return [phs];
				}
				
				// Use manually located placeholders <div class="nr_[plugin]_placeholder"></div>
				if ( (phs = this.xgebcn( 'nr_' + p + '_placeholder', 'div' )).length > 0 ) {
					this.debug("Using manually located .nr_%s_placeholder: %o", p, phs);
					return phs;
				}
				
				// Backwards compatibility, use #nrelate_[plugin]_backup_placeholder
				if ( phs = this.xgebi( 'nrelate_' + p + '_backup_placeholder' ) ) {
					this.debug("Backwards compatibility: using #nrelate_%s_backup_placeholder: %o", p, phs);
					return [phs];
				}

				this.debug("No placeholders found");
				return [];
			},
			
			
			/**
			 * Default function to fetch possible permalinks
			 *
			 * @since 0.52.0
			 */
			dfpl : function( p ) {
				var i, j, k, l, m, titles, candidates, phs,
					pls = [],
					plugin = this.options.plugins[p],
					class_selectors = ['entry-title', 'post-title', 'entry-header'],
					tag_selectors = ['h3', 'h2', 'h1'];
				
				if ( plugin.phs.length == 1 && typeof window['nr_pageurl'] != 'undefined' ) {
					this.debug("Manually defined nr_pageurl");
					return [ {href:window['nr_pageurl']} ];
				}
				
				if ( plugin.phs.length > 0 && plugin.phs[0].getAttribute('data-permalink') ) {
					for( i=0, l=plugin.phs.length; i < l; i++ ) {
						pls[ pls.length ] = { href: this.get_data_attr( plugin.phs[i], 'permalink' ) };
					}
					this.debug("Found data-permalink attribute on placeholders");
					return pls;
				}
				
				if ( ( pls = this.xgeba('a', 'title', 'permanent link') ).length > 1 ) {
					this.debug("Found title='permanent link' permalinks %o", pls);
					return pls;
				}
				
				for( i=0, l = class_selectors.length; i < l; i++ ) {
					for ( j=0, m = tag_selectors.length; j < m; j++ ) {
						if ( (titles = this.xgebcn( class_selectors[i], tag_selectors[j] )).length > 0 ) {
							for ( k=0; k < titles.length; k++ ) {
								if ( (candidates = this.xgebtn('a', titles[k])).length > 0 ) {
									pls[ pls.length ] = candidates[0];
								}
							}
						}
						if ( pls.length > 0 ) {
							this.debug("Found permalinks on %s.%s %o", tag_selectors[j], class_selectors[i], pls);
							return pls;
						}
					}
				}
				
				if ( (pls = this.xgeba('a', 'rel', 'bookmark')).length > 0 ) {
					this.debug("Found rel='bookmark' permalinks %o", pls);
					return pls;
				}
				
				if ( (pls = this.xgebcn('permalink', 'a')).length > 0 ) {
					this.debug("Found class='permalink' permalinks %o", pls);
					return pls;
				}
				
				if ( (phs = this.xgebcn('post')).length > 0 ) {
					for( i=0, l=phs.length; i < l; i++ ) {
						if ( (candidates = this.xgebtn('a', phs[i])).length > 0 ) {
							pls[ pls.length ] = candidates[0];
						}
					}
					if ( pls.length > 0 ) {
						this.debug("Found first links inside div.post %o", pls);
						return pls;
					}
				}
				
				if ( (phs = this.options.plugins[p].phs).length > 0 ) {
					for( i=0, l=phs.length; i < l; i++ ) {
						if ( (candidates = this.xgebtn('a', phs[i])).length > 0 ) {
							pls[ pls.length ] = candidates[0];
						}
					}
					if ( pls.length > 0 ) {
						this.debug("Found first links inside div.post %o", pls);
						return pls;
					}
				}
				
				this.debug("No permalinks found");
				return [];
			},
			
			/**
			 * Default function to fetch possible keywords
			 *
			 * @since 0.52.0
			 */
			dfkw : function( p ) {
				var i, l, j, m, titles, txt,
					title = document.title, 
					kws = [],
					plugin = this.options.plugins[ p ],
					class_selectors = ['entry-title', 'post-title', 'entry-header'],
					tag_selectors = ['h3', 'h2', 'h1'];
				
				if ( typeof window['nr_keywords'] != 'undefined' && window['nr_keywords'] != "encodeURI(document.title)" ) {
					this.debug("Manually defined nr_keywords");
					return [ window['nr_keywords'] ];
				}

				if ( plugin.phs.length > 0 && plugin.phs[0].getAttribute('data-title') ) {
					for( i=0, l=plugin.phs.length; i < l; i++ ) {
						kws[ kws.length ] = this.get_data_attr( plugin.phs[i], 'title' );
					}
					this.debug("Found data-title attribute on placeholders");
					return kws;
				}
				
				for( i=0, l = class_selectors.length; i < l; i++ ) {
					for ( j=0, m = tag_selectors.length; j < m; j++ ) {
						if ( (titles = this.xgebcn( class_selectors[i], tag_selectors[j] )).length > 0 ) {
							for ( k=0; k < titles.length; k++ ) {
								txt = titles[k].innerText || titles[k].textContent;
								if ( !this.is_home_page ) {
									if ( title.search( txt ) >= 0 ) {
										for ( i=0, l=plugin.pls.length; i < l; i++ ) {
											kws[ i ] = txt;
										}
										break;
									}
								} else {
									kws[ kws.length ] = txt;
								}
							}
						}
						if ( kws.length > 0 ) {
							this.debug("Found keywords on %s.%s %o", tag_selectors[j], class_selectors[i], kws);
							return kws;
						}
					}
				}
				
				if ( !this.is_home_page ) {
					for ( i=0, l=plugin.pls.length; i < l; i++ ) {
						kws[ i ] = title;
					}
					this.debug("Single post page. Using document.title as keyword");
					return kws;
				}
				
				this.debug("No keywords found");
				return [];
			},
			
			/**
			 * Adds a container
			 *
			 * @since 0.52.0
			 */
			act : function( i, p, content ) {
				var ct,
					plugin = this.options.plugins[ p ];
					content = typeof content != 'string' ? "" : content;
				
				ct = document.createElement('div');
				ct.id = "nrelate_" + p + "_" + i;
				ct.innerHTML = content;
				
				// Add CSS classes
				this.xac( ct, "nrelate");
				this.xac( ct, "nrelate_" + p );
				this.xac( ct, "nr_clear");
				
				// Init container object
				ct.nr_api_called = false;
				ct.nr_plugin = p;
				ct.nr_viewed = false;
				
				plugin.phs[i].appendChild( ct );
				plugin.cts[i] = ct;
			},
			
			/**
			 * Makes API calls
			 *
			 * @since 0.52.0
			 */
			mac : function() {
				var p, plugin, i, l, ct, url, fallback_keywords;
				
				for( p in this.options.plugins ) {
					plugin = this.options.plugins[ p ];
					
					if ( this.is_function( plugin.should_load ) ) {
						if ( !plugin.should_load.call( this, plugin ) ) {
							this.debug("Ignore %s API calls because of should_load", p);
							continue;
						}
					}
					
					this.debug("Making API calls for %s", p);
					
					// Loop through containers
					for ( i = 0, l = plugin.cts.length; i < l; i++ ) {
						if ( (ct = plugin.cts[i]).nr_api_called ) {
							// Ignore if API call was made
							continue;
						}
						
						if ( !this.is_defined( plugin.pls[i] ) ) {
							if ( l == 1 ) {
								plugin.pls[i] = {href:window.location.href};
							} else {
								continue;
							}
						}
						
						fallback_keywords = plugin.phs.length == 1 ? document.title : '';
						
						url = this.aurlp(plugin.api_url, {
							tag				: "nrelate_" + p,
							domain			: this.options.domain,
							keywords		: plugin.kws[i] || fallback_keywords,
							url				: plugin.pls[i].href,
							nr_div_number 	: i,
							pr_id			: this.get_print_id()
						});
						
						// Add the HTTP_REFERRER if supported
						if( 'referrer' in document && document.referrer ) { 
							url = this.aurlp( url, { referrer: document.referrer } );
						}

						// Store URL in the container if single post page
						if ( !this.is_home_page ) {
							ct.nr_src_url = plugin.pls[i].href;
						}
						
						// Add style parameters
						url = this.apip( plugin, url, ct );
						
						this.nr_parse_time = new Date().getTime();
						if ( 'performance' in window ) {
							var load_ms 	= this.nr_load_time - window.performance.timing.responseStart,
								cs_ms		= this.nr_cs_load_time - this.nr_load_time,
								parse_ms	= this.nr_parse_time - Math.max( window.performance.timing.domInteractive, this.nr_cs_load_time );

							this.debug("#%s Loading time: %f | CS Loading time: %f | Parse time %f", ct.id, load_ms/1000, cs_ms /1000, parse_ms/1000  );
							url = this.aurlp( url, { loading_time: load_ms, cs_time: cs_ms, parse_time: parse_ms } );
						}
						ct.nr_api_call_time = new Date().getTime();
						this.lr( url, ct.id + "-api_call");
						
						ct.nr_api_called = true;
						ct.nr_api_url = url;
					}
				}
			},
			
			/**
			 * Adds extra API call parameters
			 */
			apip : function( plugin, url, ct ) {
				var i, l,
					params = {},
					general_s = ['cssstyle', 'thumbsize', 'ad_place', 'widgetstyle', 'header', 'ad_header', 'widget_id', 'page_type_id', 'page_type', 'geo', 'article_id'],
					numeric_s = ['norelatedposts', 'ad_num'];
				
				if ( this.is_array( this.options.extra_apip['general'] ) ) {
					general_s = general_s.concat( this.options.extra_apip['general'] );
				}
				
				for ( i=0, l=general_s.length; i<l; i++ ) {
					if ( plugin[ general_s[i] ] !== null && this.is_defined( plugin[ general_s[i] ] ) ) {
						params[ general_s[i] ] = plugin[ general_s[i] ];
					}
				}
				
				if ( this.is_array( this.options.extra_apip['numeric'] ) ) {
					numeric_s = numeric_s.concat( this.options.extra_apip['numeric'] );
				}
				
				for ( i=0, l=numeric_s.length; i<l; i++ ) {
					if ( !isNaN( plugin[ numeric_s[i] ] ) ) {
						params[ numeric_s[i] ] = plugin[ numeric_s[i] ];
					}
				}
				
				if ( params.length !== {} ) {
					url = this.aurlp( url, params );
				}
				
				if ( this.is_function( plugin.capip ) ) {
					url = plugin.capip.call( this, url, plugin, ct );
				}
				
				return url;
			},
			
			/**
			 * Shows content for the specified widget
			 * 
			 * @param string content HTML to be show in the specified widget
			 * @param string The container id where the content should be placed
			 */
			sw : function( content, ct_id ) {
				var ct, plugin
					args = arguments[2] || {};
				
				if ( ct = this.xgebi( ct_id ) ) {
					this.debug("Injecting content into #%s container", ct_id);
					
					plugin = this.options.plugins[ ct.nr_plugin ];
					
					this.sctc( plugin, ct, args );
					ct.innerHTML = content;
					this.sct( plugin, ct, args );
					
					ct.nr_top_distance = this.xPageY( ct ) + this.xHeight( ct );
					ct.nr_left_distance = this.xPageX( ct ) + this.xWidth( ct );
					
					ct.nr_widget_initialized_time = new Date().getTime();

					ct.nr_count_views = args.count_views || plugin.count_views;

					this.bvc();
				} else {
					this.debug("#%s container not found", ct_id);
				}
			},
			
			/**
			 * Sets up plugin container's CSS classes
			 * 
			 * @param object plugin The plugin instance
			 * @param object ct The container's HTML node
			 * @param object args The style arguments returned in the API response
			 */
			sctc : function( plugin, ct, args ) {
					
				plugin.cssstyle = args.cssstyle || args.style || plugin.cssstyle || plugin.supported_cssstyles[1];
				plugin.cssversion = args.cssversion || 0;
				plugin.thumbsize = args.thumbsize || args.thumb_size || plugin.thumbsize || plugin.supported_thumbsizes[1];
				plugin.ad_place = args.ad_place || plugin.ad_place || plugin.supported_ad_places[1];
				plugin.widgetstyle = args.widgetstyle || ( this.is_defined(plugin.widgetstyle) ? plugin.widgetstyle : plugin.supported_widgetstyles[1] );
				plugin.whats_this_link = args.whats_this_link || plugin.whats_this_link || false;
				plugin.widget_id = args.widget_id || plugin.widget_id || "0";
				plugin.page_type_id = args.page_type_id || plugin.page_type_id || "0";
				plugin.page_type = args.page_type || plugin.page_type || "0";
				plugin.geo = args.geo || plugin.geo || "0";
				plugin.article_id = args.article_id || plugin.article_id || "0";
				
				// TODO: load only required stylesheets, or unify all plugin stylesheets
				if ( plugin.cssstyle ) {
					if ( plugin.cssstyle == 'custom' ) {
						var slug = this.to_slug( this.options.domain );
						var url = ( this.options.debug_mode ? this.options.debug_css_api_url : this.options.css_api_url ) + slug + ".css";
						url = this.aurlp( url, { v: plugin.cssversion } );
						this.debug("Loading custom style from %o",url);						
						this.lr( url, 'nrelate-' + slug + 'custom-style', { type: 'css', callback: this.fah } );
					}
				}

				this.bind( window, "load", function(){ nRelate.fah(); } );

				this.xac( ct, "nrelate_" + plugin.cssstyle );
				
				// Text style
				if ( plugin.widgetstyle === 0 ) {
					this.xac( ct, "nr_text" );
				} else {
					this.xac( ct, "nr_" + plugin.thumbsize );
				}
				
				if ( plugin.ad_place == 'Separate' ) {
					this.xac( ct, "nr_" + (plugin.cols_layout || "2col") );
				} else {
					this.xac( ct, "nr_1col" );
				}
			},
			
			/**
			 * Sets up a container
			 *
			 * @param object plugin The plugin instance
			 * @param object ct The container's HTML node
			 * @param object args The style arguments returned in the API response
			 */
			sct : function( plugin, ct, args ) {
				this.xac( ct, "nr_initializing" );
				
				if ( parseInt(plugin.widget_id) ) {
					this.xac( ct, "widget_id_" + plugin.widget_id );
				}

				
				ct.nr_cols_num = args.cols_num || 0;

				this.fh( ct );
				this.ctr( ct );
				this.aa( ct );
				this.pr( ct );
				
				if ( plugin.whats_this_link ) {
					this.wtl( ct, plugin, args );
				}
				
				// Allow container customization at plugin level
				if ( this.is_function( plugin.csct ) ) {
					plugin.csct.call( this, plugin, ct, args );
				}
				
				this.xrc( ct, "nr_initializing" );
				this.xac( ct, "nr_setup" );
			},
			
			/**
			 * Set fixed height and applies proper col|row classes
			 *
			 * @param object ct The container's HTML node
			 */
			fh : function( ct ) {
				if ( !(ct = this.xgebi(ct)) ) return;
				
				if ( ct.nr_fixing_height == true ) return;
				
				var links;
				
				if ( (links = this.xgebcn('nr_link', 'a', ct)).length == 0 ) return;
				
				ct.nr_fixing_height = true;
				
				var i, j, l, m, widget_top, link, images,
					row_y 		= 0
					link_y 		= 0,
					link_height = 0,
					img_height 	= 0,
					num_cols 	= 0, 
					num_rows 	= 0,
					row_links 	= [],
					tallest 	= 0,
					_row_counter = 1, 
					_col_counter = 1;
				
				
				// get the tallest image to fix floats
				images = this.xgebtn('img', ct);
				for ( i = 0, l = images.length; i < l; i++ ) {
					img_height = Math.max( img_height, this.xHeight(images[i]) );
				}
				
				// Reset styles in case we're executing more than once
				this.xrc( ct, 'rotate' );
				for ( i=0, l = links.length; i < l; i++) {
					links[i].style.minHeight = 0;
				}
				
				// Fix row heights
				row_y = widget_top = this.xPageY( links[0] );
				
				for ( i=0, l = links.length; i < l; i++) {
					link = links[i];
					link_height = Math.max ( this.xHeight( link ), img_height );

					if ( ct.nr_cols_num ) {
						row_links[ row_links.length ] = link;
						tallest = ( tallest < link_height ) ? link_height : tallest;
						num_cols = ct.nr_cols_num;
					} else {
						link_y = this.xPageY( link );
						
						if ( row_y != link_y ) {
							for( j=0, m = row_links.length; j < m; j++ ) {
								if ( this.xhc( ct, 'nr_text' ) ) break;
								row_links[j].style.minHeight = tallest + "px";
							}
							
							row_links = [];
							row_y = link_y;
							tallest = link_height;
							row_links[ row_links.length ] = link;
						} else {
							row_links[ row_links.length ] = link;
							tallest = ( tallest < link_height ) ? link_height : tallest;
						}
						num_cols = ( num_cols < row_links.length ) ? row_links.length : num_cols;
					}
				}
				for( j=0, m = row_links.length; j < m; j++ ) {
					if ( this.xhc( ct, 'nr_text' ) ) break;
					row_links[j].style.minHeight =  tallest + "px";
				}
				
				
				num_rows = ( links.length % num_cols ) + 1;
				
				for( i = 0, l = links.length; i < l; i++) {
					link = links[i];
					
					// Cleanup previously applied classes
					this.xrc( link, "nr_odd_row|nr_even_row|nr_odd_col|nr_even_col|nr_first_col|nr_last_col|nr_first_row|nr_last_row|nr_row_[\\d]+|nr_col_[\\d]+|nr_unit_[\\d]+" );
					
					// Apply proper classes
					this.xac( link, 'nr_unit_' + (i+1) );
					this.xac( link, 'nr_col_' + _col_counter );
					this.xac( link, 'nr_row_' + _row_counter );
					
					if ( _col_counter == 1 ) {
						this.xac( link,  'nr_first_col');
					} else if ( _col_counter == num_cols ) {
						this.xac( link,  'nr_last_col');
					}
					
					if ( _row_counter == 1 ) {
						this.xac( link,  'nr_first_row');
					} else if ( _row_counter == num_rows ) {
						this.xac( link,  'nr_last_row');
					}
					
					this.xac( link, _row_counter % 2  == 0 ? 'nr_even_row' : 'nr_odd_row' );
					this.xac( link, _col_counter % 2  == 0 ? 'nr_even_col' : 'nr_odd_col' );
					
					_col_counter++;
					if ( _col_counter > num_cols) {
						_col_counter = 1;
						_row_counter++;
					}
				}
				
				if ( this.xhc( ct, 'nrelate_pol' ) ) {
					this.xac( ct, 'rotate' );
				}
				
				ct.nr_fixing_height = false;

				if ( !this.is_dom_ready ) {
					var self = this;
					clearInterval( ct.nr_fixheight_interval );
					ct.nr_fixheight_interval = setTimeout(function(){
						self.fh( ct );
					}, 100);
				}
			},

			/**
			 * Fixes heights of alll containers
			 */
			fah : function() {
				var p, i, plugin;

				for ( p in this.options.plugins ) {
					plugin = this.options.plugins[ p ];
					for ( i = 0; i < plugin.cts.length; i++ ) {
						this.debug("Fix height of #%s", plugin.cts[i].id );
						this.fh( plugin.cts[ i ] );
					}
				}
			},
			
			/**
			 * Initializes click tracking
			 */
			ctr: function( ct ) {
				if ( !(ct = this.xgebi(ct)) ) return;
				
				var i, l, links, p,
					self = this;
				
				if ( (links = this.xgebcn('nr_link', 'a', ct)).length == 0 ) return;
				
				for ( i=0, l=links.length; i < l; i++ ) {
					p = ct.nr_plugin;
					this.bind( links[i], "click", function( evt ) { self.hclk( evt, p, ct ); } );
				}
			},
			
			/**
			 * Handles .nr_link clicks
			 */
			hclk: function( evt, p, ct ) {
				if( !evt ) var evt = window.event;
				
				var url, nr_type, custom_tracking, custom_link,
					self = this,
					e = this.evtsrc( evt );
				
				while( e.nodeName.toLowerCase() !='a' ){ e = e.parentNode; }
				
				if ( this.xhc(e, 'nr_partner') ) {
					return true;
				} else if ( this.xhc(e, 'nr_external') ) {
					nr_type = 'external';
				} else {
					nr_type = 'internal';
				}
				
				// Count view in case it wasn't counted before
				this.cwv( ct, this.options.plugins[ p ] );
				
				url = this.aurlp( this.options.tracking_url, {
					plugin			: this.options.plugins[ p ].shortname,
					type			: nr_type,
					domain			: this.options.domain,
					src_url			: ct.nr_src_url || window.location.href,
					dest_url		: this.clicked_link = e.href,
					widget_id		: this.options.plugins[ p ].widget_id,
					page_type_id	: this.options.plugins[ p ].page_type_id,
					page_type		: this.options.plugins[ p ].page_type,
					geo				: this.options.plugins[ p ].geo,
					article_id		: this.options.plugins[ p ].article_id,
					pr_id			: this.get_print_id()
				});

				// Add the HTTP_REFERRER if supported
				if( 'referrer' in document && document.referrer ) { 
					url = this.aurlp( url, { referrer: document.referrer } );
				}
				
				// Add custom tracking parameters
				if ( custom_tracking = this.parse_json( this.get_data_attr( e, "tracking-params" ) ) ) {
					url = this.aurlp( url, custom_tracking );
				}

				if ( custom_link = this.parse_json( this.get_data_attr( e, "link-params" ) ) ) {
					this.clicked_link = this.aurlp( this.clicked_link, custom_link );
				}				

				if ( evt.which == 2 || evt.ctrlKey || evt.metaKey ) {
					this.middle_click = true;
				} else {
					this.middle_click = false;
				}
				
				ifr = document.createElement('iframe');
				ifr.id = 'nr_clickthrough_frame_'+ Math.ceil( 100 * Math.random() );
				ifr.frameBorder = '0';
				ifr.allowTransparency = 'true';
				ifr.style.height = '0px';
				ifr.style.width = '0px';
				ifr.style.display = 'none';
				
				this.bind( ifr, "load", function() { 
					if ( self.clicked_link && !self.middle_click ) {
						window.location.href = self.clicked_link;
					}
					if ( e.nr_original_link ) {
						e.href = e.nr_original_link;
						e.nr_original_link = null;
					}
					self.middle_click = self.clicked_link = false;
				});
				
				ifr.src = url;
				document.body.insertBefore( ifr, document.body.firstChild );
				
				if ( this.middle_click ) {
					e.nr_original_link = e.href;
					e.href = this.clicked_link;
					return true;
				}
				
				this.prevent_default( evt );
				
				return false;
			},
			
			/**
			* Binds the view counter event handler
			*/
			bvc : function() {
				if ( this.views_handler_bound ) return;
				
				var self = this;
				
				this.bind( window, "scroll", function() { self.vch.apply(self); } );
				this.bind( window, "resize", function() { self.vch.apply(self); } );
				this.views_handler_bound = true;
				
				this.vch();
			},
			
			/**
			* Views counter event handler
			*/
			vch : function() {
				var p, plugin, ct, i, l,
					scrolled_y = this.xScrollTop() + this.xClientHeight(),
					scrolled_x = this.xScrollLeft() + this.xClientWidth();
				
				for( p in this.options.plugins ) {
					plugin = this.options.plugins[ p ];
					
					if ( this.is_array( plugin.cts ) ) {
						for ( i=0,l=plugin.cts.length; i<l; i++ ) {
							ct = plugin.cts[ i ];
							
							if ( !ct.nr_viewed && scrolled_y >= ct.nr_top_distance && scrolled_x >= ct.nr_left_distance ) {
								this.cwv( ct, plugin );
							}
						}
					}
				}
			},
			
			/**
			* Counts a widget view
			*/
			cwv : function( ct, plugin ) {
				var url;
				
				if ( ct.nr_count_views && !ct.nr_viewed ) {
					ct.nr_viewed = true;
					this.xac( ct, "nr_viewed" );
					
					url = this.aurlp( this.options.views_url, {
						plugin		: plugin.shortname,
						domain		: this.options.domain,
						url			: ct.nr_src_url || window.location.href,
						widget_id	: plugin.widget_id,
						page_type_id: plugin.page_type_id,
						page_type	: plugin.page_type,
						geo			: plugin.geo,
						pr_id		: this.get_print_id(),
						top			: ct.nr_top_distance,
						left		: ct.nr_left_distance,
						api_time 	: ct.nr_widget_initialized_time - ct.nr_api_call_time,
						page_size	: this.xDocSize()
					});
					
					this.lr( url, ct.id + "_view");
				}
			},
			
			/**
			 * Initializes ads animation
			 */
			aa: function( ct ) {
				if ( !(ct = this.xgebi(ct)) ) return;
				
				var flags;
				
				if ( (flags = this.xgebcn('nr_sponsored', 'span', ct)).length == 0 ) return;
				
				var i, l, self = this,
					xa = new xAnimation();
				
				for ( i=0, l=flags.length; i < l; i++ ) {
					this.bind( flags[i], "mouseover", function( evt ) { 
						//hover in
						var e = self.evtsrc( evt );
						xa.css(
							e,//object
							'left',//css propery
							0,//target value
							150,//time for animation
							5//acceleration type
						);
					});
					
					this.bind( flags[i], "mouseout", function( evt ) { 
						//hover out
						var e = self.evtsrc( evt ), 
							lp = self.xWidth( e.parentNode ) - 18;
						
						xa.css(
							e,//object
							'left',//css propery
							lp,//target value
							150,//time for animation
							5//acceleration type
						);
					});
				}
			},
			
			/**
			 * Post removal handling
			 */
			pr : function( ct ) {
				var i, l, btn, btns,
					self = this;
				
				if ( ( btns = this.xgebcn( "nr_remove", "span", ct ) ).length == 0 ) return;
				
				for( i=0, l=btns.length; i<l; i++ ) {
					btn = btns[ i ];
					
					this.bind( btn, "click", function( evt ){
						var url, tgt, u, u_id, xa, nr_type;
						
						tgt = self.evtsrc( evt );
						u = tgt.parentNode;
						
						u_id = self.get_data_attr( u, "nrid" );
						
						if ( confirm("Are you sure to remove this unit?\n\nYou can undo this later on partners.nrelate.com") ) {
							
							if ( self.xhc(u, 'nr_partner') ) {
								nr_type = 'ad';
							} else if ( self.xhc(u, 'nr_external') ) {
								nr_type = 'external';
							} else {
								nr_type = 'internal';
							}
							
							url = self.aurlp( self.options.remove_url, {
								nrid	: u_id,
								domain	: self.options.domain,
								ct_id	: ct.id,
								nr_type : nr_type,
								pr_id	: this.get_print_id()
							});
							
							self.lr( url );
							
							u.removeChild( tgt );
							
							// animate to give feedback to user
							xa = new xAnimation();
							xa.opacity(
								u, //object
								0.2, //target value
								500, //time for animation
								5 //acceleration type
							);
						}
						
						self.prevent_default( evt );
					});
				}
			},
			
			/**
			 * Post removal callback
			 */
			prc : function( ct ) {
				var old_api, old_api_parent, msg;
				
				if ( !(ct = this.xgebi(ct)) ) return false;
				
				if ( msg = arguments[1] ) {
					alert( msg );
				}
				
				if ( old_api = this.xgebi( ct.id + "-api_call" ) ) {
					old_api_parent = old_api.parentNode;
					old_api_parent.removeChild( old_api );
				}
				
				ct.nr_api_called = false;
				this.lr( ct.nr_api_url, ct.id + "-api_call");
			},
			
			/**
			 * What's this link setup
			 */
			wtl : function( ct, plugin, args ) {				
				var link, container,
					self = this;
				
				if ( this.xgebcn( "nr_link", "a", ct ).length == 0 ) return false;
				
				container = document.createElement("div");
				container.style.fontSize = "0.7em";
				container.style.color = "#AAA";
				container.style.textAlign = "right";
				container.style.clear = "both";
				
				link = document.createElement("span");
				link.innerHTML = args.whats_this_content || this.options.whats_this_content || plugin.whats_this_content;
				link.title = "about these links";
				link.style.cursor = "pointer";
				this.xac( link, "nr_about" );
				
				container.appendChild( link );
				ct.appendChild( container );
				
				this.bind( link, "click", function( evt ) {
					var content, dialog,
						id = plugin.fullname + "_whats_this_dialog";
					
					if ( !self.xgebi( id ) ) {
						content = document.createElement( 'iframe' );
						content.id = id;
						content.src = args.whats_this_url || plugin.whats_this_url;
						content.style.width = "100%";
						content.style.height = "260px";
						content.style.border = "0px";
						content.setAttribute("border", "0");
						document.body.appendChild( content );
					}
					
					dialog = new mDialog( id );
					dialog.show();
					
					self.prevent_default( evt );
				});
			},
			
			
			
			
			
			//**********************************************
			//
			//		DOM and helper methods
			//
			//**********************************************
			
			/**
			 * Binds a callback function to domReady event
			 *
			 * @param optional function callback Function to be invoken on DomReady
			 * @param optional string fn_name To pass a name to debug, useful for anonymous function callbacks
			 *
			 * @since 0.52.0
			 */
			bdr : function( callback, fn_name ) {
				
				if ( callback ) {
					fn_name = fn_name ? fn_name : ( callback.name ? callback.name + "()" : "anonymous function" ); 
					var args = [];
					if ( arguments.length > 1 ) {
						args = Array.prototype.slice.call(arguments, 1);
					}
					
					if ( this.is_dom_ready ) {
						this.debug("domReady already reached: calling %s", fn_name);
						callback.apply( this, Array.prototype.slice.call(args) );
					} else {
						this.domready_callbacks[ this.domready_callbacks.length ] = (function(){
							this.debug("Async calling %s", fn_name);
							return callback.apply( this, Array.prototype.slice.call(args) );
						});
					}
				}
				
				var self = this;
				
				(function(){
					if ( self.is_domready_bound ) return;
					self.is_domready_bound = true;
					
					// domReady was reached
					if ( document.readyState === "complete" ) {
						return setTimeout( function() { self.dr.call( self ); }, 1 );
					}
					
					// Mozilla, Opera and webkit
					if ( document.addEventListener ) {
						document.addEventListener( "DOMContentLoaded", function() { self.drsc.call( self ); }, false );
			
						// A fallback to window.onload, that will always work
						window.addEventListener( "load", function(){
							self.debug("window.load triggered");
							self.dr();
						}, false );
			
					// IE
					} else if ( document.attachEvent ) {
						document.attachEvent( "onreadystatechange", function() { self.drsc.call( self ); } );
			
						// A fallback to window.onload, that will always work
						window.attachEvent( "onload", function(){
							self.debug("window.onload triggered");
							self.dr();
						});
			
						// If IE and not a frame
						// continually check to see if the document is ready
						var toplevel = false;
						
						try {
							toplevel = window.frameElement == null;
						} catch(e) {}
						
						if ( document.documentElement.doScroll && toplevel ) {
							self.iesc( self );
						}
					}
				})();
			},
			
			/**
			 * Invoked on browser's built-in domReady event
			 * (DOMContentLoaded or onreadystatechange)
			 *
			 * @since 0.52.0
			 */
			drsc : function() {
				if ( document.addEventListener ) {
					this.debug("DOMContentLoaded triggered");
					document.removeEventListener( "DOMContentLoaded", arguments.callee, false );
					this.dr();
				} else if ( document.attachEvent ) {
					if ( document.readyState === "complete" ) {
						this.debug("onreadystatechange triggered with document.readyState == complete");
						document.detachEvent( "onreadystatechange", arguments.callee );
						this.dr();
					}
				}
			},
			
			/**
			 * Invokes domready_callbacks when donReady is reached
			 *
			 * @since 0.52.0
			 */
			dr : function() {
				if ( this.is_dom_ready ) return;
				
				var fn;
				
				this.debug("domReady reached");
				
				// In case IE gives troubles
				if ( !document.body ) {
					this.debug("IE body not defined");
					return setTimeout( this.dr, 1 );
				}
				
				this.is_dom_ready = true;
				
				if ( this.domready_callbacks ) {
					for ( fn in this.domready_callbacks ) {
						if ( this.is_function( this.domready_callbacks[fn] ) ) {
							this.domready_callbacks[fn].call( this );
					}
				}
				}
				
				this.domready_callbacks = {};
			},
			
			/**
			 * Aditional domReady check for IE
			 *
			 * @since 0.52.0
			 */
			iesc : function( self ) {
				if ( self.is_dom_ready ) return;
				
				self.debug("IE scroll check");
				
				try {
					document.documentElement.doScroll("left");
				} catch(e) {
					setTimeout( function() { self.iesc( self ) }, 1 );
					return;
				}
				
				self.dr();
			},
			
			/**
			 * Adds listener to event
			 *
			 * @param object e The node object to be 'listened'
			 * @param string eT The event name
			 * @param function eL The listener callback
			 */
			bind : function( e,eT,eL,cap)
			{
				if( !(e=this.xgebi(e)) ) return;
				eT = eT.toLowerCase();
				if( e.addEventListener ) e.addEventListener( eT, eL , cap || false );
				else if( e.attachEvent ) e.attachEvent('on'+eT, eL);
				else {
					var o = e['on'+eT];
					e['on'+eT] = typeof o == 'function' ? function(v){ o(v); eL(v); } : eL;
				}
			},
			
			/**
			 * Prevent Event Bubbling
			 */
			prevent_default : function( evt ) {
				evt.returnValue = false;
				evt.cancelBubble = true;
				
				if ( evt.stopPropagation ) {
					evt.stopPropagation();
					evt.preventDefault();
				}
			},
			
			/**
			 * Prints the arguments on the development console if
			 * debug_mode option is true.
			 *
			 * debug_mode is automatically turned on if 
			 * ?nrelate_debug=1 parameter is received on the URL
			 *
			 * @since 0.52.0
			 */
			debug : function() {
				if ( this.options.debug_mode && window.console ) {
					if ( this.options.browser.msie ) {
						try {
							console.log.apply( console, Array.prototype.slice.call(arguments) );
						} catch( e ) {
							console.log( Array.prototype.slice.call(arguments) );
						}
					} else {
						console.log.apply( console, Array.prototype.slice.call(arguments) );
					}
				}
			},
			
			/**
			 * Merges objects recursively. Inspired on jQuery.extend
			 *
			 * @since 0.52.0
			 */
			extend : function() {
				var opts, name, src, copy,
					target = arguments[0] || {},
					i = 1,
					length = arguments.length;
			
				for ( ; i < length; i++ ) {
					if ( (opts = arguments[ i ]) != null ) {
						for ( name in opts ) {
							src = target[ name ];
							copy = opts[ name ];
							if ( target === copy ) {
								continue;
							}
							if ( this.is_object( copy ) ) {
								if ( this.is_array( copy ) ) {
									clone = this.is_array( src ) ? src : [];
								} else {
									clone = this.is_object( src ) ? src : {};
								}
								target[ name ] = this.extend( clone, copy );
							} else if ( this.is_defined( copy ) ) {
								target[ name ] = copy;
							}
						}
					}
				}
				
				return target;
			},
			
			/**
			 * turns any string into a slug
			 *
			 * @param string s The string
			 */
			to_slug : function( s ) {
				var slug = "";
				if ( typeof s == 'string' ) {
					slug = s.replace(/[^a-zA-Z0-9]+/g, "-");
				}
				return slug;
			},
			
			/**
			 * Returns data-* attribute from tag.
			 * Evaluates javascript if required.
			 *
			 * @param object e The DOM node
			 * @param string a The attribute name
			 */
			get_data_attr : function( e, a ) {
				var node, attr;
				
				if ( node = this.xgebi(e) ) {
					if ( attr = node.getAttribute( 'data-' + a ) ) {
						if ( attr.substr(0, 11) == 'javascript:' ) {
							try {
								return eval( attr.replace('javascript:', '') );
							} catch( e ) { }
						} else {
							return attr;
						}
					}
				}
				
				return null;
			},
			
			/**
			 * in_array crossbrowser support (from jQuery)
			 *
			 * @since 0.52.0
			 */
			in_array : function( elem, array, i ) {
				var len;
				
				if ( array ) {
					if ( Array.prototype.indexOf ) {
						return Array.prototype.indexOf.call( array, elem, i );
					}
		
					len = array.length;
					i = i ? i < 0 ? Math.max( 0, len + i ) : i : 0;
		
					for ( ; i < len; i++ ) {
						// Skip accessing in sparse arrays
						if ( i in array && array[ i ] === elem ) {
							return i;
						}
					}
				}
		
				return -1;
			},
			
			/**
			 * Adds GET parameters to URL
			 *
			 * @since 0.52.0
			 */
			aurlp : function( url, params ) {
				var key, is_assoc, assoc_key, 
					glue = "&";
				
				if ( !this.is_object( params ) ) return url;
				
				if ( !(/.*\?.*/.test(url)) ) {
					glue = "?";
				}
				
				for( key in params ) {
					
					// array / object parameters
					if ( this.is_object( params[ key ] ) ) {
						is_assoc = !this.is_array( params[ key ] );
						for ( assoc_key in params[ key ] ) {
							url += glue + key + "[" + ( is_assoc ? encodeURIComponent( assoc_key ).replace(/'/g, "%27") : '' ) + "]=" + encodeURIComponent( params[ key ] [ assoc_key ] ).replace(/'/g, "%27");
						}
					// everything else
					} else {
						url += glue + key + "=" + encodeURIComponent( params[ key ] ).replace(/'/g, "%27");
					}
					
					glue = "&";
				}
				
				
				return url;
			},
			
			/**
			 * Parses JSON encoded strings into a Javascript objects
			 * (from jQuery)
			 */
			parse_json : function( data ) {
				var json = null;
				
				if ( !data || typeof data !== "string") {
					return null;
				}
				
				// trim to avoid IE error
				data = data.replace(/^\s+|\s+$/, '');
				
				// try native JSON parser
				try {
					if ( window.JSON && window.JSON.parse ) {
						json = window.JSON.parse( data );
					}
				} catch ( e ) {  }
				
				if ( this.is_object(json) ) {
					return json;
				}
				
				try {
					json = ( new Function( "return " + data ) )();
				} catch ( e ) {  }
				
				return json;
			},
			
			/**
			 * Registers an extra parameter name to be sent to API
			 *
			 * @param string param_name The parameter name to register
			 * @param optional string param_type general | numeric
			 */
			create_apip : function( param_name ) {
				var length,
					param_type = arguments[1] == 'numeric' ? 'numeric' : 'general';
				
				if ( !this.is_defined( this.options.extra_apip[param_type] ) ) {
					this.options.extra_apip[param_type] = [];
				}
				
				if ( this.in_array( param_name, this.options.extra_apip[param_type] ) < 0 ) {
					this.options.extra_apip[param_type][this.options.extra_apip[param_type].length] = param_name;
				}
			},
			
			/**
			 * Returns a unique identifier for the page print
			 * to be sent on API calls
			 */
			get_print_id : function() {
				if ( !this.print_id )  {				
					var i, 
						chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					
					this.print_id = '';
					
					for (i=0; i<24; i++) {
						this.print_id += chars[Math.round(Math.random() * (chars.length - 1))];
					}
				}
				return this.print_id;
			},
			
			/**
			 * Verifies if parameter is an object
			 *
			 * @since 0.52.0
			 */
			is_object : function( obj ) {
				return ( obj != null && typeof obj == 'object' );
			},
			
			/**
			 * Verifies if parameter is an array
			 *
			 * @since 0.52.0
			 */
			is_array : function( obj ) {
				return obj != null && typeof obj === "object" && 'splice' in obj && 'join' in obj;
			},
			
			/**
			 * Verifies if parameter is a function
			 *
			 * @since 0.52.0
			 */
			is_function : function( obj ) {
				var e;
				try {
					return /^\s*\bfunction\b/.test( obj );
				} catch (e) {
					return false ;
				}
			},
			
			/**
			 * Returns an array with the keys
			 *
			 * @since 0.52.0
			 */
			get_keys : function( obj ) {
				var key,
					keys = [];
				
				for ( key in obj ) {
					keys[keys.length] = key;
				}
				
				return keys;
			},
			
			/**
			 * Checks if all the arguments are defined
			 *
			 * @since 0.52.0
			 */
			is_defined: function()
			{
				for ( var i=0, l=arguments.length; i<l; ++i) {
					if ( typeof( arguments[i] ) === 'undefined' ) {
						return false;
					}
				}
				return true;
			},
			
			/**
			 * Return the DOM Node associated to the provided event
			 *
			 * @param object ev Event object
			 */
			evtsrc : function( evt ){
				var e = null;
				if ( evt.target ) {
					e = evt.target;
				} else if ( evt.srcElement ) {
					e = evt.srcElement;
				}
				if (e.nodeType == 3) {
					e = e.parentNode;
				}
				return e;
			},
			
			/**
			 * Loads a resource from remote URL
			 *
			 * @param string url to load
			 * @param string optional id to identify the resource and load it once on this document
			 * @param object|string optional pass 'css' to load stylesheets or an object to configure script loading
			 *
			 * @since 0.52.0 
			 */
			 lr : function( url, id, args) {
				if ( id && this.xgebi( id ) ) return;
				
				var head,
					e = false;
				
				if ( typeof args == 'string' ) {
					args = { type: args };
				}
				
				args = this.extend({
					type:'script',
					async:true,
					custom_js:''
				}, args);
				
				switch ( args.type ) {
					case 'script':
						if ( args.async ) {
							// Load asynchronously using Meebo technique
							var ifr, err, domainSrc, html, ifrdoc;
							
							e = document.createElement('div');
							e.style.display = 'none'
							if ( id ) e.id = id;
							
							ifr = document.createElement('iframe');
							ifr.frameBorder = '0';
							ifr.allowTransparency = 'true';
							
							try {
								document.body.insertBefore( e, document.body.firstChild );
							} catch ( err ) {
								args.async = false;
								this.lr( url, id, args );
							}
							
							e.appendChild( ifr );
							
							try {
								ifr.contentWindow.document.open();
							} catch( err ) {
								// Set iframe domain to prevent IE bug
								domainSrc = "javascript:var d=document.open();d.domain='" + document.domain + "';";
								ifr.src = domainSrc + "void(0);";
							}
							
							html = '<body onload="var d=document;nRelate=window.parent.nRelate;' + args.custom_js + 'd.getElementsByTagName(\'head\')[0].appendChild(d.createElement(\'script\')).src=\'' + url + '\';"></body>';
							
							try {
								ifrdoc = ifr.contentWindow.document;
								ifrdoc.write( html );
								ifrdoc.close();
							} catch ( err ) {
								ifr.src = domainSrc + 'd.write(\'' + html.replace(/"/g, '\\"') + '\');d.close();';
							}
							
							return e;
						} else {
							e = document.createElement('script');
							e.type = 'text/javascript';
							e.src = url;
						}
					break;
					
					case 'css':
					case 'link':
						e = document.createElement('link');
						e.type = 'text/css';
						e.rel = 'stylesheet';
						e.href = url;

						if ( this.is_function( args.callback ) ) {
							var img = document.createElement("img"),
							self = this;
							img.onerror = function() {
								args.callback.call( self, id, e );
							}
							img.src = url;
						}
					break;
				}
				
				if ( e ) {
					if ( id ) e.id = id;
					
					head = this.xgebtn('head')[0] || document.documentElement;
					head.insertBefore(e, head.firstChild);
				}
				
				return e;
			},
			
			/**
			 * Returns an array of elements which are descendants of parentEle and have tagName and clsName.
			 *
			 * @param string c String. A className. This can also be a regular expression.
			 * @param string t String. An HTML tagName. If omitted "*" will be used.
			 * @param object p Element reference. If omitted "document" will be used. This can be an ID string.
			 * @param function-object f (optional) Callback function, iterates thru the list of found elements.
			 */
			xgebcn : function( c, t, p, f )
			{
				var r=[], re, e, i, l;
				re = new RegExp( "(^|\\s)"+c+"(\\s|$)" );
				e = this.xgebtn( t ,p );
				for ( i=0, l=e.length; i < l; ++i ) {
					if ( re.test(e[i].className) ) {
						r[ r.length ] = e[ i ];
						if ( f ) f( e[i] );
					}
				}
				return r;
			},
			
			/**
			 * Returns an array of elements which are descendants of parent and have tagName.
			 *
			 * @param string t tagName
			 * @param object p Element reference. If omitted "document" will be used. This can be an ID string.
			 */
			xgebtn : function( t, p )
			{
				var list = null;
				t = t || '*';
				p = this.xgebi( p ) || document;
				if ( this.is_defined( p.getElementsByTagName ) ) { // DOM1
					list = p.getElementsByTagName( t );
					if ( t=='*' && (!list || !list.length) ) list = p.all; // IE5 '*' bug
				}
				else { // IE4 object model
					if (t=='*') list = p.all;
					else if ( p.all && p.all.tags ) list = p.all.tags(t);
				}
				return list || [];
			},
			
			/**
			 * Get an object reference to the element object with the passed ID.
			 *
			 * @param string e ID string or object reference
			 */
			xgebi : function( e )
			{
				if (typeof(e) == 'string') {
					if (document.getElementById) e = document.getElementById(e);
					else if (document.all) e = document.all[e];
					else e = null;
				}
				return e;
			},
			
			/**
			 * Returns an array of sTag elements whose sAtt attribute matches sRE.
			 *
			 */
			xgeba : function( sTag, sAtt, sRE, fn )
			{
				var a, l, list, found=[], re=new RegExp(sRE, 'i');
				list = this.xgebtn( sTag );
				for ( var i=0, l=list.length; i<l; ++i ) {
					a = list[i].getAttribute( sAtt );
					if ( !a ) { a = list[i][sAtt]; }
					if ( typeof(a) === 'string' && a.search(re)!==-1 ) {
						found[found.length] = list[i];
						if (fn) fn( list[i] );
					}
				}
				return found;
			},
			
			/**
			 * Returns true if an element has a specified class name
			 *
			 * @param object e Element id or object
			 * @param string c Class name
			 */
			xhc : function(e, c)
			{
			  e = this.xgebi(e);
			  if (!e || e.className=='') return false;
			  var re = new RegExp("(^|\\s)"+c+"(\\s|$)");
			  return re.test(e.className);
			},
			
			/**
			 * Adds a class name to an element
			 *
			 * @param object e Element id or object
			 * @param string c Class name
			 */
			xac : function( e, c ) {
				if ( e = this.xgebi(e) ) {
					var s = '';
					if ( e.className.length && e.className.charAt(e.className.length - 1) != ' ') {
						s = ' ';
					}
					if ( !this.xhc(e, c) ) {
						e.className += s + c;
						return true;
					}
				} 
				return false;
			},
			
			/**
			 * Removes a class name
			 *
			 * @param object e Element id or object
			 * @param string c Class name
			 */
			xrc: function(e, c)
			{
				if( !(e = this.xgebi(e)) ) {
					return false;
				}
				
				e.className = e.className.replace(
					new RegExp("(^|\\s)"+c+"(\\s|$)",'g'),
					function(str, p1, p2) { 
						return (p1 == ' ' && p2 == ' ') ? ' ' : ''; 
					}
				);
				
				// trim
				e.className = e.className.replace(/^\s+|\s+$/, '');
				
				return true;
			},
			
			
			
			
			
			//**********************************************
			//
			//		Graphic methods
			//
			//**********************************************
			
			/**
			 * Get the page-relative Y position of the element.
			 *
			 * @param object|string id string or object reference
			 */
			xPageY: function( e )
			{
				var y = 0;
				e = this.xgebi( e );
				while ( e ) {
					if ( this.is_defined( e.offsetTop ) ) {
						y += e.offsetTop;
					}
					e = ( this.is_defined( e.offsetParent ) ) ? e.offsetParent : null;
				}
				return y;
			},
			
			/**
			 * Get the page-relative X position of the element.
			 *
			 * @param object|string id string or object reference
			 */
			xPageX: function( e )
			{
				var x = 0;
				e = this.xgebi(e);
				
				while (e) {
					if ( this.is_defined(e.offsetLeft) ) {
						x += e.offsetLeft;
					}
					e = this.is_defined(e.offsetParent) ? e.offsetParent : null;
				}
				return x;
			},
			
			/**
			 * Return and optionally set the element's height
			 *
			 * @param object|string id string or object reference
			 * @param int height; it is rounded to an integer
			 */
			xHeight: function( e, h )
			{
				var css, pt=0, pb=0, bt=0, bb=0;
				if( !(e=this.xgebi(e)) ) return 0;
				
				if ( !( isNaN(h) || typeof h !== 'number' ) ) {
					if ( h < 0 ) {
						h = 0;
					} else {
						h=Math.round( h );
					}
				} else {
					h=-1;
				}
				
				css = this.is_defined( e.style );
				
				if ( e == document || e.tagName.toLowerCase() == 'html' || e.tagName.toLowerCase() == 'body') {
					h = this.xClientHeight();
				} else if( css && this.is_defined( e.offsetHeight ) && typeof e.style.height == 'string' ) {
					if( h >= 0 ) { 
						if ( document.compatMode=='CSS1Compat' ) {
							pt = this.xGetComputedStyle(e,'padding-top',1);
							if ( pt !== null ) {
								pb = this.xGetComputedStyle(e,'padding-bottom',1);
								bt = this.xGetComputedStyle(e,'border-top-width',1);
								bb = this.xGetComputedStyle(e,'border-bottom-width',1);
							}
							// Should we try this as a last resort?
							// At this point getComputedStyle and currentStyle do not exist.
							else if( this.is_defined( e.offsetHeight, e.style.height ) ) {
								e.style.height = h+'px';
								pt = e.offsetHeight-h;
							}
						}
						
						h -= (pt+pb+bt+bb);
						if( isNaN(h) || h < 0 ) return;
						else e.style.height=h+'px';
					}
					h = e.offsetHeight;
				} else if( css && this.is_defined( e.style.pixelHeight ) ) {
					if( h >= 0) e.style.pixelHeight = h;
					h = e.style.pixelHeight;
				}
				return h;
			},
			
			/**
			 * Return and optionally set the element's width
			 */
			xWidth: function(e,w)
			{
				var css, pl=0, pr=0, bl=0, br=0;
				
				if( !( e=this.xgebi(e)) ) return 0;
				
				if ( !( isNaN(w) || typeof w !== 'number' ) ) {
					if ( w < 0 ) w = 0;
					else w = Math.round( w );
				} else {
					w = -1;
				}
				
				css = this.is_defined( e.style );
				
				if (e == document || e.tagName.toLowerCase() == 'html' || e.tagName.toLowerCase() == 'body') {
					w = this.xClientWidth();
				} else if( css && this.is_defined(e.offsetWidth) && typeof e.style.width == 'string' ) {
					if ( w >= 0 ) {
						if ( document.compatMode=='CSS1Compat' ) {
							pl = this.xGetComputedStyle( e, 'padding-left', 1);
							if ( pl !== null ) {
								pr = this.xGetComputedStyle( e, 'padding-right',1);
								bl = this.xGetComputedStyle( e, 'border-left-width',1);
								br = this.xGetComputedStyle( e, 'border-right-width',1);
							}
							// Should we try this as a last resort?
							// At this point getComputedStyle and currentStyle do not exist.
							else if( this-is_defined( e.offsetWidth, e.style.width ) ) {
								e.style.width = w+'px';
								pl = e.offsetWidth-w;
							}
						}
						w -= (pl+pr+bl+br);
						if( isNaN(w) || w < 0 ) return;
						else e.style.width=w+'px';
					}
					w = e.offsetWidth;
				}
				else if( css && this.is_defined( e.style.pixelWidth ) ) {
					if ( w >= 0 ) e.style.pixelWidth = w;
					w = e.style.pixelWidth;
				}
				return w;
			},
			
			/**
			 * The inner height of the window not including any scrollbar - that is, the "viewport".
			 */
			xClientHeight: function()
			{
				var v=0,d=document,w=window;
				if( (!d.compatMode || d.compatMode == 'CSS1Compat') /* && !w.opera */ && d.documentElement && d.documentElement.clientHeight )
				{
					v = d.documentElement.clientHeight;
				} else if( d.body && d.body.clientHeight ) {
					v=d.body.clientHeight;
				} else if( this.is_defined( w.innerWidth, w.innerHeight, d.width ) ) {
					v = w.innerHeight;
					if( d.width > w.innerWidth ) v-=16;
				}
				return v;
			},
			
			/**
			 * The inner width of the window not including any scrollbar - that is, the "viewport".
			 */
			xClientWidth: function()
			{
				var v=0, d=document, w=window;
				
				if((!d.compatMode || d.compatMode == 'CSS1Compat') && !w.opera && d.documentElement && d.documentElement.clientWidth) {
					v=d.documentElement.clientWidth;
				} else if( d.body && d.body.clientWidth ) {
					v=d.body.clientWidth;
				} else if( this.is_defined( w.innerWidth, w.innerHeight, d.height ) ) {
					v = w.innerWidth;
					if ( d.height > w.innerHeight ) v-=16;
				}
				return v;
			},
			
			/**
			 * Determines the (largest) width and height of the 'document'.
			 */
			xDocSize: function()
			{
				var b = document.body, 
					e = document.documentElement,
					esw=0, eow=0, bsw=0, bow=0, esh=0, eoh=0, bsh=0, boh=0;
					
				if ( e ) {
					esw = e.scrollWidth;
					eow = e.offsetWidth;
					esh = e.scrollHeight;
					eoh = e.offsetHeight;
				}
				if ( b ) {
					bsw = b.scrollWidth;
					bow = b.offsetWidth;
					bsh = b.scrollHeight;
					boh = b.offsetHeight;
				}
				
				return { 
					w: Math.max( esw, eow, bsw, bow ),
					h: Math.max( esh, eoh, bsh, boh )
				};
			},
			
			/**
			 * Determine how far the window (or an element) has scrolled vertically
			 *
			 * @param object|id element object reference or id string
			 * @param boolean bWin if true, e is assumed to be a reference to a window object
			 */
			xScrollTop: function(e, bWin)
			{
				var w, offset=0;
				
				if ( !this.is_defined(e) || bWin || e == document || e.tagName.toLowerCase() == 'html' || e.tagName.toLowerCase() == 'body' ) {
					w = window;
					if (bWin && e) w = e;
					if( w.document.documentElement && w.document.documentElement.scrollTop) {
						offset=w.document.documentElement.scrollTop;
					} else if( w.document.body && this.is_defined( w.document.body.scrollTop ) ) {
						offset=w.document.body.scrollTop;
					}
				} else {
					e = this.xgebi( e );
					if ( e && ( !isNaN( e.scrollTop ) && typeof e.scrollTop == 'number' ) ) {
						offset = e.scrollTop;
					}
				}
				return offset;
			},
			
			/**
			 * Determine how far the window (or an element) has scrolled horizontally
			 *
			 * @param object|id element object reference or id string
			 * @param boolean bWin if true, e is assumed to be a reference to a window object
			 */
			xScrollLeft: function(e, bWin)
			{
				var w, offset=0;
				
				if ( !this.is_defined(e) || bWin || e == document || e.tagName.toLowerCase() == 'html' || e.tagName.toLowerCase() == 'body') {
					w = window;
					if ( bWin && e ) {
						w = e;
					}
					if( w.document.documentElement && w.document.documentElement.scrollLeft ) {
						offset=w.document.documentElement.scrollLeft;
					} else if( w.document.body && this.is_defined(w.document.body.scrollLeft) ) {
						offset=w.document.body.scrollLeft;
					}
				}
				else {
					e = this.xgebi(e);
					if ( e && !isNaN(e.scrollLeft) && typeof e.scrollLeft == 'number' ) {
						offset = e.scrollLeft;
					}
				}
				
				return offset;
			},
			
			/**
			 * A safe wrapper for getComputedStyle and currentStyle.
			 *
			 * @param object|id element object reference or id string
			 * @param string css property name
			 * @param bool if true, return value is an integer
			 */
			xGetComputedStyle: function( e, p, i )
			{
				if( !(e=this.xgebi(e)) ) return null;
				
				var s, v = 'undefined', dv = document.defaultView;
				if( dv && dv.getComputedStyle ) {
					s = dv.getComputedStyle(e,'');
					if ( s ) v = s.getPropertyValue( p );
				} else if( e.currentStyle ) {
					v = e.currentStyle[ this.xCamelize(p) ];
				}
				else return null;
				
				return i ? (parseInt(v) || 0) : v;
			},
			
			xOpacity: function(e, o)
			{
				var set = this.is_defined( o );
				if( !( e = this.xgebi( e ) ) ) return 2; // error
				if ( typeof e.style.opacity == 'string' ) { // CSS3
					if ( set ) e.style.opacity = o + '';
					else o = parseFloat( e.style.opacity );
				} else if ( typeof e.style.filter == 'string' ) { // IE5.5+
					if ( set ) e.style.filter = 'alpha(opacity=' + (100 * o) + ')';
					else if ( e.filters && e.filters.alpha ) { o = e.filters.alpha.opacity / 100; }
				} else if ( typeof e.style.MozOpacity == 'string' ) { // Gecko before CSS3 support
					if ( set ) e.style.MozOpacity = o + '';
					else o = parseFloat( e.style.MozOpacity );
				} else if ( typeof e.style.KhtmlOpacity == 'string' ) { // Konquerer and Safari
					if ( set ) e.style.KhtmlOpacity = o + '';
					else o = parseFloat( e.style.KhtmlOpacity );
				}
				return isNaN(o) ? 1 : o;
			},
			
			/**
			 * Converts a CSS property name string (dash-separated) to a camel-cased string
			 *
			 * @param string the property
			 */
			xCamelize: function(cssPropStr)
			{
				var i, c, a, s;
				a = cssPropStr.split('-');
				s = a[0];
				for (i=1; i<a.length; ++i) {
					c = a[i].charAt(0);
					s += a[i].replace(c, c.toUpperCase());
				}
				return s;
			},
			
			
			
			
			
			
			//**********************************************
			//
			//		nRelate private properties
			//
			//**********************************************
			
			
			/**
			 * Default configuration, customizable by the
			 * user on manual installations
			 *
			 * @since 0.52.0
			 */
			
			options : {
				auto_invoke : typeof window['nr_manual'] != 'undefined' ? !window['nr_manual'] : true,
				posts_only: typeof window['nr_posts_only'] != 'undefined' ? window['nr_posts_only'] : false,
				debug_mode : /.*\?.*nrelate_debug=.*/.test( window.location ) || Boolean( window['nr_debug'] ),
				debug_cs_api_url : "http://staticrepo.nrelate.com/custom-script/1.0/dev/",
				debug_css_api_url : "http://staticrepo.nrelate.com/custom-style/dev/"
			},
			
			/**
			 * Default constants. These cannot be
			 * overriden by the user
			 *
			 * @since 0.52.0
			 */
			constants : {
				version 				: "0.52.0",
				domain 					: (typeof window['nr_domain'] != 'undefined' ? window['nr_domain'] : window.location.hostname ),
				cs_api_url 				: "http://js.nrcdn.com/custom-script/1.0/",
				css_api_url 			: "http://css.nrcdn.com/custom-style/",
				default_stylesheet_url	: "http://css.nrcdn.com/custom-style/default.min.css",
				tracking_url			: "http://t.nrelate.com/tracking/",
				remove_url				: "http://api.nrelate.com/rcw_js/0.52.0/nr_removeID.php",
				views_url				: "http://api.nrelate.com/vt/",
				print_id				: false,
				extra_apip				: { }
			},

			/**
			 * Widget profiling timers
			 *
			 */
			nr_load_time 			: window["nr_load_time"],
			nr_cs_load_time			: 0, 
			nr_parse_time			: 0,
			
			// Flag to ensure a single instance initialization
			is_initialized 			: false,
			custom_scripts_loaded 	: false,
			is_home_page			: false,
			plugins_sanitized 		: false,
			views_handler_bound		: false,
			middle_click			: false,
			clicked_link			: false,
			
			// domReady event control properties
			is_dom_ready 		: false,
			is_domready_bound 	: false,
			domready_callbacks 	: {}
		};
		
		
		// Browser detection
		var userAgent = navigator.userAgent.toLowerCase();
		_nrelate.options.ua = userAgent;
		_nrelate.options.browser = {
			version: (userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/) || [])[1],
			safari: /webkit/.test(userAgent),
			opera: /opera/.test(userAgent),
			msie: (/msie/.test(userAgent)) && (!/opera/.test(userAgent)),
			mozilla: (/mozilla/.test(userAgent)) && (!/(compatible|webkit)/.test(userAgent))
		};
		
		
		//**********************************************
		//
		//		nRelate plugins
		//
		//**********************************************
		
		// Inherited structure, common to all plugin helpers
		var _plugin_base = {
			// cannot be overriden by user
			cts : [],
			pls : [],
			kws : [],
			phs : [],
			
			supported_cssstyles : [null, 'default', 'bty', 'dhot', 'huf', 'tre', 'toon', 'pol', 'loud', 'nsq', 'til', 'engadget', 'none', 'custom'],
			supported_thumbsizes : [null, 110, 80, 90, 100, 120, 130, 140, 150],
			supported_ad_places : [null, 'First', 'Last', 'Mixed', 'Separate'],
			supported_widgetstyles : [null, 1, 0], // 1: Thumbnail, 0: Text
			
			whats_this_url	: "http://static.nrelate.com/common_js/sponsor_info.html",

			count_views		: false,
			
			should_load: function( plugin ) {
				if ( this.is_home_page && this.options.posts_only ) {
					return false;
				}

				return true;
			},

			// options inside _defaults are applied but can be overriden by the user
			_defaults : { 
				whats_this_link 	: false,
				whats_this_content 	: "about these links",
				widget_id 			: null,
				page_type_id 		: null,
				page_type 			: null,
				geo 				: null,
				article_id 			: null
			}
		};
		
		_nrelate.plugin_helpers = {
			
			/**
			 * nRelate related plugin
			 */
			related : _nrelate.extend({}, _plugin_base, {
				api_url		: "http://api.nrelate.com/rcw_wp/" + _nrelate.constants.version + "/",
				shortname 	: "rc",
				fullname 	: "related"
			}),


			/**
			 * nRelate most popular plugin
			 */
			popular : _nrelate.extend({}, _plugin_base, {
				api_url			: "http://api.nrelate.com/mpw_wp/" + _nrelate.constants.version + "/",
				loadcounter_url	: "http://api.nrelate.com/mpw_wp/" + _nrelate.constants.version + "/loadcounter.php",
				shortname 		: "mp",
				fullname 		: "popular",
				loadcounter_log : {},

				should_load : function( plugin ) {
					this.create_apip( "maxageposts", "numeric" );
					
					if( !this.is_home_page && plugin.cts.length == 0 ) {
						var pl = ( window['nr_pageurl'] ? { href: window['nr_pageurl'] } : ( this.dfpl( plugin.fullname )[0] || { href: document.location.href } ) );

						if ( !this.is_defined( plugin.loadcounter_log[ pl.href ] ) ) {
						
							var url = this.aurlp( plugin.loadcounter_url, {
								tag 	: "nrelate_" + plugin.fullname,
								domain 	: this.options.domain,
								url 	: pl.href
							});

							plugin.loadcounter_log[ pl.href ] = url;
							this.lr( url );
						}
					}

					return true;
				},

				// Sends extra parameters to API (to allow manual style overriding)
				capip : function( url, plugin, ct ) {
					
					url = this.aurlp(url, {"increment" : this.is_home_page ? 0 : 1 });
					
					if ( this.is_home_page ) {
						url = this.aurlp(url, {"source" : "hp" });	
					}

					return url;
				}
			}),
			
			
			/**
			 * nRelate flyout plugin
			 */
			flyout : _nrelate.extend({}, _plugin_base, {
				api_url			: "http://api.nrelate.com/fow_wp/" + _nrelate.constants.version + "/",
				shortname 		: "fo",
				fullname 		: "flyout",
				home_page_only 	: true,
				
				supported_thumbsizes 		: [null, 90, 80, 100, 110, 120, 130, 140, 150],
				supported_animstyles 	: [null, 'nyt', 'simple', 'simplydk', 'centered', 'centereddk'],
				supported_animations 	: [ null, 'slideout', 'fade' ],
				supported_locations 		: [ null, 'right', 'left' ],
				supported_flyout_offsets 	: [ null, 1, 2, 3, 4 ],
				
				// Validates user entered parameters at initialization
				csnt : function( plugin ) {
					if ( this.in_array( plugin.animstyle, plugin.supported_animstyles ) < 0 ) {
						plugin.animstyle = plugin.supported_animstyles[0];
					}
					
					if ( this.in_array( plugin.animation, plugin.supported_animations ) < 0 ) {
						plugin.animation = plugin.supported_animations[0];
					}
					
					if ( this.in_array( plugin.location, plugin.supported_locations ) < 0 ) {
						plugin.location = plugin.supported_locations[0];
					}
					
					if ( this.in_array( plugin.offset, plugin.supported_flyout_offsets ) < 0 ) {
						plugin.offset = plugin.supported_flyout_offsets[0];
					}
					
					plugin.offset_node = null;
				},
				
				// Sends extra parameters to API (to allow manual style overriding)
				capip : function( url, plugin, ct ) {
					var i, param,
						extra_style_params = [ 'width', 'widthtype', 'frombot', 'frombottype', 'animstyle', 'animation', 'location', 'offset', 'element' ];
						
					for( i = 0; i < extra_style_params.length; i++ ) {
						if ( plugin[ extra_style_params[i] ] ) {
							param = { };
							param[ extra_style_params[i] ] = plugin[ extra_style_params[i] ];
							url = this.aurlp( url, param );
						}
					}
					
					return url;
				},
				
				// Custom container setup for Flyout. Handles extra CSS classes and initializations
				csct : function( plugin, ct, args ) {
					var fo_close,
						self = this;
					
					// Extra CSS classes
					plugin.animstyle = args.animstyle || plugin.animstyle || plugin.supported_animstyles[1];
					plugin.animation = (args.animation || plugin.animation || plugin.supported_animations[1]).toLowerCase();
					plugin.location = (args.location || plugin.location || plugin.supported_locations[1]).toLowerCase();
					
					this.xac( ct, "nrelate_animate_style_" + plugin.animstyle );
					this.xac( ct, "nr_animate_type_" + plugin.animation );
					this.xac( ct, "nr_" + plugin.animation + "_" + plugin.location );
					
					// TODO: update to load the proper stylesheet (if required)
					plugin.default_stylesheet_url = "http://css.nrcdn.com/common_js/0.52.0/nrelate-flyout-"+ plugin.animstyle +".min.css";	
					this.lr( plugin.default_stylesheet_url, 'nrelate-fo-'+plugin.animstyle, "css" );
					
					// Width and distance from bottom
					plugin.width = args.width || plugin.width || 360;
					plugin.widthtype = args.widthtype || plugin.widthtype || 'px';
					plugin.frombot = args.frombot || plugin.frombot || 0;
					plugin.frombottype = args.frombottype || plugin.frombottype || 'px';
					
					ct.style.width = plugin.width + plugin.widthtype;
					ct.style.bottom = plugin.frombot + plugin.frombottype;
					
					// Create and setup open handle
					if ( !(fo_open = this.xgebi( "nrelate_flyout_open" )) ) {
						fo_open = document.createElement('div');
						fo_open.id = "nrelate_flyout_open";
						this.xac( fo_open, "nrelate_animate_style_" + plugin.animstyle );
						this.xac( fo_open, "nr_" + plugin.animation + "_" + plugin.location );
						document.body.insertBefore( fo_open, ct.nextSibling );
						this.bind( fo_open, "click", function(){
							plugin.fo_animate_open.call( self, plugin, ct );
							plugin.fo_animate_close.call( self, plugin, ct, fo_open );
							xCookie.del( "nr_fo_closed" );
						});
					}
					
					// Hide elements by default
					ct.style.display = "none";
					ct.style[ plugin.location ] = '0px';
					fo_open.style.display = "none";
					fo_open.style[ plugin.location ] = '0px';
					
					fo_open.style.bottom = plugin.frombot + plugin.frombottype;
					
					// Setup close button
					if ( fo_close = this.xgebi('nrelate_flyout_close') ) {
						
						// TODO: evaluate moving this to CSS
						fo_close.style.background = "#fff url(http://static.nrelate.com/common_js/close_window.gif) no-repeat 0 0";
						
						this.bind( fo_close, "click", function(){
							plugin.fo_animate_close.call( self, plugin, ct );
							plugin.fo_animate_open.call( self, plugin, ct, fo_open);
							xCookie.set( "nr_fo_closed", "true", 7, '/' );
						});
					}
					
					plugin.offset = args.offset || plugin.offset || 1;
					plugin.element = args.element || plugin.element || null;
					
					// Setup scroll handling
					plugin.hscroll.call( self, null, plugin, ct );
					this.bind( window, "scroll", function( evt ) {
						plugin.hscroll.call( self, evt, plugin, ct );
					});
				},
				
				// Handle scroll event to display the FO
				hscroll : function( evt, plugin, ct ) {
					var open_flyout, fo_open;
					
					// contents not ready
					if ( !nRelate.flyout_show ) {
						return false;
					}
					
					open_flyout = plugin.should_open.call( this, plugin );
					
					// closed with cookie
					if ( xCookie.get('nr_fo_closed') == 'true' ) {
						if ( !( fo_open = this.xgebi( "nrelate_flyout_open" ) ) ) {
							return false;
						}
						
						if ( open_flyout ) {
							plugin.fo_animate_open.call( this, plugin, ct, fo_open);
						} else {
							plugin.fo_animate_close.call( this, plugin, ct, fo_open);
						}
						
						return false;
					}
					
					if ( open_flyout ) {
						plugin.fo_animate_open.call( this, plugin, ct );
					} else {
						plugin.fo_animate_close.call( this, plugin, ct );
					}
				},
				
				fo_animate_open : function( plugin, ct, target ) {
					var xa = new xAnimation(),
						self = this;
					
					target = target || ct;
					
					if ( this.xhc( target, "nr_animating|nr_fo_opened" ) ) {
						return false;
					}
					
					this.xac( target, "nr_animating" );
					this.xrc( target, "nr_fo_opened|nr_fo_closed" );
					
					target.style.display = "block";
					
					if ( plugin.animation == 'slideout' ) {
						
						target.style[ plugin.location ] = '-'+ (this.xWidth(target) + 40) +'px';
						
						xa.css(
							target, //object
							plugin.location, //css propery
							0, //target value
							400, //time for animation
							5, //acceleration type
							0, // bounces
							function() { // onEnd callback
								self.xrc( this.e, "nr_animating");
								self.xac( this.e, "nr_fo_opened");
							}
						);
					} else if ( plugin.animation == 'fade' ) {
						xa.opacity(
							target, //object
							1, //target value
							400, //time for animation
							5, //acceleration type
							0, // bounces
							function() { // onEnd callback
								self.xrc( this.e, "nr_animating");
								self.xac( this.e, "nr_fo_opened");
							}
						);
					}
				},
				
				fo_animate_close : function( plugin, ct, target ) {
					var xa = new xAnimation(),
						self = this;
					
					target = target || ct;
					
					if ( this.xhc( target, "nr_animating|nr_fo_closed" ) ) {
						return false;
					}
					
					this.xac( target, "nr_animating" );
					this.xrc( target, "nr_fo_opened|nr_fo_closed" );
					
					if ( plugin.animation == 'slideout' ) {
						xa.css(
							target, //object
							plugin.location, //css propery
							-1 * ( this.xWidth(target) + 40 ), //target value
							400, //time for animation
							5, //acceleration type
							0, // bounces
							function() { // onEnd callback
								self.xrc( this.e, "nr_animating");
								self.xac( this.e, "nr_fo_closed");
								this.e.style.display = "none";
							}
						);
					} else if ( plugin.animation == 'fade' ) {
						xa.opacity(
							target, //object
							0, //target value
							400, //time for animation
							5, //acceleration type
							0, // bounces
							function() { // onEnd callback
								self.xrc( this.e, "nr_animating");
								self.xac( this.e, "nr_fo_closed");
								this.e.style.display = "none";
							}
						);
					}
				},
				
				// Determine if should open or not
				should_open : function( plugin ) {
					var open_flyout = false,
						scrolled = this.xScrollTop( window, true ),
						viewport_height = this.xClientHeight(),
						doc_size = this.xDocSize();
					
					if ( !plugin.offset_node ) {
						if ( plugin.offset == 4 ) {
							if ( plugin.element[0] == '.' ) {
								plugin.offset_node = this.xgebcn( plugin.element.replace('.', '') )[0];
							} else {
								plugin.offset_node = this.xgebi( plugin.element.replace('#', '') );
							}
							
							if ( !plugin.offset_node ) plugin.offset = 1;
						} else if ( plugin.offset == 3 ) {
							// no need to have a node, it'll use document scrollHeight
							plugin.offset_node = true;
						}
						
						// Probably using offset 1 or 2... or 4 failed finding the node
						if ( !plugin.offset_node ) {
							plugin.offset_node = plugin.fo_faph.call( this, plugin );
						}
						
						// Last resort, use the 50% page scroll
						if ( !plugin.offset_node ) {
							plugin.offset = 0;
							plugin.offset_node = true;
						}
					}
					
					switch( plugin.offset ) {
						case 1:
							open_flyout = scrolled + viewport_height >= this.xPageY( plugin.offset_node ) + ( this.xHeight( plugin.offset_node ) / 2 );
						break;
						
						case 2:
							open_flyout = scrolled + viewport_height >= this.xPageY( plugin.offset_node ) + this.xHeight( plugin.offset_node );
						break;
						
						case 3:
							open_flyout = scrolled + viewport_height >= doc_size.h;
						break;
						
						case 4:
							open_flyout = scrolled + viewport_height >= this.xPageY( plugin.offset_node );
						break;
						
						default:
							open_flyout = scrolled + viewport_height >= ( doc_size.h / 2 )
						break;
					}
					
					return open_flyout;
				},
				
				// Determines if the plugin should be shown on current page
				should_load : function( plugin ) {
					// Don't load on home page
					if ( this.is_home_page ) return false;
					
					// Don't load on mobile browsers
					if ( is_mobile() ) return false;
					
					return true;
				},
				
				// Fetches the article placeholder that will be used to trigger FO open according to window scroll
				fo_faph : function( plugin ) {
					var aph = this.dfph( plugin.fullname )[0];
					this.debug("fo_faph result: %o", aph);
					return aph;
				},
				
				// FO always injects in the body, no need to parse the HTML
				fph	: function( p ) {
					return [ document.body ];
				}
			})
		};
		
		
		
		
		
		//**********************************************
		//
		//		xAnimation class
		//
		//**********************************************
		function xAnimation(r)
		{
		  this.res = r || 10;
		};

		// Initialize an array of n axis objects.
		xAnimation.prototype.axes = function(n)
		{
		  var j, i = this;
		  if (!i.a || i.a.length != n) {
			i.a = [];
			for (j = 0; j < n; ++j) {
			  i.a[j] = { i:0, t:0, d:0, v:0 }; // initial value, target value, displacement, instantaneous value
			}
		  }
		};
		// The caller must set the axes' initial and target values before calling init.
		xAnimation.prototype.init = function(e,t,or,ot,oe,at,b)
		{
		  var ai, i = this;
		  i.e = _nrelate.xgebi(e);
		  i.t = t;
		  i.or = or; // onRun
		  i.ot = ot; // onTarget
		  i.oe = oe; // onEnd
		  i.at = at || 0; // acceleration type
		  i.v = xAnimation.vf[i.at];
		  i.qc = 1 + (b || 0); // quarter-cycles
		  i.fq = 1 / i.t; // frequency
		  if (i.at > 0 && i.at < 4) {
			i.fq *= i.qc * Math.PI;
			if (i.at == 1 || i.at == 2) { i.fq /= 2; }
		  }
		  // displacements
		  for (ai = 0; ai < i.a.length; ++ai) {
			i.a[ai].d = i.a[ai].t - i.a[ai].i;
		  }
		};
		xAnimation.prototype.run = function(r)
		{
		  var ai, qcm2, rep, i = this;
		  if (!r) { i.t1 = new Date().getTime(); }
		  if (!i.tmr) i.tmr = setInterval(
			function() {
			  i.et = new Date().getTime() - i.t1; // elapsed time
			  if (i.et < i.t) {
				// instantaneous values
				i.f = i.v(i.et * i.fq);
				for (ai = 0; ai < i.a.length; ++ai) {
				  i.a[ai].v = i.a[ai].d * i.f + i.a[ai].i;
				}
				i.or(i); // call onRun
			  }
			  else { // target time reached
				clearInterval(i.tmr);
				i.tmr = null;
				qcm2 = i.qc % 2;
				for (ai = 0; ai < i.a.length; ++ai) {
				  if (qcm2) { i.a[ai].v = i.a[ai].t; }
				  else { i.a[ai].v = i.a[ai].i; }
				}
				i.ot(i); // call onTarget
				// handle onEnd
				rep = false;
				if (typeof i.oe == 'function') { rep = i.oe(i); }
				else if (typeof i.oe == 'string') { rep = eval(i.oe); }
				if (rep) { i.resume(1); }
			  }
			}, i.res
		  );
		};
		xAnimation.prototype.pause = function()
		{
		  clearInterval(this.tmr);
		  this.tmr = null;
		};
		xAnimation.prototype.resume = function(fs)
		{
		  if (typeof this.tmr != 'undefined' && !this.tmr) {
			this.t1 = new Date().getTime();
			if (!fs) {this.t1 -= this.et;}
			this.run(!fs);
		  }
		};
		xAnimation.prototype.css = function(e,p,v,t,a,b,oe)
		{
		  var i = this;
		  i.axes(1);
		  i.a[0].i = _nrelate.xGetComputedStyle(e,p,true); // initial value
		  i.a[0].t = v; // target value
		  i.prop = _nrelate.xCamelize(p);
		  i.init(e,t,h,h,oe,a,b);
		  i.run();
		  function h(i) {i.e.style[i.prop] = Math.round(i.a[0].v) + 'px';}
		};
		xAnimation.prototype.opacity = function(e,o,t,a,b,oe)
		{
		  var i = this;
		  i.axes(1);
		  i.a[0].i = _nrelate.xOpacity(e); i.a[0].t = o; // initial and target opacity
		  i.init(e,t,h,h,oe,a,b);
		  i.run();
		  function h(i) {_nrelate.xOpacity(i.e, i.a[0].v);} // onRun and onTarget
		};
		// Static array of velocity functions
		xAnimation.vf = [
		  function(r){return r;},
		  function(r){return Math.abs(Math.sin(r));},
		  function(r){return 1-Math.abs(Math.cos(r));},
		  function(r){return (1-Math.cos(r))/2;},
		  function(r) {return (1.0 - Math.exp(-r * 6));},
		  // 'swing' easing from jQuery
		  function(r) {return ( -Math.cos( r*Math.PI ) / 2 ) + 0.5}
		];
		// end xAnimation
		
		
		
		
		
		
		//**********************************************
		//
		//		xCookie object
		//
		//**********************************************
		var xCookie = {
			get: function( name ) {
				var c = document.cookie.match( new RegExp('(^|;)\\s*' + name + '=([^;\\s]*)') );
				return ( (c && c.length >= 3 ) ? unescape( c[2] ) : null);  
			},
			set: function( name, value, days, path, domain, secure ) {
				if (days) {
					var d = new Date();
					d.setTime(d.getTime() + (days * 8.64e7));
				}
				document.cookie = 	name + '=' + escape(value) +
									( days ? ('; expires=' + d.toGMTString()) : '' ) +
									'; path=' + ( path || '/' ) +
									( domain ? ('; domain=' + domain) : '' ) +
									( secure ? '; secure' : '' );
			},
			del: function(name, path, domain) {
				this.set(name, '', -1, path, domain);
			}
		};
		// end xCookie
		
		
		
		
		
		
		//**********************************************
		//
		//		mDialog object
		//
		//**********************************************
		function mDialog( sDialogId )
		{
			this.dialog = _nrelate.xgebi( sDialogId );
			if ( !this.dialog ) return false;
			
			mDialog.instances[sDialogId] = this;
			
			var dcont = document.createElement("div");
			dcont.className = "nr_dialog";
			dcont.style.position = "absolute";
			dcont.style.zIndex = 9000;
			dcont.style.top = 0;
			dcont.style.left = "-9999px";
			dcont.style.width = "550px";
			dcont.style.padding = "30px 10px 5px";
			dcont.style.backgroundColor = "#fff";
			dcont.style.border = "solid 1px #333";
			
			var closebtn = document.createElement("img");
			closebtn.src = "http://static.nrelate.com/common_js/close_window.gif";
			closebtn.title = "Close dialog";
			closebtn.style.cursor = "pointer";
			closebtn.style.position = "absolute";
			closebtn.style.top = "5px";
			closebtn.style.right = "5px";
			_nrelate.bind( closebtn, "click", function( evt ){
				mDialog.instances[sDialogId].hide();
			});
			
			dcont.appendChild( closebtn );
			dcont.appendChild( this.dialog.parentNode.removeChild( this.dialog ) );
			this.dialog = dcont;
			document.body.appendChild( this.dialog );
			
			var e = mDialog.grey;
			if (!e) {
				e = document.createElement('div');
				e.className = 'nr_dialog_overlay';
				e.style.position = "fixed";
				e.style.overflow = "hidden";
				e.style.zIndex = "9000";
				e.style.opacity = 0.3;
				e.style.filter = 'alpha(opacity=30)';
				e.style.backgroundColor = "#000";
				mDialog.grey = document.body.appendChild(e);
				
				_nrelate.bind( e, "click", function(){
					var i, instance;
					for ( i in mDialog.instances ) {
						mDialog.instances[i].hide();
					}
				});
			}
			
			_nrelate.bind( window, "resize", function(){
				var i, instance;
				for ( i in mDialog.instances ) {
					if ( mDialog.instances[i].is_opened ) {
						mDialog.instances[i].center();
					}
				}
			});
		}
		mDialog.prototype.show = function()
		{
			var e = mDialog.grey;
			
			this.is_opened = true;
			
			if (e) {
				this.dialog.greyZIndex = _nrelate.xGetComputedStyle(e, 'z-index', 1);
				e.style.zIndex = _nrelate.xGetComputedStyle(this.dialog, 'z-index', 1) - 1;
			}
			if ( this.dialog ) {
				this.center();
			}
		};
		mDialog.prototype.center = function() {
			var width = _nrelate.xClientWidth(),
				height = _nrelate.xClientHeight(),
				e = mDialog.grey;
			
			if ( !_nrelate.is_defined( this.dialog.max_dimensions ) ) {
				this.dialog.max_dimensions = {
					width : this.dialog.offsetWidth - 30,
					height : this.dialog.offsetHeight
				};
			}
			
			this.dialog.style.width = Math.min( width - 30, this.dialog.max_dimensions.width ) + "px";
			
			this.dialog.style.left = Math.max( 0, ( (width-this.dialog.offsetWidth)/2 ) ) + "px";
			this.dialog.style.top = Math.max( 0, ( _nrelate.xScrollTop()+(height-this.dialog.offsetHeight)/2) ) + "px";
			
			if ( e ) {
				e.style.left = "0px";
				e.style.top = "0px";
				e.style.width = width + "px";
				e.style.height = height + "px";
			}
		};
		mDialog.prototype.hide = function( dialogOnly )
		{
			var e = mDialog.grey;
			
			this.is_opened = false;
			
			if (e) {
				if (!dialogOnly) {
					e.style.left = "-10px";
					e.style.top = "-10px";
					e.style.width = "10px";
					e.style.height = "10px";
				}
				if (this.dialog) {
					e.style.zIndex = this.dialog.greyZIndex;
					this.dialog.style.left = -this.dialog.offsetWidth + "px";
					this.dialog.style.top = "0px";
				}
			}
		};
		mDialog.grey = null;
		mDialog.instances = {};
		// end mDialog
		
		
		
		//**********************************************
		//
		//		Mobile browsers detection
		//		from http://detectmobilebrowsers.com/
		//
		//**********************************************
		function is_mobile() {
			var a = navigator.userAgent||navigator.vendor||window.opera,
				m = /android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4));
			
			return m;
		}
		
		
		
		

		//**********************************************
		//
		//		nRelate public object
		//
		//**********************************************

		return {
			/**
			 * Public methods
			 */
			setup : function() {
				return _nrelate.init.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			bind_dom_ready : function() {
				return _nrelate.bdr.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			acs : function() {
				return _nrelate.acs.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			sw : function() {
				return _nrelate.sw.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			prc : function() {
				return _nrelate.prc.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			fah : function() {
				return _nrelate.fah.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			
			/**
			 * Public properties
			 */
			auto_invoke : _nrelate.options.auto_invoke,
			
			/**
			 * Public helper methods
			 * These are exposed to ease debug
			 */
			xgebi : function() {
				return _nrelate.xgebi.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			xgebcn : function() {
				return _nrelate.xgebcn.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			xgeba : function() {
				return _nrelate.xgeba.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			xgebtn : function() {
				return _nrelate.xgebtn.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			xrc : function() {
				return _nrelate.xrc.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			parse_json : function() {
				return _nrelate.parse_json.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			get_data_attr : function() {
				return _nrelate.get_data_attr.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			lr : function() {
				return _nrelate.lr.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			debug : function() {
				return _nrelate.debug.apply( _nrelate, Array.prototype.slice.call(arguments) );
			},
			
			/**
			 * Public cookies handler
			 */
			cookie : xCookie
		};
	
	// Execute to initialize nRelate public object
	}();
	
	/**
	 * By default nRelate automatically executes itself
	 * using the built-in configuration.
	 *
	 * If the user wants to override the configuration
	 * var nr_manual = true; needs to be  executed before
	 * loading this script to avoid the automatic execution
	 */
	if ( nRelate.auto_invoke ) {

		// Check if there are inline configuration options
		var script_tag, inline_options;
		if ( (script_tag = nRelate.xgebi("nrelate_loader_script")) && (inline_options = nRelate.parse_json(nRelate.get_data_attr( script_tag, "nrelate-options" ))) ) {
			nRelate.debug("Inline options found: %o", inline_options);
		} else {
			inline_options = {};
		}

		nRelate.setup( inline_options );
	}
	
	// Always detect domReady
	nRelate.bind_dom_ready();
}