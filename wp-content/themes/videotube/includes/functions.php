<?php
/**
 * VideoTube Common Functions
 *
 * @author 		Toan Nguyen
 * @category 	Core
 * @version     1.0.0
 */
if( ! defined('ABSPATH') ) exit;

if( ! function_exists( 'videotube_filter_body_class' ) ){
	/**
	 * Filter the site body classes
	 *
	 * 
	 * @param  array $classes
	 * @return array $classes
	 *
	 * @since 3.4.2
	 * 
	 */
	function videotube_filter_body_class( $classes ){
		return array_merge( $classes, array( 'd-flex', 'flex-column' ,'h-100' ) );
	}

	add_filter( 'body_class', 'videotube_filter_body_class', 10, 1 );
}

if( ! function_exists( 'videotube_get_post_excerpt_length' ) ){
	/**
	 *
	 * Get post excerpt length
	 * 
	 * @return int
	 */
	function videotube_get_post_excerpt_length(){
		global $videotube;

		return isset( $videotube['excerpt_length'] ) ? absint($videotube['excerpt_length']) : 15;
	}
}

if( ! function_exists( 'videotube_add_query_vars_filter' ) ){
	
	/**
	 *
	 * Add custom query vars
	 *
	 * @link https://codex.wordpress.org/Function_Reference/get_query_var
	 *
	 * @param array $vars
	 * @return array
	 *
	 */
	
	function videotube_add_query_vars_filter( $vars ) {
		$vars[] = 'action';
		return $vars;
	}
	add_filter( 'query_vars', 'videotube_add_query_vars_filter' );
}

if( ! function_exists( 'videotube_filter_the_video_edit_page' ) ){
	
	/**
	 *
	 * Filter the video editting page
	 *
	 * @param string $template
	 *
	 */
	
	function videotube_filter_the_video_edit_page( $template ) {
		
		$action = get_query_var( 'action' );
		
		if( is_singular( 'video' ) && videotube_can_user_edit_video( get_the_ID() ) && $action == 'edit-video' ){
			$template = locate_template('page-templates/page-edit-video.php');
		}
		
		return $template;
		
	}
	
	add_filter( 'template_include' , 'videotube_filter_the_video_edit_page', 10, 1 );
}

if( ! function_exists( 'videotube_can_user_edit_video' ) ){
	
	/**
	 * 
	 * Check if current user can edit video
	 * @param int $post_id
	 */
	
	function videotube_can_user_edit_video( $post_id ) {
		
		$can = false;
		
		$post = get_post( $post_id );
		
		if( $post->post_author == get_current_user_id() || current_user_can( 'edit_post', $post_id ) ){
			$can = true;
		}
		
		return apply_filters( 'videotube_can_user_edit_video' , $can, $post_id );
	}
	
}

if( ! function_exists( 'videotube_enable_read_more_less' ) ){
	function videotube_enable_read_more_less( $r ) {

		global $videotube;
		
		$videotube = wp_parse_args( $videotube, array(
			'read_more_less'	=>	'1'
		) );
		
		if( ! $videotube['read_more_less'] || $videotube['read_more_less'] == 0){
			return false;
		}
		return $r;
	}
	add_filter( 'read_more_js' , 'videotube_enable_read_more_less', 10, 1 );
}

if( ! function_exists( 'videotube_get_video_aspect_ratio' ) ){
	/**
	 * 
	 * Get video aspect ratio
	 * 
	 * @since 3.1
	 * 
	 */
	function videotube_get_video_aspect_ratio() {
		
		$classes = '';

		$ratio = get_post_meta( get_the_ID(), 'aspect_ratio', true );

		if( empty( $ratio ) ){
			$ratio = '16by9';
		}
		
		$classes = sprintf( 'embed-responsive embed-responsive-%s', $ratio );
			
		return apply_filters( 'aspect_ratio' , $classes );
	}
}

if( ! function_exists( 'videotube_get_player_wrap_classes' ) ){
	/**
	 *
	 * Get player wrapper classes
	 * 
	 * @return array
	 */
	function videotube_get_player_wrap_classes(){
		global $videotube;

		$classes = array( 'player', 'player-large', 'player-wrap' );

		if( isset( $videotube['sticky_player'] ) && $videotube['sticky_player'] == '1' ){
			$classes[] = 'player-sticky';
		}

		return array_unique( $classes );
	}	
}

