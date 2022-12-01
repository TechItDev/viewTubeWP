<?php
if( ! defined( 'ABSPATH' ) ){
	exit;
}

if( ! function_exists( 'videotube_do_ajax_add_comment' ) ){
	/**
	 *
	 * AJAX add comment
	 *
	 */
	function videotube_do_ajax_add_comment(){

		if( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( $_POST['_ajax_nonce'], 'do_ajax_security' ) ){
			wp_send_json_error( array(
				'msg'	=>	esc_html__( 'Invalid requested.', 'videotube' )
			) );
		}		

		$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );

		if( is_wp_error( $comment ) ){
			wp_send_json_error( array(
				'msg'	=>	$comment->get_error_message()
			) );			
		}

		$output = '';

		ob_start();

		$GLOBALS['comment'] = $comment;

		videotube_theme_comment_style( $comment , array(
			'avatar_size'	=>	64,
			'max_depth'		=>	get_option( 'thread_comments_depth' ),
			'callback'		=>	'videotube_theme_comment_style'
		), videotube_get_comment_depth( $comment )+1 );

		$output = ob_get_clean() . '</li>';

		wp_send_json_success( array(
			'msg'			=>	esc_html__( 'Your comment has been posted.', 'videotube' ),
			'comment'		=>	$comment,
			'comment_count'	=>	get_comments_number_text( false, false, false, $_POST['comment_post_ID'] ),
			'output'		=>	$output
		) );
	}

	add_action('wp_ajax_vt_ajax_comment', 'videotube_do_ajax_add_comment' );
	add_action('wp_ajax_nopriv_vt_ajax_comment', 'videotube_do_ajax_add_comment' );
}

if( ! function_exists( 'videotube_do_ajax_load_comments' ) ){
	/**
	 *
	 * AJAX add comment
	 *
	 */
	function videotube_do_ajax_load_comments(){

		if( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( $_POST['_ajax_nonce'], 'do_ajax_security' ) ){
			wp_send_json_error( array(
				'msg'	=>	esc_html__( 'Invalid requested.', 'videotube' )
			) );
		}

		$output = '';

		$post_id	= absint( $_POST['post_id'] );
		$cpage		= absint( $_POST['cpage'] );

		if( get_option( 'comment_order' ) == 'asc' ){
			$cpage++;
		}
		else{
			if( $cpage > 0 ){
				$cpage--;
			}
		}

		if( $cpage > 0 ){

			$GLOBALS['post'] = get_post( $post_id );

			$output = wp_list_comments( array_merge( videotube_comments_list_args(), array(
				'page'				=>	$cpage,
				'per_page'			=>	get_option('comments_per_page'),
				'echo'				=>	false
			) ) );
		}

		wp_send_json_success( array(
			'output'		=>	$output,
			'cpage'			=>	$cpage
		) );

	}
	add_action('wp_ajax_vt_ajax_load_comments', 'videotube_do_ajax_load_comments' );
	add_action('wp_ajax_nopriv_vt_ajax_load_comments', 'videotube_do_ajax_load_comments' );	
}

