<?php
/**
 * Twitter
 * 
 * @since 3.1
 * 
 */

if( ! defined( 'ABSPATH' ) ){
	exit();
}

if( ! class_exists( 'Mars_Twitter' ) ){
	
	class Mars_Twitter{
		
		function __construct() {
			add_action( 'save_post' , array( $this , 'save_post' ), 20, 1 );
		}
		
		/**
		 * Retrieve the Twitter status ID from an url or embed code
		 * @param string $url
		 * @return false or video ID
		 */
		
		function get_status_id( $url ) {
			
			if( empty( $url ) ){
				return false;
			}
			
			preg_match( '~/status/(?:t\.\d+/)?(\d+)~i', $url, $matched );
			
			return isset( $matched[1] ) ? $matched[1] : false;
			
		}
		
		/**
		 * Retrieve the twitter video embed url
		 * @param string $url
		 * @return embed url
		 */
		
		function get_embed_url( $input = '' ) {
			
			$embed_url	=	'';
			
			$id	=	$this->get_status_id( $input );
			
			if( ! empty( $id ) ){
				$embed_url	=	'https://twitter.com/i/videos/' . $id;
			}

			return $embed_url;
		}
		
		/**
		 * Generate twitter Iframe basing on the twitter video url
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
		
		function get_content( $input ) {
			
			$content	=	array();
			
			if( empty( $input ) ){
				return new WP_Error( 'url_not_found', esc_html__( 'URL not found', 'videotube' ) );
			}
			
			$url	=	$this->get_embed_url( $input );
			
			$data =  wp_remote_get( $url );
			
			if( is_wp_error( $data ) ){
				return $data;
			}
			
			$body =  wp_remote_retrieve_body( $data );
			
			preg_match( '/data-config=\"(.*?)\"/', $body, $matched );
			
			if( isset( $matched[1] ) ){
				
				$json = json_decode( str_replace( '&quot;', '"', $matched[1] ), true );
				
				if( isset( $json['videoInfo']['title'] ) ){
					$content['title']	=	$json['videoInfo']['title'];
				}
				
				if( isset( $json['videoInfo']['description'] ) ){
					$content['description']	=	$json['videoInfo']['description'];
				}
				
				if( isset( $json['duration'] ) ){
					$content['length']	=	ceil( $json['duration']/1000 );
				}
				
				if( isset( $json['image_src'] ) ){
					$content['thumbnail_url']	=	$json['image_src'];
				}
				
			}
			return $content;
		}
		
		function save_post( $post_id ){
			if( get_post_type( $post_id ) !== 'video' ){
				return;
			}
			
			if( has_post_thumbnail( $post_id ) ){
				return;
			}
			
			if( $embed = get_post_meta( $post_id, 'video_url', true ) ){
				if( $this->get_status_id( $embed ) != '' ){
					
					$content = $this->get_content( $embed );
					
					if( is_wp_error( $content ) ){
						return;
					}
					
					if( isset( $content['thumbnail_url'] ) ){
						$desc = sprintf( esc_html__( '%s thumbnail', 'videotube' ), get_the_title( $post_id ) );
						$attachment_id = media_sideload_image( $content['thumbnail_url'] , $post_id, $desc, 'id' );
						
						if( $attachment_id ){
							set_post_thumbnail( $post_id , $attachment_id );
						}
					}
				}
			}
		}
		
	}
	
	$videotube_twitter = new Mars_Twitter();
}