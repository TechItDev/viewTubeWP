<?php

/**
 * Video Player: Video.js.
 *
 * @link     https://plugins360.com
 * @since    2.0.0
 *
 * @package All_In_One_Video_Gallery
 */

$settings = array(
	'controlBar'    => array(),
	'autoplay'      => isset( $_GET['autoplay'] ) ? (int) $_GET['autoplay'] : (int) $player_settings['autoplay'],
	'muted'         => isset( $_GET['muted'] ) ? (int) $_GET['muted'] : (int) $player_settings['muted'],
	'playbackRates' => array( 0.5, 0.75, 1, 1.5, 2 ),
	'aiovg'         => array(
		'postID'           => $post_id,
		'postType'         => $post_type,
		'share'            => isset( $_GET['share'] ) ? (int) $_GET['share'] : isset( $player_settings['controls']['share'] ),	
		'embed'            => isset( $_GET['embed'] ) ? (int) $_GET['embed'] : isset( $player_settings['controls']['embed'] ),
		'download'         => 0,
		'cc_load_policy'   => isset( $_GET['cc_load_policy'] ) ? (int) $_GET['cc_load_policy'] : (int) $player_settings['cc_load_policy'],	
		'showLogo'         => 0,
		'contextmenuLabel' => ''
	)
);

// Video Sources
$sources = array();
$allowed_types = array( 'mp4', 'webm', 'ogv', 'youtube', 'vimeo', 'dailymotion', 'facebook' );

if ( ! empty( $post_meta ) ) {
	$type = $post_meta['type'][0];

	if ( 'default' == $type ) {
		$types = array( 'mp4', 'webm', 'ogv' );

		foreach ( $types as $type ) {
			if ( ! empty( $post_meta[ $type ][0] ) ) {
				$ext   = $type;
				$label = '';

				if ( 'mp4' == $type ) {
					$ext = aiovg_get_file_ext( $post_meta[ $type ][0] );
					if ( ! in_array( $ext, array( 'webm', 'ogv' ) ) ) {
						$ext = 'mp4';
					}

					if ( ! empty( $post_meta['quality_level'][0] ) ) {
						$label = $post_meta['quality_level'][0];
					}
				}

				$sources[ $type ] = array(
					'type'  => "video/{$ext}",
					'src'   => $post_meta[ $type ][0],
					'label' => $label
				);
			}
		}

		if ( ! empty( $post_meta['sources'][0] ) ) {
			$_sources = unserialize( $post_meta['sources'][0] );

			foreach ( $_sources as $source ) {
				if ( ! empty( $source['quality'] ) && ! empty( $source['src'] ) ) {	
					$ext = aiovg_get_file_ext( $source['src'] );
					if ( ! in_array( $ext, array( 'webm', 'ogv' ) ) ) {
						$ext = 'mp4';
					}

					$label = $source['quality'];

					$sources[ $label ] = array(
						'type'  => "video/{$ext}",
						'src'   => $source['src'],
						'label' => $label
					);
				}
			}
		}
	} else {
		if ( in_array( $type, $allowed_types ) && ! empty( $post_meta[ $type ][0] ) ) {
			$src = $post_meta[ $type ][0];

			$sources[ $type ] = array(
				'type' => "video/{$type}",
				'src'  => $src
			);
		}
	}
} else {
	foreach ( $allowed_types as $type ) {		
		if ( isset( $_GET[ $type ] ) && ! empty( $_GET[ $type ] ) ) {
			$src = aiovg_sanitize_url( $_GET[ $type ] );

			$sources[ $type ] = array(
				'type' => "video/{$type}",
				'src'  => $src
			);
		}	
	}
}

$sources = apply_filters( 'aiovg_video_sources', $sources );

// Video Tracks
$tracks_enabled = isset( $_GET['tracks'] ) ? (int) $_GET['tracks'] : isset( $player_settings['controls']['tracks'] );
$tracks = array();

if ( $tracks_enabled && ! empty( $post_meta['track'] ) ) {
	foreach ( $post_meta['track'] as $track ) {
		$tracks[] = unserialize( $track );
	}
	
	$has_srt_found = 0;

	foreach ( $tracks as $index => $track ) {
        $ext = pathinfo( $track['src'], PATHINFO_EXTENSION );
        if ( 'srt' == strtolower( $ext ) ) {
            $has_srt_found = 1;			
			break;
        }
    }

	if ( $has_srt_found ) {
		$settings['aiovg']['tracks'] = $tracks;
		$tracks = array();
	}
}

$tracks = apply_filters( 'aiovg_video_tracks', $tracks );

