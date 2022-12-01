<?php
$post_data = get_post(get_the_ID()); 
$url_image = has_post_thumbnail() ? wp_get_attachment_url( get_post_thumbnail_id(get_the_ID())) : null;
$current_url = get_permalink( get_the_ID() );
$current_title = $post_data->post_title;

$length = apply_filters( 'social_short_content_length' , 20 );

if( $post_data->post_excerpt ){
	$current_short_content = wp_trim_words( $post_data->post_excerpt, $length, '' );
}
else{
	$current_short_content = wp_trim_words( $post_data->post_content , $length, '' );
}
?>

<div class="share-buttons">
	<a target="_blank" href="<?php echo esc_url( add_query_arg( array( 'u' => $current_url ), 'https://www.facebook.com/sharer/sharer.php' ) );?>">
		<img src="<?php echo esc_url( get_template_directory_uri() );?>/img/facebook.png" alt="<?php echo esc_attr( esc_html__( 'Facebook', 'videotube' ) );?>" />
	</a>

	<a target="_blank" href="<?php echo esc_url( add_query_arg( array( 'url' => $current_url, 'text' => $current_short_content  ), 'https://twitter.com/intent/tweet' ) );?>">
		<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/twitter.png" alt="<?php echo esc_attr( esc_html__( 'Twitter', 'videotube' ) );?>" />
	</a>
	
	<a target="_blank" href="<?php echo esc_url( add_query_arg( array( 'url' => $current_url, 'media' => $url_image, 'description' => $current_short_content ), 'https://pinterest.com/pin/create/button/' ) );?>">
		<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/pinterest.png" alt="<?php echo esc_attr( esc_html__( 'Pinterest', 'videotube' ) );?>" />
	</a>
	
	<a target="_blank" href="<?php echo esc_url( add_query_arg( array( 'url' => $current_url ), 'http://www.reddit.com/submit' ) )?>">
		<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/reddit.png" alt="<?php echo esc_attr( esc_html__( 'Reddit', 'videotube' ) );?>" />
	</a>
	
	<a target="_blank" href="<?php echo esc_url( add_query_arg( array( 'mini' => 'true', 'url' => $current_url, 'title' => $current_title, 'summary' => $current_short_content, 'source' => home_url('/') ), 'https://www.linkedin.com/shareArticle' ) );?>">
		<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/linkedin.png" alt="<?php echo esc_attr( esc_html__( 'Linkedin', 'videotube' ) );?>" />
	</a>					

	<a href="mailto:?Subject=<?php print esc_attr( $current_title );?>&Body=<?php printf( __('I saw this and thought of you! %s','videotube'), esc_url( $current_url ) );?>">
		<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/email.png" alt="<?php echo esc_attr( esc_html__( 'Email', 'videotube' ) );?>" />
	</a>
</div>