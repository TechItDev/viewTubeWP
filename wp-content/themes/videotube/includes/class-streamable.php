<?php
/**
 * Streamable
 *
 * @since 3.1
 *
 */

if( ! defined( 'ABSPATH' ) ){
	exit();
}


if( ! class_exists( 'Mars_Streamable' ) ){
	
	
	class Mars_Streamable{
		
		function __construct(){
			add_action( 'save_post' , array( $this , 'save_post' ), 20, 1 );
		}		
		
		/**
		 * Get the streamable video ID
		 * @param string $url
		 * @return string or null
		 */
		
		function get_video_id( $url ) {
			
			preg_match( '/streamable.com\/(?P<id>(.+))/', $url, $matched );
			
			return isset( $matched['id'] ) ? $matched['id'] : '';
			
		}
		
		/**
		 * Get the streamable embed url
		 * @param string $url
		 */
		
		function get_embed_url( $url ) {	
			if( $video_id = $this->get_video_id( $url ) ){
				return 'https://streamable.com/s/' . $video_id;
			}
			
			return false;
		}
		
		/**
		 * Generate streamable Iframe based on the streamable video url
		 * @param array $args
		 * @return iframe
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
		 * 
		 * Get video content
		 * 
		 * @param string $url
		 */
		function get_thumbnail_url( $url ) {
			
			$thumbnail_url = '';
			
			$apiurl = add_query_arg( array(
				'url'	=>	$url
			), 'https://api.streamable.com/oembed.json' );
			
			$response = wp_remote_get( $apiurl );
			
			if( is_wp_error( $response ) ){
				return $response;
			}
			
			$response = json_decode( wp_remote_retrieve_body( $response ), true );
			
			if( is_array( $response ) && isset( $response['thumbnail_url'] ) ){
				
				$thumbnail_url = remove_query_arg( array( 'height', 'width' ), $response['thumbnail_url'] );
				
				return sprintf( 'http:%s', $thumbnail_url );
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

	$videotube_streamable = new Mars_Streamable();
}