if( ! function_exists( 'videotube_do_ajax_like_video' ) ){
	
	function videotube_do_ajax_like_video() {
		// Always check the ajax referer
		check_ajax_referer( 'do_ajax_security', '_ajax_nonce' );
		
		global $videotube;
	
		$videotube = wp_parse_args( $videotube, array(
			'guestlike'	=>	0
		));
		
		if( $videotube['guestlike'] == 0 && ! is_user_logged_in() ){
			echo json_encode( array(
				'resp'			=>	'error',
				'message'		=>	esc_html__( 'Sorry, you have to log in to like this video.', 'videotube' )
			) );
			exit;
		}
		
		$post_id = isset( $_POST['post_id'] ) ? (int)$_POST['post_id'] : false;
		
		if( ! $post_id|| get_post_type( $post_id) != 'video' ){
			echo json_encode( array(
				'resp'			=>	'error',
				'message'		=>	esc_html__( 'Sorry, You cannot like this video.', 'videotube' )
			) );
			exit;
		}

		$cookie_name = 'postlike-' . $post_id;

		if( false !== get_transient( $cookie_name ) ){
			echo json_encode( array(
				'resp'			=>	'error',
				'message'		=>	esc_html__( 'You are liking video too quickly, slow down.', 'videotube' )
			) );
			exit;			
		}

		$like_count = (int)get_post_meta( $post_id, 'like_key', true );

		if( ! array_key_exists( $cookie_name, $_COOKIE ) ){
			$like_count = $like_count+1;
			// add new like.
			update_post_meta( $post_id, 'like_key', $like_count );			

			if( is_user_logged_in() && function_exists( 'videotube_core_get_user_liked_videos' ) ){

				$user_like = array_merge( videotube_core_get_user_liked_videos(), array( $post_id ) );

				update_user_meta( get_current_user_id(), '_liked', array_unique(array_unique( $user_like )) );
			}

			set_transient( $cookie_name, '1', 10 );

			setcookie( $cookie_name, time() - ( 15 * 60 ) );

			echo json_encode( array(
				'resp'			=>	'success',
				'count'			=>	videotube_format_number( $like_count, 0 ),
				'action'		=>	'liked',
				'message'		=>	esc_html__( 'You liked this video.', 'videotube' )
			) );
			exit;
		}
		else{

			if( $like_count > 0 ){
				$like_count = $like_count-1;
				update_post_meta( $post_id, 'like_key', $like_count );
			}

			if( is_user_logged_in() && function_exists( 'videotube_core_get_user_liked_videos' ) ){

				$user_like = videotube_core_get_user_liked_videos();

				$pos = array_search( $post_id, $user_like );

				if( false != $pos ){
					unset( $user_like[ $pos ] );
					update_user_meta( get_current_user_id(), '_liked', array_unique( array_merge( $user_like ) ) );
				}
			}

			set_transient( $cookie_name, '1', 10 );

			setcookie( $cookie_name, "", time()-3600 );

			echo json_encode( array(
				'resp'			=>	'success',
				'count'			=>	videotube_format_number( $like_count, 0 ),
				'action'		=>	'unliked',
				'message'		=>	esc_html__( 'You unliked this video.', 'videotube' )
			) );
			exit;
		}
		
	}
	
	add_action('wp_ajax_do_ajax_like_video', 'videotube_do_ajax_like_video' );
	add_action('wp_ajax_nopriv_do_ajax_like_video', 'videotube_do_ajax_like_video' );
}

