<?php
if( !defined('ABSPATH') ) exit;
if ( ! isset( $content_width ) ) $content_width = 750;
### Define
if( !defined('VIDEOTUBE_THEME_URI') ){
	define('VIDEOTUBE_THEME_URI', get_template_directory_uri());
}
if( !defined('VIDEOTUBE_THEME_DIR') ){
	define('VIDEOTUBE_THEME_DIR', get_template_directory());
}

require_once ( VIDEOTUBE_THEME_DIR . '/includes/functions.php');
require_once ( VIDEOTUBE_THEME_DIR . '/includes/style-function.php');
require_once ( VIDEOTUBE_THEME_DIR . '/includes/awesomeicon-array.php');

require_once ( VIDEOTUBE_THEME_DIR . '/includes/class-videotube-walker-menu.php');
require_once ( VIDEOTUBE_THEME_DIR . '/includes/class-tgm-plugin-activation.php');
require_once ( VIDEOTUBE_THEME_DIR . '/includes/class-author.php');
require_once ( VIDEOTUBE_THEME_DIR . '/includes/class-twitter.php');
require_once ( VIDEOTUBE_THEME_DIR . '/includes/class-streamable.php');
require_once ( VIDEOTUBE_THEME_DIR . '/includes/class-openload.php');
require_once ( VIDEOTUBE_THEME_DIR . '/includes/class-facebook.php');
require_once ( VIDEOTUBE_THEME_DIR . '/includes/plugins.php');

require_once ( VIDEOTUBE_THEME_DIR . '/includes/hooks.php');

require_once ( VIDEOTUBE_THEME_DIR . '/includes/template-login.php');

require_once ( VIDEOTUBE_THEME_DIR . '/includes/theme-options.php');
require_once ( VIDEOTUBE_THEME_DIR . '/includes/media.php');
require_once ( VIDEOTUBE_THEME_DIR . '/includes/ajax.php');

if( class_exists( 'WP_Easy_Review' ) ){
	require_once ( VIDEOTUBE_THEME_DIR . '/includes/wp-easy-review.php' );
}


if( !function_exists( 'videotube_after_setup_theme' ) ){
	function videotube_after_setup_theme() {
		//------------------------------ Load Language -----------------------------------------//
		load_theme_textdomain( 'videotube', get_template_directory() . '/languages' );
		//------------------------------ Add Theme Support -----------------------------------------//
		
		add_theme_support('post-thumbnails');
		add_theme_support( 'title-tag' );
		add_theme_support('woocommerce');
		add_theme_support('custom-background', array(
			'default-color'          => '',
			'default-image'          => '',
			'admin-head-callback'    => '',
			'admin-preview-callback' => ''
		));
		add_theme_support( 'jetpack-responsive-videos' );
		add_theme_support( 'automatic-feed-links' );

		register_nav_menus( array(
	    	'header_main_navigation' => esc_html__('Primary Navigation','videotube'),
	    	'user_nav'				=>	esc_html__( 'User Dropdown Navigation', 'videotube' )
	    ) );

		//------------------------------ And Theme Support -----------------------------------------//
		//------------------------------ Add Image Size -----------------------------------------//
		add_image_size('video-featured', 360, 240, true);
		add_image_size('video-lastest', 230, 150, true);
		add_image_size('video-category-featured', 295, 197, true);
		add_image_size('video-item-category-featured', 750, 440, true);
		### sidebar
		add_image_size('most-video-2col', 165, 108, true);
		### Blog
		add_image_size('blog-large-thumb', 750, 'auto', true);


		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );
	}
	add_action('after_setup_theme', 'videotube_after_setup_theme');
}

