<?php

if( !defined( 'ABSPATH' ) ) exit();

if ( ! class_exists( 'WP_Bootstrap_Navwalker' ) ) {
	require_once ( VIDEOTUBE_THEME_DIR . '/includes/class-wp-bootstrap-navwalker.php');
}

if( ! class_exists( 'VideoTube_Walker_Nav_Menu' ) ){
	/**
	 * 		
	 */
	class VideoTube_Walker_Nav_Menu extends WP_Bootstrap_Navwalker{
		
	}
}