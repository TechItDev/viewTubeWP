<?php

if( !defined('ABSPATH') ) exit;

if ( ! class_exists( 'Redux' ) ) {
	return;
}

// This is your option name where all the Redux data is stored.
$opt_name = "videotube";

$theme = wp_get_theme(); // For use with some settings. Not necessary.

$args = array(
	// TYPICAL -> Change these values as you need/desire
	'opt_name'             => $opt_name,
	// This is where your data is stored in the database and also becomes your global variable name.
	'display_name'         => $theme->get( 'Name' ),
	// Name that appears at the top of your panel
	'display_version'      => $theme->get( 'Version' ),
	// Version that appears at the top of your panel
	'menu_type'            => 'submenu',
	//Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
	'allow_sub_menu'       => true,
	// Show the sections below the admin menu item or not
	'menu_title'           => __( 'Theme Options', 'videotube' ),
	'page_title'           => __( 'Theme Options', 'videotube' ),
	// You will need to generate a Google API key to use this feature.
	// Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
	'google_api_key'       => '',
	// Set it you want google fonts to update weekly. A google_api_key value is required.
	'google_update_weekly' => false,
	// Must be defined to add google fonts to the typography module
	'async_typography'     => true,
	// Use a asynchronous font on the front end or font string
	//'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
	'admin_bar'            => true,
	// Show the panel pages on the admin bar
	'admin_bar_icon'       => 'dashicons-portfolio',
	// Choose an icon for the admin bar menu
	'admin_bar_priority'   => 50,
	// Choose an priority for the admin bar menu
	'global_variable'      => 'videotube',
	// Set a different name for your global variable other than the opt_name
	'dev_mode'             => false,
	// Show the time the page took to load, etc
	'update_notice'        => false,
	// If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
	'customizer'           => true,
	// Enable basic customizer support
	//'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
	//'disable_save_warn' => true,                    // Disable the save warning when a user changes a field
	
	// OPTIONAL -> Give you extra features
	'page_priority'        => null,
	// Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
	'page_parent'          => 'themes.php',
	// For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
	'page_permissions'     => 'manage_options',
	// Permissions needed to access the options panel.
	'menu_icon'            => '',
	// Specify a custom URL to an icon
	'last_tab'             => '',
	// Force your panel to always open to a specific tab (by id)
	'page_icon'            => 'icon-themes',
	// Icon displayed in the admin panel next to your menu_title
	'page_slug'            => '_options',
	// Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
	'save_defaults'        => true,
	// On load save the defaults to DB before user clicks save or not
	'default_show'         => false,
	// If true, shows the default value next to each field that is not the default value.
	'default_mark'         => '',
	// What to print by the field's title if the value shown is default. Suggested: *
	'show_import_export'   => true,
	// Shows the Import/Export panel when not used as a field.
	
	// CAREFUL -> These options are for advanced use only
	'transient_time'       => 60 * MINUTE_IN_SECONDS,
	'output'               => true,
	// Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
	'output_tag'           => true,
	// Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
	// 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
	
	// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
	'database'             => '',
	// possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
	'system_info'          => false
	// REMOVE
);

// Panel Intro text -> before the form
if ( ! isset( $args['global_variable'] ) || $args['global_variable'] !== false ) {
	if ( ! empty( $args['global_variable'] ) ) {
		$v = $args['global_variable'];
	} else {
		$v = str_replace( '-', '_', $args['opt_name'] );
	}
	$args['intro_text'] = sprintf( __( '<p>Did you know that Redux sets a global variable for you? To access any of your saved options from within your code you can use your global variable: <strong>$%1$s</strong></p>', 'videotube' ), $v );
} else {
	$args['intro_text'] = __( '<p>This text is displayed above the options panel. It isn\'t required, but more info is always better! The intro_text field accepts all HTML.</p>', 'videotube' );
}

Redux::setArgs( $opt_name, $args );

