<?php
if( !defined('ABSPATH') ) exit;
function videotube_core_aside_videos_widget_register() {
	register_widget('VideoTube_Core_Widget_Aside_Videos');
}
add_action('widgets_init', 'videotube_core_aside_videos_widget_register');

class VideoTube_Core_Widget_Aside_Videos extends WP_Widget{
	
	function __construct(){
		$widget_ops = array( 'classname' => 'mars-videos-sidebar-widget', 'description' => __('[VideoTube] Aside Videos', 'mars') );
		parent::__construct('mars-videos-sidebar-widget', __('[VideoTube] Aside Videos', 'mars') , $widget_ops);
	}

	function widget($args, $instance){

		extract( $args );
		wp_reset_postdata(); wp_reset_query();

		$instance = wp_parse_args( $instance, array(
			'title'					=>	__('Latest Videos', 'mars'),
			'hide_empty_thumbnail'	=>	''
		) );

		$instance['title'] = ! empty( $instance['title'] ) ? apply_filters('widget_title', $instance['title'] ) : '';

		$video_category = isset( $instance['video_category'] ) ? $instance['video_category'] : null;
		$video_tag = isset( $instance['video_tag'] ) ? $instance['video_tag'] : null;
		$video_date = isset( $instance['date'] ) ? $instance['date'] : null;
		$today = isset( $instance['today'] ) ? $instance['today'] : null;
		$thisweek = isset( $instance['thisweek'] ) ? $instance['thisweek'] : null;		
		$video_orderby = isset( $instance['video_orderby'] ) ? $instance['video_orderby'] : 'ID';
		$video_order = isset( $instance['video_order'] ) ? $instance['video_order'] : 'DESC';
		$widget_column = isset( $instance['widget_column'] ) ? (int)$instance['widget_column'] : 2;
		
		$widget_column = ceil( 12/$widget_column);
		
		$tablet_columns = isset( $instance['tablet_columns'] ) ? (int)$instance['tablet_columns'] : 2;
		
		$tablet_columns = ceil(12/$tablet_columns);
		
		$mobile_columns = isset( $instance['mobile_columns'] ) ? (int)$instance['mobile_columns'] : 1;
		
		$mobile_columns = ceil(12/$mobile_columns);
		
		$thumbnail_size = isset( $instance['thumbnail_size'] ) ? $instance['thumbnail_size'] : 'video-category-featured';
		
		if( empty( $thumbnail_size ) ){
			$thumbnail_size  = 'video-featured';
		}
		
		$video_shows = isset( $instance['video_shows'] ) ? (int)$instance['video_shows'] : 4;  

		echo $before_widget;

		if( ! empty( $instance['title'] ) ){
			if( ! empty( $instance['view_more'] ) ){
				$instance['title'] = '<a href="'. esc_url( $instance['view_more'] ) .'">'. $instance['title'] .'</a>';
			}
			echo $before_title . $instance['title'] . $after_title;
		}
		
		$videos_query = array(
			'post_type'	=>	'video',
			'showposts'	=>	$video_shows,
			'no_found_rows'	=>	true
		);

		if( $instance['hide_empty_thumbnail'] ){
			$videos_query['meta_query'][] = array(
				'key'		=>	'_thumbnail_id',
				'compare'	=>	'EXISTS'
			);
		}
  
		if( $video_category ){
			$videos_query['tax_query'][] = 	array(
				'taxonomy' => 'categories',
				'field' => 'id',
				'terms' => $video_category,
				'operator'	=>	'IN'
			);
		}

		if( $video_tag ){

			$parsed_tags = array();

			$tags = explode( ',', $video_tag );

			if( is_array( $tags ) ){
				for ( $i=0;  $i < count( $tags );  $i++) { 
					if( absint( $tags[$i] ) > 0 ){
						$term = get_term_by( 'id', $tags[$i], 'video_tag' );

						if( $term ){
							$parsed_tags[] = $term->slug;	
						}
						
					}
					else{
						$parsed_tags[] = $tags[$i];
					}
				}
			}

			$videos_query['tax_query'][] = array(
				'taxonomy' => 'video_tag',
				'field' => 'slug',
				'terms' => $parsed_tags
			);
		}
		
		if( $video_orderby ){
			if( $video_orderby == 'views' ){
				$videos_query['meta_key'] = 'count_viewed';
				$videos_query['orderby']	=	'meta_value_num';
			}
			elseif( $video_orderby == 'likes' ){
				$videos_query['meta_key'] = 'like_key';
				$videos_query['orderby']	=	'meta_value_num';				
			}			
			else{
				$videos_query['orderby'] = $video_orderby;	
			}
		}
		if( $video_order ){
			$videos_query['order']	=	$video_order;
		}	
		if( is_singular() ){
			$videos_query['post__not_in'] = array( get_the_ID() );
		}
		
		if( !empty( $video_date ) ){
			$dateime = explode("-", $video_date);
			$videos_query['date_query'] = array(
				array(
					'year'  => isset( $dateime[0] ) ? $dateime[0] : null,
					'month' => isset( $dateime[1] ) ? $dateime[1] : null,
					'day'   => isset( $dateime[2] ) ? $dateime[2] : null,
				)
			);
		}
		
		if( !empty( $today ) ){
			$is_today = getdate();
			$videos_query['date_query'][]	= array(
				'year'  => $is_today['year'],
				'month' => $is_today['mon'],
				'day'   => $is_today['mday']
			);
		}
		if( !empty( $thisweek ) ){
			$videos_query['date_query'][]	= 	array(
				'year' => date( 'Y' ),
				'week' => date( 'W' )
			);
		}		

		$videos_query	=	apply_filters( 'mars_side_widget_args' , $videos_query, $this->id);
		
		$videos_query	=	apply_filters( 'videotube_side_widget_args' , $videos_query, $this->id);
		
		$wp_query = new WP_Query( $videos_query );
		?>
	        <div class="row row-5">
	        	<?php if( $wp_query->have_posts() ): while ( $wp_query->have_posts() ): $wp_query->the_post();?>
	            <div class="col-md-<?php echo esc_attr( $widget_column )?> col-sm-<?php echo esc_attr( $tablet_columns );?> col-<?php echo esc_attr( $mobile_columns );?> item responsive-height <?php print $this->id; ?>-<?php print get_the_ID();?>">
	            	<article <?php post_class();?>>
	            		<?php if( has_post_thumbnail() ):?>
		            	<div class="item-img">
			             	<?php the_post_thumbnail( $thumbnail_size, array('class'=>'img-responsive') );?>
							<a href="<?php echo get_permalink(get_the_ID()); ?>"><div class="img-hover"></div></a>
						</div>	
						<?php endif;?>            	

						<div class="post-header">
			                <?php the_title( '<h3 class="post-title"><a href="'.esc_url( get_permalink() ).'">', '</a></h3>' );?>
							<?php do_action( 'videotube_video_meta' );?>
						</div>
					</article>
	       		</div>
	       		<?php endwhile;endif;?>
	        </div>
	    <?php 		
		echo $after_widget;
		wp_reset_postdata();wp_reset_query();
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
			'title' => __('Right Sidebar Videos', 'mars'),
			'hide_empty_thumbnail'	=>	'',
			'view_more'	=>	'',
			'date'	=>	'',
			'today'	=>	'',
			'thisweek'	=>	'',
			'tablet_columns'	=>	2,
			'mobile_columns'	=>	1,
			'thumbnail_size'	=>	''
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'mars'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo ( isset( $instance['title'] ) ? $instance['title'] : null ); ?>" style="width:100%;" />
		</p>				
		<p>  
		    <label for="<?php echo $this->get_field_id( 'video_category' ); ?>"><?php _e('Video Category:', 'mars'); ?></label>
		    	<?php 
					wp_dropdown_categories($args = array(
							'show_option_all'    => 'All',
							'orderby'            => 'ID', 
							'order'              => 'ASC',
							'show_count'         => 1,
							'hide_empty'         => 1, 
							'child_of'           => 0,
							'echo'               => 1,
							'selected'           => isset( $instance['video_category'] ) ? $instance['video_category'] : null,
							'hierarchical'       => 0, 
							'name'               => $this->get_field_name( 'video_category' ),
							'id'                 => $this->get_field_id( 'video_category' ),
							'taxonomy'           => 'categories',
							'hide_if_empty'      => true,
							'class'              => 'postform mars-dropdown',
			    		)
		    		);
		    	?>
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'video_tag' ); ?>"><?php _e('Video Tag:', 'mars'); ?></label>
		    <input placeholder="<?php _e('Eg: tag1,tag2,tag3','mars');?>" id="<?php echo $this->get_field_id( 'video_tag' ); ?>" name="<?php echo $this->get_field_name( 'video_tag' ); ?>" value="<?php echo ( isset( $instance['video_tag'] ) ? $instance['video_tag'] : null ); ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e('Date (Show posts associated with a certain time, (yyyy-mm-dd)):', 'mars'); ?></label>
		    <input class="vt-datetime" id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>" value="<?php echo ( isset( $instance['date'] ) ? $instance['date'] : null ); ?>" style="width:100%;" />
		</p>
		<p>  
			<label><?php _e('Display the post today','mars')?></label>
			<input <?php checked( 'on', $instance['today'], true );?> type="checkbox" id="<?php echo $this->get_field_id( 'today' ); ?>" name="<?php echo $this->get_field_name( 'today' ); ?>"/>
			<label><?php _e('Or this week','mars')?></label>
			<input <?php checked( 'on', $instance['thisweek'], true );?> type="checkbox" id="<?php echo $this->get_field_id( 'thisweek' ); ?>" name="<?php echo $this->get_field_name( 'thisweek' ); ?>"/>
			<br/>
			<small><?php _e('Do not choose two options.','mars')?></small>
		</p>		
		<p>  
		    <label for="<?php echo $this->get_field_id( 'video_orderby' ); ?>"><?php _e('Orderby:', 'mars'); ?></label>
		    <select style="width:100%;" id="<?php echo $this->get_field_id( 'video_orderby' ); ?>" name="<?php echo $this->get_field_name( 'video_orderby' ); ?>">
		    	<?php 
		    		foreach ( post_orderby_options('video') as $key=>$value ){
		    			$selected = ( $instance['video_orderby'] == $key ) ? 'selected' : null;
		    			?>
		    				<option <?php print $selected; ?> value="<?php print $key;?>"><?php print $value;?></option>
		    			<?php 
		    		}
		    	?>
		    </select>  
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'video_order' ); ?>"><?php _e('Order:', 'mars'); ?></label>
		    <select style="width:100%;" id="<?php echo $this->get_field_id( 'video_order' ); ?>" name="<?php echo $this->get_field_name( 'video_order' ); ?>">
		    	<?php 
		    		foreach ( $this->widget_video_order() as $key=>$value ){
		    			$selected = ( $instance['video_order'] == $key ) ? 'selected' : null;
		    			?>
		    				<option <?php print $selected; ?> value="<?php print $key;?>"><?php print $value;?></option>
		    			<?php 
		    		}
		    	?>
		    </select>  
		</p>								 
		<p>  
		    <label for="<?php echo $this->get_field_id( 'widget_column' ); ?>"><?php _e('Desktop Column:', 'mars'); ?></label>
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
		    <label for="<?php echo $this->get_field_id( 'tablet_columns' ); ?>"><?php _e('Tablet Columns:', 'mars'); ?></label>
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
		    <label for="<?php echo $this->get_field_id( 'mobile_columns' ); ?>"><?php _e('Mobile Columns:', 'mars'); ?></label>
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
		    <label for="<?php echo $this->get_field_id( 'thumbnail_size' ); ?>"><?php _e('Thumbnail Size:', 'mars'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'thumbnail_size' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail_size' ); ?>" value="<?php echo esc_attr( $instance['thumbnail_size'] );?>" style="width:100%;" />
		    <span class="description">
		    	<?php 
		    		esc_html_e( 'Enter the custom image size of leave blank for default.', 'mars' );
		    	?>
		    </span>
		</p>

		<p>  
		    <label for="<?php echo $this->get_field_id( 'hide_empty_thumbnail' ); ?>"><?php _e('Hide empty thumbnail posts:', 'mars'); ?></label>
		    <input type="checkbox" id="<?php echo $this->get_field_id( 'hide_empty_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty_thumbnail' ); ?>" <?php  print isset( $instance['hide_empty_thumbnail'] ) && $instance['hide_empty_thumbnail'] =='on' ? 'checked' : null;?> />
		</p>

		<p>  
		    <label for="<?php echo $this->get_field_id( 'video_shows' ); ?>"><?php _e('Shows:', 'mars'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'video_shows' ); ?>" name="<?php echo $this->get_field_name( 'video_shows' ); ?>" value="<?php echo isset( $instance['video_shows'] ) ? (int)$instance['video_shows'] : 4; ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'view_more' ); ?>"><?php _e('View more link', 'mars'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'view_more' ); ?>" name="<?php echo $this->get_field_name( 'view_more' ); ?>" value="<?php echo ( isset( $instance['view_more'] ) ? $instance['view_more'] : null ); ?>" style="width:100%;" />
		</p>			
	<?php		
	}
	function widget_video_column(){
		return array(
			'2'	=>	__('2 Columns','mars'),
			'1'	=>	__('1 Column','mars')
		);
	}
	function widget_video_order(){
		return array(
			'DESC'	=>	__('DESC','mars'),
			'ASC'	=>	__('ASC','mars')
		);
	}		
}

