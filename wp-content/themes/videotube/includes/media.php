<?php
if( !defined( 'ABSPATH' ) ) exit();
if( !function_exists( 'videotube_add_query_vars' ) ){
	/**
	 * Adding tape var
	 * @param array $vars
	 * @return array
	 */
	function videotube_add_query_vars( $vars ) {
		$vars[] = 'tape';
		return $vars;
	}
	add_filter( 'query_vars' , 'videotube_add_query_vars', 100, 1);
}
if( !function_exists( 'videotube_get_video_type' ) ){
	/**
	 * get video type
	 * @param int $post_id
	 * return string.
	 */
	function videotube_get_video_type( $post_id ) {
		if( get_post_type( $post_id ) != 'video' || !$post_id )
			return;
		$type = get_post_meta( $post_id, 'video_type', true ) ? get_post_meta( $post_id, 'video_type', true ) : 'normal';
		return $type;
	}
}

if( !function_exists( 'videotube_get_media_object' ) ){
	/**
	 * Get the media object, this can be a string, int or an array.
	 * @param unknown_type $post_id
	 * @return void|Ambigous <mixed, string, multitype:, boolean, unknown>
	 */
	function videotube_get_media_object( $post_id ) {
		$tape = get_query_var( 'tape' ) ? absint( get_query_var( 'tape' ) ) : 1;
		$tape = $tape - 1; 
		$object = array();
		if( !$post_id ){
			return;	
		}
		if( videotube_get_video_type( $post_id ) == 'files' ){
			$object	= get_post_meta( $post_id, 'video_file', true );
			if( is_array( $object ) && count( $object ) > 1 ){
				// this is an array of media files.
				// get the embed code of the first element.
				if( isset( $object[ $tape ] ) ){
					print videotube_get_embedcode( $object[ $tape ] , $post_id);
				}
				else{
					print videotube_get_embedcode( $object[0] , $post_id);
				}
				
			}
			elseif( is_array( $object ) && count( $object )  == 1 ){
				print videotube_get_embedcode( $object[0] , $post_id);
			}
			else{
				// this is a single media file.
				// this should ne a integer.
				print videotube_get_embedcode( $object , $post_id);				
			}
		}
		else{
			$object = get_post_meta( $post_id, 'video_url', true ) ? get_post_meta( $post_id, 'video_url', true ) : ( get_post_meta( $post_id, 'video_frame', true ) ? get_post_meta( $post_id, 'video_frame', true ) : null );
			$object	=	explode("\r\n", $object);
			$object	=	array_filter( $object );			
			if( is_array( $object ) ){
				if( count( $object ) > 1 ){
					if( isset( $object[ $tape ] ) ){
						print videotube_get_embedcode( $object[ $tape ] , $post_id);
					}
					else{
						print videotube_get_embedcode( $object[0] , $post_id);
					}
				}
				elseif( count( $object ) == 1 ){
					print videotube_get_embedcode( $object[0] , $post_id);
				}
			}
			else{
				print videotube_get_embedcode( $object , $post_id);
			}
		}
	}
	add_action( 'videotube_media' , 'videotube_get_media_object', 10, 1);
}