// General
Redux::setSection( $opt_name, array(
	'title' => __( 'General', 'videotube' ),
	'id'    => 'general',
	'icon'  => 'el el-home',
	'fields'	=>	array(
		array(
			'id'	=>	'logo',
			'type'	=>	'media',
			'url' => true,
			'subtitle' => __('Upload any media using the WordPress native uploader', 'videotube'),
			'default' => array('url' => get_template_directory_uri() . '/img/logo.png'),					
			'title'	=>	__('Logo (194*31px)','videotube')
		),
		array(
			'id' => 'custom_css',
			'type' => 'ace_editor',
			'title' => __('Custom CSS', 'videotube'),
			'subtitle' => __('Paste your CSS code here, no style tag.', 'videotube'),
			'mode' => 'css',
			'theme' => 'monokai'
		),
		array(
			'id' => 'custom_css_mobile',
			'type' => 'ace_editor',
			'title' => __('Mobile Custom CSS', 'videotube'),
			'subtitle' => __('Paste your CSS code here, no style tag, this CSS will effect to the site on Mobile.', 'videotube'),
			'mode' => 'css',
			'theme' => 'monokai'
		),		
		array(
			'id' => 'custom_js',
			'type' => 'ace_editor',
			'title' => __('Custom JS', 'videotube'),
			'subtitle' => __('Paste your JS code here, no script tag, eg: alert(\'hello world\');', 'videotube'),
			'mode' => 'javascript',
			'theme' => 'chrome'
		),
		array(
			'id' => 'custom_js_mobile',
			'type' => 'ace_editor',
			'title' => __('Mobile Custom JS', 'videotube'),
			'subtitle' => __('Paste your JS code here, no script tag, this JS will effect to the site on Mobile eg: alert(\'hello world\');', 'videotube'),
			'mode' => 'javascript',
			'theme' => 'chrome'
		)		
	)
));