//------------------------------ Enqueue Scripts && Styles-----------------------------------------//
if( !function_exists('videotube_enqueue_scripts') ){
	function videotube_enqueue_scripts() {
		### Core JS

		wp_enqueue_style(
			'bootstrap', 
			VIDEOTUBE_THEME_URI . '/assets/css/bootstrap.min.css', 
			array(),
			filemtime( get_theme_file_path( '/assets/css/bootstrap.min.css' ) )
		);		

		wp_enqueue_style('fontawesome', VIDEOTUBE_THEME_URI . '/assets/css/all.min.css' );
		wp_enqueue_style('fontawesome-solid', VIDEOTUBE_THEME_URI . '/assets/css/solid.min.css' );

		if( is_rtl() ){
			wp_enqueue_style('fontawesomertl', VIDEOTUBE_THEME_URI . '/assets/css/font-awesome-rtl.css');
		}
		wp_enqueue_style('google-font','//fonts.googleapis.com/css?family=Lato:300,400,700,900');	

		wp_enqueue_style(
			'videotube-style',
			get_template_directory_uri() . '/style.css',
			array( 'bootstrap' ),
			filemtime( get_template_directory() . '/style.css' )
		);

		if( function_exists( 'WC' ) ){
			wp_enqueue_style(
				'videotube-woocommerce',
				VIDEOTUBE_THEME_URI . '/assets/css/woocommerce.css',
				array( 'bootstrap', 'woocommerce-general' ),
				filemtime( get_theme_file_path( '/assets/css/woocommerce.css' ) )
			);
		}

		if( is_single() || is_page() ){
			wp_enqueue_script('comment-reply');
		}

		wp_enqueue_script( 
			'bootstrap', 
			VIDEOTUBE_THEME_URI . '/assets/js/bootstrap.min.js', 
			array('jquery'), 
			filemtime( get_theme_file_path( '/assets/js/bootstrap.min.js' ) ), 
			true 
		);

		wp_enqueue_script( 
			'jquery.cookies', 
			VIDEOTUBE_THEME_URI . '/assets/js/jquery.cookie.js', 
			array('jquery'), 
			filemtime( get_theme_file_path( '/assets/js/jquery.cookie.js' ) ), 
			true 
		);		

		wp_enqueue_script( 
			'readmore', 
			VIDEOTUBE_THEME_URI . '/assets/js/readmore.min.js', 
			array('jquery'), 
			filemtime( get_theme_file_path( '/assets/js/readmore.min.js' ) ), 
			true 
		);

		wp_enqueue_script(
			'jquery.appear', 
			VIDEOTUBE_THEME_URI . '/assets/js/jquery.appear.js', 
			array('jquery'), 
			filemtime( get_theme_file_path( '/assets/js/jquery.appear.js' ) ), 
			true 
		);

		wp_enqueue_script(
			'autosize', 
			VIDEOTUBE_THEME_URI . '/assets/js/autosize.min.js', 
			array('jquery'), 
			filemtime( get_theme_file_path( '/assets/js/autosize.min.js' ) ), 
			true 
		);		

		wp_enqueue_script( 
			'videotube-custom', 
			VIDEOTUBE_THEME_URI . '/assets/js/custom.js', 
			array('jquery'), 
			filemtime( get_theme_file_path( '/assets/js/custom.js' ) ), 
			true 
		);

		wp_localize_script( 'videotube-custom' , 'jsvar', apply_filters( 'jsvar' , array(
			'home_url'					=>	home_url('/'),
			'ajaxurl'					=>	admin_url( 'admin-ajax.php' ),
			'_ajax_nonce'				=>	wp_create_nonce( 'do_ajax_security' ),
			'video_filetypes'			=>	wp_get_video_extensions(),
			'image_filetypes'			=>	array( 'jpg', 'gif', 'png' ),
			'error_image_filetype'		=>	esc_html__( 'Please upload an image instead.', 'videotube' ),
			'error_video_filetype'		=>	esc_html__( 'Please upload a video instead.', 'videotube' ),
			'delete_video_confirm'		=>	esc_html__( 'Do you want to delete this video?', 'videotube' ),
			'uploading'					=>	esc_html__( 'Uploading ...', 'videotube' )
		)) );
	}
	add_action('wp_enqueue_scripts', 'videotube_enqueue_scripts');
}
if( !function_exists( 'videotube_load_custom_stylesheet' ) ){
	/**
	 *
	 * Load custom style
	 * 
	 */
	function videotube_load_custom_stylesheet() {
		global $videotube;
		if( isset( $videotube['style'] ) && ! in_array( $videotube['style'] , array( 'default','custom' )) ){
			$custom_style = esc_url(  $videotube['style'] );
			$name = wp_make_link_relative( $custom_style );
			wp_enqueue_style( $name , $custom_style, array( 'videotube-style' ), null);
		}
	}
	add_action('wp_enqueue_scripts', 'videotube_load_custom_stylesheet', 100 );
}
if( !function_exists( 'videotube_load_custom_code_style' ) ){
	
	function videotube_load_custom_code_style() {
		global $videotube;
		if( isset( $videotube['style'] ) && $videotube['style'] == 'custom' && !empty( $videotube['style_custom'] ) ){
			wp_add_inline_style( 'videotube-style', $videotube['style_custom'] );
		}
	}
	add_action( 'wp_enqueue_scripts' , 'videotube_load_custom_code_style');
}
if( !function_exists('videotube_admin_enqueue_scripts') ){
	function videotube_admin_enqueue_scripts() {
		global $pagenow;
		if( $pagenow == 'widgets.php' ){
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_style('jquery-ui-datepicker', VIDEOTUBE_THEME_URI . '/assets/css/ui-lightness/jquery-ui-1.10.4.custom.min.css');
			wp_enqueue_script('mars-admin.js', VIDEOTUBE_THEME_URI . '/assets/js/admin.js', array(), '', true);
		}
		wp_enqueue_style('redux-admin', VIDEOTUBE_THEME_URI . '/assets/css/redux-admin.css');
		wp_enqueue_style('mars-admin-style', VIDEOTUBE_THEME_URI . '/assets/css/admin.css');
	}
	add_action('admin_enqueue_scripts', 'videotube_admin_enqueue_scripts');
}
//------------------------------ End Scripts && Styles-----------------------------------------//