if( ! function_exists( 'videotube_do_ajax_upload_video_file' ) ){
	
	function videotube_do_ajax_upload_video_file() {
		// Always check the ajax referer
		check_ajax_referer( 'do_ajax_security', '_ajax_nonce' );
		
		global $videotube;
		
		$videotube = wp_parse_args( $videotube, array(
			'submit_permission'	=>	'off',
			'videosize'			=>	10
		) );
		
		$videotube['videosize'] = (int)$videotube['videosize']*1024*1024;
		
		if( $videotube['videosize'] > wp_max_upload_size() ){
			$videotube['videosize'] = wp_max_upload_size();
		}
		
		if( $videotube['submit_permission'] == 'off' && ! is_user_logged_in() ){
			echo json_encode( array(
				'resp'	=>	'error',
				'message'	=>	esc_html__( 'Please login to upload video.', 'videotube' )
			) );
			exit;
		}
		
		if( ! isset( $_FILES[ 'file' ] ) ){
			echo json_encode( array(
				'resp'			=>	'error',
				'message'		=>	esc_html__( 'Invalid request.', 'videotube' )
			) );
			exit;
		}
		
		$default_types = wp_get_video_extensions();
		$type = wp_check_filetype( $_FILES[ 'file' ]['name'], wp_get_mime_types() );
		
		if ( ! in_array( strtolower( $type['ext'] ), $default_types ) ) {
			echo json_encode( array(
				'resp'			=>	'error',
				'message'		=>	esc_html__( 'The upload file format is not supported, please upload a video instead.', 'videotube' )
			) );
			exit;
		}
		
		$file_size = isset( $_FILES['file']['size'] ) ? (int)$_FILES['file']['size'] : 0;
		// Check filesize
		if( $file_size > $videotube['videosize'] ){
			echo json_encode( array(
				'resp'			=>	'error',
				'message'		=>	sprintf(
					esc_html__( 'File size (%sMB) has exceeded the limit (%sMB).', 'videotube' ),
					ceil( $file_size/1024/1024),
					ceil( $videotube['videosize']/1024/1024 )
				)
			) );
			exit;
		}
		
		$errors = new WP_Error();
		
		/**
		 * 
		 * Filter the errors
		 * @param WP_Error $errors
		 * @param $file
		 * 
		 */
		$errors = apply_filters( 'do_ajax_upload_video_file' , $errors, $_FILES['file'] );
		
		if( $errors->get_error_code() ){
			echo json_encode( array(
				'resp'			=>	'error',
				'message'		=>	$errors->get_error_message()
			) );
			exit;
		}
		
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		
		$movefile = wp_handle_upload( $_FILES['file'], array( 'test_form' => false ) );
		
		if ( $movefile && ! isset( $movefile['error'] ) ) {
			
			$post_data	=	array(
				'post_mime_type'=> $movefile['type'],
				'post_title'    => preg_replace( '/\.[^.]+$/', '', basename( $_FILES['file']['name'] ) ),
				'post_status'   => 'inherit',
				'post_author'	=> get_current_user_id(),
				'post_parent'	=>	0
			);
			
			$attachment_id = wp_insert_attachment( $post_data, $movefile['file'], true );
			
			if( is_wp_error( $attachment_id ) ){
				@unlink( $movefile['file'] );
				echo json_encode( array(
					'resp'			=>	'error',
					'message'		=>	$attachment_id->get_error_message()
				) );
				exit;
			}
			else{
				// Update attachment metadata
				$attach_data = wp_generate_attachment_metadata( $attachment_id, $movefile['file'] );
				wp_update_attachment_metadata( $attachment_id, $attach_data );
			}
			
			echo json_encode( array(
				'resp'			=>	'success',
				'attachment_id'		=>	$attachment_id,
				'attachment_name'	=>	$_FILES['file']['name'],
				'attachment_url'	=>	wp_get_attachment_url( $attachment_id ),
				'message'		=>	'OK'
			) );
			exit;
		}
		
		echo json_encode( array(
			'resp'			=>	'error',
			'message'		=>	sprintf(
				esc_html__( 'Error: %s, cannot upload file.', 'videotube' ),
				$movefile['error']
			)
		) );
		exit;
		
	}
	
	add_action( 'wp_ajax_do_ajax_upload_video_file' , 'videotube_do_ajax_upload_video_file');
	add_action( 'wp_ajax_nopriv_do_ajax_upload_video_file' , 'videotube_do_ajax_upload_video_file');
}


