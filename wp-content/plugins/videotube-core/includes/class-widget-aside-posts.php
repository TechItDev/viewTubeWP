<?php
if( !defined('ABSPATH') ) exit;

function videotube_core_aside_posts_widget_register() {
	register_widget('VideoTube_Core_Widget_Aside_Posts');
}
add_action('widgets_init', 'videotube_core_aside_posts_widget_register');

class VideoTube_Core_Widget_Aside_Posts extends WP_Widget{
	
	function __construct(){
		$widget_ops = array( 'classname' => 'mars-posts-sidebar-widget', 'description' => __('[VideoTube] Aside Posts', 'videotube-core') );
	
		parent::__construct( 'mars-posts-sidebar-widget' , __('[VideoTube] Aside Posts', 'videotube-core' ) , $widget_ops);
	}	
	
	function widget($args, $instance){
		$WidgetHTML = null;
		extract( $args );
		wp_reset_postdata();wp_reset_query();

		$instance = wp_parse_args( $instance, array(
			'title'					=>	__('Latest Posts', 'videotube-core'),
			'hide_empty_thumbnail'	=>	''
		) );

		$instance['title'] = ! empty( $instance['title'] ) ? apply_filters('widget_title', $instance['title'] ) : '';

		$post_category = isset( $instance['post_category'] ) ? $instance['post_category'] : null;
		$post_tag = isset( $instance['post_tag'] ) ? $instance['post_tag'] : null;
		$post_date = isset( $instance['date'] ) ? $instance['date'] : null;
		$today = isset( $instance['today'] ) ? $instance['today'] : null;
		$thisweek = isset( $instance['thisweek'] ) ? $instance['thisweek'] : null;		
		$post_orderby = isset( $instance['post_orderby'] ) ? $instance['post_orderby'] : 'ID';
		$post_order = isset( $instance['post_order'] ) ? $instance['post_order'] : 'DESC';
		$widget_column = isset( $instance['widget_column'] ) ? (int)$instance['widget_column'] : 2;
		
		$widget_column = ceil(12/$widget_column);
		
		$tablet_columns = isset( $instance['tablet_columns'] ) ? (int)$instance['tablet_columns'] : 2;
		
		$tablet_columns = ceil(12/$tablet_columns);
		
		$mobile_columns = isset( $instance['mobile_columns'] ) ? (int)$instance['mobile_columns'] : 1;
		
		$mobile_columns = ceil(12/$mobile_columns);
		
		$thumbnail_size = isset( $instance['thumbnail_size'] ) ? $instance['thumbnail_size'] : 'video-category-featured';
		
		if( empty( $thumbnail_size ) ){
			$thumbnail_size  = 'video-featured';
		}
		
		$post_shows = isset( $instance['post_shows'] ) ? (int)$instance['post_shows'] : 4; 


		$posts_query = array(
			'post_type'				=>	'post',
			'showposts'				=>	$post_shows,
			'ignore_sticky_posts'	=>	true,
			'no_found_rows'			=>	true				
		);

		if( $instance['hide_empty_thumbnail'] ){
			$posts_query['meta_query'][] = array(
				'key'		=>	'_thumbnail_id',
				'compare'	=>	'EXISTS'
			);
		}		
                       	
		if( $post_category ){
			$posts_query['tax_query'] = array(
				array(
				'taxonomy' => 'category',
				'field' => 'id',
				'terms' => $post_category
				)		                       		
			);
		}
		if( $post_tag ){

			$parsed_tags = array();

			$tags = explode( ',', $post_tag );

			if( is_array( $tags ) ){
				for ( $i=0;  $i < count( $tags );  $i++) { 
					if( absint( $tags[$i] ) > 0 ){
						$term = get_term_by( 'id', $tags[$i], 'post_tag' );

						if( $term ){
							$parsed_tags[] = $term->slug;	
						}
						
					}
					else{
						$parsed_tags[] = $tags[$i];
					}
				}
			}

			$posts_query['tax_query'][] = array(
				'taxonomy' => 'post_tag',
				'field' => 'slug',
				'terms' => $parsed_tags
			);
		}
		
		if( $post_orderby ){
			$posts_query['orderby'] = $post_orderby;	
		}
		if( $post_order ){
			$posts_query['order']	=	$post_order;
		}	
		if( is_singular() ){
			$posts_query['post__not_in'] = array( get_the_ID() );
		}
		
		if( !empty( $post_date ) ){
			$dateime = explode("-", $post_date);
			$posts_query['date_query'] = array(
				array(
					'year'  => isset( $dateime[0] ) ? $dateime[0] : null,
					'month' => isset( $dateime[1] ) ? $dateime[1] : null,
					'day'   => isset( $dateime[2] ) ? $dateime[2] : null,
				)
			);
		}
		
		if( !empty( $today ) ){
			$is_today = getdate();
			$posts_query['date_query'][]	= array(
				'year'  => $is_today['year'],
				'month' => $is_today['mon'],
				'day'   => $is_today['mday']
			);
		}
		if( !empty( $thisweek ) ){
			$posts_query['date_query'][]	= 	array(
				'year' => date( 'Y' ),
				'week' => date( 'W' )
			);
		}

		$posts_query	=	apply_filters( 'mars_side_widget_args' , $posts_query, $this->id);		
		
		$posts_query	=	apply_filters( 'videotube_side_widget_args' , $posts_query, $this->id);		
		
		$wp_query = new WP_Query( $posts_query );

		if( ! $wp_query->have_posts() ){
			return;
		}

		print  $before_widget;
		
		if( ! empty( $instance['title'] ) ){
			if( ! empty( $instance['view_more'] ) ){
				$instance['title'] = '<a href="'. esc_url( $instance['view_more'] ) .'">'. $instance['title'] .'</a>';
			}
			print $before_title . $instance['title'] . $after_title;
		}
		?>
	        <div class="row row-5">
	        	<?php while ( $wp_query->have_posts() ): $wp_query->the_post();?>
	            <div id="post-right-<?php print $this->id; ?>-<?php the_ID();?>" <?php post_class('col-md-'.esc_attr( $widget_column ).' col-sm-'. esc_attr( $tablet_columns ) .' col-'.esc_attr( $mobile_columns ).' item responsive-height'); ?>>

	            	<?php if( has_post_thumbnail() ):?>
	            		<div class="item-img">
							<a href="<?php the_permalink()?>">
								<?php the_post_thumbnail( $thumbnail_size, array('class'=>'img-responsive') );?>
							</a>
						</div>
					<?php endif;?>

                    <div class="post-header">              	         	
	                	<?php the_title( '<h3 class="post-title"><a href="'.esc_url( get_permalink() ).'">', '</a></h3>' );?>
						<span class="post-meta">
							<i class="far fa-clock"></i> <?php print get_the_date();?>
						</span>	                	
	                </div>
	       		</div>
	       		<?php endwhile;?>
	        </div>
	    <?php 		
	    wp_reset_postdata();wp_reset_query();

		print $after_widget;

	}

