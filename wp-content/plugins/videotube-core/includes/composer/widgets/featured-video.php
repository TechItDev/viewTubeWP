<?php
if( !defined('ABSPATH') ) exit;
if( !function_exists( 'videotube_core_vc_featured_videos' ) ){
	function videotube_core_vc_featured_videos() {
		// add the shortcode.
		add_shortcode( 'videotube_core_vc_featured_videos' , 'videotube_core_vc_featured_videos_shortcode');
		add_shortcode( 'mars_vc_featured_videos' , 'videotube_core_vc_featured_videos_shortcode');
		// map the widget.
		if( !function_exists( 'vc_map' ) )
			return;

		global $_wp_additional_image_sizes;
		$image_size = array();
		if( is_array( $_wp_additional_image_sizes ) ){
			foreach ($_wp_additional_image_sizes as $k=>$v) {
				$image_size[]	=	$k;
			}
		}

		$args = array(
			'name'	=>	__('Featured Videos','videotube-core'),
			'base'	=>	'mars_vc_featured_videos',
			'category'	=>	__('VideoTube','videotube-core'),
			'class'	=>	'videotube',
			'icon'	=>	'videotube',
			'description'	=>	__('Display the Featured Videos Widget.','videotube-core'),
			'admin_enqueue_css' => array(get_template_directory_uri().'/assets/css/vc.css'),
			'params'	=>	array(
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Title','videotube-core'),
					'param_name'	=>	'title'
				),
				array(
					'type'	=>	'iconpicker',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Icon','videotube-core'),
					'param_name'	=>	'icon',
					'settings'	=> array(
						'emptyIcon' => true,
						'type' => 'fontawesome',
						'iconsPerPage' => 50
					)
				),		
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'param_name'	=>	'auto',
					'value'	=>	array(
						__('Auto Carousel','videotube-core') 	=>	'on'
					)
				),					
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Number','videotube-core'),
					'param_name'	=>	'video_shows',
					'value'	=>	10
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Rows','videotube-core'),
					'param_name'	=>	'rows',
					'description'	=>	__('How many Rows will be shown?','videotube-core'),
					'value'	=>	1,
					'dependency'	=>	array(
						'element'	=>	'carousel',
						'value'	=>	'on'
					)
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Desktop Columns','videotube-core'),
					'param_name'	=>	'columns',
					'description'	=>	__('How many Columns will be shown on desktop devices?','videotube-core'),
					'value'	=>	3
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Tablet Columns','videotube-core'),
					'param_name'	=>	'tablet_columns',
					'description'	=>	__('How many Columns will be shown on tablet devices?','videotube-core'),
					'value'	=>	2
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Mobile Columns','videotube-core'),
					'param_name'	=>	'mobile_columns',
					'description'	=>	__('How many Columns will be shown on mobile devices?','videotube-core'),
					'value'	=>	1
				),			
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__( 'Video Category','videotube-core'),
					'param_name'	=>	'video_category',
					'description'	=>	__('Choose the Video Category or leave for default.','videotube-core'),
					'value'	=>	videotube_core_get_video_category_array( 'categories' ),
					'dependency'	=>	array(
						'element'	=>	'post_type',
						'value'	=>	'video'
					)
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Video Tags','videotube-core'),
					'param_name'	=>	'video_tag',
					'description'	=>	__('Set Video Tag to a comma separated list of Video Tag ID to only show those.','videotube-core'),
					'dependency'	=>	array(
						'element'	=>	'post_type',
						'value'	=>	'video'
					)
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('IDs','videotube-core'),
					'param_name'	=>	'ids',
					'description'	=>	__('Set the Video/Post ID to a comma separated list of Video/Post ID to only show those.','videotube-core')					
				),
				array(
					'type'	=>	'dropdown',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Order by','videotube-core'),
					'param_name'	=>	'video_orderby',
					'description'	=>	__('Order by Views/Likes is not available for the Regular Post.','videotube-core'),
					'value'			=>	function_exists( 'post_orderby_options' ) ? array_flip( post_orderby_options() ) : array()
				),
				array(
					'type'	=>	'dropdown',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	__('Order','videotube-core'),
					'param_name'	=>	'video_order',
					'value'			=>	array_flip( array(
						'DESC'	=>	esc_html__( 'Descending', 'videotube-core' ),
						'ASC'	=>	esc_html__( 'Ascending', 'videotube-core')
					) )
				)
			)
		);
		vc_map( $args );		
	}
	add_action( 'init' , 'videotube_core_vc_featured_videos');
}

if( !function_exists( 'videotube_core_vc_featured_videos_shortcode' ) ){
	/**
	 * call the widget
	 * @param unknown_type $atts
	 * @param unknown_type $content
	 * @return string
	 */
	function videotube_core_vc_featured_videos_shortcode( $atts, $content = null ) {
		$output = $title = '';

		extract( shortcode_atts( array(
			'title' => '',
		), $atts ) );
		
		ob_start();


		the_widget( 'VideoTube_Core_Widget_Featured_Videos', $atts, array(
			'before_widget' => '<div class="widget widget-featured widget-builder %1$s"><div class="widget-content">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>'
		) );

		$output = ob_get_clean();

		return $output;
	}
}