// Footer
Redux::setSection( $opt_name, array(
	'title' => __( 'Styling', 'videotube' ),
	'id'    => 'styling',
	'icon'  => 'el-icon-brush',
	'fields'	=>	array(
		array(
			'id'	=>	'style',
			'type'	=>	'select',
			'url' => true,
			'title'	=>	__('Style','videotube'),
			'subtitle'	=>	__('Choose the Style.','videotube'),
			'options'   => apply_filters( 'videotube_get_styles' , null),
			'default'   => 'default'
		),
		array(
			'id' => 'style_custom',
			'type' => 'ace_editor',
			'title' => __('Custom CSS', 'videotube'),
			'subtitle' => __('Write your CSS code here, no style tag, this will override to the default style.', 'videotube'),
			'desc'	=>	__('This style will be lose if you reset the settings.','videotube'),
			'mode' => 'css',
			'theme' => 'monokai',
			'indent'    => false,
			'required'  => array('style', "=", 'custom'),
		),
		array(
			'id' => 'color-header',
			'type' => 'color',
			'title' => __('Header Background Color', 'videotube'),
			'subtitle' => __('Pick a background color for the header (default: #ffffff).', 'videotube'),
			'default' => '#ffffff',
			'validate' => 'color',
		),
		array(
			'id' => 'body-background',
			'type' => 'background',
			'output' => array('body'),
			'title' => __('Body Background', 'videotube'),
			'subtitle' => __('Body background with image, color, etc.', 'videotube'),
			'default' => '#FFFFFF',
		),
		array(
			'id'        => 'typography-body',
			'type'      => 'typography',
			'title'     => __('Body Text', 'videotube'),
			'google'    => true,
			'font-style'	=>	false,
			'subsets'	=>	false,
			'font-weight'	=> false,
			'font-size'	=>	false,
			'line-height'	=>	false,
			'text-align'	=>	false,
			'color'		=>	false
		),
		array(
			'id'        => 'typography-headings',
			'type'      => 'typography',
			'title'     => __('Headings', 'videotube'),
			'google'    => true,
			'font-style'	=>	false,
			'subsets'	=>	false,
			'font-weight'	=> false,
			'font-size'	=>	false,
			'line-height'	=>	false,
			'text-align'	=>	false,
			'color'		=>	false
		),
		array(
			'id'        => 'typography-menu',
			'type'      => 'typography',
			'title'     => __('Menu', 'videotube'),
			'google'    => true,
			'font-style'	=>	false,
			'subsets'	=>	false,
			'font-weight'	=> false,
			'font-size'	=>	false,
			'line-height'	=>	false,
			'text-align'	=>	false,
			'color'		=>	false
		),
		array(
			'id' => 'color-widget',
			'type' => 'color',
			'title' => __('Widget Title Background', 'videotube'),
			'subtitle' => __('Pick a background color for the Widget title (default: #e73737), only for Right Widget.', 'videotube'),
			'default' => '#e73737',
			'validate' => 'color',
		),
		array(
			'id' => 'color-text-widget',
			'type' => 'color',
			'title' => __('Widget Title Color', 'videotube'),
			'subtitle' => __('Pick a color for the Widget title (default: #e73737), only for Right Widget', 'videotube'),
			'default' => 'hsl(0, 100%, 100%)',
			'validate' => 'color',
		),
		array(
			'id' => 'color-header-navigation',
			'type' => 'color',
			'title' => __('Header Navigation Background', 'videotube'),
			'subtitle' => __('Pick a background color for the Header Navigation (Header Menu) (default: #4c5358).', 'videotube'),
			'default' => '#4c5358',
			'validate' => 'color',
		),
		array(
			'id' => 'color-text-header-navigation',
			'type' => 'color',
			'title' => __('Header Navigation Color', 'videotube'),
			'subtitle' => __('Pick a color for the Header Navigation (Header Menu) (default: #4c5358).', 'videotube'),
			'default' => 'hsl(0, 100%, 100%)',
			'validate' => 'color',
		),
		array(
			'id' => 'color-footer',
			'type' => 'color',
			'title' => __('Footer Background', 'videotube'),
			'subtitle' => __('Pick a background color for the footer (default: #111111).', 'videotube'),
			'default' => '#111111',
			'validate' => 'color',
		),
		array(
			'id' => 'color-footer-text',
			'type' => 'color',
			'title' => __('Footer Text Color', 'videotube'),
			'subtitle' => __('Pick a color for Text in the footer (default: #ffffff).', 'videotube'),
			'default' => '#ffffff',
			'validate' => 'color',
		)		
	)
));


// Socials
Redux::setSection( $opt_name, array(
	'title' => __( 'Socials', 'videotube' ),
	'id'    => 'socials',
	'icon'  => 'el-icon-share',
	'fields'	=>	array(
		array(
			'id' => 'guestlike',
			'type' => 'switch',
			'title' => __('Allow Guest to Like', 'videotube'),
			"default" => 0,
			'on' => __('Yes','videotube'),
			'off' => __('No','videotube'),
		),					
		array(
			'id'	=>	'facebook',
			'title'	=>	__('Facebook','videotube'),
			'type'	=>	'text',
			'desc'	=>	__('Facebook Profile or Fanpage URL','videotube')
		),
		array(
			'id'	=>	'twitter',
			'title'	=>	__('Twitter','videotube'),
			'type'	=>	'text',
			'desc'	=>	__('Twitter URL','videotube')
		),
		array(
			'id'	=>	'google-plus',
			'title'	=>	__('Google Plus','videotube'),
			'type'	=>	'text',
			'desc'	=>	__('Google Plus URL','videotube')
		),
		array(
			'id'	=>	'instagram',
			'title'	=>	__('Instagram','videotube'),
			'type'	=>	'text',
			'desc'	=>	__('Instagram URL','videotube')
		),
		array(
			'id'	=>	'linkedin',
			'title'	=>	__('Linkedin','videotube'),
			'type'	=>	'text',
			'desc'	=>	__('Linkedin URL','videotube')
		),
		array(
			'id'	=>	'tumblr',
			'title'	=>	__('Tumblr','videotube'),
			'type'	=>	'text',
			'desc'	=>	__('Tumblr URL','videotube')
		),
		array(
			'id'	=>	'youtube',
			'title'	=>	__('Youtube','videotube'),
			'type'	=>	'text',
			'desc'	=>	__('Youtube URL','videotube')
		),
		array(
			'id'	=>	'vimeo',
			'title'	=>	__('Vimeo','videotube'),
			'type'	=>	'text',
			'desc'	=>	__('Vimeo URL','videotube')
		),
		array(
			'id'	=>	'soundcloud',
			'title'	=>	__('Soundcloud','videotube'),
			'type'	=>	'text',
			'desc'	=>	__('Soundcloud URL','videotube')
		),
		array(
			'id'	=>	'pinterest',
			'title'	=>	__('Pinterest','videotube'),
			'type'	=>	'text',
			'desc'	=>	__('Pinterest URL','videotube')
		),
		array(
			'id'	=>	'snapchat',
			'title'	=>	__('Snapchat','videotube'),
			'type'	=>	'text',
			'desc'	=>	__('Snapchat URL','videotube')
		)		
	)
));

