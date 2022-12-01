<?php
if( !defined('ABSPATH') ) exit;
if( !function_exists( 'videotube_core_add_vc_column_param_fullwidth' ) ){
	
	function videotube_core_add_vc_column_param_fullwidth(){
		if( !function_exists( 'vc_add_param' ) ) return;
		$attributes = array(
			'type' => 'checkbox',
			'heading' => '',
			'param_name' => 'fullwidth',
			'value' => array(
				__('Fullwidth (no wrapper)','mars') 	=>	'on'
			)
		);
		vc_add_param('vc_column', $attributes);
	}	
	add_action( 'init' , 'videotube_core_add_vc_column_param_fullwidth');
}