// Video Attributes
$attributes = array( 
	'id'          => 'player',
	'style'       => 'width: 100%; height: 100%;',
	'controls'    => '',
	'playsinline' => ''
);

$attributes['preload'] = esc_attr( $player_settings['preload'] );

$loop = isset( $_GET['loop'] ) ? (int) $_GET['loop'] : (int) $player_settings['loop'];
if ( $loop ) {
	$attributes['loop'] = true;
}

if ( isset( $_GET['poster'] ) ) {
	$attributes['poster'] = $_GET['poster'];
} elseif ( ! empty( $post_meta ) ) {
	$image_data = aiovg_get_image( $post_id, 'large' );
	$attributes['poster'] = $image_data['src'];
}

if ( ! empty( $attributes['poster'] ) ) {
	$attributes['poster'] = aiovg_sanitize_url( aiovg_resolve_url( $attributes['poster'] ) );
} else {
	unset( $attributes['poster'] );
}

$attributes = apply_filters( 'aiovg_video_attributes', $attributes );

// Player Settings
$controls = array( 
	'playpause'  => 'PlayToggle', 
	'current'    => 'CurrentTimeDisplay', 
	'progress'   => 'progressControl', 
	'duration'   => 'durationDisplay',
	'tracks'     => 'SubtitlesButton',
	'audio'      => 'AudioTrackButton',
	'quality'    => 'qualitySelector',
	'speed'      => 'PlaybackRateMenuButton',  
	'volume'     => 'VolumePanel', 
	'fullscreen' => 'fullscreenToggle'
);

foreach ( $controls as $index => $control ) {
	$enabled = isset( $_GET[ $index ] ) ? (int) $_GET[ $index ] : isset( $player_settings['controls'][ $index ] );

	if ( $enabled && 'tracks' == $index ) {
		$player_settings['controls']['audio'] = 1;
	}

	if ( ! $enabled ) {	
		unset( $controls[ $index ] );	
	}	
}

$settings['controlBar']['children'] = array_values( $controls );
if ( empty( $settings['controlBar']['children'] ) ) {
	$attributes['class'] = 'vjs-no-control-bar';
}

if ( isset( $sources['youtube'] ) ) {
	$settings['techOrder'] = array( 'youtube' );
	$settings['youtube']   = array( 
		'iv_load_policy' => 3 
	);

	parse_str( $sources['youtube']['src'], $queries );

	if ( isset( $queries['start'] ) ) {
		$settings['aiovg']['start'] = (int) $queries['start'];
	}

	if ( isset( $queries['t'] ) ) {
		$settings['aiovg']['start'] = (int) $queries['t'];
	}

	if ( isset( $queries['end'] ) ) {
		$settings['aiovg']['end'] = (int) $queries['end'];
	}
}

if ( isset( $sources['vimeo'] ) ) {
	$settings['techOrder'] = array( 'vimeo2' );

	if ( strpos( $sources['vimeo']['src'], 'player.vimeo.com' ) !== false ) {
		$oembed = aiovg_get_vimeo_oembed_data( $sources['vimeo']['src'] );
        $sources['vimeo']['src'] = 'https://vimeo.com/' . $oembed['video_id'];
	}
}

if ( isset( $sources['dailymotion'] ) ) {
	if ( empty( $attributes['poster'] ) ) {
		$settings['bigPlayButton'] = false;
	}
	
	$settings['techOrder'] = array( 'dailymotion' );
}

if ( isset( $sources['facebook'] ) ) {
	if ( empty( $attributes['poster'] ) ) {
		$settings['bigPlayButton'] = false;
	}
	$settings['autoplay'] = 0;
	$settings['techOrder'] = array( 'facebook' );

	$sources['facebook']['src'] = add_query_arg( 'nocache', rand(), $sources['facebook']['src'] );
}