if( ! function_exists( 'videotube_generate_iframe_tag' ) ){
	/**
	 * Generate the iframe tag
	 * @param array $args
	 * @return html Iframe tag
	 * @since NT 1.0
	 */
	function videotube_generate_iframe_tag( $args = array() ) {
		
		$output	=	$attributes	=	'';
		
		$args	=	wp_parse_args( $args, array(
			'src'		=>	'',
			'width'		=>	'750',
			'height'	=>	'422'
		) );
		
		$attrs	=	array(
			'src'					=>	$args['src'],
			'frameborder'			=>	'0',
			'scrolling'				=>	'no',
			'allowfullscreen'		=>	'',
			'webkitallowfullscreen'	=>	'',
			'mozallowfullscreen'	=>	''
		);
		
		/**
		 * Filter the attribute tags
		 * @param array
		 */
		$attrs	=	apply_filters( 'videotube_generate_iframe_tag_attrs' , $attrs );
		
		if( ! empty( $attrs ) && is_array( $attrs ) ){
			
			foreach( $attrs as $key => $value){
				$attributes .= $key.'="'. esc_attr( $value ) .'" ';
			}
			
			$output .= '<iframe '. $attributes .'></iframe>';
		}
		
		/**
		 * Filter the iframe tag
		 * @param iframe $output
		 * @param array $attrs
		 * @param array $args
		 */
		return apply_filters( 'videotube_generate_iframe_tag', $output, $attrs, $args );
	}
}

if( !function_exists('videotube_get_thumbnail_image') ){
	function videotube_get_thumbnail_image( $post_id ) {
		global $videotube;
		$post_status = $videotube['submit_status'] ? $videotube['submit_status'] : 'pending';
		if( $post_status == 'publish' && get_post_type( $post_id ) == 'video' && function_exists( 'get_video_thumbnail' ) ){
			get_video_thumbnail($post_id);
		}
	}
	add_action('videotube_save_post', 'videotube_get_thumbnail_image', 9999, 1);
}
if( !function_exists('get_google_apikey') ){
	function get_google_apikey(){
		global $videotube;
		$google_apikey = isset( $videotube['google-api-key'] ) ? trim( $videotube['google-api-key'] ) : null;
		return $google_apikey;
	}
}
if( !function_exists('videotube_get_user_role') ){
	/**
	 * Get given user roles
	 * @param  int $user_id
	 * @return array
	 */
	function videotube_get_user_role( $user_id = false ) {
		if( ! $user_id ){
			return;
		}

		$user = new WP_User( $user_id );

		return $user->roles;
	}
}

if( ! function_exists('videotube_socials_url') ){
	function videotube_socials_url() {
		return array(
			'facebook'		=>	esc_html__( 'Facebook','videotube'),
			'twitter'		=>	esc_html__('twitter','videotube'),
			'google-plus'	=>	esc_html__('Google Plus','videotube'),
			'instagram'		=>	esc_html__('Instagram','videotube'),
			'linkedin'		=>	esc_html__('Linkedin','videotube'),
			'tumblr'		=>	esc_html__('Tumblr','videotube'),
			'youtube'		=>	esc_html__('Youtube','videotube'),
			'vimeo-square'	=>	esc_html__('Vimeo','videotube'),
			'pinterest'		=>	esc_html__('Pinterest','videotube'),
			'snapchat'		=>	esc_html__('Snapchat','videotube')				
		);
	}
}

if( !function_exists('post_orderby_options') ){
	function post_orderby_options( $post_type='post' ) {
		$orderby = array(
			'ID'			=>	__('Order by Post ID','videotube'),
			'author'		=>	__('Order by Author','videotube'),
			'title'			=>	__('Order by Title','videotube'),
			'name'			=>	__('Order by Post name (Post slug)','videotube'),
			'date'			=>	__('Order by Date','videotube'),
			'modified'		=>	__('Order by Last modified date','videotube'),
			'rand'			=>	__('Order by Random','videotube'),
			'comment_count'	=>	__('Order by Number of comments','videotube'),
			'views'			=>	__('Order by Views','videotube'),
			'likes'			=>	__('Order by Likes','videotube')
		);
		return $orderby;
	}
}


if( ! function_exists( 'videotube_get_comment_depth' ) ){
	/**
	 *
	 * Get the comment depth number
	 *
	 * @since 1.0.0
	 *
	 */
	function videotube_get_comment_depth( $comment, $depth = 0 ) {

		if( $comment->comment_parent > 0 ){
			$depth++;
			$comment	=	get_comment( $comment->comment_parent );
			return (int)call_user_func( __FUNCTION__, $comment, $depth );
		}
		else{
			return (int)$depth;
		}
	}
}

