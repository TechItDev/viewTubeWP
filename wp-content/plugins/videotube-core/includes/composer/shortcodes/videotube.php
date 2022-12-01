<?php
if( ! defined('ABSPATH') ) exit;

if( ! function_exists( 'videotube_core_map_videotube' ) ){

	function videotube_core_map_videotube(){

		global $_wp_additional_image_sizes;
		$image_size = array();
		if( is_array( $_wp_additional_image_sizes ) ){
			foreach ($_wp_additional_image_sizes as $k=>$v) {
				$image_size[]	=	$k;
			}
		}
		$args = array(
			'name'	=>	esc_html__('Page Builder','videotube-core'),
			'base'	=>	'videotube',
			'category'	=>	esc_html__('VideoTube','videotube-core'),
			'class'	=>	'videotube-core',
			'icon'	=>	'videotube-core',
			'admin_enqueue_css' => array(get_template_directory_uri().'/assets/css/vc.css'),
			'description'	=>	esc_html__('Video/Post Page/Widget Builder.','videotube-core'),
			'params'	=>	array(
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Title','videotube-core'),
					'param_name'	=>	'title'
				),
				array(
					'type'	=>	'iconpicker',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Icon','videotube-core'),
					'param_name'	=>	'icon',
					'settings'	=> array(
						'emptyIcon' => true,
						'type' => 'fontawesome',
						'iconsPerPage' => 50
					)
				),
				array(
					'type'	=>	'dropdown',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Post Type','videotube-core'),
					'param_name'	=>	'post_type',
					'value'	=>array(
						esc_html__('Video','videotube-core') 	=>	'video',
						esc_html__('Post','videotube-core') 	=>	'post'
					),
					'std'	=>	'video'
				),
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'param_name'	=>	'excerpt',
					'value'	=>	array(
						esc_html__('Post Excerpt','videotube-core') 	=>	'on'
					),
					'description'	=>	esc_html__( 'Displays the post excerpt', 'videotube-core' )
				),					
				array(
					'type'	=>	'dropdown',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Type','videotube-core'),
					'param_name'	=>	'type',
					'value'	=>array(
						esc_html__('Main Content','videotube-core') 	=>	'main',
						esc_html__('Widget','videotube-core') 	=>	'widget'	
					)
				),
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'param_name'	=>	'carousel',
					'value'	=>	array(
						esc_html__('Carousel','videotube-core') 	=>	'on'		
					),
					'dependency'	=>	array(
						'element'	=>	'type',
						'value'	=>	'widget'
					)
				),
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'param_name'	=>	'autoplay',
					'value'	=>	array(
						esc_html__('Auto Carousel','videotube-core') 	=>	'on'
					),
					'dependency'	=>	array(
						'element'	=>	'type',
						'value'	=>	'widget'
					)
				),					
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Show Posts','videotube-core'),
					'param_name'	=>	'show',
					'value'	=>	10,
					'description'	=>	esc_html__('How many video will be shown?','videotube-core')
				),					
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Section ID','videotube-core'),
					'param_name'	=>	'id',
					'description'	=>	esc_html__('Enter an unique name if you check on Carousel checkbox.','videotube-core'),
					'value'	=>	'pagebuilder-' . rand(1000, 9999)
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Rows','videotube-core'),
					'param_name'	=>	'rows',
					'description'	=>	esc_html__('How many Rows will be shown?','videotube-core'),
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
					'heading'	=>	esc_html__('Desktop Columns','videotube-core'),
					'param_name'	=>	'columns',
					'description'	=>	esc_html__('How many Columns will be shown on desktop devices?','videotube-core'),
					'value'	=>	3
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Tablet Columns','videotube-core'),
					'param_name'	=>	'tablet_columns',
					'description'	=>	esc_html__('How many Columns will be shown on tablet devices?','videotube-core'),
					'value'	=>	2
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Mobile Columns','videotube-core'),
					'param_name'	=>	'mobile_columns',
					'description'	=>	esc_html__('How many Columns will be shown on mobile devices?','videotube-core'),
					'value'	=>	1
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Thumbnail Image Size','videotube-core'),
					'param_name'	=>	'thumbnail_size',
					'description'	=>	 sprintf(esc_html__('Enter image size. Example: <strong>%s , thumbnail, medium, large, full</strong>, or leave blank for default.','videotube-core'), implode(", ",$image_size))
				),
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Video Category','videotube-core'),
					'param_name'	=>	'cat',
					'description'	=>	esc_html__('Choose the Video Category or leave for default.','videotube-core'),
					'value'	=>	videotube_core_get_video_category_array( 'categories' ),
					'dependency'	=>	array(
						'element'	=>	'post_type',
						'value'	=>	'video'
					)
				),
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Post Category','videotube-core'),
					'param_name'	=>	'post_category',
					'description'	=>	esc_html__('Choose the Post Category or leave for default.','videotube-core'),
					'value'	=>	videotube_core_get_video_category_array( 'category' ),
					'dependency'	=>	array(
						'element'	=>	'post_type',
						'value'	=>	'post'
					)
				),	
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Post Tags','videotube-core'),
					'param_name'	=>	'post_tags',
					'description'	=>	esc_html__('Set Post Tag to a comma separated list of Post Tag slug to only show those.','videotube-core'),
					'dependency'	=>	array(
						'element'	=>	'post_type',
						'value'	=>	'post'
					)
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Video Tags','videotube-core'),
					'param_name'	=>	'tag',
					'description'	=>	esc_html__('Set Video Tag to a comma separated list of Video Tag ID to only show those.','videotube-core'),
					'dependency'	=>	array(
						'element'	=>	'post_type',
						'value'	=>	'video'
					)						
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('IDs','videotube-core'),
					'param_name'	=>	'ids',
					'description'	=>	esc_html__('Set the Video/Post ID to a comma separated list of Video/Post ID to only show those.','videotube-core')					
				),
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Authors','videotube-core'),
					'param_name'	=>	'author',
					'description'	=>	esc_html__('Set Author ID to a comma separated list of Video/Post to only show those.','videotube-core')
				),
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'param_name'	=>	'current_author',
					'value'			=>	array(
						esc_html__('Current Author','videotube-core')	=>	'on'
					),
					'description'	=>	esc_html__('Retrieves current author posts.','videotube-core')
				),	
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'value'			=>	array(
						esc_html__('Current Logged In User','videotube-core')	=>	'on'
					),
					'param_name'	=>	'current_logged_in',
					'description'	=>	esc_html__('Retrieves current logged in user posts.','videotube-core')
				),	
				array(
					'type'	=>	'textfield',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Date','videotube-core'),
					'param_name'	=>	'date',
					'description'	=>	esc_html__('Show posts associated with a certain time, (yyyy-mm-dd)','videotube-core')
				),
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'param_name'	=>	'today',
					'value'	=>	array( esc_html__('Today','videotube-core')	=>	'on' ),
					'description'	=>	esc_html__('Show posts today','videotube-core')
				),
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'param_name'	=>	'thisweek',
					'value'	=>	array( esc_html__('This Week','videotube-core')	=>	'on' ),
					'description'	=>	esc_html__('Show posts this week','videotube-core')
				),	
				array(
					'type'	=>	'checkbox',
					'holder'	=>	'div',
					'class'	=>	'',
					'value'			=>	array(
						esc_html__('Hide empty thumbnail posts','videotube-core')	=>	'on'
					),
					'param_name'	=>	'hide_empty_thumbnail',
					'description'	=>	esc_html__('Do not retrieves empty thumbnail posts.','videotube-core')
				),
				array(
					'type'	=>	'dropdown',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Order by','videotube-core'),
					'param_name'	=>	'orderby',
					'description'	=>	esc_html__('Order by Views/Likes is not available for the Regular Post.','videotube-core'),
					'value'			=>	function_exists( 'post_orderby_options' ) ? array_flip( post_orderby_options() ) : array()
				),
				array(
					'type'	=>	'dropdown',
					'holder'	=>	'div',
					'class'	=>	'',
					'heading'	=>	esc_html__('Order','videotube-core'),
					'param_name'	=>	'order',
					'value'			=>	array_flip( array(
						'DESC'	=>	esc_html__( 'Descending', 'videotube-core' ),
						'ASC'	=>	esc_html__( 'Ascending', 'videotube-core')
					) )
				),

				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'videotube-core' ),
					'param_name' => 'el_class',
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'videotube-core' )
				)					
			)
		);
		vc_map( $args );
	}	
	add_action( 'init' , 'videotube_core_map_videotube');
}