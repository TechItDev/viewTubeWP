<?php
/**
 * VideoTube Common Hooks
 *
 * @author 		Toan Nguyen
 * @category 	Core
 * @version     1.0.0
 */
if( ! defined('ABSPATH') ) exit;
if( ! function_exists('videotube_blog_metas') ){
	/**
	 * Display Blog meta as Author, Date, Category
	 */
	function videotube_blog_metas() {
		global $post;
		$output = '';
		$author = get_the_author_meta('display_name');
		$category = get_the_category($post->ID); 
		$output .= '
			<span class="post-meta"><i class="fa fa-user"></i> <a href="'.get_author_posts_url(get_the_author_meta('ID')).'">'.$author.'</a> <span class="sep">/</span> 
			<i class="far fa-clock"></i> '.get_the_date().' <span class="sep">/</span>';
			if( has_category('', $post->ID) ):
				$output .= '<i class="fa fa-folder-open"></i> '. get_the_category_list(', ', '', $post->ID) .'</span>';	
			endif;
		
		echo wp_kses_post( $output );
	}
	add_action('videotube_blog_metas', 'videotube_blog_metas', 10);
}
if( ! function_exists('videotube_post_meta') ){
	/**
	 * Display Blog meta as Author, Date, Category
	 */
	function videotube_post_meta() {
		$output = '
			<div class="meta">
				<span class="date">'. sprintf( __('%s ago','videotube'), human_time_diff( get_the_time('U'), current_time('timestamp') ) ).'</span>
			</div>		
		';
		echo wp_kses_post( $output );
	}

	add_action('videotube_post_meta', 'videotube_post_meta', 10 );
}

if( !function_exists('videotube_video_meta') ){
	/**
	 * Display Video Meta as Viewed, Liked
	 */
	function videotube_video_meta(){
		global $post, $videotube;
		if( get_post_type( $post->ID ) != 'video' )
			return;
		$views = apply_filters( 'postviews' , videotube_get_count_viewed() );
		$datetime_format = isset( $videotube['datetime_format'] ) ? $videotube['datetime_format'] : 'videotube';
		$comments = wp_count_comments( $post->ID );
		$output = '
			<div class="meta">';
				// insert the code here
				
				if( $datetime_format != 'videotube' ){
					$output .= '<span class="date">'.get_the_date().'</span>';
				}
				else{
					$output .= '<span class="date">'.sprintf( __('%s ago','videotube'), human_time_diff( get_the_time('U'), current_time('timestamp') ) ).'</span>';
				}
				
				if( isset( $views ) && $views > 0 ){
					$output .= '<span class="views"><i class="fa fa-eye"></i>'.$views.'</span>';
				}
				
				if(function_exists('videotube_get_like_count')) {
					$likes = videotube_get_like_count($post->ID);
					$likes = apply_filters( 'postlikes' , $likes );
					$output .= '<span class="heart"><i class="fa fa-thumbs-up"></i>'.$likes.'</span>';
				}
				$output .= '
					<span class="fcomments"><i class="fa fa-comments"></i>'.$comments->approved.'</span>
				';
				// video category.
				if( has_term( '', 'categories', $post->ID ) && apply_filters( 'videotube_post_meta_category' , false) === true ){
					$output .= '<span class="fcategory"><i class="fa fa-folder-open"></i>';
						$output .= get_the_term_list( $post->ID , 'categories');
					$output .= '</span>';
				}

				$output .= '
			</div>
		';
		echo wp_kses_post( $output );
	}
	add_action('videotube_video_meta', 'videotube_video_meta', 10);
}

if( !function_exists('videotube_copyright') ){
	/**
	 * Dislay Copyright in Footer.
	 */
	function videotube_copyright(){
		global $videotube;

		if( isset( $videotube['copyright_text'] ) ){
			echo '<p>'.$videotube['copyright_text'].'</p>';
		}
		else{
			?>
			<p>
				<?php
					printf(
						esc_html__( 'Copyright %1$s &copy; %2$s All rights reserved. Powered by WordPress and %2$s', 'videotube' ),
						date( 'Y' ),
						'<a href="https://1.envato.market/DdaAG">VideoTube</a>'
					);
				?>
			</p>
			<?php
		}
	}
	add_action('videotube_copyright', 'videotube_copyright', 1);
}