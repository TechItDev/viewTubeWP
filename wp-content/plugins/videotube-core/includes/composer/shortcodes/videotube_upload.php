<?php
if( !defined('ABSPATH') ) exit;
if( !function_exists( 'videotube_core_map_videotube_core_upload' ) ){
	function videotube_core_map_videotube_core_upload(){
		if( !function_exists( 'vc_map' ) )
			return;
		$args = array(
			'name'	=>	__('Upload Form','videotube-core'),
			'base'	=>	'videotube_core_upload',
			'category'	=>	__('VideoTube','videotube-core'),
			'class'	=>	'videotube-core',
			'icon'	=>	'videotube-core',
			'admin_enqueue_css' => array(get_template_directory_uri().'/assets/css/vc.css'),
			'description'	=>	__('Video Upload Form.','videotube-core'),
			'params'	=>	array(	
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Title','videotube-core'),
					'param_name'	=>	'title',
					'description'	=>	__('This title is not displayed at Frontend.','videotube-core')
				),
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'param_name'	=>	'vcategory',
					'value'	=>	array( __('Hide Category field','videotube-core') => 'off' )
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Category Exclude','videotube-core'),
					'param_name'	=>	'cat_exclude',
					'dependency'	=>	array(
						'element'	=>	'vcategory',
						'is_empty'	=>	true
					),						
					'description'	=>	__('A string of category ids to exclude, comma-separated ids.','videotube-core')
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Category Include','videotube-core'),
					'param_name'	=>	'cat_include',
					'dependency'	=>	array(
						'element'	=>	'vcategory',
						'is_empty'	=>	true
					),
					'description'	=>	__('A string of category ids to include, comma-separated ids.','videotube-core')
				),
				array(
					'type'	=>	'dropdown',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Category Order by','videotube-core'),
					'param_name'	=>	'cat_orderby',
					'dependency'	=>	array(
						'element'	=>	'vcategory',
						'is_empty'	=>	true
					),			
					'value'	=>	array(
						__('ID','videotube-core') 	=>	'id',
						__('Count','videotube-core') => 'count',
						__('Name','videotube-core') => 'name',
						__('Slug','videotube-core') => 'slug'
					)
				),	
				array(
					'type'	=>	'dropdown',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Category Order','videotube-core'),
					'param_name'	=>	'cat_order',
					'dependency'	=>	array(
						'element'	=>	'vcategory',
						'is_empty'	=>	true
					),						
					'value'	=>	array(
						__('ASC','videotube-core') 	=>	'ASC',
						__('DESC','videotube-core') => 'DESC'
					)
				),
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'param_name'	=>	'vtag',
					'value'	=>	array( __('Hide Tag field','videotube-core') => 'off' )
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Extra class name', 'videotube-core' ),
					'param_name' => 'el_class',
					'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'videotube-core' )
				)
			)
		);
		vc_map( $args );
	}	
	add_action( 'init' , 'videotube_core_map_videotube_core_upload');
}