if( ! function_exists( 'videotube_comments_list_args' ) ){
	/**
	 *
	 * Get default comments lisy params
	 *
	 * 
	 * @return array
	 */
	function videotube_comments_list_args(){
		return apply_filters( 'videotube_comments_list_args', array(
			'walker'            => null,
			'style'             => 'ul',
			'callback'          => 'videotube_theme_comment_style',
			'end-callback'      => null,
			'type'              => 'comment',
			'reply_text'        => esc_html__( 'Reply', 'videotube' ),
			'avatar_size'       => 64,
			'reverse_top_level' => null,
			'format'            => 'html5'
		) );
	}
}

if( ! function_exists('videotube_theme_comment_style') ){
	/**
	 *
	 * Comment list callback
	 *
	 */
	function videotube_theme_comment_style( $comment, $args, $depth ){
		?>
		<li <?php comment_class();?> id="comment-<?php echo esc_attr( $comment->comment_ID );?>">
			<div class="the-comment">
				<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation text-danger small">
						<?php _e( 'Your comment is awaiting moderation.', 'videotube' ); ?>
					</p>
				<?php endif; ?>			
				<div class="avatar"><?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['avatar_size'] ); ?></div>
				<div class="comment-content">
					<?php if( !$comment->user_id || $comment->user_id == 0 ):?>
						<span class="author">
							<?php if( get_comment_author_url() ):?>
								<a href="<?php echo esc_url( get_comment_author_url() );?>"><?php echo wp_kses_post( $comment->comment_author );?></a> <small><?php printf( __('%s ago','videotube') , human_time_diff( get_comment_time('U'), current_time('timestamp') ) );?></small>
							<?php else:?>
								<?php echo wp_kses_post($comment->comment_author);?> <small><?php printf( __('%s ago','videotube') , human_time_diff( get_comment_time('U'), current_time('timestamp') ) );?></small>
							<?php endif;?>
							
						</span>
					<?php else:?>
						<span class="author"><a href="<?php echo get_author_posts_url( $comment->user_id );?>"><?php echo wp_kses_post($comment->comment_author);?></a> <small><?php printf( __('%s ago','videotube') , human_time_diff( get_comment_time('U'), current_time('timestamp') ) );?></small></span>
					<?php endif;?>
					
					<div class="comment-text">
						<?php comment_text() ?>
					</div>
					<?php 
						echo get_comment_reply_link( array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => '<i class="fa fa-reply"></i> ' . esc_html__( 'Reply', 'videotube' ) )), $comment->comment_ID);
					?>
					<?php if( current_user_can('add_users') ):?>
						<a href="<?php echo get_edit_comment_link( $comment->comment_ID );?>" class="edit"><i class="fa fa-edit"></i> <?php _e('Edit','videotube');?></a>
					<?php endif;?>
				</div>
			</div>		
		<?php 
	}
}

if( ! function_exists( 'videotube_replace_reply_link_class' ) ){
	/**
	 *
	 * Filter reply link class
	 *
	 */
	function videotube_replace_reply_link_class($class){
	    $class = str_replace("class='comment-reply-link", "class='comment-reply-link reply", $class);
	    return $class;
	}
	add_filter('comment_reply_link', 'videotube_replace_reply_link_class');
}

if( !function_exists( 'videotube_filter_youtube_autoplay' ) ){
	function videotube_filter_youtube_autoplay( $html, $url, $args ) {
		global $videotube;

		if( ! isset( $videotube['autoplay'] ) ){
			$videotube['autoplay'] = 1;
		}

		if ( ( strpos( $url, 'youtube') !== FALSE || strpos( $url, 'youtu.be') !== FALSE ) && $videotube['autoplay'] == 1) {
			return str_replace("?feature=oembed", "?feature=oembed&autoplay=1", $html);
		}
		return $html;
	}
	add_filter( 'oembed_result', 'videotube_filter_youtube_autoplay', 20, 3);
	add_filter( 'embed_oembed_html', 'videotube_filter_youtube_autoplay', 10, 3 );
}

if( !function_exists( 'videotube_fully_video_responsive' ) ){
	/**
	 * @param unknown_type $html
	 * @param unknown_type $url
	 * @param unknown_type $args
	 * @return unknown
	 */
	function videotube_fully_video_responsive( $html, $url, $args ) {
		
		if( ( is_single() || is_page() ) && is_main_query() ){

			$html = '<div class="'. apply_filters( 'aspect_ratio' , 'embed-responsive embed-responsive-16by9') .'">'. $html .'</div>';
		}
		return $html;
	}
	add_filter('embed_oembed_html', 'videotube_fully_video_responsive', 30, 3 );
}

