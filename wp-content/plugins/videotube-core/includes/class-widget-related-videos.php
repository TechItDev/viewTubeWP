<?php
if( !defined('ABSPATH') ) exit;
function videotube_core_related_videos_widget_register() {
	register_widget('VideoTube_Core_Widget_Related_Videos');
}
add_action( 'widgets_init', 'videotube_core_related_videos_widget_register');


class VideoTube_Core_Widget_Related_Videos extends WP_Widget{
	
	function __construct(){
		$widget_ops = array( 'classname' => 'mars-relatedvideo-widgets', 'description' => __('[VideoTube] Related Videos', 'videotube-core') );
		
		parent::__construct( 'mars-relatedvideo-widgets' , __('[VideoTube] Related Videos', 'videotube-core') , $widget_ops);
	}	


	function get_post_terms( $post_id, $taxonomy ){
		$terms = wp_get_post_terms($post_id,$taxonomy, array("fields" => "ids"));
		return $terms;
	}	

	function widget($args, $instance){
		global $post;	
		extract( $args );
		wp_reset_postdata();wp_reset_query();
		$title = apply_filters('widget_title', $instance['title'] );
		$video_orderby = isset( $instance['video_orderby'] ) ? $instance['video_orderby'] : 'ID';
		$video_order = isset( $instance['video_order'] ) ? $instance['video_order'] : 'DESC';
		$video_filter_condition = isset( $instance['video_filter_condition'] ) ? $instance['video_filter_condition'] : 'both';
		$video_rows = isset( $instance['rows'] ) ? (int)$instance['rows'] : 1;
		$columns = isset( $instance['columns'] ) ? absint( $instance['columns'] ) : 3;
		$class_columns = ( 12%$columns == 0 ) ? 12/$columns : 3;
		
		$tablet_columns = isset( $instance['tablet_columns'] ) ? (int)$instance['tablet_columns'] : 2;
		
		$tablet_columns = ceil(12/$tablet_columns);
		
		$mobile_columns = isset( $instance['mobile_columns'] ) ? (int)$instance['mobile_columns'] : 1;
		
		$mobile_columns = ceil(12/$mobile_columns);
		
		$thumbnail_size = isset( $instance['thumbnail_size'] ) ? $instance['thumbnail_size'] : 'video-featured';
		
		if( empty( $thumbnail_size ) ){
			$thumbnail_size  = 'video-featured';
		}
		
		$autoplay = isset( $instance['auto'] ) ? $instance['auto'] : null;		
		$video_shows = isset( $instance['video_shows'] ) ? (int)$instance['video_shows'] : 16;  
		$current_videoID = get_the_ID();
		
		$video_category = $this->get_post_terms($current_videoID,'categories');
		$video_tag = $this->get_post_terms($current_videoID,'video_tag');

		$i=0;
		$videos_query = array(
			'post_type'	=>	'video',
			'showposts'	=>	$video_shows,
			'posts_per_page'	=>	$video_shows,
			'post__not_in'	=>	array($current_videoID),
			'no_found_rows'	=>	true
		);
        if( $video_filter_condition == 'both' ){

        	if( !empty( $video_category ) ){
				$videos_query['tax_query'] = array(
					array(
						'taxonomy' => 'categories',
						'field' => 'id',
						'terms' => $video_category
					)
				);
			}
			if( !empty( $video_tag ) ){
				$videos_query['tax_query'] = array(
					array(
						'taxonomy' => 'video_tag',
						'field' => 'id',
						'terms' => $video_tag
					)
				);
			}
        }
         if( $video_filter_condition == 'categories' ){
            if( !empty( $video_category ) ){
				$videos_query['tax_query'] = array(
					array(
						'taxonomy' => 'categories',
						'field' => 'id',
						'terms' => $video_category
					)
				);
			}         	
         }
        
	    if( $video_filter_condition == 'video_tag' ){
	    	if( !empty( $video_tag ) ){
				$videos_query['tax_query'] = array(
					array(
						'taxonomy' => 'video_tag',
						'field' => 'id',
						'terms' => $video_tag
					)
				);
			}         	
         }        
        
		if( isset( $video_orderby ) ){
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
		if( isset( $video_order ) ){
			$videos_query['order']	=	$video_order;
		}
		if( isset( $post->ID ) ){
			$videos_query['post__not_in'] = array( $post->ID  );
		}
		
		// Do not show the empty image.
		$videos_query['meta_query'][] = array(
			'key'	=>	'_thumbnail_id',
			'compare'	=>	'EXISTS'
		);

		$videos_query	=	apply_filters( 'mars_related_widget_args' , $videos_query, $this->id );
		
		$videos_query	=	apply_filters( 'videotube_related_widget_args' , $videos_query, $this->id );
		
		$wp_query = new WP_Query( $videos_query );

		if( ! $wp_query->have_posts() ){
			return;
		}
		
		$is_carousel = false;
		
		if( $video_shows >= $wp_query->post_count && $video_shows > $columns*$video_rows ){
			$is_carousel = true;
		}

		ob_start();

		$carousel_setup = array(
			'interval' => $autoplay ? 5000 : false
		);				
		
		?>
			<div data-setup="<?php echo esc_attr( json_encode( $carousel_setup ) )?>" id="carousel-latest-<?php print $this->id; ?>" class="related-posts carousel carousel-<?php print $this->id; ?> slide video-section" <?php if($video_shows>3):?> data-ride="carousel"<?php endif;?>>
				<?php if( ! empty( $title ) || $is_carousel === true ):?>
	          		<div class="section-header">
	          			<?php if( ! empty( $title ) ):?>
	          				<?php echo $args['before_title']?>
                        		<?php print $title;?>
                        	<?php echo $args['after_title']?>
                        <?php endif;?>
                        <?php if( $is_carousel ):?>
				            <ol class="carousel-indicators section-nav">
				            	<li data-target="#carousel-latest-<?php print $this->id; ?>" data-slide-to="0" class="bullet active"></li>
				                <?php 
				                	$c = 0;
				                	for ($j = 1; $j < $wp_query->post_count; $j++) {
				                		if ( $j % ($columns*$video_rows) == 0 && $j < $video_shows ){
					                    	$c++;
					                    	print '<li data-target="#carousel-latest-'.$this->id.'" data-slide-to="'.$c.'" class="bullet"></li> '; 
					                    }	
				                	}
				                ?>
				            </ol>
			            <?php endif;?>
                    </div><!-- end section header -->
                   <?php endif;?>
                   
	               <div class="carousel-inner">
	                   	<?php
	                   	if( $wp_query->have_posts() ) : 
	                   		$i =0;
	                       	while ( $wp_query->have_posts() ) : $wp_query->the_post();
	                       	$i++;
	                       	?>
	                       	<?php if( $i == 1 ):?>
	                       		<div class="carousel-item item active"><div class="row row-5">
	                       	<?php endif;?>	
	                       		<div class="col-xl-<?php print $class_columns;?> col-lg-<?php print $class_columns;?> col-md-<?php echo esc_attr( $tablet_columns );?> col-<?php echo esc_attr( $mobile_columns );?> item responsive-height <?php print $this->id; ?>-<?php print get_the_ID();?>">
	                       			<div class="item-img">
	                                <?php 
	                                	if(has_post_thumbnail()){
	                                		if( $columns ==2 ){
	                                			print '<a href="'.get_permalink(get_the_ID()).'">'. get_the_post_thumbnail(null,apply_filters( 'related_video/thumbnail_size' , $thumbnail_size), array('class'=>'img-responsive')).'</a>';
	                                		}
	                                		else{
	                                			print '<a href="'.get_permalink(get_the_ID()).'">'. get_the_post_thumbnail(null,apply_filters( 'related_video/thumbnail_size' , $thumbnail_size), array('class'=>'img-responsive')).'</a>';
	                                		}
	                                	}
	                                ?>
										<a href="<?php echo get_permalink(get_the_ID()); ?>"><div class="img-hover"></div></a>
									</div>

									<div class="post-header">		                                
										<?php the_title( '<h3 class="post-title"><a href="'.esc_url( get_permalink() ).'">', '</a></h3>' );?>
										<?php do_action( 'videotube_video_meta' );?>
									</div>
	                             </div> 
		                    <?php
		                    if ( $i % ($video_rows*$columns) == 0 && $i < $video_shows ){	
		                    	?></div></div><div class="carousel-item item"><div class="row row-5"><?php 
		                    } 	             
	                       	endwhile;
	                      ?></div></div><?php 
	                   	endif;
	                   	?> 
	                </div>
                </div><!-- /#carousel-->
			<?php 

		wp_reset_query();wp_reset_postdata();			

		$widget_content = ob_get_clean();

		echo $args['before_widget'] . $widget_content . $args['after_widget'];

	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['video_orderby'] = strip_tags( $new_instance['video_orderby'] );
		$instance['video_order'] = strip_tags( $new_instance['video_order'] );
		$instance['video_filter_condition'] = strip_tags( $new_instance['video_filter_condition'] );
		$instance['video_shows'] = strip_tags( $new_instance['video_shows'] );
		$instance['rows'] = absint( $new_instance['rows'] );
		$instance['columns']	=	absint( $new_instance['columns'] );
		$instance['tablet_columns'] = strip_tags( $new_instance['tablet_columns'] );
		$instance['mobile_columns'] = strip_tags( $new_instance['mobile_columns'] );
		$instance['thumbnail_size'] = strip_tags( $new_instance['thumbnail_size'] );
		$instance['auto'] = strip_tags( $new_instance['auto'] );		
		return $instance;
		
	}
	function form( $instance ){
		$defaults = array( 
			'title' => __('Related Videos', 'mars'),
			'columns'	=>	3,
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
		    <label for="<?php echo $this->get_field_id( 'video_filter_condition' ); ?>"><?php _e('Filter Condition:', 'mars'); ?></label>
		    <select style="width:100%;" id="<?php echo $this->get_field_id( 'video_filter_condition' ); ?>" name="<?php echo $this->get_field_name( 'video_filter_condition' ); ?>">
		    	<?php 
		    		foreach ( $this->condition() as $key=>$value ){
		    			$selected = ( $instance['video_filter_condition'] == $key ) ? 'selected' : null;
		    			?>
		    				<option <?php print $selected; ?> value="<?php print $key;?>"><?php print $value;?></option>
		    			<?php 
		    		}
		    	?>
		    </select>  
		</p>		
		<p>  
		    <label for="<?php echo $this->get_field_id( 'video_shows' ); ?>"><?php _e('Shows:', 'mars'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'video_shows' ); ?>" name="<?php echo $this->get_field_name( 'video_shows' ); ?>" value="<?php echo isset( $instance['video_shows'] ) ? (int)$instance['video_shows'] : 16; ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php _e('Desktop Columns:', 'mars'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>" value="<?php echo $instance['columns']; ?>" style="width:100%;" />
		    <small><?php _e('You can set the columns for displaying the Videos, example: 3,4 or 6.','mars');?></small>
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'tablet_columns' ); ?>"><?php _e('Tablet Columns:', 'mars'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'tablet_columns' ); ?>" name="<?php echo $this->get_field_name( 'tablet_columns' ); ?>" value="<?php echo $instance['tablet_columns']; ?>" style="width:100%;" />
		</p>		
		<p>  
		    <label for="<?php echo $this->get_field_id( 'mobile_columns' ); ?>"><?php _e('Mobile Columns:', 'mars'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'mobile_columns' ); ?>" name="<?php echo $this->get_field_name( 'mobile_columns' ); ?>" value="<?php echo $instance['mobile_columns']; ?>" style="width:100%;" />
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
		    <label for="<?php echo $this->get_field_id( 'rows' ); ?>"><?php _e('Rows:', 'mars'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'rows' ); ?>" name="<?php echo $this->get_field_name( 'rows' ); ?>" value="<?php echo (isset( $instance['rows'] )) ? (int)$instance['rows'] : 1; ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'auto' ); ?>"><?php _e('Auto Carousel', 'mars'); ?></label>
		    <input type="checkbox" id="<?php echo $this->get_field_id( 'auto' ); ?>" name="<?php echo $this->get_field_name( 'auto' ); ?>" <?php  print isset( $instance['auto'] ) && $instance['auto'] =='on' ? 'checked' : null;?> />
		</p>
	<?php
	}
	function widget_video_order(){
		return array(
			'ASC'	=>	__('ASC','mars'),
			'DESC'	=>	__('DESC','mars')
		);
	}
	function condition(){
		return array(
			'both'			=>	__('Video Category and Video Tag','mars'),
			'categories'	=>	__('Video Category','mars'),
			'video_tag'		=>	__('Video Tag','mars')
		);
	}
}