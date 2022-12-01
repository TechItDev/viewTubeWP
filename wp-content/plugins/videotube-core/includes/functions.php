<?php
if( ! defined('ABSPATH') ) exit;

if( ! function_exists( 'videotube_core_get_video_category_array' ) ){

	function videotube_core_get_video_category_array( $taxonomy ){
		$terms_array = array();
		if( !taxonomy_exists( $taxonomy ) )
			return;
		$args = array(
			'orderby'       => 'name',
			'order'         => 'ASC',
			'hide_empty'    => true,
			'exclude'       => array(),
			'exclude_tree'  => array(),
			'include'       => array(),
			'fields'        => 'all',
			'hierarchical'  => true,
			'child_of'      => 0,
			'pad_counts'    => false,
			'cache_domain'  => 'core'
		);
		$terms = get_terms( $taxonomy, $args );
		if ( !empty( $terms ) && !is_wp_error( $terms ) ){
			foreach ( $terms as $term ) {
				$terms_array[ $term->name ]	=	$term->term_id;
			}
		}
		return $terms_array;
	}
}

if( ! function_exists('videotube_core_get_socials_count') ){

	function videotube_core_get_socials_count( $key ) {
		$count = 0;
		switch ($key) {			
			case 'subscriber':
				$result = count_users();
				$count = isset( $result['avail_roles'][$key] ) ? $result['avail_roles'][$key] : 0;
			break;
		}
		return !empty( $count ) ? $count : 0;
	}
}


/**
 *
 * Get user's liked videos
 * 
 * @return array
 */
function videotube_core_get_user_liked_videos(){

	if( ! is_user_logged_in() ){
		return array();
	}

	$liked = get_user_meta( get_current_user_id(), '_liked', true );

	if( empty( $liked ) || ! is_array( $liked ) ){
		$liked = array();
	}

	return array_unique( $liked );
}

function videotube_core_filter_body_classes( $classes ){
	if( ! is_user_logged_in() ){
		return $classes;
	}

	$liked = videotube_core_get_user_liked_videos();

	if( is_singular( 'video' ) && array_search( get_the_ID(), $liked ) ){
		$classes[] = 'has-liked';
	}

	return $classes;
}
add_filter( 'body_class', 'videotube_core_filter_body_classes', 10, 1 );