//------------------------------ Register Sidebar-----------------------------------------//
if( !function_exists('videotube_register_sidebars') ){
	function videotube_register_sidebars() {
		register_sidebar( $args = array(
				'name'          => esc_html__( 'Right HomePage', 'videotube' ),
				'id'            => 'mars-homepage-right-sidebar',
				'description'   => esc_html__('Add widgets here to appear in right sidebar on HomePage.','videotube'),
				'before_widget' => '<div id="%1$s" class="widget widget-primary %2$s"><div class="widget-content">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>'
			)
		);
		### is page
		register_sidebar( $args = array(
				'name'          => esc_html__( 'Inner Page Right', 'videotube' ),
				'id'            => 'mars-inner-page-right-sidebar',
				'description'   => esc_html__('Add widgets here to appear in right sidebar on inner pages.','videotube'),
				'before_widget' => '<div id="%1$s" class="widget widget-primary %2$s"><div class="widget-content">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>'
			)
		);
		register_sidebar( $args = array(
				'name'          => esc_html__( 'Featured', 'videotube' ),
				'id'            => 'mars-featured-videos-sidebar',
				'description'   => esc_html__('Add widgets here to appear in featured sidebar.','videotube'),
				'before_widget' => '<div id="%1$s" class="widget widget-featured %2$s"><div class="widget-content">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>'
			)
		);
		register_sidebar( $args = array(
				'name'          => esc_html__( 'Main HomePage', 'videotube' ),
				'id'            => 'mars-home-videos-sidebar',
				'description'   => esc_html__('Add widgets here to appear in main HomePage content.','videotube'),
				'before_widget' => '<div id="%1$s" class="widget widget-main %2$s"><div class="widget-content">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>'
			)
		);
		register_sidebar( $args = array(
				'name'          => esc_html__( 'Author Right', 'videotube' ),
				'id'            => 'mars-author-page-right-sidebar',
				'description'   => esc_html__('Add widgets here to appear in right sidebar on Author page.','videotube'),
				'before_widget' => '<div id="%1$s" class="widget widget-primary %2$s"><div class="widget-content">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>'
			)
		);
		register_sidebar( $args = array(
				'name'          => esc_html__( 'Footer Sidebar', 'videotube' ),
				'id'            => 'mars-footer-sidebar',
				'description'   => esc_html__('Add widgets here to appear in Footer.','videotube'),
				'before_widget' => '<div id="%1$s" class="col-12 col-sm-6 col-lg-3 widget widget-footer %2$s"><div class="widget-content">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h4 class="footer-widget-title">',
				'after_title'   => '</h4>'
			)
		);
		register_sidebar( $args = array(
				'name'          => esc_html__( 'Video Content Bottom', 'videotube' ),
				'id'            => 'mars-video-single-below-sidebar',
				'description'   => esc_html__('Add widgets here to appear in video content bottom.','videotube'),
				'before_widget' => '<div id="%1$s" class="widget widget-content-bottom %2$s"><div class="widget-content">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>'
			)
		);
		register_sidebar( $args = array(
				'name'          => esc_html__( 'Post Content Bottom', 'videotube' ),
				'id'            => 'mars-post-single-below-content-sidebar',
				'description'   => esc_html__('Add widgets here to appear in blog post content bottom','videotube'),
				'before_widget' => '<div id="%1$s" class="widget widget-content-bottom %2$s"><div class="widget-content">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>'
			)
		);
	}
	add_action('widgets_init', 'videotube_register_sidebars');
}
//------------------------------ End Sidebar-----------------------------------------//