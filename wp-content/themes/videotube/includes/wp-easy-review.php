<?php
/**
 *
 * WP Easy Review plugin compatibility file
 * 
 */
if( ! defined( 'ABSPATH' ) ){
	exit;
}

/**
 *
 * Add review support for video post
 * 
 * @param  array $array
 * @return array $array
 *
 *
 * @since 3.4.2
 * 
 */
function videotube_easy_review_video_support( $array ){

	if( is_string( $array['screen'] ) ){
		$array['screen'] .= ',video';
	}

	if( is_array( $array['screen'] ) ){
		$array['screen'][] = 'video';
	}

	return $array;

}
add_filter( 'wp-easy-review_metaboxes_pre', 'videotube_easy_review_video_support', 9999, 1 );

// Remove default review box, we use our hook
remove_action( 'the_content' , array( $GLOBALS['wp_easy_review'] , 'the_content' ), 10 , 1 );