// Share
if ( ! empty( $settings['aiovg']['share'] ) ) {
	$socialshare_settings = get_option( 'aiovg_socialshare_settings' );

	$share_url = get_permalink( $post_id );

	$share_title = get_the_title( $post_id );
	$share_title = str_replace( ' ', '%20', $share_title );
	$share_title = str_replace( '|', '%7C', $share_title );

	$share_image = isset( $attributes['poster'] ) ? $attributes['poster'] : '';

	$share_buttons = array();
		
	if ( isset( $socialshare_settings['services']['facebook'] ) ) {
		$share_buttons[] = array(
			'service'   => 'facebook',
			'url'       => "https://www.facebook.com/sharer/sharer.php?u={$share_url}",
			'iconClass' => 'vjs-icon-facebook'				
		);
	}

	if ( isset( $socialshare_settings['services']['twitter'] ) ) {
		$share_buttons[] = array(
			'service'   => 'twitter',			
			'url'       => "https://twitter.com/intent/tweet?text={$share_title}&amp;url={$share_url}",
			'iconClass' => 'vjs-icon-twitter'
		);
	}		

	if ( isset( $socialshare_settings['services']['linkedin'] ) ) {
		$share_buttons[] = array(	
			'service'   => 'linkedin',		
			'url'       => "https://www.linkedin.com/shareArticle?url={$share_url}&amp;title={$share_title}",
			'iconClass' => 'vjs-icon-linkedin'
		);
	}

	if ( isset( $socialshare_settings['services']['pinterest'] ) ) {
		$pinterest_url = "https://pinterest.com/pin/create/button/?url={$share_url}&amp;description={$share_title}";

		if ( ! empty( $share_image ) ) {
			$pinterest_url .= "&amp;media={$share_image}";
		}

		$share_buttons[] = array(
			'service'   => 'pinterest',			
			'url'       => $pinterest_url,
			'iconClass' => 'vjs-icon-pinterest'
		);
	}

	if ( isset( $socialshare_settings['services']['tumblr'] ) ) {
		$tumblr_url = "https://www.tumblr.com/share/link?url={$share_url}&amp;name={$share_title}";

		$share_description = aiovg_get_excerpt( $post_id, 160, '', false ); 
		if ( ! empty( $share_description ) ) {
			$share_description = str_replace( ' ', '%20', $share_description );
			$share_description = str_replace( '|', '%7C', $share_description );	

			$tumblr_url .= "&amp;description={$share_description}";
		}

		$share_buttons[] = array(
			'service'   => 'tumblr',			
			'url'       => $tumblr_url,
			'iconClass' => 'vjs-icon-tumblr'
		);
	}

	if ( isset( $socialshare_settings['services']['whatsapp'] ) ) {
		if ( wp_is_mobile() ) {
			$whatsapp_url = "whatsapp://send?text={$share_title} " . rawurlencode( $share_url );
		} else {
			$whatsapp_url = "https://api.whatsapp.com/send?text={$share_title}&nbsp;{$share_url}";
		}

		$share_buttons[] = array(	
			'service'   => 'whatsapp',		
			'url'       => $whatsapp_url,
			'iconClass' => 'aiovg-icon-whatsapp'
		);
	}

	$settings['aiovg']['shareButtons'] = apply_filters( 'aiovg_player_socialshare_buttons', $share_buttons );
}

// Embed
if ( ! empty( $settings['aiovg']['embed'] ) ) {
	$protocol = ( ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) ? 'https://' : 'http://';
    $current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	$settings['aiovg']['embedCode'] = sprintf(
		'<div style="position:relative;padding-bottom:%s;height:0;overflow:hidden;"><iframe src="%s" style="width:100%%;height:100%%;position:absolute;left:0px;top:0px;overflow:hidden" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>',
		( isset( $_GET['ratio'] ) ? (float) $_GET['ratio'] : (float) $player_settings['ratio'] ) . '%',
		esc_url( $current_url )
	);
}

// Download
if ( isset( $sources['mp4'] ) ) {
	$settings['aiovg']['download'] = isset( $player_settings['controls']['download'] );
	$download_url = '';

	if ( ! empty( $post_meta ) ) {
		if ( isset( $post_meta['download'] ) && empty( $post_meta['download'][0] ) ) {
			$settings['aiovg']['download'] = 0;
		}

		$download_url = home_url( '?vdl=' . $post_id );
	}

	if ( isset( $_GET['download'] ) ) {
		$settings['aiovg']['download'] = (int) $_GET['download'];
	}

	if ( ! empty( $settings['aiovg']['download'] ) ) {
		if ( empty( $download_url ) ) {
			$download_url = home_url( '?vdl=' . aiovg_get_temporary_file_download_id( $sources['mp4']['src'] ) );
		}

		$settings['aiovg']['downloadUrl'] = esc_url( $download_url );
	}
}

// Logo
if ( ! empty( $brand_settings ) ) {
	$settings['aiovg']['showLogo'] = ! empty( $brand_settings['logo_image'] ) ? (int) $brand_settings['show_logo'] : 0;
	$settings['aiovg']['logoImage'] = aiovg_sanitize_url( aiovg_resolve_url( $brand_settings['logo_image'] ) );
	$settings['aiovg']['logoLink'] = esc_url_raw( $brand_settings['logo_link'] );
	$settings['aiovg']['logoPosition'] = sanitize_text_field( $brand_settings['logo_position'] );
	$settings['aiovg']['logoMargin'] = (int) $brand_settings['logo_margin'];
	$settings['aiovg']['contextmenuLabel'] = apply_filters( 'aiovg_translate_strings', sanitize_text_field( $brand_settings['copyright_text'] ), 'copyright_text' );
}

