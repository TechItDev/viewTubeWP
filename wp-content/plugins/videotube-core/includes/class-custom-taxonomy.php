<?php
if( !defined('ABSPATH') ) exit;
class VideoTube_Core_Custom_Taxonomy{
	
	function __construct() {
		add_action('init', array($this,'cptui_register_my_taxes_categories'));
		add_action('init', array($this,'cptui_register_my_taxes_key'));
		
		add_filter( 'cptui_register_my_taxes_categories' , array( $this, 'rewrite_taxes_categories' ), 10, 1 );
		add_filter( 'cptui_register_my_taxes_key' , array( $this, 'rewrite_taxes_key' ), 10, 1 );
		
		add_action( 'restrict_manage_posts', array( $this , 'category_filter' ), 10, 2 );
	}

	function cptui_register_my_taxes_categories() {
		$labels = array(
			'name'              => __( 'Video Category', 'mars' ),
			'singular_name'     => __( 'Video Category', 'mars' ),
			'search_items'      => __( 'Search Video Category','mars' ),
			'all_items'         => __( 'All Video Category','mars' ),
			'parent_item'       => __( 'Parent Video Category','mars' ),
			'parent_item_colon' => __( 'Parent Video Category:','mars' ),
			'edit_item'         => __( 'Edit Video Category','mars' ),
			'update_item'       => __( 'Update Video Category','mars' ),
			'add_new_item'      => __( 'Add New Video Category','mars' ),
			'new_item_name'     => __( 'New Video Category','mars' ),
			'menu_name'         => __( 'Video Category','mars' ),
		);
		
		$args = array( 'hierarchical' => true,
			'label' => __( 'Video Category', 'mars' ),
			'show_ui' => true,
			'query_var' => true,
			'show_admin_column' => true,
			'labels' => $labels,
			'rewrite'    => array( 'slug' => 'categories' ),
			'show_tagcloud'	=>	true
		);
		$args = apply_filters( 'cptui_register_my_taxes_categories' , $args);
		register_taxonomy( 'categories',array ( 0 => 'video' ), $args ); 
	}
	
	function category_filter( $post_type, $which ){
		if( $post_type != 'video' ){
			return;
		}
		
		$taxonomy_slug = 'categories';
		$taxonomy      = get_taxonomy($taxonomy_slug);
		$selected      = '';
		$request_attr  = 'categories'; //this will show up in the url
		
		if ( isset( $_REQUEST[ $request_attr ] ) ) {
			$selected = $_REQUEST[ $request_attr ]; //in case the current page is already filtered
		}
		
		wp_dropdown_categories(array(
			'show_option_all' =>  __("Show All {$taxonomy->label}"),
			'taxonomy'        =>  $taxonomy_slug,
			'name'            =>  $request_attr,
			'orderby'         =>  'name',
			'selected'        =>  $selected,
			'hierarchical'    =>  true,
			'depth'           =>  3,
			'show_count'      =>  true, // Show number of post in parent term
			'hide_empty'      =>  false, // Don't show posts w/o terms
			'value_field'	  =>  'slug'
		) );
	}
	
	function rewrite_taxes_categories( $args ){
		global $videotube;
		if( isset( $videotube['rewrite_slug_category'] ) && $videotube['rewrite_slug_category'] != 'categories' ){
			$args['rewrite'] = array(
				'slug'	=> esc_attr( $videotube['rewrite_slug_category'] )
			);
		}
		return $args;
	}
	
	function cptui_register_my_taxes_key() {
		$labels = array(
			'name'              => __( 'Video Tag', 'mars' ),
			'singular_name'     => __( 'Video Tag', 'mars' ),
			'search_items'      => __( 'Search Video Tag','mars' ),
			'all_items'         => __( 'All Video Tag','mars' ),
			'parent_item'       => __( 'Parent Video Tag','mars' ),
			'parent_item_colon' => __( 'Parent Video Tag:','mars' ),
			'edit_item'         => __( 'Edit Video Tag','mars' ),
			'update_item'       => __( 'Update Video Tag','mars' ),
			'add_new_item'      => __( 'Add New Video Tag','mars' ),
			'new_item_name'     => __( 'New Video Tag','mars' ),
			'menu_name'         => __( 'Video Tag','mars' ),
		);
		$args = array( 'hierarchical' => false,
			'label' => __( 'Video Tag', 'mars' ),
			'rewrite'    => array( 'slug' => 'video_tag' ),
			'show_ui' => true,
			'query_var' => true,
			'show_admin_column' => true,
			'labels' => $labels,
			'show_tagcloud'	=>	true
		);
		$args = apply_filters( 'cptui_register_my_taxes_key' , $args);
		register_taxonomy( 'video_tag',array ( 0 => 'video' ), $args ); 
	}	

	function rewrite_taxes_key( $args ){
		global $videotube;
		if( isset( $videotube['rewrite_slug_tag'] ) && $videotube['rewrite_slug_tag'] != 'categories' ){
			$args['rewrite'] = array(
				'slug'	=> esc_attr( $videotube['rewrite_slug_tag'] )
			);
		}
		return $args;
	}	
}

new VideoTube_Core_Custom_Taxonomy();