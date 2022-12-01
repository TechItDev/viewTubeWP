<?php
if( !defined('ABSPATH') ) exit;
function videotube_core_related_posts_widget_register() {
	register_widget('VideoTube_Core_Widget_Related_Posts');
}
add_action('widgets_init', 'videotube_core_related_posts_widget_register');

class VideoTube_Core_Widget_Related_Posts extends WP_Widget{
	
	function __construct(){
		$widget_ops = array( 'classname' => 'mars-relatedblog-widgets', 'description' => __('[VideoTube] Related Posts', 'videotube-core') );
	
		parent::__construct( 'mars-relatedblog-widgets' , __('[VideoTube] Related Posts', 'videotube-core') , $widget_ops);
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
		$post_orderby = isset( $instance['post_orderby'] ) ? $instance['post_orderby'] : 'ID';
		$post_order = isset( $instance['post_order'] ) ? $instance['post_order'] : 'DESC';
		$post_filter_condition = isset( $instance['post_filter_condition'] ) ? $instance['post_filter_condition'] : 'both';
		
		$post_rows = isset( $instance['rows'] ) ? (int)$instance['rows'] : 1;
		$columns = isset( $instance['columns'] ) ? absint( $instance['columns'] ) : 3;
		$class_columns = ( 12%$columns == 0 ) ? 12/$columns : 3;
		
		$tablet_columns = isset( $instance['tablet_columns'] ) ? (int)$instance['tablet_columns'] : 2;
		
		$tablet_columns = ceil(12/$tablet_columns);
		
		$mobile_columns = isset( $instance['mobile_columns'] ) ? (int)$instance['mobile_columns'] : 1;
		
		$mobile_columns = ceil(12/$mobile_columns);
		
		$thumbnail_size = isset( $instance['thumbnail_size'] ) ? $instance['thumbnail_size'] : 'video-category-featured';
		
		if( empty( $thumbnail_size ) ){
			$thumbnail_size  = 'video-featured';
		}
		
		$autoplay = isset( $instance['auto'] ) ? $instance['auto'] : null;			

		$post_shows = isset( $instance['post_shows'] ) ? (int)$instance['post_shows'] : 16;  
		$current_postID = get_the_ID();
		
		$post_category = $this->get_post_terms($current_postID,'category');
		$post_tag = $this->get_post_terms($current_postID,'post_tag');

		$i=0;
		$posts_query = array(
			'post_type'	=>	'post',
			'showposts'	=>	$post_shows,
			'posts_per_page'	=>	$post_shows,
			'post__not_in'	=>	array($current_postID),
			'no_found_rows'	=>	true				
		);
		if( $post_filter_condition == 'both' ){
			if( isset( $post_category ) ){
				$posts_query['category__in'] = $post_category;
			}
			if( isset( $post_tag ) ){
				$posts_query['tag__in'] = $post_tag;
			}			
		}
		if( $post_filter_condition == 'category' ){
			if( isset( $post_category ) ){
				$posts_query['category__in'] = $post_category;
			}			
		}
		if( $post_filter_condition == 'post_tag' ){
			if( isset( $post_tag ) ){
				$posts_query['tag__in'] = $post_tag;
			}			
		}		
		if( $post_orderby ){
			$posts_query['orderby'] = $post_orderby;
		}
		if( $post_order ){
			$posts_query['order']	=	$post_order;
		}

		$posts_query	=	apply_filters( 'mars_related_widget_args' , $posts_query, $this->id);
		
		$posts_query	=	apply_filters( 'videotube_related_widget_args' , $posts_query, $this->id);
		
		$wp_query = new WP_Query( $posts_query );

		if( ! $wp_query->have_posts() ){
			return;
		}
		
		$is_carousel = false;
		
		if( $post_shows >= $wp_query->post_count && $post_shows > $columns*$post_rows ){
			$is_carousel = true;
		}

		ob_start();

		$carousel_setup = array(
			'interval' => $autoplay ? 5000 : false
		);				

		?>
		<div data-setup="<?php echo esc_attr( json_encode( $carousel_setup ) )?>" id="carousel-latest-<?php print $this->id; ?>" class="related-posts carousel carousel-<?php print $this->id; ?> slide video-section" <?php if($post_shows>3):?> data-ride="carousel"<?php endif;?>>
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
			                		if ( $j % ($columns*$post_rows) == 0 && $j < $post_shows ){
				                    	$c++;
				                    	print '<li data-target="#carousel-latest-'.$this->id.'" data-slide-to="'.$c.'" class="bullet"></li> '; 
				                    }	
			                	}
			                ?>
			            </ol>
		            <?php endif;?>
                </div>
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
			            	<?php if( has_post_thumbnail() ):?>
			            		<div class="item-img">
									<a href="<?php the_permalink()?>">
										<?php the_post_thumbnail( $thumbnail_size, array('class'=>'img-responsive') );?>
									</a>
								</div>
							<?php endif;?>
							<div class="post-header">
                            	<?php the_title( '<h3 class="post-title"><a href="'.esc_url( get_permalink() ).'">', '</a></h3>' );?>
                            	<?php do_action( 'videotube_post_meta' );?>
                        	</div>
                         </div> 
                    <?php
                    if ( $i % ($post_rows*$columns) == 0 && $i < $post_shows ){	
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
		$instance['post_orderby'] = strip_tags( $new_instance['post_orderby'] );
		$instance['post_order'] = strip_tags( $new_instance['post_order'] );
		$instance['post_filter_condition'] = strip_tags( $new_instance['post_filter_condition'] );
		$instance['post_shows'] = strip_tags( $new_instance['post_shows'] );
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
			'title' => __('Related posts', 'videotube-core'),
			'columns'	=>	3,
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
		    		foreach ( $this->widget_post_order() as $key=>$value ){
		    			$selected = ( $instance['post_order'] == $key ) ? 'selected' : null;
		    			?>
		    				<option <?php print $selected; ?> value="<?php print $key;?>"><?php print $value;?></option>
		    			<?php 
		    		}
		    	?>
		    </select>  
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'post_filter_condition' ); ?>"><?php _e('Filter Condition:', 'videotube-core'); ?></label>
		    <select style="width:100%;" id="<?php echo $this->get_field_id( 'post_filter_condition' ); ?>" name="<?php echo $this->get_field_name( 'post_filter_condition' ); ?>">
		    	<?php 
		    		foreach ( $this->condition() as $key=>$value ){
		    			$selected = ( $instance['post_filter_condition'] == $key ) ? 'selected' : null;
		    			?>
		    				<option <?php print $selected; ?> value="<?php print $key;?>"><?php print $value;?></option>
		    			<?php 
		    		}
		    	?>
		    </select>  
		</p>		
		<p>  
		    <label for="<?php echo $this->get_field_id( 'post_shows' ); ?>"><?php _e('Shows:', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'post_shows' ); ?>" name="<?php echo $this->get_field_name( 'post_shows' ); ?>" value="<?php echo isset( $instance['post_shows'] ) ? (int)$instance['post_shows'] : 16; ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php _e('Columns:', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>" value="<?php echo $instance['columns']; ?>" style="width:100%;" />
		    <small><?php _e('You can set the columns for displaying the Videos, example: 3,4 or 6.','videotube-core');?></small>
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'tablet_columns' ); ?>"><?php _e('Tablet Columns:', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'tablet_columns' ); ?>" name="<?php echo $this->get_field_name( 'tablet_columns' ); ?>" value="<?php echo $instance['tablet_columns']; ?>" style="width:100%;" />
		</p>		
		<p>  
		    <label for="<?php echo $this->get_field_id( 'mobile_columns' ); ?>"><?php _e('Mobile Columns:', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'mobile_columns' ); ?>" name="<?php echo $this->get_field_name( 'mobile_columns' ); ?>" value="<?php echo $instance['mobile_columns']; ?>" style="width:100%;" />
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
		    <label for="<?php echo $this->get_field_id( 'rows' ); ?>"><?php _e('Rows:', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'rows' ); ?>" name="<?php echo $this->get_field_name( 'rows' ); ?>" value="<?php echo (isset( $instance['rows'] )) ? (int)$instance['rows'] : 1; ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'auto' ); ?>"><?php _e('Auto Carousel:', 'videotube-core'); ?></label>
		    <input type="checkbox" id="<?php echo $this->get_field_id( 'auto' ); ?>" name="<?php echo $this->get_field_name( 'auto' ); ?>" <?php  print isset( $instance['auto'] ) && $instance['auto'] =='on' ? 'checked' : null;?> />
		</p>
	<?php
	}
	function widget_post_order(){
		return array(
			'ASC'	=>	__('ASC','videotube-core'),
			'DESC'	=>	__('DESC','videotube-core')
		);
	}	
	function condition(){
		return array(
			'both'			=>	__('Category and Post Tag','videotube-core'),
			'category'	=>	__('Category','videotube-core'),
			'post_tag'		=>	__('Post Tag','videotube-core')
		);
	}
}