if( !function_exists('videotube_orderblock_videos') ){
	function videotube_orderblock_videos() {
		$order = isset( $_REQUEST['orderby'] ) ?  trim($_REQUEST['orderby']) : 'date';
		$sort_array = array(
			'date'	=>	__('Latest','videotube'),
			'viewed'	=>	__('Viewed','videotube'),
			'liked'		=>	__('Liked','videotube'),
			'comment_count'	=>	__('Comments','videotube')
		);
		$block = '<div class="section-nav"><ul class="sorting"><li class="sort-text">'.__('Sort by:','videotube').'</li>';
			foreach ( $sort_array as $key=>$value ){
				$active = ( $order == $key ) ? 'class="active"' : null;
				if( is_search() ){
					$block .= '<li '.$active.'><a href="'. esc_url( add_query_arg( array( 's'=> rawurlencode( get_search_query() ), 'orderby' => $key ) ) ) .'">'.$value.'</a></li>';
				}
				else{
					$block .= '<li '.$active.'><a href="'.esc_url( add_query_arg( array( 'orderby' => $key ) ) ).'">'.$value.'</a></li>';
				}
			}
		$block .= '</ul></div>';
		
		echo wp_kses_post( $block );
	}
	add_action('videotube_orderblock_videos', 'videotube_orderblock_videos');
}

if( !function_exists('videotube_orderquery_videos') ){
	function videotube_orderquery_videos( $query ) {
		$order = isset( $_REQUEST['orderby'] ) ? trim( $_REQUEST['orderby'] ) : null;
		if( $query->is_home() ||  $query->is_search || is_tax() || is_archive() && !is_admin() ){
			if( $query->is_main_query() ){
				switch ( $order ) {
					case 'viewed':
						$query->set( 'meta_key','count_viewed' );
						$query->set( 'orderby', 'meta_value_num' );
					break;
					case 'liked':
						$query->set( 'meta_key','like_key' );
						$query->set( 'orderby', 'meta_value_num' );
					break;
				}
			}
			$query->set( 'order', 'DESC' );
		}
	}
	add_action('pre_get_posts', 'videotube_orderquery_videos');
}

if( !function_exists('videotube_get_count_viewed') ){
	function videotube_get_count_viewed( $post_id  = 0 ) {
		
		if( ! $post_id || $post_id == 0 ){
			$post_id = get_the_ID();
		}
		
		$count = (int)get_post_meta( $post_id,'count_viewed',true );
		
		return apply_filters( 'videotube_get_count_viewed' , $count, $post_id );
		
	}
}

if( !function_exists('videotube_update_post_view') ){
	function videotube_update_post_view() {
		
		if( ! is_singular( 'video' ) ){
			return;
		}
		
		$cookie_name = 'postview-' . get_the_ID();
		
		if( ! isset( $_COOKIE[ $cookie_name ] ) ){
			$postviews = (int)get_post_meta( get_the_ID(), 'count_viewed', true );
			$postviews = $postviews + 1;
			update_post_meta( get_the_ID() , 'count_viewed', $postviews );
			
			return setcookie( $cookie_name, time() - ( 15 * 60 ) );
		}
		
	}
	add_action('wp', 'videotube_update_post_view');
}

if( ! function_exists( 'videotube_format_number' ) ){
	
	function videotube_format_number( $int ) {
		
		$formatted = number_format( $int, 0 );
		
		return apply_filters( 'videotube_format_number' , $formatted, $int );
	}
	
	add_filter( 'postviews' , 'videotube_format_number', 10, 1 );
	add_filter( 'postlikes' , 'videotube_format_number', 10, 1 );
}

//---------------------------------------- like and dislike button ------------------------------------------
if( !function_exists('videotube_get_like_count') ){
	function videotube_get_like_count($post_id) {
		return get_post_meta($post_id, 'like_key',true) ? get_post_meta($post_id, 'like_key',true)  : 0;
	}	
}
if( !function_exists('videotube_get_dislike_count') ){
	function videotube_get_dislike_count($post_id) {
		return get_post_meta($post_id, 'dislike_key',true) ? get_post_meta($post_id, 'dislike_key',true)  : 0;
	}	
}
//---------------------------------------- like and dislike button ------------------------------------------

if(!function_exists('videotube_get_editor')){
	function videotube_get_editor($content, $id, $name, $display_media = false) {
		ob_start();
		$settings = array(
			'textarea_name' => $name,
			'media_buttons' => $display_media,
			'textarea_rows'	=>	5,
			'quicktags'	=>	false,
			'teeny'		=>	true
		);
		// Echo the editor to the buffer
		wp_editor($content,$id, $settings);
		// Store the contents of the buffer in a variable
		$editor_contents = ob_get_clean();
		$editor_contents = str_ireplace("<br>","", $editor_contents);
		// Return the content you want to the calling function
		return $editor_contents;
	}
}