$settings = apply_filters( 'aiovg_video_settings', $settings );
?>
<!DOCTYPE html>
<html translate="no">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">

    <?php if ( $post_id > 0 ) : ?>    
        <title><?php echo wp_kses_post( get_the_title( $post_id ) ); ?></title>    
        <link rel="canonical" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" />
    <?php endif; ?>

	<link rel="stylesheet" href="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/video-js.min.css?v=7.20.1" />

	<?php if ( in_array( 'qualitySelector', $settings['controlBar']['children'] ) && ( isset( $sources['mp4'] ) || isset( $sources['webm'] ) || isset( $sources['ogv'] ) ) ) : ?>
		<link rel="stylesheet" href="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/quality-selector/quality-selector.css?v=1.2.5" />
	<?php endif; ?>

	<?php if ( ! empty( $settings['aiovg']['share'] ) || ! empty( $settings['aiovg']['embed'] ) || ! empty( $settings['aiovg']['download'] ) || ! empty( $settings['aiovg']['showLogo'] ) ) : ?>
		<link rel="stylesheet" href="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/overlay/videojs-overlay.css?v=2.1.5" />
	<?php endif; ?>

	<style type="text/css">
        html, 
        body,
		.video-js {
            width: 100%;
            height: 100%;
            margin: 0; 
            padding: 0; 
            overflow: hidden;
        }

		@font-face {
			font-family: 'aiovg-icons';
			src: url('<?php echo AIOVG_PLUGIN_URL; ?>public/assets/fonts/aiovg-icons.eot?j6tmf3');
			src: url('<?php echo AIOVG_PLUGIN_URL; ?>public/assets/fonts/aiovg-icons.eot?j6tmf3#iefix') format('embedded-opentype'),
				url('<?php echo AIOVG_PLUGIN_URL; ?>public/assets/fonts/aiovg-icons.ttf?j6tmf3') format('truetype'),
				url('<?php echo AIOVG_PLUGIN_URL; ?>public/assets/fonts/aiovg-icons.woff?j6tmf3') format('woff'),
				url('<?php echo AIOVG_PLUGIN_URL; ?>public/assets/fonts/aiovg-icons.svg?j6tmf3#aiovg-icons') format('svg');
			font-weight: normal;
			font-style: normal;
			font-display: swap;
		}
		
		[class^="aiovg-icon-"],
		[class*=" aiovg-icon-"] {
			/* use !important to prevent issues with browser extensions that change fonts */
			font-family: 'aiovg-icons' !important;
			speak: none;
			color: #666;
			font-style: normal;
			font-weight: normal;
			font-variant: normal;
			text-transform: none;
			line-height: 1;
		
			/* Better Font Rendering =========== */
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}

		.aiovg-icon-download:before {
			content: "\e9c7";
		}

		.aiovg-icon-whatsapp:before {
			content: "\ea93";
		}

		.vjs-no-control-bar .vjs-control-bar {
			display: none;
		}

		.video-js .vjs-current-time,
		.vjs-no-flex .vjs-current-time,
		.video-js .vjs-duration,
		.vjs-no-flex .vjs-duration {
			display: block;
		}

		.video-js .vjs-subtitles-button .vjs-icon-placeholder:before {
			content: "\f10d";
		}

		.video-js .vjs-menu li.vjs-selected:focus,
		.video-js .vjs-menu li.vjs-selected:hover {
			background-color: #fff;
			color: #2b333f;
		}

		.vjs-youtube-mobile .vjs-poster {
			display: none;
		}

		.video-js .vjs-big-play-button {
			width: 1.5em;
			height: 1.5em;
			top: 50%;
			left: 50%;
			margin-top: 0;
			margin-left: 0;
			background-color: rgba( 0, 0, 0, 0.6 );
			border: none;
			border-radius: 50%;
			font-size: 6em;
			line-height: 1.5em;			
			transform: translateX( -50% ) translateY( -50% );
		}

		.video-js:hover .vjs-big-play-button,
		.video-js .vjs-big-play-button:focus {
			background-color: rgba( 0, 0, 0, 0.7 );
		}

		.vjs-waiting .vjs-big-play-button {
			display: none !important;
		}				

		.vjs-share,
		.vjs-download {
			margin: 5px;
			cursor: pointer;
		}		

		.vjs-share a,
		.vjs-download a {
			display: flex;
			margin: 0;
			padding: 10px;
    		background-color: rgba( 0, 0, 0, 0.5 );			
			border-radius: 1px;
			font-size: 16px;
			color: #fff;
		}	
		
		.vjs-share:hover a,
		.vjs-download:hover a {
			background-color: rgba( 0, 0, 0, 0.7 );
		}

		.vjs-download .aiovg-icon-download {
			color: inherit;
		}

		.vjs-download.vjs-has-share {
			margin-top: 50px;
		}

		.vjs-has-started .vjs-share,
		.vjs-has-started .vjs-download {
			display: block;
			visibility: visible;
			opacity: 1;
			transition: visibility .1s,opacity .1s;
		}

		.vjs-has-started.vjs-user-inactive.vjs-playing .vjs-share,
		.vjs-has-started.vjs-user-inactive.vjs-playing .vjs-download {
			visibility: visible;
			opacity: 0;
			transition: visibility 1s,opacity 1s;
		}

		.video-js .vjs-modal-dialog-share-embed {
            background: #111 !important;
        }

		.video-js .vjs-modal-dialog-share-embed .vjs-close-button {
            margin: 7px;
        }

		.video-js .vjs-share-embed {
            display: flex !important;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;   
        }

		.video-js .vjs-share-embed-content {
            width: 100%;
        }

		.video-js .vjs-share-buttons {
            text-align: center;
        }

		.video-js .vjs-share-button {
            display: inline-block;
			margin: 2px;
            width: 40px;
			height: 40px;
            line-height: 1;
			vertical-align: middle;
        }       

        .video-js .vjs-share-button,
        .video-js .vjs-share-button:hover,
        .video-js .vjs-share-button:focus {
            text-decoration: none;
        } 

		.video-js .vjs-share-button:hover {
            opacity: 0.9;
        }

        .video-js .vjs-share-button-facebook {
            background-color: #3B5996;
        }   
		
		.video-js .vjs-share-button-twitter {
            background-color: #55ACEE;
        }

        .video-js .vjs-share-button-linkedin {
            background-color: #006699;
        }

        .video-js .vjs-share-button-pinterest {
            background-color: #C00117;
        }

        .video-js .vjs-share-button-tumblr {
            background-color: #28364B;
        } 
		
		.video-js .vjs-share-button-whatsapp {
            background-color: #25d366;
        }  

        .video-js .vjs-share-button span {
            color: #fff;
            font-size: 24px;
			line-height: 40px;
        }

        .video-js .vjs-embed-code {
            margin: 20px;
        }

        .video-js .vjs-embed-code p {
			margin: 0 0 10px 0;
            text-align: center;
			text-transform: uppercase;
        }

        .video-js .vjs-embed-code input {
            width: 100%;
            padding: 7px;
            background: #fff;
            border: 1px solid #fff;
            color: #000;
        }

        .video-js .vjs-embed-code input:focus {
            border: 1px solid #fff;
            outline-style: none;
        }

		.vjs-logo {
			opacity: 0;
			cursor: pointer;
		}

		.vjs-has-started .vjs-logo {
			opacity: 0.5;
			transition: opacity 0.1s;
		}

		.vjs-has-started.vjs-user-inactive.vjs-playing .vjs-logo {
			opacity: 0;
			transition: opacity 1s;
		}

		.vjs-has-started .vjs-logo:hover {
			opacity: 1;
		}

		.vjs-logo img {
			max-width: 100%;
		}

		.vjs-ended .vjs-control-bar,
		.vjs-ended .vjs-text-track-display,
		.vjs-ended .vjs-logo {
			display: none;
		}

		.vjs-ended .vjs-poster,
		.vjs-ended .vjs-big-play-button {
			display: block;
		}

		.contextmenu {
            position: absolute;
            top: 0;
            left: 0;
            margin: 0;
            padding: 0;
            background-color: #2B333F;
  			background-color: rgba( 43, 51, 63, 0.7 );
			border-radius: 2px;
            z-index: 9999999999; /* make sure it shows on fullscreen */
        }
        
        .contextmenu-item {
            margin: 0;
            padding: 8px 12px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #FFF;		
            white-space: nowrap;
            cursor: pointer;
        }
    </style>

	<?php do_action( 'aiovg_player_head', $settings, $attributes, $sources, $tracks ); ?>
