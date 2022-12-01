<?php
/**
 * VideoTube Styling and Typography
 * Add External Style as: Color, Background.
 * @author 		Toan Nguyen
 * @category 	Core
 * @version     1.0.0
 */
if( ! defined('ABSPATH') ) exit;

function videotube_custom_option_style(){
	global $videotube;
	$style = null;
	$child_style = null;
	
	$font_body =  isset( $videotube['typography-body'] ) ? $videotube['typography-body'] : null;
	$font_headings =  isset( $videotube['typography-headings'] ) ? $videotube['typography-headings'] : null;
	$font_menu = isset( $videotube['typography-menu'] ) ? $videotube['typography-menu'] : null;			
	
	if( isset( $videotube['color-header'] ) && $videotube['color-header'] != '#ffffff' ){
		$child_style .= 'div#header{background:'.$videotube['color-header'].'}';
	}
	if( isset( $videotube['color-header-navigation'] ) && $videotube['color-header-navigation'] != '#4c5358' ){
		$child_style .= '#navigation-wrapper{background:'.$videotube['color-header-navigation'] . '}';
		$child_style .= '.dropdown-menu{background:'.$videotube['color-header-navigation'].';}';
	}
	if( isset( $videotube['color-text-header-navigation'] ) && $videotube['color-text-header-navigation'] != 'hsl(0, 100%, 100%)' && !empty( $videotube['color-text-header-navigation'] ) ){
		$child_style .= '#navigation-wrapper ul.menu li a{color:'.$videotube['color-text-header-navigation'].'}';
	}
	if( isset( $videotube['color-widget'] ) && $videotube['color-widget'] != '#e73737' && !empty( $videotube['color-widget'] ) ){
		$child_style .= '.widget.widget-primary .widget-title, .sidebar .wpb_wrapper .widgettitle, .sidebar .widget.widget-builder .widget-title{background:'.$videotube['color-widget'].'}';
	}
	if( isset( $videotube['color-text-widget'] ) && $videotube['color-text-widget'] != 'hsl(0, 100%, 100%)' && !empty( $videotube['color-text-widget'] ) ){
		$child_style .= '.widget.widget-primary .widget-title, .sidebar .wpb_wrapper .widgettitle, .sidebar .widget.widget-builder .widget-title{color:'.$videotube['color-text-widget'].'}';
	}
	
	if( isset( $videotube['color-footer'] ) && $videotube['color-footer'] != '#111111' && !empty( $videotube['color-footer'] ) ){
		$child_style .= '#footer{background:'.$videotube['color-footer'].'}';
	}
	if( isset( $videotube['color-footer-text'] ) && $videotube['color-footer-text'] != '#ffffff' && !empty( $videotube['color-footer-text'] ) ){
		$child_style .= '#footer .widget ul li a, #footer .widget p a{color:'.$videotube['color-footer-text'].'}#footer .widget p{color:'.$videotube['color-footer-text'].'}';
	}
	if( isset( $font_body['font-family'] ) && ! empty( $font_body['font-family'] ) ){
		$child_style .= 'body{font-family:'.$font_body['font-family'].';}';
	}
	if( isset( $font_headings['font-family'] ) && ! empty( $font_headings['font-family'] ) ){
		$child_style .= 'h1,h2,h3,h4,h5,h6 {font-family:'.$font_headings['font-family'].'}';
	}
	if( isset( $font_menu['font-family'] ) && ! empty( $font_menu['font-family'] ) ){
		$child_style .= '#navigation-wrapper ul.menu li a{font-family:'.$font_menu['font-family'].', sans-serif;}';
	}

	if( $child_style ){
		wp_add_inline_style( 'videotube-style', $child_style );	
	}
	
}

add_action( 'wp_enqueue_scripts', 'videotube_custom_option_style', 100 );