if( ! function_exists( 'videotube_do_ajax_upload_image_file' ) ){
	
	function videotube_do_ajax_upload_image_file() {
		// Always check the ajax referer
		check_ajax_referer( 'do_ajax_security', '_ajax_nonce' );
		
		global $videotube;
		
		$videotube = wp_parse_args( $videotube, array(
			'submit_permission'	=>	'off',
			'imagesize'			=>	2
		) );
		
		$videotube['imagesize'] = (int)$videotube['imagesize']*1024*1024;
		
		if( $videotube['imagesize'] > wp_max_upload_size() ){
			$videotube['imagesize'] = wp_max_upload_size();
		}
		
		if( $videotube['submit_permission'] == 'off' && ! is_user_logged_in() ){
			echo json_encode( array(
				'resp'	=>	'error',
				'message'	=>	esc_html__( 'Please login to upload video.', 'videotube' )
			) );
			exit;
		}
		
		if( ! isset( $_FILES[ 'file' ] ) ){
			echo json_encode( array(
				'resp'			=>	'error',
				'message'		=>	esc_html__( 'Invalid request.', 'videotube' )
			) );
			exit;
		}
		
		$check = wp_check_filetype( $_FILES[ 'file' ]['name'] );
		
		if( ! $check['type'] || ! in_array( $check['type'], array( 'image/jpeg', 'image/gif', 'image/png' ) ) ){
			print json_encode( array(
				'resp'			=>	'error',
				'message'		=>	esc_html__( 'The upload file format is not supported, please upload an image instead.', 'videotube' )
			) );
			exit;
		}
		
		$file_size = isset( $_FILES['file']['size'] ) ? (int)$_FILES['file']['size'] : 0;
		// Check filesize
		if( $file_size > $videotube['imagesize'] ){
			echo json_encode( array(
				'resp'			=>	'error',
				'message'		=>	sprintf(
					esc_html__( 'File size (%sMB) has exceeded the limit (%sMB).', 'videotube' ),
					ceil( $file_size/1024/1024),
					ceil( $videotube['imagesize']/1024/1024 )
				)
			) );
			exit;
		}
		
		$errors = new WP_Error();
		
		/**
		 *
		 * Filter the errors
		 * @param WP_Error $errors
		 * @param $file
		 *
		 */
		$errors = apply_filters( 'do_ajax_upload_image_file' , $errors, $_FILES['file'] );
		
		if( $errors->get_error_code() ){
			echo json_encode( array(
				'resp'			=>	'error',
				'message'		=>	$errors->get_error_message()
			) );
			exit;
		}
		
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		
		$movefile = wp_handle_upload( $_FILES['file'], array( 'test_form' => false ) );
		
		if ( $movefile && ! isset( $movefile['error'] ) ) {
			
			$post_data	=	array(
				'post_mime_type'=> $movefile['type'],
				'post_title'    => preg_replace( '/\.[^.]+$/', '', basename( $_FILES['file']['name'] ) ),
				'post_status'   => 'inherit',
				'post_author'	=> get_current_user_id(),
				'post_parent'	=>	0
			);
			
			$attachment_id = wp_insert_attachment( $post_data, $movefile['file'], true );
			
			if( is_wp_error( $attachment_id ) ){
				@unlink( $movefile['file'] );
				echo json_encode( array(
					'resp'			=>	'error',
					'message'		=>	$attachment_id->get_error_message()
				) );
				exit;
			}
			else{
				// Update attachment metadata
				$attach_data = wp_generate_attachment_metadata( $attachment_id, $movefile['file'] );
				wp_update_attachment_metadata( $attachment_id, $attach_data );
			}
			
			$thumbnail = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
			
			echo json_encode( array(
				'resp'				=>	'success',
				'attachment_id'		=>	$attachment_id,
				'attachment_name'	=>	$_FILES['file']['name'],
				'attachment_url'	=>	$thumbnail[0],
				'message'			=>	__( 'Uploaded', 'videotube' )
			) );
			exit;
		}
		
		echo json_encode( array(
			'resp'			=>	'error',
			'message'		=>	sprintf(
				esc_html__( 'Error: %s, cannot upload file.', 'videotube' ),
				$movefile['error']
			)
		) );
		exit;
		
	}
	
	add_action( 'wp_ajax_do_ajax_upload_image_file' , 'videotube_do_ajax_upload_image_file');
	add_action( 'wp_ajax_nopriv_do_ajax_upload_image_file' , 'videotube_do_ajax_upload_image_file');
}