</head>
<body id="body" class="vjs-waiting">
    <video-js <?php the_aiovg_video_attributes( $attributes ); ?>>
        <?php 
		// Video Sources
		foreach ( $sources as $source ) {
			printf( 
				'<source type="%s" src="%s" label="%s"/>', 
				esc_attr( $source['type'] ), 
				esc_attr( aiovg_resolve_url( $source['src'] ) ),
				( isset( $source['label'] ) ? esc_attr( $source['label'] ) : '' ) 
			);
		}
		
		// Video Tracks
		foreach ( $tracks as $index => $track ) {
        	printf( 
				'<track src="%s" kind="subtitles" srclang="%s" label="%s"%s>', 
				esc_attr( aiovg_resolve_url( $track['src'] ) ), 
				esc_attr( $track['srclang'] ), 
				esc_attr( $track['label'] ),
				( 0 == $index && 1 == $settings['aiovg']['cc_load_policy'] ? ' default' : '' )
			);
		}
       ?>       
	</video-js>

	<?php if ( ! empty( $settings['aiovg']['share'] ) || ! empty( $settings['aiovg']['embed'] ) ) : ?>
		<div id="vjs-share-embed" class="vjs-share-embed" style="display: none;">
			<div class="vjs-share-embed-content">
				<?php if ( isset( $settings['aiovg']['shareButtons'] ) ) : ?>
					<!-- Share Buttons -->
					<div class="vjs-share-buttons">
						<?php
						foreach ( $settings['aiovg']['shareButtons'] as $button ) {
							printf( 
								'<a href="%s" class="vjs-share-button vjs-share-button-%s" target="_blank"><span class="%s"></span></a>',							
								esc_attr( $button['url'] ), 
								esc_attr( $button['service'] ),
								esc_attr( $button['iconClass'] ) 
							);
						}
						?>
					</div>
				<?php endif; ?>

				<?php if ( isset( $settings['aiovg']['embedCode'] ) ) : ?>
					<!-- Embed Code -->
					<div class="vjs-embed-code">
						<p><?php esc_html_e( 'Paste this code in your HTML page', 'all-in-one-video-gallery' ); ?></p>
						<input type="text" id="vjs-copy-embed-code" value="<?php echo htmlspecialchars( $settings['aiovg']['embedCode'] ); ?>" readonly />
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $settings['aiovg']['contextmenuLabel'] ) ) : ?>
		<div id="contextmenu" class="contextmenu" style="display: none;">
            <div class="contextmenu-item"><?php echo esc_html( $settings['aiovg']['contextmenuLabel'] ); ?></div>
        </div>
	<?php endif; ?>
    
	<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs/video.min.js?v=7.20.1" type="text/javascript"></script>

	<?php if ( in_array( 'qualitySelector', $settings['controlBar']['children'] ) && ( isset( $sources['mp4'] ) || isset( $sources['webm'] ) || isset( $sources['ogv'] ) ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/quality-selector/silvermine-videojs-quality-selector.min.js?v=1.2.5" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( isset( $sources['youtube'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/youtube/Youtube.min.js?v=2.6.1" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( isset( $settings['aiovg']['start'] ) || isset( $settings['aiovg']['end'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/offset/videojs-offset.min.js?v=2.1.3" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( isset( $sources['vimeo'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>public/assets/videojs-plugins/vimeo/videojs-vimeo2.min.js?v=1.3.0" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( isset( $sources['dailymotion'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>public/assets/videojs-plugins/dailymotion/videojs-dailymotion.min.js?v=1.2.0" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( isset( $sources['facebook'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>public/assets/videojs-plugins/facebook/videojs-facebook.min.js?v=1.4.0" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( ! empty( $settings['aiovg']['share'] ) || ! empty( $settings['aiovg']['embed'] ) || ! empty( $settings['aiovg']['download'] ) || ! empty( $settings['aiovg']['showLogo'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/overlay/videojs-overlay.min.js?v=2.1.5" type="text/javascript"></script>
	<?php endif; ?>

	<?php if ( ! empty( $settings['autoplay'] ) ) : ?>
		<script src="<?php echo AIOVG_PLUGIN_URL; ?>vendor/videojs-plugins/can-autoplay/can-autoplay.min.js?v=3.0.2" type="text/javascript"></script>
    <?php endif; ?>

	<?php do_action( 'aiovg_player_footer', $settings, $attributes, $sources, $tracks ); ?>

    <script type="text/javascript">
		'use strict';			
			
		// Vars
		var settings = <?php echo json_encode( $settings ); ?>;

		settings.html5 = {
			vhs: {
      			overrideNative: ! videojs.browser.IS_ANY_SAFARI,
    		}
		};

		var overlays = [];

		/**
		 * Merge attributes.
		 *
		 * @since  2.0.0
		 * @param  {array}  attributes Attributes array.
		 * @return {string} str        Merged attributes string to use in an HTML element.
		 */
		function combineAttributes( attributes ) {
			var str = '';

			for ( var key in attributes ) {
				str += ( key + '="' + attributes[ key ] + '" ' );
			}

			return str;
		}

		/**
		 * Convert SRT to WebVTT.
		 *
		 * @since 2.6.3
		 */
		function srtToWebVTT( data ) {
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
            		result += convertSrtCue( cuelist[ i ] );
            	}
          	}

          	return result;
        }

		function convertSrtCue( caption ) {
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

		async function fileGetContents( track, callback ) {
            let res = await fetch( track.src );
            track.text = await res.text();
            return callback ? callback( track ) : track; // a Promise() actually.
        }

		/**
		 * Update video views count.
		 *
		 * @since 2.0.0
		 */
		function updateViewsCount() {
			var xmlhttp;

			if ( window.XMLHttpRequest ) {
				xmlhttp = new XMLHttpRequest();
			} else {
				xmlhttp = new ActiveXObject( 'Microsoft.XMLHTTP' );
			};
			
			xmlhttp.onreadystatechange = function() {				
				if ( 4 == xmlhttp.readyState && 200 == xmlhttp.status ) {					
					if ( xmlhttp.responseText ) {
						// Do nothing
					}						
				}					
			};	

			xmlhttp.open( 'GET', '<?php echo admin_url( 'admin-ajax.php' ); ?>?action=aiovg_update_views_count&post_id=<?php echo $post_id; ?>&security=<?php echo wp_create_nonce( "aiovg_video_{$post_id}_views_nonce" ); ?>', true );
			xmlhttp.send();							
		}

		/**
		 * Check unmuted autoplay support.
		 *
		 * @since 2.0.0
		 */
		function checkUnmutedAutoplaySupport() {
			canAutoplay
				.video({ timeout: 100, muted: false })
				.then(function( response ) {
					if ( response.result === false ) {
						// Unmuted autoplay is not allowed
						checkMutedAutoplaySupport();
					} else {
						// Unmuted autoplay is allowed
						settings.autoplay = true;
						initPlayer();
					}
				});
		}

		/**
		 * Check muted autoplay support.
		 *
		 * @since 2.0.0
		 */
		function checkMutedAutoplaySupport() {
			canAutoplay
				.video({ timeout: 100, muted: true })
				.then(function( response ) {
					if ( response.result === false ) {
						// Muted autoplay is not allowed
						settings.autoplay = false;
					} else {
						// Muted autoplay is allowed
						settings.autoplay = true;
						settings.muted = true;
					}
					initPlayer();
				});
		}

		/**
		 * Initialize the player.
		 *
		 * @since 2.0.0
		 */		
		function initPlayer() {
			var player = videojs( 'player', settings );			

			// Maintained for backward compatibility
			if ( typeof window['onPlayerInitialized'] === 'function' ) {
				window.onPlayerInitialized( player );
			}

			// Dispatch an event
			var evt = document.createEvent( 'CustomEvent' );
			evt.initCustomEvent( 'player.init', false, false, { player: player } );
			window.dispatchEvent( evt );

			// On player ready
			player.ready(function() {
				document.getElementById( 'body' ).className = '';
			});

			// Add support for SRT
			player.one( 'loadedmetadata', function() {
				if ( settings.aiovg.hasOwnProperty( 'tracks' ) ) {
					for ( var i = 0, max = settings.aiovg.tracks.length; i < max; i++ ) {
						var track = settings.aiovg.tracks[ i ];

						if ( /srt/.test( track.src.toLowerCase() ) ) {
							fileGetContents( track, function( track ) {
								var vttText = srtToWebVTT( track.text );
								var vttBlob = new Blob([ vttText ], { type : 'text/vtt' });
								var blobURL = URL.createObjectURL( vttBlob );

								var trackObj = {
									src: blobURL,
									srclang: track.srclang,
									label: track.label,
									kind: 'subtitles'
								};

								if ( 1 == settings.aiovg.cc_load_policy && 0 == i ) {
									trackObj.mode = 'showing';
								}

								player.addRemoteTextTrack( trackObj, true ); 
							});
						} else {
							var trackObj = {
								src: track.src,
								srclang: track.srclang,
								label: track.label,
								kind: 'subtitles'
							};

							if ( 1 == settings.aiovg.cc_load_policy && 0 == i ) {
								trackObj.mode = 'showing';
							}

							player.addRemoteTextTrack( trackObj, true ); 
						}					               
					}  
				}            
			});

			// Fired the first time a video is played
			player.one( 'play', function() {
				if ( 'aiovg_videos' == settings.aiovg.postType ) {
					updateViewsCount();
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

			if ( settings.aiovg.start ) {
				offset.start = settings.aiovg.start;
			}

			if ( settings.aiovg.end ) {
				offset.end = settings.aiovg.end;
			}
			
			if ( Object.keys( offset ).length > 1 ) {
				offset.restart_beginning = false;
				player.offset( offset );
			}			

			// Share / Embed
			if ( settings.aiovg.share || settings.aiovg.embed ) {
				overlays.push({
					content: '<a href="javascript:void(0)" id="vjs-share-embed-button" class="vjs-share-embed-button" style="text-decoration:none;"><span class="vjs-icon-share"></span></a>',
					class: 'vjs-share',
					align: 'top-right',
					start: 'controlsshown',
					end: 'controlshidden',
					showBackground: false					
				});					
			}

			// Download
			if ( settings.aiovg.download ) {
				var __class = 'vjs-download';

				if ( settings.aiovg.share || settings.aiovg.embed ) {
					__class += ' vjs-has-share';
				}

				overlays.push({
					content: '<a href="' + settings.aiovg.downloadUrl + '" id="vjs-download-button" class="vjs-download-button" style="text-decoration:none;" target="_blank"><span class="aiovg-icon-download"></span></a>',
					class: __class,
					align: 'top-right',
					start: 'controlsshown',
					end: 'controlshidden',
					showBackground: false					
				});
			}

			// Logo
			if ( settings.aiovg.showLogo ) {
				var attributes = [];
				attributes['src'] = settings.aiovg.logoImage;

				if ( settings.aiovg.logoMargin ) {
					settings.aiovg.logoMargin = settings.aiovg.logoMargin - 5;
				}

				var align;
				switch ( settings.aiovg.logoPosition ) {
					case 'topleft':
						align = 'top-left';
						attributes['style'] = 'margin: ' + settings.aiovg.logoMargin + 'px;';
						break;
					case 'topright':
						align = 'top-right';
						attributes['style'] = 'margin: ' + settings.aiovg.logoMargin + 'px;';
						break;					
					case 'bottomright':
						align = 'bottom-right';
						attributes['style'] = 'margin: ' + settings.aiovg.logoMargin + 'px;';
						break;
					default:						
						align = 'bottom-left';
						attributes['style'] = 'margin: ' + settings.aiovg.logoMargin + 'px;';
						break;					
				}

				if ( settings.aiovg.logoLink ) {
					attributes['onclick'] = "top.window.location.href='" + settings.aiovg.logoLink + "';";
				}

				overlays.push({
					content: '<img ' +  combineAttributes( attributes ) + ' alt="" />',
					class: 'vjs-logo',
					align: align,
					start: 'controlsshown',
					end: 'controlshidden',
					showBackground: false					
				});
			}

			// Overlay
			if ( overlays.length > 0 ) {
				player.overlay({
					content: '',
					overlays: overlays
				});

				if ( settings.aiovg.share || settings.aiovg.embed ) {
					var options = {};
					options.content = document.getElementById( 'vjs-share-embed' );
					options.temporary = false;

					var ModalDialog = videojs.getComponent( 'ModalDialog' );
					var modal = new ModalDialog( player, options );
					modal.addClass( 'vjs-modal-dialog-share-embed' );

					player.addChild( modal );

					var wasPlaying = true;
					document.getElementById( 'vjs-share-embed-button' ).addEventListener( 'click', function() {
						wasPlaying = ! player.paused;
						modal.open();						
					});

					modal.on( 'modalclose', function() {
						if ( wasPlaying ) {
							player.play();
						}						
					});
				}

				if ( settings.aiovg.embed ) {
					document.getElementById( 'vjs-copy-embed-code' ).addEventListener( 'focus', function() {
						document.getElementById( 'vjs-copy-embed-code' ).select();	
						document.execCommand( 'copy' );					
					});
				}
			}
		}

		if ( settings.autoplay ) {
			checkUnmutedAutoplaySupport();
		} else {
			initPlayer();
		}					

		// Custom contextmenu
		if ( settings.aiovg.contextmenuLabel ) {
			var contextmenu = document.getElementById( 'contextmenu' );
			var timeout_handler = '';
			
			document.addEventListener( 'contextmenu', function( e ) {						
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
			
			if ( settings.aiovg.logoLink ) {
				contextmenu.addEventListener( 'click', function() {
					top.window.location.href = settings.aiovg.logoLink;
				});
			}
			
			document.addEventListener( 'click', function() {
				contextmenu.style.display = 'none';								 
			});	
		}
    </script>	
</body>
</html>