if( !function_exists('videotube_custom_css') ){
	function videotube_custom_css() {
		global $videotube;
		if( isset( $videotube['custom_css'] ) && trim( $videotube['custom_css'] ) != '' ){
			wp_add_inline_style( 'videotube-style', $videotube['custom_css'] );
		}
	}
	add_action('wp_enqueue_scripts', 'videotube_custom_css', 100 );
}

if( !function_exists('videotube_custom_css_on_mobile') ){
	function videotube_custom_css_on_mobile() {
		global $videotube;
		if( isset( $videotube['custom_css_mobile'] ) && trim( $videotube['custom_css_mobile'] ) != '' && wp_is_mobile() ){
			wp_add_inline_style( 'videotube-style', $videotube['custom_css_mobile'] );
		}
		
	}
	add_action('wp_enqueue_scripts', 'videotube_custom_css_on_mobile', 100 );
}

if( !function_exists('videotube_custom_js') ){
	function videotube_custom_js() {
		global $videotube;
		if( isset( $videotube['custom_js'] ) && trim( $videotube['custom_js'] ) != '' ){
			wp_add_inline_script( 'videotube-custom', $videotube['custom_js'] );
		}
	}
	add_action('wp_enqueue_scripts', 'videotube_custom_js', 100 );
}

if( !function_exists('videotube_custom_js_on_mobile') ){
	function videotube_custom_js_on_mobile() {
		global $videotube;

		if( isset( $videotube['custom_js_mobile'] ) && trim( $videotube['custom_js_mobile'] ) != '' && wp_is_mobile() ){
			wp_add_inline_script( 'videotube-custom', $videotube['custom_js_mobile'] );
		}
	}
	add_action('wp_enqueue_scripts', 'videotube_custom_js_on_mobile', 100 );
}

if( !function_exists('videotube_special_nav_class') ){
	function videotube_special_nav_class($classes, $item){
	     if( in_array('current-menu-item', $classes) ){
	     	$classes[] = 'active ';
	     }
	     return $classes;
	}	
}
add_filter('nav_menu_css_class' , 'videotube_special_nav_class' , 10 , 2);

if( !function_exists( 'videotube_get_user_postcount' ) ){
	function videotube_get_user_postcount( $user_id, $post_type="video" ) {
		return count_user_posts( $user_id , $post_type  );
	}
}
if( !function_exists( 'videotube_get_user_metacount' ) ){
	function videotube_get_user_metacount( $user_id, $key ) {
		global $wpdb;
		
		if( false === ( $query = get_transient( $user_id . 'meta_count' . $key ) ) ){
			$query = $wpdb->get_var( $wpdb->prepare(
					"
					SELECT sum(meta_value)
					FROM $wpdb->postmeta LEFT JOIN $wpdb->posts ON ( $wpdb->postmeta.post_id = $wpdb->posts.ID )
					LEFT JOIN $wpdb->users ON ( $wpdb->posts.post_author = $wpdb->users.ID )
					WHERE meta_key = %s
					AND $wpdb->users.ID = %s
					AND $wpdb->posts.post_status = %s
					AND $wpdb->posts.post_type = %s
					",
					$key,
					$user_id,
					'publish',
					'video'
			) );
			
			if( (int)$query > 0 ){
				set_transient( $user_id . 'meta_count' . $key , $query, 600);
			} 
		}
		return (int)$query > 0 ? number_format_i18n($query) : 0; 
	}
}
if( !function_exists( 'videotube_viaudiofile_format' ) ){
	function videotube_viaudiofile_format() {
		$allowed_formats = array(
			'asf',
			'asx',
			'wmv',
			'wmx',
			'wm',
			'avi',
			'divx',
			'flv',
			'mov',
			'qt',
			'mpeg',
			'mpg',
			'mpe',
			'mp4',
			'm4v',
			'ogv',
			'webm',
			'mkv',
			'mp3',
			'm4a',
			'm4b',
			'ra',
			'ram',
			'wav',
			'ogg',
			'oga',
			'mid',
			'midi',
			'wma',
			'wax',
			'mka'
		);
		return apply_filters( 'videotube_viaudiofile_format/filetypes' , $allowed_formats);
	}
}
if( !function_exists( 'videotube_imagefile_format' ) ){
	function videotube_imagefile_format() {
		return apply_filters( 'videotube_imagefile_format/filetypes' , array('jpg','jpeg','png','gif'));
	}
}
if( !function_exists( 'videotube_check_file_allowed' ) ){
	function videotube_check_file_allowed( $file, $type = 'video' ){
		$bool = false;
		if( $type == 'video' ){
			$mimes = videotube_viaudiofile_format();
		}
		else{
			$mimes = videotube_imagefile_format();
		}
		$filetype = wp_check_filetype($file['name'], null);
		
		$ext = isset( $filetype ) ? strtolower( $filetype['ext'] ) : '';
		
		if( in_array( $ext , $mimes) ){
			$bool = true;
		}

		return $bool;
	}
}
if( !function_exists( 'videotube_check_file_size_allowed' ) ){
	function videotube_check_file_size_allowed( $file, $type = 'video' ){
		global $videotube;
		if( !$file )
			return false;
		if( $type == 'video' ){
			$filesize = isset( $videotube['videosize'] ) ? (int)$videotube['videosize'] : 10;	
		}
		else{
			$filesize = isset( $videotube['imagesize'] ) ? (int)$videotube['imagesize'] : 2;
		}
		if( $filesize == -1 ){
			return true;
		}
		$byte_limit = videotube_convert_mb_to_b( $filesize );
		if( $file["size"] > $byte_limit ){
			return false;
		}
		return true;
	}
}

