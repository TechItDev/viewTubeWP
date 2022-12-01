<?php
if( !defined('ABSPATH') ) exit;
class VideoTube_Core_Custom_Post_Type{
	
	function __construct() {
		add_action('init', array( $this,'video') );
		add_filter('manage_edit-video_columns' , array($this,'cpt_columns'));
		add_action( "manage_video_posts_custom_column", array($this,'modify_column'), 10, 2 );		
	}

	public function video() {

		global $videotube;

		$rewrite_slug = 'video';

		if( isset( $videotube['rewrite_slug'] ) && ! empty( $videotube['rewrite_slug'] ) ){
			$rewrite_slug = sanitize_key( $videotube['rewrite_slug'] );
		}

		$args = array(
			'label' => __('Videos','mars'),
			'description' => '',
			'public' => true,
			'has_archive'	=>true,
			'show_ui' => true,
			'show_in_menu' => true,
			'capability_type' => 'post',
			'map_meta_cap' => true,
			'hierarchical' => false,
			'menu_icon'	=>	'dashicons-video-alt',
			'rewrite' => array('slug' => $rewrite_slug, 'with_front' => true),
			'query_var' => true,
			'supports' => array('title','editor','publicize','comments','thumbnail','author','post-formats'),
			'labels' => array (
				  'name' => 'Videos',
				  'singular_name' => __('Videos','mars'),
				  'menu_name' => __('Videos','mars'),
				  'add_new' => __('Add Videos','mars'),
				  'add_new_item' => __('Add New Videos','mars'),
				  'edit' => __('Edit','mars'),
				  'edit_item' => __('Edit Videos','mars'),
				  'new_item' => __('New Videos','mars'),
				  'view' => __('View Videos','mars'),
				  'view_item' => __('View Videos','mars'),
				  'search_items' => __('Search Videos','mars'),
				  'not_found' => __('No Videos Found','mars'),
				  'not_found_in_trash' => __('No Videos Found in Trash','mars'),
				  'parent' => __('Parent Videos','mars'),
				)
		);
		$args	=	apply_filters( 'mars_video_post_type_args' , $args);
		register_post_type('video', $args); 
	}

	function cpt_columns($columns){
		$new_columns = array(
			'user'	=>	__('Author','videotube'),
			'likes'	=>	__('Likes','videotube'),
			'views'	=>	__('Views','videotube'),
			'layout'	=>	__('Layout','videotube')
		);
		unset( $columns['author'] );
	    return array_merge($columns, $new_columns);			
	}
	
	function modify_column($column, $post_id){
		switch ($column) {
			case 'user':
				$video = get_post( $post_id );
				echo get_avatar( $video->post_author, 64 );
			break;
			case 'likes':
				echo videotube_get_like_count($post_id);
			break;
			case 'views':
				//print videotube_get_count_viewed();
				echo get_post_meta($post_id,'count_viewed',true) ? get_post_meta($post_id,'count_viewed',true) : 1;
			break;
			case 'layout':
				$layout = get_post_meta($post_id,'layout',true) ? get_post_meta($post_id,'layout',true) : 'small';
				echo $layout;
			break;
		}	
	}		
}

new VideoTube_Core_Custom_Post_Type();