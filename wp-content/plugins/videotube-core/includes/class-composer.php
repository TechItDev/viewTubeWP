<?php
/**
 * JS Composer
 * @author 		Toan Nguyen
 * @category 	Core
 * @version     1.0.0
 */

if( ! function_exists( 'vc_map' ) ){
	return;
}

if( !function_exists( 'videotube_core_get_page_array' ) ){
	function videotube_core_get_page_array() {
		$page_array = array();
		$pages = get_pages( array(
			'sort_order' => 'ASC',
			'sort_column' => 'post_title',
			'post_type' => 'page',
			'post_status'	=>	'publish'
		) );
		if( !empty( $pages ) ){
			foreach ( $pages  as $page) {
				$page_array[ $page->post_title ]	=	$page->ID;
			}
		}
		else{
			$page_array[ __('You have no page, please create a new one.','mars') ] = 0;
		}
		return $page_array;
	}
}
if( function_exists( 'add_shortcode_param' ) ){
	require_once ( VIDEOTUBE_CORE_PATH . 'includes/composer/attribute/order.php');
	require_once ( VIDEOTUBE_CORE_PATH . 'includes/composer/attribute/orderby.php');
	require_once ( VIDEOTUBE_CORE_PATH . 'includes/composer/attribute/fontawesome.php');
	vc_add_shortcode_param( 'order' , 'videotube_core_order_attr');
	vc_add_shortcode_param( 'orderby' , 'videotube_core_orderby_attr');
	vc_add_shortcode_param( 'fontawesome' , 'videotube_core_vc_fontawesome_attr');
}
// templates
require_once ( VIDEOTUBE_CORE_PATH . 'includes/composer/templates/index.php');
// param

// shortcodes
require_once ( VIDEOTUBE_CORE_PATH . 'includes/composer/shortcodes/videotube.php');
require_once ( VIDEOTUBE_CORE_PATH . 'includes/composer/shortcodes/videotube_upload.php');
// widgets.
require_once ( VIDEOTUBE_CORE_PATH . 'includes/composer/widgets/featured-video.php');

require_once ( VIDEOTUBE_CORE_PATH . 'includes/composer/widgets/login-form.php');
require_once ( VIDEOTUBE_CORE_PATH . 'includes/composer/widgets/socialsbox.php');
require_once ( VIDEOTUBE_CORE_PATH . 'includes/composer/widgets/vt-tag-cloud.php');
require_once ( VIDEOTUBE_CORE_PATH . 'includes/composer/widgets/big-post.php');
require_once ( VIDEOTUBE_CORE_PATH . 'includes/composer/widgets/social-count-plus.php');