if( !function_exists( 'videotube_convert_mb_to_b' ) ){
	function videotube_convert_mb_to_b( $megabyte ) {
		if( !$megabyte || $megabyte == 0 )
			return 0;
		return (int)$megabyte * 1048576;
	}
}

if( !function_exists( 'videotube_insert_attachment' ) ){
	function videotube_insert_attachment($file_handler, $post_id, $setthumb='false', $post_meta = '') {
		// check to make sure its a successful upload
		if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();
	
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	
		$attach_id = media_handle_upload( $file_handler, $post_id );
	
		if ($setthumb) update_post_meta($post_id,'_thumbnail_id',$attach_id);
		if(!$setthumb && $post_meta!=''){
			update_post_meta($post_id, $post_meta, array( $attach_id ));
		}
		return $attach_id;
	}
}
if( !function_exists( 'videotube_videolayout' ) ){
	function videotube_videolayout() {
		return array( 
			'small'	=>	__('Small','videotube'),
			'large'	=>	__('Large','videotube')
		);
	}
}
if( !function_exists( 'bootstrap_link_pages' ) ){
	/**
	 * Link Pages
	 * @author toscha
	 * @link http://wordpress.stackexchange.com/questions/14406/how-to-style-current-page-number-wp-link-pages
	 * @param  array $args
	 * @return void
	 * Modification of wp_link_pages() with an extra element to highlight the current page.
	 */
	function bootstrap_link_pages( $args = array () ) {
	    $defaults = array(
	        'before'      => '<p>' . __('Pages:','videotube'),
	        'after'       => '</p>',
	        'before_link' => '',
	        'after_link'  => '',
	        'current_before' => '',
	        'current_after' => '',
	        'link_before' => '',
	        'link_after'  => '',
	        'pagelink'    => '%',
	        'echo'        => 1
	    );
	 
	    $r = wp_parse_args( $args, $defaults );
	    $r = apply_filters( 'wp_link_pages_args', $r );
	    extract( $r, EXTR_SKIP );
	 
	    global $page, $numpages, $multipage, $more, $pagenow;
	 
	    if ( ! $multipage )
	    {
	        return;
	    }
	 
	    $output = $before;
	 
	    for ( $i = 1; $i < ( $numpages + 1 ); $i++ )
	    {
	        $j       = str_replace( '%', $i, $pagelink );
	        $output .= ' ';
	 
	        if ( $i != $page || ( ! $more && 1 == $page ) )
	        {
	            $output .= "{$before_link}" . _wp_link_page( $i ) . "{$link_before}{$j}{$link_after}</a>{$after_link}";
	        }
	        else
	        {
	            $output .= "{$current_before}{$link_before}<a>{$j}</a>{$link_after}{$current_after}";
	        }
	    }
	 
	    echo wp_kses_post( $output . $after );
	}	
}

