<?php
if( ! defined('ABSPATH') ) exit;

if( ! function_exists( 'videotube_core_vc_login_form' ) ){

	function videotube_core_vc_login_form() {
		// map the widget.
		$args = array(
			'name'	=>	__( '[VideoTube] Login Form','videotube'),
			'base'	=>	'mars_vc_login_form',
			'category'	=>	__('WordPress Widgets','videotube'),
			'class'	=>	'videotube',
			'icon'	=>	'videotube',
			'description'	=>	__('Display the Login Form Widget.','videotube'),
			'admin_enqueue_css' => array(get_template_directory_uri().'/assets/css/vc.css'),
			'params'	=>	array(
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Title','videotube'),
					'param_name'	=>	'title',
					'value'	=>	__('Profile','videotube')
				),
				array(
					'type'	=>	'dropdown',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Video Upload Page','videotube'),
					'param_name'	=>	'uploader_url',
					'value'	=>videotube_core_get_page_array()
				),
				array(
					'type'	=>	'dropdown',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Profile Page','videotube'),
					'param_name'	=>	'profile_url',
					'value'	=>videotube_core_get_page_array()
				),					
				array(
					'type' => 'textfield',
					'heading' => __( 'Extra class name', 'videotube' ),
					'param_name' => 'el_class',
					'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'videotube' )
				)
			)
		);
		vc_map( $args );		
	}
	add_action( 'init' , 'videotube_core_vc_login_form');
}

if( ! function_exists( 'videotube_core_vc_login_form_shortcode' ) ){
	/**
	 * call the widget
	 * @param unknown_type $atts
	 * @param unknown_type $content
	 * @return string
	 */
	function videotube_core_vc_login_form_shortcode( $atts, $content = null ) {
		$output = $title = $el_class = '';
		extract( shortcode_atts( array(
			'title' => '',
			'uploader_url'	=>	'',
			'profile_url'	=>	'',
			'el_class' => ''
		), $atts ) );
		
		ob_start();
		the_widget( 'VideoTube_Core_Widget_LoginForm', $atts, array() );
		$output .= ob_get_clean();
		return $output;
	}

	add_shortcode( 'mars_vc_login_form' , 'videotube_core_vc_login_form_shortcode');
}