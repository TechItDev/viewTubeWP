<?php
/**
 * Facebook
 *
 * @since 3.1
 *
 */

if( ! defined( 'ABSPATH' ) ){
	exit();
}

if( ! class_exists( 'Mars_Facebook' ) ){
	
	class Mars_Facebook{
		
		function __construct(){
		}
		
		/**
		 * Retrieve Facebook video ID
		 * @param string $url
		 * @return false or video ID
		 */
		function get_video_id( $url ) {
			$id	=	'';
			if( empty( $url ) ){
				return false;
			}
			
			preg_match( '~/videos/(?:t\.\d+/)?(\d+)~i', $url, $matched );
			
			$id	=	isset( $matched[1] ) ? $matched[1] : false;
			
			/**
			 * Filter the facebook video ID
			 * @param string video $id
			 * @param string $facebook_url
			 */
			return apply_filters( 'ninetube_get_facebook_video_id' , $id, $url );
		}
		
		/**
		 * Retrieve the Facebook video embed url
		 * @param string $url
		 * @return embed url
		 */
		
		function get_embed_url( $url = '' ) {
			
			$embed_url	=	'';
			
			$id	=	$this->get_video_id( $url );
			
			if( ! empty( $id ) ){
				$embed_url	=	add_query_arg( array( 'video_id' => $id ), 'https://www.facebook.com/video/embed' );
			}
			return $embed_url;
		}
		
		/**
		 * Generate Facebook Iframe basing on the facebook video url
		 * @param array $args
		 * @return iframe
		 */
		function get_iframe( $args ) {

			$args	=	wp_parse_args( $args, array(
				'src'		=>	'',
				'autoplay'	=>	'',
			) );
			
			if( $this->get_embed_url( $args['src'] ) == '' ){
				return;
			}

			if( empty( $args['src'] ) ){
				return;
			}
			
			$args['src'] = add_query_arg( array(
				'href' => $args['src'],
				'show_text'	=>	'0',
				'width'	=>	'750',
			), 'https://www.facebook.com/plugins/video.php' );
			
			if( $args['autoplay'] ){
				$args['src']	=	add_query_arg( array( 'autoplay' => $args['autoplay'] ), $args['src'] );
			}
			
			return videotube_generate_iframe_tag( $args );
			
		}
		
		/**
		 * Retrieve facebook thumbnail image url
		 * @param string facebook url
		 */
		function get_thumbnail_url( $url ) {
			
			$content	=	array();
			
			if( empty( $url ) ){
				return new WP_Error( 'url_not_found', esc_html__( 'URL not found', 'videotube' ) );
			}
			
			$url	=	$this->get_embed_url( $url );
			
			$data =  wp_remote_get( $url );
			
			if( is_wp_error( $data ) ){
				return $data;
			}
			
			$body =  wp_remote_retrieve_body( $data );
			
			preg_match_all('/background-image?\s*?:.*?url\(["|\']??(.+)["|\']??\)/', $body, $matched );
			
			if( isset( $matched[1][0] ) ){
				
				return $matched[1][0];
				
			}
			return new WP_Error( 'error_undefined', esc_html__( 'Error Undefined', 'videotube' ) );
		}
	}
	
	$videotube_facebook = new Mars_Facebook();

}