	/**
	 * {@inheritDoc}
	 * @see WP_Widget::update()
	 */
	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ){
		$defaults = array( 
			'title' => __('Latest Posts', 'videotube-core'),
			'hide_empty_thumbnail'	=>	'',
			'date'	=>	'',
			'today'	=>	'',
			'thisweek'	=>	'',
			'view_more'	=>	'',
			'tablet_columns'	=>	2,
			'mobile_columns'	=>	1,
			'thumbnail_size'	=>	''
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'videotube-core'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo ( isset( $instance['title'] ) ? $instance['title'] : null ); ?>" style="width:100%;" />
		</p>		
		<p>  
		    <label for="<?php echo $this->get_field_id( 'video_category' ); ?>"><?php _e('Video Category:', 'videotube-core'); ?></label>
		    	<?php 
					wp_dropdown_categories($args = array(
							'show_option_all'    => 'All',
							'orderby'            => 'ID', 
							'order'              => 'ASC',
							'show_count'         => 1,
							'hide_empty'         => 1, 
							'child_of'           => 0,
							'echo'               => 1,
							'selected'           => isset( $instance['post_category'] ) ? $instance['post_category'] : null,
							'hierarchical'       => 0, 
							'name'               => $this->get_field_name( 'post_category' ),
							'id'                 => $this->get_field_id( 'post_category' ),
							'taxonomy'           => 'category',
							'hide_if_empty'      => true,
							'class'              => 'postform mars-dropdown',
			    		)
		    		);
		    	?>
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'post_tag' ); ?>"><?php _e('Post Tag:', 'videotube-core'); ?></label>
		    <input placeholder="<?php _e('Eg: tag1,tag2,tag3','videotube-core');?>" id="<?php echo $this->get_field_id( 'post_tag' ); ?>" name="<?php echo $this->get_field_name( 'post_tag' ); ?>" value="<?php echo ( isset( $instance['post_tag'] ) ? $instance['post_tag'] : null ); ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e('Date (Show posts associated with a certain time, (yyyy-mm-dd)):', 'videotube-core'); ?></label>
		    <input class="vt-datetime" id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>" value="<?php echo ( isset( $instance['date'] ) ? $instance['date'] : null ); ?>" style="width:100%;" />
		</p>
		<p>  
			<label><?php _e('Display the post today','videotube-core')?></label>
			<input <?php checked( 'on', $instance['today'], true );?> type="checkbox" id="<?php echo $this->get_field_id( 'today' ); ?>" name="<?php echo $this->get_field_name( 'today' ); ?>"/>
			<label><?php _e('Or this week','videotube-core')?></label>
			<input <?php checked( 'on', $instance['thisweek'], true );?> type="checkbox" id="<?php echo $this->get_field_id( 'thisweek' ); ?>" name="<?php echo $this->get_field_name( 'thisweek' ); ?>"/>
			<br/>
			<small><?php _e('Do not choose two options.','videotube-core')?></small>
		</p>		
		<p>  
		    <label for="<?php echo $this->get_field_id( 'post_orderby' ); ?>"><?php _e('Orderby:', 'videotube-core'); ?></label>
		    <select style="width:100%;" id="<?php echo $this->get_field_id( 'post_orderby' ); ?>" name="<?php echo $this->get_field_name( 'post_orderby' ); ?>">
		    	<?php 
		    		foreach ( post_orderby_options() as $key=>$value ){
		    			$selected = ( $instance['post_orderby'] == $key ) ? 'selected' : null;
		    			?>
		    				<option <?php print $selected; ?> value="<?php print $key;?>"><?php print $value;?></option>
		    			<?php 
		    		}
		    	?>
		    </select>
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'post_order' ); ?>"><?php _e('Order:', 'videotube-core'); ?></label>
		    <select style="width:100%;" id="<?php echo $this->get_field_id( 'post_order' ); ?>" name="<?php echo $this->get_field_name( 'post_order' ); ?>">
		    	<?php 
		    		foreach ( $this->widget_video_order() as $key=>$value ){
		    			$selected = ( $instance['post_order'] == $key ) ? 'selected' : null;
		    			?>
		    				<option <?php print $selected; ?> value="<?php print $key;?>"><?php print $value;?></option>
		    			<?php 
		    		}
		    	?>
		    </select>  
		</p>								 
		<p>  
		    <label for="<?php echo $this->get_field_id( 'widget_column' ); ?>"><?php _e('Desktop Column:', 'videotube-core'); ?></label>
		    <select style="width:100%;" id="<?php echo $this->get_field_id( 'widget_column' ); ?>" name="<?php echo $this->get_field_name( 'widget_column' ); ?>">
		    	<?php 
		    		foreach ( $this->widget_video_column() as $key=>$value ){
		    			$selected = ( $instance['widget_column'] == $key ) ? 'selected' : null;
		    			?>
		    				<option <?php print $selected; ?> value="<?php print $key;?>"><?php print $value;?></option>
		    			<?php 
		    		}
		    	?>
		    </select> 
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'tablet_columns' ); ?>"><?php _e('Tablet Columns:', 'videotube-core'); ?></label>
		    <select style="width:100%;" id="<?php echo $this->get_field_id( 'tablet_columns' ); ?>" name="<?php echo $this->get_field_name( 'tablet_columns' ); ?>">
		    	<?php 
		    		foreach ( $this->widget_video_column() as $key=>$value ){
		    			$selected = ( $instance['tablet_columns'] == $key ) ? 'selected' : null;
		    			?>
		    				<option <?php print $selected; ?> value="<?php print $key;?>"><?php print $value;?></option>
		    			<?php 
		    		}
		    	?>
		    </select>
		</p>		
		<p>  
		    <label for="<?php echo $this->get_field_id( 'mobile_columns' ); ?>"><?php _e('Mobile Columns:', 'videotube-core'); ?></label>
		    <select style="width:100%;" id="<?php echo $this->get_field_id( 'mobile_columns' ); ?>" name="<?php echo $this->get_field_name( 'mobile_columns' ); ?>">
		    	<?php 
		    		foreach ( $this->widget_video_column() as $key=>$value ){
		    			$selected = ( $instance['mobile_columns'] == $key ) ? 'selected' : null;
		    			?>
		    				<option <?php print $selected; ?> value="<?php print $key;?>"><?php print $value;?></option>
		    			<?php 
		    		}
		    	?>
		    </select>
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'thumbnail_size' ); ?>"><?php _e('Thumbnail Size:', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'thumbnail_size' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail_size' ); ?>" value="<?php echo esc_attr( $instance['thumbnail_size'] );?>" style="width:100%;" />
		    <span class="description">
		    	<?php 
		    		esc_html_e( 'Enter the custom image size of leave blank for default.', 'videotube-core' );
		    	?>
		    </span>
		</p>		

		<p>  
		    <label for="<?php echo $this->get_field_id( 'hide_empty_thumbnail' ); ?>"><?php _e('Hide empty thumbnail posts:', 'videotube-core'); ?></label>
		    <input type="checkbox" id="<?php echo $this->get_field_id( 'hide_empty_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty_thumbnail' ); ?>" <?php  print isset( $instance['hide_empty_thumbnail'] ) && $instance['hide_empty_thumbnail'] =='on' ? 'checked' : null;?> />
		</p>			
		<p>  
		    <label for="<?php echo $this->get_field_id( 'post_shows' ); ?>"><?php _e('Shows:', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'post_shows' ); ?>" name="<?php echo $this->get_field_name( 'post_shows' ); ?>" value="<?php echo isset( $instance['post_shows'] ) ? (int)$instance['post_shows'] : 4; ?>" style="width:100%;" />
		</p>	
		<p>  
		    <label for="<?php echo $this->get_field_id( 'view_more' ); ?>"><?php _e('View more link', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'view_more' ); ?>" name="<?php echo $this->get_field_name( 'view_more' ); ?>" value="<?php echo ( isset( $instance['view_more'] ) ? $instance['view_more'] : null ); ?>" style="width:100%;" />
		</p>	
	<?php		
	}
	function widget_video_column(){
		return array(
			'2'	=>	__('2 Columns','videotube-core'),
			'1'	=>	__('1 Column','videotube-core')
		);
	}
	function widget_video_order(){
		return array(
			'DESC'	=>	__('DESC','videotube-core'),
			'ASC'	=>	__('ASC','videotube-core')
		);
	}		
}