$user_db = NULL;
$users = get_users(array('role'=>null));
foreach ( $users as $user ){
	$user_db[ $user->ID ] = $user->user_login;
}

// Submit Form
Redux::setSection( $opt_name, array(
	'title' => __( 'Submit Form', 'videotube' ),
	'id'    => 'submit-form',
	'icon'  => 'el-icon-cloud',
	'fields'	=>	array(
		array(
			'id' => 'submit_permission',
			'type' => 'switch',
			'title' => __('Allow Guest submit the video.', 'videotube'),
			'subtitle' => __('By default, Only register can submit the video, you can limit the role in below selectbox', 'videotube'),
			"default" => 0,
			'on' => __('Yes','videotube'),
			'off' => __('No','videotube'),
		),	

		array(
			'id' => 'video-type',
			'type' => 'select',
			'multi' => true,
			'title' => __('Video Type', 'videotube'),
			'subtitle' => __('Choose the Video Type, which is available in Submit Form at Frontend.', 'videotube'),
			'options' => array('videolink' => __('Link','videotube'), 'embedcode' => __('Embed Code','videotube'), 'videofile' => __('File','videotube')), //Must provide key => value pairs for select options
			'default' => 'videolink'
		),   
		array(
			'id'	=>	'videosize',
			'title'	=>	__('Video File Size','videotube'),
			'type'	=>	'text',
			'desc'	=>	sprintf(
				__('The maximum video file size allowed, 10MB is default size, this size must be smaller than %sMB','videotube'),
				wp_max_upload_size()/1024/1024
			),
			'default'	=>	10
		),
		array(
			'id'	=>	'imagesize',
			'title'	=>	__('Preview Image Size','videotube'),
			'type'	=>	'text',
			'desc'	=>	sprintf(
				__('The maximum Preview Image size allowed, 2MB is default size, this size must be smaller than %sMB','videotube'),
				wp_max_upload_size()/1024/1024
			),
			'default'	=>	2
		),					
		array(
			'id' => 'submit_redirect_to',
			'type' => 'select',
			'data' => 'pages',
			'title' => __('Redirect to', 'videotube')
		),                    
		array(
			'id' => 'submit_assigned_user',
			'type' => 'select',
			'title' => __('User assignment', 'videotube'),
			'options' => $user_db,
			'default' => '1'
		),
		array(
			'id' => 'submit_roles',
			'type' => 'select',
			'multi' => true,
			'data' => 'roles',
			'title' => __('Who can submit the video?', 'videotube')
		),
		array(
			'id' => 'submit_status',
			'type' => 'button_set',
			'title' => __('Default Video Status', 'videotube'),
			'subtitle' => __('The Public status will be shown on Frontend.', 'videotube'),
			'options' => array('publish' => __('Publish','videotube'), 'pending' => __('Pending','videotube'), 'draft' => __('draft','videotube')),
			'default' => 'pending'
		),
		array(
			'id' => 'submit_editor',
			'type' => 'switch',
			'title' => __('Use WP Visual Editor', 'videotube'),
			"default" => 0,
			'on' => __('Yes','videotube'),
			'off' => __('No','videotube'),
		),
		array(
			'id' => 'videolayout',
			'type' => 'button_set',
			'title'	=>	__('Show the Layout dropdown','videotube'),
			'options' => array('yes' => __('Yes','videotube'), 'no' => __('No','videotube')),
			'default' => 'yes'
		)		
	)
));


