<?php
/**
 * Openload
 *
 * @since 3.1
 *
 */

if( ! defined( 'ABSPATH' ) ){
	exit();
}


if( ! class_exists( 'Mars_Openload' ) ){

	class Mars_Openload{
		
		function __construct(){
			add_action( 'save_post' , array( $this , 'save_post' ), 20, 1 );
		}
		
		/**
		 * Get the openload video ID
		 * @param string $url
		 * @return string or null
		 */
		
		function get_video_id( $url ) {
			
			// Find the video hash ID or upload ID number.
			preg_match( '/openload.co\/(f|embed)\/(?P<id>.{11}|\d+)/', $url, $matched );
			
			return isset( $matched['id'] ) ? $matched['id'] : '';
			
		}
		
		/**
		 * Get the openload embed url
		 * @param string $url
		 * @since NT 1.0
		 */
		
		function get_embed_url( $url ) {
			
			if( $video_id =  $this->get_video_id( $url ) ){
				return 'https://openload.co/embed/' . $video_id;
			}
			return false;
		}
		
		/**
		 * Generate the openload iframe
		 * @param array $args
		 * @return html iframe
		 */
		
		function get_iframe( $args ) {
			
			$args	=	wp_parse_args( $args, array(
				'src'		=>	'',
				'autoplay'	=>	''
			) );
			
			$args['src']	=	$this->get_embed_url( $args['src'] );
			
			if( empty( $args['src'] ) ){
				return;
			}
			
			if( $args['autoplay'] ){
				$args['src']	=	add_query_arg( array( 'autoplay' => $args['autoplay'] ), $args['src'] );
			}
			
			return videotube_generate_iframe_tag( $args );
		}
		
		/**
		 * Retrieve the Openload video content
		 * @param string $url
		 */
		function get_thumbnail_url( $url ) {
			
			$embed_url	=	$this->get_embed_url( $url );
			
			if( empty( $embed_url ) ){
				return new WP_Error( 'url_not_found', esc_html__( 'URL not found', 'videotube' ) );
			}
			
			$tags	=	get_meta_tags( $embed_url );
			
			if( isset( $tags['og:title'] ) ){
				$content['title']	=	$tags['og:title'];
			}
			
			if( isset( $tags['og:image'] ) ){
				return $tags['og:image'];
			}
			
			return new WP_Error( 'error_undefined', esc_html__( 'Error Undefined', 'videotube' ) );
		}
		
		function save_post( $post_id ){
			if( get_post_type( $post_id ) !== 'video' ){
				return;
			}
			
			if( has_post_thumbnail( $post_id ) ){
				return;
			}
			
			if( $embed = get_post_meta( $post_id, 'video_url', true ) ){
				if( $this->get_video_id( $embed ) != '' ){
					
					$thumbnail_url = $this->get_thumbnail_url( $embed );
					
					if( is_wp_error( $thumbnail_url) ){
						return;
					}
					
					$desc = sprintf( esc_html__( '%s thumbnail', 'videotube' ), get_the_title( $post_id ) );
					$attachment_id = media_sideload_image( $thumbnail_url, $post_id, $desc, 'id' );
					
					if( $attachment_id ){
						set_post_thumbnail( $post_id , $attachment_id );
					}
				}
			}
		}
	}
	
	$videotube_openload = new Mars_Openload();
}