if( !function_exists( 'videotube_get_embedcode' ) ){
	/**
	 * Return embedcode/iframe
	 * @param string/int $media_object.
	 * return iframe html;
	 */
	function videotube_get_embedcode( $media_object, $post_id ) {
		global $videotube;
		$output = $shortcode = $poster = '';
		// check if this is media file.
		// 'mp4', 'm4v', 'webm', 'ogv', 'wmv', 'flv' support.
		if( videotube_get_video_type( $post_id ) == 'files'){
			$media_object = (int)$media_object;
			// check if this file is publish.
			if( is_integer( $media_object ) ){
				// this is media file id.
				$media_url = wp_get_attachment_url( $media_object );
				if( !$media_url )
					return;
				// set the thumbnail image as poster.
				if( has_post_thumbnail( $post_id ) ){
					$post_thumbnail_id = get_post_thumbnail_id( $post_id );
					$thumb_image = wp_get_attachment_image_src( $post_thumbnail_id,'full' );					
					$poster = $thumb_image[0];
				}
				$media_object_file_args = array(
					'src'		=>		!empty( $media_url ) ? esc_url( $media_url ) : '',
					'poster'	=>		!empty( $poster ) ? esc_url( $poster ) : '',
					'loop'		=>		false,
					'autoplay'	=>		isset( $videotube['autoplay'] ) && $videotube['autoplay'] == 1 ? 'true' : 'false',
					'preload'	=>		'metadata',
					'width'		=>		'750',
					'height'	=>		'442'
				);
			
				$media_object_file_args = apply_filters( 'videotube_media_object_file_args' , $media_object_file_args);
				// extract the array.
				extract($media_object_file_args, EXTR_PREFIX_SAME,'mediapress');
				
				if( shortcode_exists( 'KGVID' ) ){
					$options = get_option('kgvid_video_embed_options');
					$shortcode = '
						[KGVID
							poster="'.$poster.'"
							height="'.$options['height'].'"
							width="'.$options['width'].'"
							autoplay="'.$autoplay.'"
						]
							'.$src.'
						[/KGVID]
					';
				}
				else{
					$shortcode = '
						[video
							src="'.$src.'"
							poster="'.$poster.'"
							loop="'.$loop.'"
							autoplay="'.$autoplay.'"
							preload="'.$preload.'"
							height="'.$height.'"
							width="'.$width.'"
						]
					';					
				}
				$output .= $shortcode;
			}
		}

		if( videotube_get_video_type($post_id) == 'normal'){

			$embed_html = '';
			
			global $videotube_twitter, $videotube_openload, $videotube_facebook, $videotube_streamable;
			
			// get the embed code.
			$videotube_object_url_args = array(
				'width'	=>	'',
				'height'	=>	''	
			);
			$videotube_object_url_args = apply_filters( 'videotube_media_object_url_args' , $videotube_object_url_args );
			

			if( $embed_html = $videotube_twitter->get_iframe( array( 'src' => $media_object ))){
				$output = $embed_html;
			}
			elseif( $embed_html = $videotube_openload->get_iframe( array( 'src' => $media_object ))){
				$output = $embed_html;
			}
			elseif( $embed_html = $videotube_facebook->get_iframe( array( 'src' => $media_object ))){
				$output = $embed_html;
			}
			elseif( $embed_html = $videotube_streamable->get_iframe( array( 'src' => $media_object ))){
				$output = $embed_html;
			}
			elseif( $embed_html = wp_oembed_get( $media_object, $videotube_object_url_args ) ){
				$output = $embed_html;
			}
			elseif( strpos( $media_object, 'dropbox.com/s/' ) !== false ){
				$output = sprintf(
					'<video controls><source src="%s"></video>',
					esc_url( add_query_arg( array(
						'dl'	=>	'1'
					), $media_object ) )
				);
			}
			else{
				$output = $media_object;
			}
		}
		if( ! post_password_required( $post_id ) ){
			$output = apply_filters( 'videotube_player' , $output, $media_object );
			return do_shortcode( $output );
		}
	}
}

if( !function_exists( 'videotube_get_media_pagination' ) ){
	function videotube_get_media_pagination( $post_id ) {
		global $post;
		$tape = get_query_var( 'tape' ) ? absint( get_query_var( 'tape' ) ) : 1;
		$pagination_array = $temp = array();
		if( videotube_get_video_type( $post_id ) == 'normal' ){
			$media_object = get_post_meta( $post_id, 'video_url', true );
			$temp	=	explode("\r\n", $media_object);
			if( is_array( $temp ) && count( $temp ) > 1 ){
				$pagination_array	=	$temp;
			}
		}
		else{
			$media_object = get_post_meta( $post_id, 'video_file', true );
			if( is_array( $media_object ) && !empty( $media_object ) ){
				$pagination_array	=	$media_object;
			}
		}
		$pagination_array	=	array_filter($pagination_array);
		if( is_array( $pagination_array ) && count( $pagination_array ) > 1 ){
			$prefix = get_option( 'permalink_structure' ) ? '?' : '&';
			print '<ul class="pagination pagination-sm post-tape">';
				for ($i = 1; $i <= count( $pagination_array ); $i++) {
					$current_link = isset( $post->ID ) ? esc_url( add_query_arg( array( 'tape'=>$i ) ) ) : null;
					if( !isset( $pagination_array[$tape-1] ) ){
						$tape = 1;
					}
					$current_item = ( $i == $tape ) ? 'current' : null;
					print '<li class="page-item"><a class="page-link '.$current_item.'" href="'.$current_link.'">'.$i.'</a></li>';
				}
			print '</ul>';
		}
	}
	add_action( 'videotube_media_pagination' , 'videotube_get_media_pagination', 10, 1);
}