if( ! function_exists( 'videotube_do_ajax_submit_video' ) ){
	
	function videotube_do_ajax_submit_video() {
		
		// Always check the ajax referer
		check_ajax_referer( 'do_ajax_security', '_ajax_nonce' );
		
		global $videotube;
		
		$edit = false;
		
		$videotube = wp_parse_args( $videotube, array(
			'submit_permission'		=>	'off',
			'submit_status'			=>	'pending',
			'submit_assigned_user'	=>	1,
			'submit_redirect_to'	=>	''
		) );
		
		if( $videotube['submit_permission'] == 'off' && ! is_user_logged_in() ){
			echo json_encode( array(
				'resp'	=>	'error',
				'message'	=>	esc_html__( 'Please login to submit video.', 'videotube' )
			) );
			exit;
		}
		
		$data = array();
		
		if( ! isset( $_POST['data'] ) ){
			echo json_encode( array(
				'resp'		=>	'error',
				'message'	=>	esc_html__( 'Invalid request', 'videotube' )
			) );
			exit;
		}
		else{
			$data = $_POST['data'];
		}
		
		parse_str( $_POST['data'], $data );
		
		$data = wp_parse_args( $data, array(
			'post_id'				=>	'',
			'post_content'			=>	'',
			'video_category'		=>	array(),
			'video_tag'				=>	'',
			'video_type'			=>	'',
			'comment_status'		=>	'open',
			'attachment_id'			=>	'',
			'_thumbnail_id'			=>	'',
			'layout'				=>	'small',
			'video_type'			=>	'video_link_type'
		) );

		if( ! $data['video_type'] ){
			$data['video_type'] = 'video_link_type';
		}
		
		if( ! empty( $data['post_id'] ) ){
			if( ! videotube_can_user_edit_video( $data['post_id'] ) ){
				echo json_encode( array(
					'resp'	=>	'error',
					'message'	=>	esc_html__( 'Sorry, you do not have permission for updating this video.', 'videotube' )
				) );
				exit;
			}
			else{
				$edit = true;
			}
		}
		
		if( empty( $data['post_title'] ) ){
			echo json_encode( array(
				'resp'	=>	'error',
				'message'	=>	esc_html__( 'The video title is required.', 'videotube' )
			) );
			exit;
		}
		
		$post_args = array(
			'post_type'		=>	'video',
			'post_status'	=>	$videotube['submit_status'],
			'post_title'	=>	$data['post_title'],
			'post_content'	=>	$data['post_content'],
			'post_author'	=>	is_user_logged_in() ? get_current_user_id() : $videotube['submit_assigned_user'],
			'comment_status'	=>	$data['comment_status'],
			'meta_input'		=>	array(
				'layout'	=>	$data['layout']
			)
		);
		
		/**
		 * Error handler
		 * @since Videotube V2.2.7
		 */
		$errors = new WP_Error();

		if( ! $edit ){
			switch ( $data['video_type']) {
				case 'video_link_type':
					if( ! empty( $data['video_url'] ) ){
						$post_args['meta_input']['video_url'] = $data['video_url'];
					}
					else{
						$errors->add( 'empty_video_url', __( 'Please enter a video url', 'videotube' ) );
					}
				break;
				
				case 'embed_code_type':
					if( ! empty( $data['embed_code'] ) ){
						$post_args['meta_input']['video_url'] = $data['embed_code'];
					}
					else{
						$errors->add( 'empty_video_url', __( 'Please enter an embed', 'videotube' ) );
					}
				break;
				
				case 'file_type':
					if( ! empty( $data['attachment_id'] ) ){
						$post_args['meta_input']['video_file'] = $data['attachment_id'];
						$post_args['meta_input']['video_type'] = 'files';
					}
					else{
						$errors->add( 'empty_video_url', __( 'Please upload a video file.', 'videotube' ) );
					}
				break;
			}
		}
		
		if( $data['_thumbnail_id'] ){
			$post_args['meta_input']['_thumbnail_id'] = $data['_thumbnail_id'];
		}
		
		$errors	=	apply_filters( 'do_ajax_submit_video_errors' , $errors, $data );
		
		if ( $errors->get_error_code() ) {
			echo json_encode(array(
				'resp'	=>	'error',
				'message'	=>	$errors->get_error_message()
			));
			exit;
		}
		
		/**
		 * 
		 * Filter the post args
		 * 
		 * @param array $post_args
		 * @param array $data
		 * 
		 */
		$post_args =	apply_filters( 'videotube_submit_data_args' , $post_args, $data );
		
		if( ! $edit ){
			$post_id = wp_insert_post( $post_args, true);
		}
		else{
			$update_args = array(
				'ID'			=>	$data['post_id'],
				'post_title'	=>	$data['post_title'],
				'post_name'		=>	sanitize_title( $data['post_title'] ),
				'post_content'	=>	$data['post_content']
			);
			
			if( ! empty( $data['_thumbnail_id'] ) ){
				$update_args['meta_input']['_thumbnail_id'] = $data['_thumbnail_id'];
			}
			
			$post_id = wp_update_post($update_args);
		}
		
		if ( is_wp_error( $post_id ) ){
			echo json_encode(array(
				'resp'	=>	'error',
				'message'	=>	$post_id->get_error_message()
			));
			exit;
		}
		
		if( ! empty( $data['attachment_id'] ) && videotube_can_user_edit_video( $data['attachment_id'] ) ){
			wp_update_post( array(
				'ID'			=>	$data['attachment_id'],
				'post_parent'	=>	$post_id
			) );
		}
		
		if( ! empty( $data['_thumbnail_id'] ) ){
			wp_update_post( array(
				'ID'			=>	$data['_thumbnail_id'],
				'post_parent'	=>	$post_id
			) );
		}
		
		if( is_array( $data['video_category'] ) ){
			wp_set_post_terms( $post_id, $data['video_category'],'categories',false );
		}
		else{
			wp_delete_object_term_relationships( $post_id, 'video_category' );
		}
		
		if( ! empty( $data['video_tag'] ) ){
			wp_set_post_terms( $post_id, $data['video_tag'],'video_tag',true);
		}
		
		$redirect_to = ! empty( $videotube['submit_redirect_to'] ) ? get_permalink( $videotube['submit_redirect_to'] ) : home_url( '/' );
		
		if( current_user_can( 'level_1' ) || get_post_status( $post_id ) == 'publish' ){
			$redirect_to = get_permalink( $post_id );
		}
		
		if( $edit ){
			$redirect_to = add_query_arg( array( 'action' => 'edit-video', 'resp' => 'success' ), $redirect_to );
		}
		
		/**
		 * 
		 * Fires after post saved.
		 * 
		 */
		do_action( 'videotube_save_post', $post_id, $post_args, $data , $edit );
		
		echo json_encode(array(
			'resp'			=>	'success',
			'message'		=>	__('Submitted','videotube'),
			'post_id'		=>	$post_id,
			'redirect_to'	=>	$redirect_to
		));
		exit;
		
	}
	add_action( 'wp_ajax_do_ajax_submit_video' , 'videotube_do_ajax_submit_video');
	add_action( 'wp_ajax_nopriv_do_ajax_submit_video' , 'videotube_do_ajax_submit_video');
}

