<?php
/**
 * VideoTube Tags Cloud
 * Add Tags Cloud widget, video_key and tag taxonomy is supported.
 * @author 		Toan Nguyen
 * @category 	Core
 * @version     1.0.0
 */
if( ! defined('ABSPATH') ) exit;


function videotube_core_tags_cloud_widget_register() {
	register_widget('VideoTube_Core_Tags_Cloud_Widget');
}
add_action('widgets_init', 'videotube_core_tags_cloud_widget_register');

class VideoTube_Core_Tags_Cloud_Widget extends WP_Widget{

	function __construct(){
		$widget_ops = array( 'classname' => 'mars-keycloud-widgets', 'description' => __('[VideoTube] Tags Cloud', 'videotube-core') );

		parent::__construct( 'mars-keycloud-widgets' , __('[VideoTube] Tags Cloud', 'videotube-core') , $widget_ops);
	}

	function widget($args, $instance){
		extract( $args );
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$title = apply_filters('widget_title', $title );
		$taxonomy = !empty( $instance['taxonomy'] ) ? explode(",", $instance['taxonomy']) : array('post_tag','video_tag');
		$smallest = !empty( $instance['smallest'] ) ? absint( $instance['smallest'] ) : 8;
		$largest = !empty( $instance['largest'] ) ? absint( $instance['largest'] ) : 15;
		$number = !empty( $instance['number'] ) ? absint( $instance['number'] ) : 20;
		$format = !empty( $instance['format'] ) ? $instance['format'] : 'flat';
		$show_count = ! empty( $instance['show_count'] ) && in_array( $instance['show_count'], array('1', true, 'true', 'on') ) ? true : false;
		$tag_cloud = array(
		    'smallest'                  => $smallest,
		    'largest'                   => $largest,
		    'unit'                      => 'pt',
		    'number'                    => $number,
		    'format'                    => $format,
		    'separator'                 => ' ',
		    'orderby'                   => 'name',
		    'order'                     => 'ASC',
		    'exclude'                   => null,
		    'include'                   => null,
		    'link'                      => 'view',
				'taxonomy'  								=> $taxonomy,
				'show_count'								=>	$show_count,
		    'echo'                      => false
		);
		echo  $before_widget;
			if( !empty( $title ) ){
				echo $before_title . $title . $after_title;
			}

			echo wp_tag_cloud( apply_filters( 'mars_tag_cloud_value' , $tag_cloud) );

		echo $after_widget;
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['taxonomy'] = strip_tags( $new_instance['taxonomy'] );
		$instance['smallest'] = absint( $new_instance['smallest'] );
		$instance['largest'] = absint( $new_instance['largest'] );
		$instance['number'] = absint( $new_instance['number'] );
		$instance['show_count'] = $new_instance['show_count'];
		$instance['format'] = strip_tags( $new_instance['format'] );
		return $instance;

	}
	function form( $instance ){
		$defaults = array(
			'title' => __('Tags Cloud', 'videotube-core'),
			'smallest'	=>	8,
			'largest'	=>	 15,
			'taxonomy'	=>	'post_tag,video_tag',
			'number'	=>	20,
			'format'		=>	'flat',
			'show_count'	=>	'0'
		);

		$format = array( 'flat', 'list' );

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'videotube-core'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e('Taxonomies:', 'videotube-core'); ?></label>
			<input id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" value="<?php echo $instance['taxonomy']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'smallest' ); ?>"><?php _e('Smallest Size:', 'videotube-core'); ?></label>
			<input id="<?php echo $this->get_field_id( 'smallest' ); ?>" name="<?php echo $this->get_field_name( 'smallest' ); ?>" value="<?php echo $instance['smallest']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'largest' ); ?>"><?php _e('Largest Size:', 'videotube-core'); ?></label>
			<input id="<?php echo $this->get_field_id( 'largest' ); ?>" name="<?php echo $this->get_field_name( 'largest' ); ?>" value="<?php echo $instance['largest']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e('Number:', 'videotube-core'); ?></label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'format' ); ?>"><?php _e('Format:', 'videotube-core'); ?></label>
			<select id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>" style="width:100%;">
				<?php for( $i = 0; $i<count( $format ); $i++ ):?>
					<option <?php selected( $format[$i], $instance['format'], true )?> value="<?php echo esc_attr( $format[$i] )?>"><?php echo esc_html( ucfirst( $format[$i] ) );?></option>
				<?php endfor;?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php _e('Show Count:', 'videotube-core'); ?></label>
			<input type="checkbox" <?php checked( 'on', $instance['show_count'], true )?> id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>"/>
		</p>
	<?php
	}
}
