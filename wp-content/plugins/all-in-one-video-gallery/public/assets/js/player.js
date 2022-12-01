(function( $ ) {
	'use strict';
	
	// Vars
	var aiovg_autoplay_allowed = false;
	var aiovg_autoplay_requires_muted = false;
	var aiovg_players = [];	

	/**
	 * Convert SRT to WebVTT.
	 *
	 * @since 2.6.3
	 */
	 function aiovg_srt_to_webvtt( data ) {
		// Remove dos newlines
		var srt = data.replace( /\r+/g, '' );

		// Trim white space start and end
		srt = srt.replace( /^\s+|\s+$/g, '' );

		// Get cues
		var cuelist = srt.split( '\n\n' );
		var result = "";

		if ( cuelist.length > 0 ) {
		  result += "WEBVTT\n\n";
		  for ( var i = 0; i < cuelist.length; i = i+1 ) {
			  result += aiovg_convert_srt_cue( cuelist[ i ] );
		  }
		}

		return result;
  	}

  	function aiovg_convert_srt_cue( caption ) {
		// Remove all html tags for security reasons
		// srt = srt.replace( /<[a-zA-Z\/][^>]*>/g, '' );

		var cue = "";
		var s = caption.split( /\n/ );

		// Concatenate muilt-line string separated in array into one
		while ( s.length > 3 ) {
			for ( var i = 3; i < s.length; i++ ) {
				s[2] += "\n" + s[ i ]
			}
			s.splice( 3, s.length - 3 );
		}

		var line = 0;

		// Detect identifier
		if ( ! s[0].match( /\d+:\d+:\d+/ ) && s[1].match( /\d+:\d+:\d+/ ) ) {
		  cue += s[0].match( /\w+/ ) + "\n";
		  line += 1;
		}

		// Get time strings
		if ( s[ line ].match( /\d+:\d+:\d+/ ) ) {
		  // Convert time string
		  var m = s[1].match( /(\d+):(\d+):(\d+)(?:,(\d+))?\s*--?>\s*(\d+):(\d+):(\d+)(?:,(\d+))?/ );
		  if ( m ) {
				cue += m[1] + ":" + m[2] + ":" + m[3] + "." + m[4] + " --> " + m[5] + ":" + m[6] + ":" + m[7] + "." + m[8] + "\n";
				line += 1;
		  } else {
				// Unrecognized timestring
				return "";
		  }
		} else {
		  // File format error or comment lines
		  return "";
		}

		// Get cue text
		if ( s[ line ] ) {
		  cue += s[ line ] + "\n\n";
		}

		return cue;
  	}

	async function aiovg_file_get_contents( track, callback ) {
		let res = await fetch( track.src );
		track.text = await res.text();
		return callback ? callback( track ) : track; // a Promise() actually.
	}

	/**
	 * Check unmuted autoplay support.
	 *
	 * @since 2.4.0
	 */
	function aiovg_check_unmuted_autoplay_support() {
		canAutoplay
			.video({ timeout: 100, muted: false })
			.then(function( response ) {
				if ( response.result === false ) {
					// Unmuted autoplay is not allowed
					aiovg_check_muted_autoplay_support();
				} else {
					// Unmuted autoplay is allowed
					aiovg_autoplay_allowed = true;
					aiovg_init_player();
				}
			});
	}

	/**
	 * Check muted autoplay support.
	 *
	 * @since 2.4.0
	 */
	function aiovg_check_muted_autoplay_support() {
		canAutoplay
			.video({ timeout: 100, muted: true })
			.then(function( response ) {
				if ( response.result === false ) {
					// Muted autoplay is not allowed
					aiovg_autoplay_allowed = false;
				} else {
					// Muted autoplay is allowed
					aiovg_autoplay_allowed = true;
					aiovg_autoplay_requires_muted = true;					
				};
				
				aiovg_init_player();
			});
	}

	/**
	 * Update video views count.
	 *
	 * @since 2.4.0
	 * @param {Object} settings The settings array.
	 */
	function aiovg_update_views_count( settings ) {
		if ( 'aiovg_videos' == settings.post_type ) {
			var data = {
				'action': 'aiovg_update_views_count',
				'post_id': settings.post_id,
				'security': settings.views_nonce
			};

			$.post( 
				aiovg_player.ajax_url, 
				data, 
				function( response ) {
					// Do nothing
				}
			);
		}
	}

	/**
	 * jQuery Plugin: aiovg_player
	 *
	 * @since 2.4.0
	 */
	$.fn.aiovg_player = function() {
		// Vars
		var $elem    = $( this );
		var id       = $elem.data( 'id' );
		var settings = window[ 'aiovg_player_' + id ];
		var player   = null;

		// GDPR consent
		var gdpr_consent = function() {		
			var data = {
				'action': 'aiovg_set_cookie',
				'security': aiovg_player.ajax_nonce
			};

			$.post( 
				aiovg_player.ajax_url, 
				data, 
				function( response ) {
					if ( response.success ) {
						init_player();
						$elem.find( '.aiovg-privacy-wrapper' ).remove();
					}
				}
			);
		}

		// Init player
		var init_player = function() {
			// Is iframe?
			if ( 'iframe' == settings.type ) {
				$( '#aiovg-player-' + id ).replaceWith( '<iframe width="560" height="315" src="' + settings.iframe_src + '" frameborder="0" scrolling="no" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>' );
				aiovg_update_views_count( settings );
			} else {
				player = videojs( 'aiovg-player-' + id, settings.player );
				
				settings.html5 = {
					vhs: {
						overrideNative: ! videojs.browser.IS_ANY_SAFARI,
					}
				};

				var overlays = [];
				
				// Trigger ready event
				var config = {
					player: player,
					id: id,
					settings: settings					
				};

				$elem.trigger( 'player.init', config );

				// Fired when the player is ready
				player.ready(function() {
					$elem.removeClass( 'vjs-waiting' );
					aiovg_players.push( player );
				});

				// Add support for SRT
				player.one( 'loadedmetadata', function() {
					if ( settings.hasOwnProperty( 'tracks' ) ) {
						for ( var i = 0, max = settings.tracks.length; i < max; i++ ) {
							var track = settings.tracks[ i ];

							if ( /srt/.test( track.src.toLowerCase() ) ) {
								aiovg_file_get_contents( track, function( track ) {
									var vtt_text = aiovg_srt_to_webvtt( track.text );
									var vtt_blob = new Blob([ vtt_text ], { type : 'text/vtt' });
									var blob_url = URL.createObjectURL( vtt_blob );

									var track_obj = {
										src: blob_url,
										srclang: track.srclang,
										label: track.label,
										kind: 'subtitles'
									};

									if ( 1 == settings.cc_load_policy && 0 == i ) {
										track_obj.mode = 'showing';
									}

									player.addRemoteTextTrack( track_obj, true ); 
								});
							} else {
								var track_obj = {
									src: track.src,
									srclang: track.srclang,
									label: track.label,
									kind: 'subtitles'
								};

								if ( 1 == settings.cc_load_policy && 0 == i ) {
									track_obj.mode = 'showing';
								}

								player.addRemoteTextTrack( track_obj, true ); 
							}					               
						}
					}              
				});

				// Fired the first time a video is played
				var viewed = false;

				player.on( 'play', function( e ) {
					if ( ! viewed ) {
						viewed = true;
						aiovg_update_views_count( settings );
					}
					
					// Determine which player the event is coming from
					var id = e.target.id;
					if ( id.indexOf( '_' ) !== -1 ) {
						id = id.split( '_' );
						id = id[0];
					}

					// Loop through the array of players
					for ( var i = 0; i < aiovg_players.length; i++ ) {
						// Get the player(s) that did not trigger the play event
						if ( aiovg_players[ i ].id() != id ) {
							// Pause the other player(s)
							videojs( aiovg_players[ i ].id() ).pause();
						}
					}
				});

				player.on( 'playing', function() {
					player.trigger( 'controlsshown' );
				});
	
				player.on( 'ended', function() {
					player.trigger( 'controlshidden' );
				});

				// Offset
				var offset = {};

				if ( settings.start ) {
					offset.start = settings.start;
				}

				if ( settings.end ) {
					offset.end = settings.end;
				}
				
				if ( Object.keys( offset ).length > 1 ) {
					offset.restart_beginning = false;
					player.offset( offset );
				}				

				// Share / Embed
				if ( settings.share || settings.embed ) {
					overlays.push({
						content: '<a href="javascript:void(0)" class="vjs-share-embed-button" style="text-decoration:none;"><span class="vjs-icon-share"></span></a>',
						class: 'vjs-share',
						align: 'top-right',
						start: 'controlsshown',
						end: 'controlshidden',
						showBackground: false					
					});					
				}

				// Download
				if ( settings.download ) {
					var __class = 'vjs-download';

					if ( settings.share || settings.embed ) {
						__class += ' vjs-has-share';
					}

					overlays.push({
						content: '<a href="' + settings.download_url + '" class="vjs-download-button" style="text-decoration:none;" target="_blank"><span class="aiovg-icon-download"></span></a>',
						class: __class,
						align: 'top-right',
						start: 'controlsshown',
						end: 'controlshidden',
						showBackground: false					
					});
				}

				// Logo
				if ( settings.show_logo ) {
					init_logo( overlays );
				}

				// Overlay
				if ( overlays.length > 0 ) {
					player.overlay({
						content: '',
						overlays: overlays
					});

					if ( settings.share || settings.embed ) {
						var options = {};
						options.content = $elem.find( '.vjs-share-embed' ).get(0);
						options.temporary = false;
	
						var ModalDialog = videojs.getComponent( 'ModalDialog' );
						var modal = new ModalDialog( player, options );
						modal.addClass( 'vjs-modal-dialog-share-embed' );
	
						player.addChild( modal );
	
						var wasPlaying = true;
						$elem.find( '.vjs-share-embed-button' ).on( 'click', function() {
							wasPlaying = ! player.paused;
							modal.open();						
						});
	
						modal.on( 'modalclose', function() {
							if ( wasPlaying ) {
								player.play();
							}						
						});
					}
	
					if ( settings.embed ) {
						$elem.find( '.vjs-copy-embed-code' ).on( 'focus', function() {
							$( this ).select();	
							document.execCommand( 'copy' );					
						});
					}
				}

				// Custom contextmenu
				if ( settings.copyright_text ) {
					init_contextmenu();
				}
			}
		}

		// Merge attributes
		var merge_attributes = function( attributes ) {
			var str = '';

			for ( var key in attributes ) {
				str += ( key + '="' + attributes[ key ] + '" ' );
			}
	
			return str;
		}

		// Logo overlay
		var init_logo = function( overlays ) {
			var attributes = [];
			attributes['src'] = settings.logo_image;

			if ( settings.logo_margin ) {
				settings.logo_margin = settings.logo_margin - 5;
			}

			var align;
			switch ( settings.logo_position ) {
				case 'topleft':
					align = 'top-left';
					attributes['style'] = 'margin: ' + settings.logo_margin + 'px;';
					break;
				case 'topright':
					align = 'top-right';
					attributes['style'] = 'margin: ' + settings.logo_margin + 'px;';
					break;					
				case 'bottomright':
					align = 'bottom-right';
					attributes['style'] = 'margin: ' + settings.logo_margin + 'px;';
					break;
				default:						
					align = 'bottom-left';
					attributes['style'] = 'margin: ' + settings.logo_margin + 'px;';
					break;					
			}

			if ( settings.logo_link ) {
				attributes['onclick'] = "window.location.href='" + settings.logo_link + "';";
			}

			overlays.push({
				content: '<img ' +  merge_attributes( attributes ) + ' alt="" />',
				class: 'vjs-logo',
				align: align,
				start: 'controlsshown',
				end: 'controlshidden',
				showBackground: false					
			});
		}

		// Custom contextmenu
		var init_contextmenu = function() {
			if ( ! $( '#aiovg-contextmenu' ).length ) {
				$( 'body' ).append( '<div id="aiovg-contextmenu" style="display: none;"><div id="aiovg-contextmenu-item">' + settings.copyright_text + '</div></div>' );
			}

			var contextmenu = document.getElementById( 'aiovg-contextmenu' );
			var timeout_handler = '';
			
			$( '#aiovg-player-' + id ).on( 'contextmenu', function( e ) {						
				if ( 3 === e.keyCode || 3 === e.which ) {
					e.preventDefault();
					e.stopPropagation();
					
					var width = contextmenu.offsetWidth,
						height = contextmenu.offsetHeight,
						x = e.pageX,
						y = e.pageY,
						doc = document.documentElement,
						scrollLeft = ( window.pageXOffset || doc.scrollLeft ) - ( doc.clientLeft || 0 ),
						scrollTop = ( window.pageYOffset || doc.scrollTop ) - ( doc.clientTop || 0 ),
						left = x + width > window.innerWidth + scrollLeft ? x - width : x,
						top = y + height > window.innerHeight + scrollTop ? y - height : y;
			
					contextmenu.style.display = '';
					contextmenu.style.left = left + 'px';
					contextmenu.style.top = top + 'px';
					
					clearTimeout( timeout_handler );

					timeout_handler = setTimeout(function() {
						contextmenu.style.display = 'none';
					}, 1500 );				
				}														 
			});
			
			document.addEventListener( 'click', function() {
				contextmenu.style.display = 'none';								 
			});
		}

		// Init
    	this.init = function() {
			// Autoplay
			if ( 'iframe' != settings.type ) {
				if ( settings.player.autoplay ) {
					settings.player.autoplay = aiovg_autoplay_allowed;

					if ( aiovg_autoplay_requires_muted ) {
						settings.player.muted = true;
					}
				}
			}

			// Init player
			if ( settings.show_consent ) {
				$elem.find( '.aiovg-privacy-consent-button' ).on( 'click', function() {
					$( this ).html( '...' );

					if ( 'iframe' != settings.type ) {
						settings.player.autoplay = true;
					}

					gdpr_consent();
				});
			} else {
				init_player();
			}			
		}

		// ...
		return this.init();
	}

	/**
	 * Init player.
	 *
	 * @since 2.4.0
	 */
	function aiovg_init_player() {
		$( '.aiovg-player-standard' ).each(function() {
			$( this ).aiovg_player();
		});
	}

	/**
	 * Called when the page has loaded.
	 *
	 * @since 1.0.0
	 */
	$(function() {
		
		// Init Player
		if ( typeof canAutoplay === 'undefined' ) {
			aiovg_init_player();
		} else {
			aiovg_check_unmuted_autoplay_support();
		}

		// Update views count for the non-iframe embeds
		$( '.aiovg-player-raw' ).each(function() {
			var $elem    = $( this );
			var id       = $elem.data( 'id' );
			var settings = window[ 'aiovg_player_' + id ];

			aiovg_update_views_count( settings );
		});

	});

})( jQuery );