if( ! function_exists( 'videotube_do_ajax_delete_video' ) ){
	
	function videotube_do_ajax_delete_video() {
		
		// Always check the ajax referer
		check_ajax_referer( 'do_ajax_security', '_ajax_nonce' );
		
		$post_id = isset( $_POST['post_id'] ) ? (int)$_POST['post_id'] : false;
		
		if( ! $post_id ){
			echo json_encode( array(
				'resp'		=>	'error',
				'message'	=>	esc_html__( 'Invalid request', 'videotube' )
			) );
			exit;
		}
		
		if( ! videotube_can_user_edit_video( $post_id ) ){
			echo json_encode( array(
				'resp'	=>	'error',
				'message'	=>	esc_html__( 'Sorry, you do not have permission for deleting this video.', 'videotube' )
			) );
			exit;
		}
		
		$errors = new WP_Error();
		$errors=	apply_filters( 'do_ajax_delete_video_errors' , $errors, $data );
		
		if ( $errors->get_error_code() ) {
			echo json_encode(array(
				'resp'		=>	'error',
				'message'	=>	$errors->get_error_message()
			));
			exit;
		}
		
		wp_delete_post( $post_id );
		
		$redirect_to = apply_filters( 'ajax_delete_video_redirect_to' , home_url( '/' ));
		
		echo json_encode( array(
			'resp'			=>	'success',
			'redirect_to'	=>	$redirect_to,
			'message'		=>	esc_html__( 'Deleted', 'videotube' )
		) );
		exit;
	}
	
	add_action( 'wp_ajax_do_ajax_delete_video' , 'videotube_do_ajax_delete_video');
	add_action( 'wp_ajax_nopriv_do_ajax_delete_video' , 'videotube_do_ajax_delete_video');
}

if( ! function_exists( 'videotube_do_ajax_load_more_posts' ) ){
	function videotube_do_ajax_load_more_posts(){

		if( ! isset( $_POST['data'] ) ){
			exit;
		}

		$data =  json_decode( stripslashes( $_POST['data']), true );

		echo videotube_get_scroll_posts( $data );

		exit;
	}

	add_action( 'wp_ajax_load_more_posts' , 'videotube_do_ajax_load_more_posts');
	add_action( 'wp_ajax_nopriv_load_more_posts' , 'videotube_do_ajax_load_more_posts');	
}