<?php
if( !defined('ABSPATH') ) exit;
function videotube_core_social_count_widget_register() {
	register_widget('VideoTube_Core_Widget_Socials_Count');
}
add_action('widgets_init', 'videotube_core_social_count_widget_register');

class VideoTube_Core_Widget_Socials_Count extends WP_Widget{
	
	function __construct(){
		$widget_ops = array( 'classname' => 'mars-subscribox-widget', 'description' => __('[VideoTube] Social Count', 'videotube-core') );
	
		parent::__construct( 'mars-subscribox-widget' , __('[VideoTube] Social Count', 'videotube-core') , $widget_ops);
	}
		
	function widget($args, $instance){
		global $videotube, $post;
		$settings = get_option( 'socialcountplus_settings' );

		$instance = wp_parse_args( $instance, array(
			'title'	=>	''
		) );

		extract( $args );

		$title = apply_filters('widget_title', $instance['title'] );

		print  $before_widget;

		if( ! empty( $title ) ){
			print $before_title . $title . $after_title;
		}

		echo '<div class="socials-list row row-5">';
		
		$socials = array(
			'facebook'	=>	esc_html__('Fans','videotube-core'),
			'twitter'	=>	esc_html__('Followers','videotube-core'),
			'googleplus'	=>	esc_html__('Fans','videotube-core'),
			'soundcloud'	=>	esc_html__('Followers','videotube-core'),
			'youtube'		=>	esc_html__('Subscribers','videotube-core'),
			'instagram'		=>	esc_html__('Followers','videotube-core'),
			'linkedin'		=>	esc_html__('Followers','videotube-core'),
			'pinterest'		=>	esc_html__('Followers','videotube-core'),
			'tumblr'		=>	esc_html__('Followers','videotube-core'),
			'vimeo'			=>	esc_html__('Subscribers','videotube-core')
		);
		
		$socials = apply_filters( 'marstheme_subscriber_widget_socials' , $socials);
		
		foreach ( $socials  as $key=>$value) {
			if( isset( $settings[ $key . '_active'] ) ){
				$icon_class = ( $key == 'googleplus' ) ? 'google-plus' : $key;
				?>
			        <div class="social-counter-item col-3 mb-2">
			            <a target="_blank" href="<?php print ( isset( $videotube[ $icon_class ] ) ? esc_url( $videotube[ $icon_class ] ) : '#' ) ;?>">
			                <i class="fab fa-<?php print esc_attr( $icon_class );?>"></i>
			                <span class="counter"><?php if( function_exists('get_scp_counter') ): print get_scp_counter( $key ); endif; ?></span>
			                <span class="counter-text"><?php print $value;?></span>
			            </a>
			        </div>
				<?php 
			}
		}
		?>
        <?php if( get_option('users_can_register') ):?>
	        <div class="social-counter-item col-3 mb-2 subscribe">
	            <a href="#" data-toggle="modal" data-target="#subscrib-modal">
	                <i class="fa fa-rss"></i>
	                <span class="counter"><?php echo videotube_core_get_socials_count('subscriber'); ?></span>
	                <span class="counter-text"><?php _e('Subscribers','videotube-core')?></span>
	            </a>
	        </div>
		<?php endif;?>

		</div>
		<div class="clearfix"></div>
		<?php 
		print $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['video_category'] = strip_tags( $new_instance['video_category'] );
		$instance['video_key'] = strip_tags( $new_instance['video_key'] );
		$instance['video_orderby'] = strip_tags( $new_instance['video_orderby'] );
		$instance['video_order'] = strip_tags( $new_instance['video_order'] );
		$instance['widget_column'] = strip_tags( $new_instance['widget_column'] );
		$instance['video_shows'] = strip_tags( $new_instance['video_shows'] );
		$instance['view_more'] = strip_tags( $new_instance['view_more'] );
		return $instance;		
		
	}
	function form( $instance ){
		$defaults = array( 
			'title' => __('Social Subscribox', 'videotube-core'),
			'columns'	=>	4
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'videotube-core'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>		
	<?php		
	}	
}

