<?php
if( !defined('ABSPATH') ) exit;
if( !function_exists( 'videotube_core_vc_vt_tag_cloud' ) ){
	function videotube_core_vc_vt_tag_cloud() {
		// add the shortcode.
		add_shortcode( 'videotube_core_vc_vt_tag_cloud' , 'videotube_core_vc_vt_tag_cloud_shortcode');
		// map the widget.
		if( !function_exists( 'vc_map' ) )
			return;
		$args = array(
			'name'	=>	__('VT Tag Cloud','mars'),
			'base'	=>	'videotube_core_vc_vt_tag_cloud',
			'category'	=>	__('WordPress Widgets','mars'),
			'class'	=>	'videotube',
			'icon'	=>	'videotube',
			'description'	=>	__('Display the Tags Cloud (Popular Keys) Widget.','mars'),
			'admin_enqueue_css' => array(get_template_directory_uri().'/assets/css/vc.css'),
			'params'	=>	array(
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Title','mars'),
					'param_name'	=>	'title'
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Taxonomies','mars'),
					'param_name'	=>	'taxonomy',
					'value'	=>	'post_tag,video_tag',
					'description'	=>	__('Separated by commas(,)','mars')
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Smallest Size','mars'),
					'param_name'	=>	'smallest'
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Largest Size','mars'),
					'param_name'	=>	'largest'
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Number Size','mars'),
					'param_name'	=>	'number'
				),
				array(
					'type'	=>	'dropdown',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Format','mars'),
					'param_name'	=>	'format',
					'value'	=>	array_flip(array(
						'flat'	=>	esc_html__( 'Flat', 'mars' ),
						'list'	=>	esc_html__( 'List', 'mars' )
					))
				),
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Show Count','mars'),
					'param_name'	=>	'show_count'
				)
			)
		);
		vc_map( $args );
	}
	add_action( 'init' , 'videotube_core_vc_vt_tag_cloud');
}

if( !function_exists( 'videotube_core_vc_vt_tag_cloud_shortcode' ) ){
	/**
	 * call the widget
	 * @param unknown_type $atts
	 * @param unknown_type $content
	 * @return string
	 */
	function videotube_core_vc_vt_tag_cloud_shortcode( $atts, $content = null ) {
		$output = $title = $el_class = '';
		extract( shortcode_atts( array(
			'title' => '',
			'taxonomy'	=>	'post_tag,video_tag',
			'smallest'	=>	8,
			'largest'	=>	15,
			'number'	=>	20,
			'el_class' => ''
		), $atts ) );

		ob_start();
		the_widget( 'VideoTube_Core_Tags_Cloud_Widget', $atts, array() );
		$output .= ob_get_clean();
		return $output;
	}
}