if ( ! function_exists( 'videotube_pagination' ) ){
	/**
	 * Display navigation to next/previous set of posts when applicable.
	 */
	function videotube_pagination( $query = null ) {
		// Don't print empty markup if there's only one page.
		global $wp_query;
		if( empty( $query ) )
			$query = $wp_query;
		if ( $query->max_num_pages < 2 ) {
			return;
		}
		if( is_front_page() ){
			$paged        = get_query_var( 'page' ) ? intval( get_query_var( 'page' ) ) : 1;
		}
		else{
			$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
		}

		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$query_args   = array();
		$url_parts    = explode( '?', $pagenum_link );

		if ( isset( $url_parts[1] ) ) {
			wp_parse_str( $url_parts[1], $query_args );
		}

		$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
		$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

		$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';
		
		// Set up paginated links.
		$links = paginate_links( array(
			'base'     => $pagenum_link,
			'format'   => $format,
			'total'    => $query->max_num_pages,
			'current'  => $paged,
			'mid_size' => 3,
			'type'	=>	'array',
			'add_args' => array_map( 'urlencode', $query_args ),
			'prev_next'    => true,
			'prev_text' => !is_rtl() ? __( '&larr; Previous ', 'videotube' ) : __( ' &rarr; Previous', 'videotube' ),
			'next_text' => !is_rtl() ? __( 'Next &rarr;', 'videotube' ) : __( 'Next &larr;', 'videotube' )
		) );

		if ( is_array( $links ) ) :
            echo '<nav class="posts-pagination my-3"><ul class="pagination">';
            foreach ( $links as $page ) {
                printf( '<li class="page-item">%s</li>', $page );
            }
           echo '</ul></nav>';
		endif;
		
	}
	add_action('videotube_pagination', 'videotube_pagination', 10, 1);
}

if( !function_exists( 'videotube_get_columns' ) ){
	/**
	 * @return mixed
	 */
	function videotube_get_columns( $device = 'desktop' ) {

		global $videotube;
		
		$columns = 3;
		
		$videotube = wp_parse_args( $videotube, array(
			'desktop_columns'	=>	3,
			'tablet_columns'	=>	2,
			'mobile_columns'	=>	2
		));
		
		if( isset( $videotube[ $device . '_columns'] ) ){
			$columns =ceil( 12/$videotube[ $device . '_columns'] );
		}
		
		return apply_filters( 'videotube_get_columns' , $columns, $device );
	}
}

if( !function_exists( 'videotube_add_breadcrumbs' ) ){
	function videotube_add_breadcrumbs() {
		if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
		}
	}
	add_action( 'videotube_before_video_title' , 'videotube_add_breadcrumbs', 10);
}

if( !function_exists( 'videotube_convert_columns_to_thumbnail_size' ) ){
	/**
	 * get the thumbnail size.
	 * @param int $columns
	 * return string of the thumbnail size, defined in functions.php.
	 */
	function videotube_convert_columns_to_thumbnail_size() {
		$columns =  videotube_get_columns();
		$size = 'video-lastest';
		switch ( $columns ) {
			// 1 columns, fullwidth
			case '12':
				$size = 'blog-large-thumb';
			break;
			
			case '6':
				$size = 'video-featured';
			break;			
			
			case '4':
				//$size = 'blog-large-thumb';
				// using the default size.
			break;	

			case '3':
				$size = 'video-featured';
			break;			
				
			case '2':
				//$size = 'blog-large-thumb';
			break;
				
		}
		return apply_filters( 'video_thumbnail_size' , $size);
	}
}

if( !function_exists( 'videotube_add_styles' ) ){
	/**
	 * Add style
	 * @param array $styles
	 * @return mixed
	 */
	function videotube_add_styles( $styles ) {
		$styles = array(
			'default'	=>	__('Default','videotube'),
			get_template_directory_uri() . '/assets/style/blue.css' =>	__('Blue','videotube'),
			get_template_directory_uri() . '/assets/style/splash-orange.css' =>	__('Splash Orange','videotube'),
			get_template_directory_uri() . '/assets/style/orange.css' =>	__('Orange','videotube'),
			get_template_directory_uri() . '/assets/style/wood.css' =>	__('Wood','videotube'),
			get_template_directory_uri() . '/assets/style/splash-red.css' =>	__('Splash Red','videotube'),
			get_template_directory_uri() . '/assets/style/green.css' =>	__('Green','videotube'),
			'custom'	=>	__('Custom','videotube')
		);
		return apply_filters( 'videotube_add_styles' , $styles );
	}
	add_filter( 'videotube_get_styles' , 'videotube_add_styles', 10, 1);
}
if( !function_exists( 'videotube_add_video_feed' ) ){
	function videotube_add_video_feed($qv) {
		global $videotube;
		if (isset($qv['feed']) && !isset($qv['post_type']) && $videotube['video_feed'] == 1)
			$qv['post_type'] = array('post', 'video');
		return $qv;
	}
	add_filter('request', 'videotube_add_video_feed');
}