// Miscellaneous
Redux::setSection( $opt_name, array(
	'title' => __( 'Misc', 'videotube' ),
	'id'    => 'miscellaneous',
	'icon'  => 'el-icon-wrench',
	'fields'	=>	array(
		array(
			'id' => 'rewrite_slug',
			'type'	=>	'text',
			'title'	=>	__('Video Slug','videotube'),
			'default'	=>	'video',
			'subtitle'	=>	sprintf('This option will change the default slug of the video post type, if you change this key, you must go to %s and click on Save Changes button','<a href="'.admin_url('options-permalink.php').'">'.__('Settings/Permalink','videotube').'</a>')
		),
		array(
			'id' => 'rewrite_slug_category',
			'type'	=>	'text',
			'title'	=>	__('Video Category Slug','videotube'),
			'default'	=>	'categories',
			'subtitle'	=>	sprintf('This option will change the default slug of the video category taxonomy, if you change this key, you must go to %s and click on Save Changes button','<a href="'.admin_url('options-permalink.php').'">'.__('Settings/Permalink','videotube').'</a>')
		),
		array(
			'id' => 'rewrite_slug_tag',
			'type'	=>	'text',
			'title'	=>	__('Video Tag Slug','videotube'),
			'default'	=>	'video_tag',
			'subtitle'	=>	sprintf('This option will change the default slug of the video tag taxonomy, if you change this key, you must go to %s and click on Save Changes button','<a href="'.admin_url('options-permalink.php').'">'.__('Settings/Permalink','videotube').'</a>')
		),

		array(
			'id' => 'excerpt_length',
			'type'	=>	'text',
			'title'	=>	__('Post Excerpt Length','videotube'),
			'default'	=>	15
		),		
		array(
			'id' => 'video_feed',
			'type' => 'checkbox',
			'title'	=>	__('Feeds','videotube'),
			'description'	=>	__('Including the Video in Feed Page.','videotube')
		),

		array(
			'id' => 'loginmenu',
			'type' => 'switch',
			'title'	=>	__('Login Menu','videotube'),
			'description'	=>	__('Enable the login menu.','videotube'),
			'on' => __('Yes','videotube'),
			'off' => __('No','videotube'),
			'default'	=>	1	
		),

		array(
			'id' => 'load_comments',
			'type' => 'button_set',
			'title'	=>	__('AJAX Comments','videotube'),
			'options' => array(
				'click' 	=> __('Click','videotube'), 
				'infinite'	=> __('Infinite Scroll','videotube')
			),
			'default' => 'click'
		),		
		array(
			'id' => 'datetime_format',
			'type' => 'button_set',
			'title'	=>	__('Time format','videotube'),
			'options' => array('default' => __('Default','videotube'), 'videotube' => __('Diffing','videotube')),
			'default' => 'videotube'
		),

		array(
			'id' => 'sticky_player',
			'type' => 'switch',
			'title'	=>	__('Sticky Player','videotube'),
			'on' => __('Yes','videotube'),
			'off' => __('No','videotube'),
			'default'	=>	0	
		),		
		
		array(
			'id' => 'aspect_ratio',
			'type' => 'button_set',
			'title'	=>	__('Aspect Ratio','videotube'),
			'options' => array('16by9' => __('16by9','videotube'), '4by3' => __('4by3','videotube')),
			'default' => '16by9'
		),		
		
		array(
			'id' => 'autoplay',
			'type' => 'switch',
			'title' => __('AutoPlay', 'videotube'),
			'subtitle'	=>	__('Works for youtube and video self-hosted file.','videotube'),
			"default" => 1,
			'on' => __('Yes','videotube'),
			'off' => __('No','videotube'),
		),
		array(
			'id' => 'enable_channelpage',
			'type' => 'switch',
			'title' => __('User Channel', 'videotube'),
			'desc'	=>	__('Activate the channel page.','videotube'),
			"default" => 0,
			'on' => __('Yes','videotube'),
			'off' => __('No','videotube'),
		),
		array(
			'id' => 'read_more_less',
			'type' => 'switch',
			'title' => __('Read more/read less', 'videotube'),
			"default" => '1',
			'on' => __('Yes','videotube'),
			'off' => __('No','videotube'),
		),		
		array(
			'id' => 'desktop_columns',
			'type' => 'select',
			'multi' => false,
			'title' => __('Desktop Columns', 'videotube'),
			"default" => 3,
			"options"	=>	array(
				'1'	=>	esc_html__( '1 column', 'videotube' ),
				'2'	=>	esc_html__( '2 columns', 'videotube' ),
				'3'	=>	esc_html__( '3 columns', 'videotube' ),
				'4'	=>	esc_html__( '4 columns', 'videotube' ),
				'6'	=>	esc_html__( '6 columns', 'videotube' )
			)
		),
		array(
			'id' => 'tablet_columns',
			'type' => 'select',
			'multi' => false,
			'title' => __('Tablet Columns', 'videotube'),
			"default" => 2,
			"options"	=>	array(
				'1'	=>	esc_html__( '1 column', 'videotube' ),
				'2'	=>	esc_html__( '2 columns', 'videotube' ),
				'3'	=>	esc_html__( '3 columns', 'videotube' ),
				'4'	=>	esc_html__( '4 columns', 'videotube' )
			)
		),
		array(
			'id' => 'mobile_columns',
			'type' => 'select',
			'multi' => false,
			'title' => __('Mobile Columns', 'videotube'),
			"default" => 2,
			"options"	=>	array(
				'1'	=>	esc_html__( '1 column', 'videotube' ),
				'2'	=>	esc_html__( '2 columns', 'videotube' ),
				'3'	=>	esc_html__( '3 columns', 'videotube' ),
				'4'	=>	esc_html__( '4 columns', 'videotube' )
			)
		)
	)
));

// Footer
Redux::setSection( $opt_name, array(
	'title' => __( 'Footer', 'videotube' ),
	'id'    => 'footer',
	'icon'  => 'el-icon-wrench',
	'fields'	=>	array(
		array(
			'id'	=>	'copyright_text',
			'title'	=>	__('Copyright Text','videotube'),
			'type'	=>	'editor',
			'default'	=>	'<p>Copyright 2015 By MarsTheme All rights reserved. Powered by WordPress &amp; MarsTheme</p>'
		)	
	)
));
// Update
Redux::setSection( $opt_name, array(
	'title' => __( 'Update', 'videotube' ),
	'id'    => 'update',
	'icon'  => 'el el-refresh',
	'fields'	=>	array(
		array(
			'id'	=>	'purchase_code',
			'title'	=>	__('Purchase Code','videotube'),
			'type'	=>	'text'
		),
		array(
			'id'	=>	'access_token',
			'title'	=>	esc_html__('Personal Access Token','videotube'),
			'desc'	=>	sprintf( esc_html__('Get one key %s','videotube'), '<a target="_blank" href="https://build.envato.com/create-token/">'. esc_html__( 'here', 'videotube' ) .'</a>' ),
			'type'	=>	'text'
		)		
	)
));