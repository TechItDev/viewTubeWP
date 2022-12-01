<?php
if( !defined('ABSPATH') ) exit;
function videotube_core_social_widget_register() {
	register_widget('VideoTube_Core_Widget_Socials');
}
add_action('widgets_init', 'videotube_core_social_widget_register');

class VideoTube_Core_Widget_Socials extends WP_Widget{
	
	function __construct(){
		$widget_ops = array( 'classname' => 'mars-connected-widget', 'description' => __('[VideoTube] Socials', 'videotube-core') );
	
		parent::__construct( 'mars-connected-widget' , __('[VideoTube] Socials', 'videotube-core') , $widget_ops);
	}

	private function socials() {
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
	
	function widget($args, $instance){
		extract( $args );
		global $videotube;
		$title = apply_filters('widget_title', $instance['title'] );
		print  $before_widget;
		print $before_title . $title . $after_title;	
			print '<ul class="list-unstyled social">';
				$social_array = $this->socials();
				foreach ( $social_array as $key=>$value ){
					if( !empty( $videotube[$key] ) ){
						print '<li><a href="'. esc_url( $videotube[$key] ) .'"><i class="fab fa-'. esc_attr( $key ) .'"></i> '.$value.'</a></li>';
					}
				}
				print '<li><a href="'. esc_url( get_bloginfo('rss_url') ) .'"><i class="fa fa-rss"></i> RSS</a></li>';
			print '</ul>';
		print $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );		
		return $instance;
	}
	function form( $instance ){
		$defaults = array( 'title' => __('Stay Connected', 'videotube-core'));
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'videotube-core'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
	<?php		
	}	
}