if( ! function_exists( 'videotube_filter_the_archive_title' ) ){
	/**
	 * Filter the archive title.
	 * @param string $archive_title
	 */
	function videotube_filter_the_archive_title( $archive_title ) {
		
		if( is_tax( 'categories' ) || is_tax( 'video_tag' ) ){
			$archive_title  =   single_term_title( '', false );
		}
		
		return $archive_title;
	}
	add_filter( 'get_the_archive_title' , 'videotube_filter_the_archive_title', 10, 1 );
}

if( function_exists( 'vc_set_as_theme' ) ){
	vc_set_as_theme( apply_filters( 'videotube_vc_set_as_theme' , true) );
}

if( ! function_exists( 'videotube_remove_wpbakery_meta_tag' ) ){
	function videotube_remove_wpbakery_meta_tag() {
		if( function_exists( 'visual_composer' ) ){
			remove_action('wp_head', array(visual_composer(), 'addMetaData'));
		}
	}
	add_action( 'init' , 'videotube_remove_wpbakery_meta_tag' );
}

if( ! function_exists( 'videotube_add_noindex_tag' ) ){
	/**
	 *
	 * Add a noindex tag once "latest" param found.
	 *
	 */
	function videotube_add_noindex_tag() {
		if( isset( $_GET['order_post'] ) && $_GET['order_post'] == 'latest' ){
			echo '<meta name="robots" content="noindex, follow">';
		}
	}
	add_action( 'wp_head' , 'videotube_add_noindex_tag',1);
}

if( ! function_exists( 'videotube_get_scroll_posts' ) ){
	/**
	 *
	 * Get scroll posts
	 *
	 * 
	 * @param  array  $args
	 * @return HTML
	 *
	 * @since 3.2.9.2
	 * 
	 */
	function videotube_get_scroll_posts( $args = array() ){

		$args = wp_parse_args( $args, array(
			'post_type'			=>	'video',
			'posts_per_page'	=>	get_option( 'posts_per_page' ),
			'current_page'		=>	get_the_ID(),
			'meta_query'		=>	array(
				array(
					'key'		=>	'_thumbnail_id',
					'compare'	=>	'EXISTS'
				)
			)
		) );

		$args['post_status'] = 'publish';

		$args = apply_filters( 'mars_scrolling_post_args' , $args );

		/**
		 * Filter post args
		 */
		$args = apply_filters( 'videotube_scrolling_post_args' , $args );

		$query_posts = new WP_Query( $args );

		if( ! $query_posts->have_posts() ){
			return sprintf(
				'<div class="alert alert-info">%s</div>',
				esc_html__( 'No posts were found.', 'videotube' )
			);
		}

		ob_start();

		while( $query_posts->have_posts() ){
			$query_posts->the_post();

			get_template_part( 'loop', 'scroll' );
		}

		if( $query_posts->post_count >= $args['posts_per_page'] ){
			$args['paged']++;
			/**
			 *
			 * Limit scroll times to show a pagination
			 *
			 * default 5
			 * 
			 */
			
			$scroll_times = apply_filters( 'videotube_scrolling_post_times', 5 );

			if( $args['paged'] % ($scroll_times+2) == 0 ){

				$link_args = array();

				if( untrailingslashit( get_permalink( $args['current_page'] ) ) == untrailingslashit( home_url() ) ){
					$link_args['page'] = $args['paged'];
				}
				else{
					$link_args['paged'] = $args['paged'];
				}

				printf(
					'<a data-setup="%s" href="%s" class="btn btn-primary btn-block">%s</a>',
					esc_attr( json_encode( $args ) ),
					esc_url( add_query_arg( $link_args, get_permalink( $args['current_page'] ) )),
					esc_html__( 'load more', 'videotube' )
				);
			}
			else{
				printf(
					'<button data-setup="%s" class="btn btn-outline-secondary btn-sm btn-block load-more-posts infinite-scroll-posts" type="button">%s</button>',
					esc_attr( json_encode( $args ) ),
					esc_html__( 'load more', 'videotube' )
				);
			}
		}

		return ob_get_clean();
	}
}

if( ! function_exists('videotube_socials_share') ){
	/**
	 *
	 * The socials share
	 * 
	 */
	function videotube_socials_share(){

		ob_start();

		if( function_exists( 'meks_ess_share' ) ){
			meks_ess_share();
		}
		else{
			get_template_part( 'template-parts/socials-share' );
		}

		echo apply_filters( 'videotube_socials_share', ob_get_clean() );
	}
}