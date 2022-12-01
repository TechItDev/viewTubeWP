<?php
/**
 * VideoTube Featured Widget
 * Add Video Featured Widget
 * @author 		Toan Nguyen
 * @category 	Core
 * @version     1.0.0
 */
if( !defined('ABSPATH') ) exit;


function videotube_core_featured_posts_widget_register() {

	register_widget('VideoTube_Core_Widget_Featured_Posts');
}

add_action('widgets_init', 'videotube_core_featured_posts_widget_register');


class VideoTube_Core_Widget_Featured_Posts extends WP_Widget{
	
	function __construct(){
		$widget_ops = array( 'classname' => 'mars-featuredpost-widgets', 'description' => __('[VideoTube] Featured Posts', 'videotube-core') );
	
		parent::__construct( 'mars-featuredpost-widgets' , __('[VideoTube] Featured Posts', 'videotube-core') , $widget_ops);
	}	
	
	function widget($args, $instance){
		extract( $args );
		wp_reset_postdata();wp_reset_query();

		$instance = wp_parse_args( $instance, array(
			'title'	=>	__('Featured Posts', 'videotube-core'),
			'icon'	=>	'fa fa-pencil-alt'
		) );

		$instance['title'] = apply_filters('widget_title', $instance['title'] );

		$post_category = isset( $instance['post_category'] ) ? $instance['post_category'] : null;
		$post_tag = isset( $instance['post_tag'] ) ? $instance['post_tag'] : null;
		$post_date = isset( $instance['date'] ) ? $instance['date'] : null;
		$today = isset( $instance['today'] ) ? $instance['today'] : null;
		$thisweek = isset( $instance['thisweek'] ) ? $instance['thisweek'] : null;		
		$post_orderby = isset( $instance['post_orderby'] ) ? $instance['post_orderby'] : 'ID';
		$post_order = isset($instance['post_order']) ? $instance['post_order'] : 'DESC';
		$post_ids = isset( $instance['ids'] ) ? $instance['ids'] : null;
		$post_shows = isset( $instance['post_shows'] ) ? (int)$instance['post_shows'] : 9;  
		$post_sticky = isset( $instance['post_sticky'] ) ? $instance['post_sticky'] : null;
		$post_rows = isset( $instance['rows'] ) ? (int)$instance['rows'] : 1;
		$columns = isset( $instance['columns'] ) ? absint( $instance['columns'] ) : 3;
		$class_columns = ( 12%$columns == 0 ) ? 12/$columns : 3;		
			
		$tablet_columns = isset( $instance['tablet_columns'] ) ? (int)$instance['tablet_columns'] : 3;
		
		$tablet_columns = ceil(12/$tablet_columns);
		
		$mobile_columns = isset( $instance['mobile_columns'] ) ? (int)$instance['mobile_columns'] : 1;
		
		$mobile_columns = ceil(12/$mobile_columns);
		
		$autoplay = isset( $instance['auto'] ) ? $instance['auto'] : null;
		$i=0;
		$posts_query = array(
			'post_type'			=>	'post',
			'posts_per_page'	=>	$post_shows,
			'no_found_rows'		=>	true,
			'meta_query'		=>	array(
				array(
					'key'		=>	'_thumbnail_id',
					'compare'	=>	'EXISTS'
				)
			)			
		);
		if( $post_sticky =='on' ){
			$sticky = get_option( 'sticky_posts' );
			$posts_query['post__in']	=	$sticky;
		}
		else{
			$posts_query['ignore_sticky_posts']	=	true;
		}
		if( $post_category ){
			$posts_query['tax_query'][] = array(
				'taxonomy' => 'category',
				'field' => 'id',
				'terms' => explode( ',', $post_category )
			);
		}

		if( ! empty( $post_tag ) ){

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
				'taxonomy'	=> 'post_tag',
				'field' 	=> 'slug',
				'terms' 	=>	$parsed_tags
			);
		}

		if( $post_orderby ){
			$posts_query['orderby'] = $post_orderby;	
		}
		if( $post_order ){
			$posts_query['order']	=	$post_order;
		}
		if( $post_ids ){
			$posts_query['post__in']	=	explode(",", $post_ids);
		}
		if( $post_date ){
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

		$posts_query	=	apply_filters( 'mars_featured_widget_args' , $posts_query, $this->id);		
		
		$posts_query	=	apply_filters( 'videotube_featured_widget_args' , $posts_query, $this->id);
		
		$wp_query = new WP_Query( $posts_query );

		ob_start();

		$carousel_setup = array(
			'interval' => $autoplay ? 5000 : false
		);		
		?>
		    <div data-setup="<?php echo esc_attr( json_encode( $carousel_setup ) )?>" id="carousel-featured-<?php print $this->id; ?>" class="carousel carousel-<?php print $this->id; ?> slide" data-ride="carousel">
		    	<div class="container">
			        <div class="section-header d-flex align-items-center justify-content-between">
			        	<?php if( ! empty( $instance['title'] ) ): ?>
			        		<?php echo $args['before_title']?>
			        		<?php 

			        			if( $instance['icon'] ){
			        				printf( '<i class="%s"></i>', esc_attr( $instance['icon'] ) );
			        			}

			        			if( isset( $instance['view_more'] ) && ! empty( $instance['view_more'] ) ){
			        				printf(
			        					'<a href="%s">%s</a>',
			        					esc_url( $instance['view_more'] ),
			        					$instance['title']
			        				);
			        			}
			        			else{
			        				echo $instance['title'];
			        			}
			        		?>
			        		<?php echo $args['after_title']?>
			            <?php endif;?>

			            <?php if( $post_shows >= $wp_query->post_count && $post_shows > $columns*$post_rows ):?>
				            <ol class="carousel-indicators section-nav">
				            	<li data-target="#carousel-featured-<?php print $this->id; ?>" data-slide-to="0" class="bullet active"></li>
				                <?php 
				                	$c = 0;
				                	for ($j = 1; $j < $wp_query->post_count; $j++) {
				                		if ( $j % ($columns*$post_rows) == 0 && $j < $post_shows ){
					                    	$c++;
					                    	print '<li data-target="#carousel-featured-'.$this->id.'" data-slide-to="'.$c.'" class="bullet"></li> '; 
					                    }	
				                	}
				                ?>
				            </ol>
			            <?php endif;?>
			        </div>
		    	</div>
		        <div class="featured-wrapper">
		            <div class="container">
	                     <div class="carousel-inner">
	                       	<?php
	                       	$i=0;
	                       	$wp_query = new WP_Query( $posts_query );
	                       	if( $wp_query->have_posts() ) : 
		                       	while ( $wp_query->have_posts() ) : $wp_query->the_post();
		                       	$i++;
		                       	?>
		                       	<?php if( $i == 1 ):?>
		                       		<div class="carousel-item item active <?php print $i;?>"><div class="row row-5">
		                       	<?php endif;?>	
		                                <div id="video-featured-<?php the_ID()?>" class="col-xl-<?php print $class_columns;?> col-lg-<?php print $class_columns;?> col-md-<?php echo esc_attr( $tablet_columns );?> col-<?php echo esc_attr( $mobile_columns );?> <?php print $this->id; ?>-<?php print get_the_ID();?>">
		                                	<article <?php post_class()?>>
		                                		<div class="item-img">
				                                <?php 
				                                	if(has_post_thumbnail()){
				                                		printf(
				                                			'<a href="%s" title="%s">%s</a>',
				                                			esc_url( get_permalink() ),
				                                			esc_attr( get_the_title() ),
				                                			get_the_post_thumbnail( get_the_ID(), 'video-featured', array(
															'class'=>'img-responsive'
				                                		) )
				                                		);
				                                	}
				                                ?>
				                                </div>                             
			                                    <div class="feat-item">
			                                        <div class="feat-info post post-info-<?php print get_the_ID();?>">
			                                        	<div class="post-header">
				                                            <?php the_title( '<h3 class="post-title"><a href="'.esc_url( get_permalink() ).'">', '</a></h3>' );?>
				                                            <span class="meta post-meta">
				                                            	<i class="far fa-clock"></i> <?php print get_the_date();?>
				                                            </span>
			                                            </div>
			                                        </div>
													
			                                    </div>
		                                	</article>
		                                </div> 
			                    <?php
			                    if ( $i % ($columns*$post_rows) == 0 && $i < $post_shows ){
			                    	?></div></div><div class="carousel-item item"><div class="row row-5"><?php 
			                    }
		                       	endwhile;
		                      ?></div></div>
		                  <?php endif;?> 
	                        </div>
	                  </div>
		        </div>
			</div><!-- /#carousel-featured -->
		<?php 
		wp_reset_postdata();wp_reset_query();

		$widget_content = ob_get_clean();		

		echo $args['before_widget'] . $widget_content . $args['after_widget'];
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
			'title' => __('Featured Posts', 'videotube-core'),
			'icon'			=>	'fa fa-pencil-alt',
			'post_shows'	=>	9,
			'columns'	=>	3,
			'tablet_columns'	=>	3,
			'mobile_columns'	=>	1,
			'today'		=>	'',
			'thisweek'	=>	'',
			'view_more'	=>	''
		);
		$instance['post_category'] = isset( $instance['post_category'] ) ? $instance['post_category'] : null;
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'videotube-core'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo ( isset( $instance['title'] ) ? $instance['title']: null ); ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'icon' ); ?>"><?php _e('Icon:', 'videotube-core'); ?></label>
			<input id="<?php echo $this->get_field_id( 'icon' ); ?>" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="<?php echo ( isset( $instance['icon'] ) ? $instance['icon']: null ); ?>" style="width:100%;" />
		</p>	

		<p>  
		    <label for="<?php echo $this->get_field_id( 'post_category' ); ?>"><?php _e('Post Category:', 'videotube-core'); ?></label>
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
							'class'              => 'regular-text mars-dropdown',
			    		)
		    		);
		    	?>
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'post_tag' ); ?>"><?php _e('Post Tag:', 'videotube-core'); ?></label>
		    <input placeholder="<?php _e('Eg: tag1,tag2,tag3','videotube-core');?>" id="<?php echo $this->get_field_id( 'post_tag' ); ?>" name="<?php echo $this->get_field_name( 'post_tag' ); ?>" value="<?php echo ( isset( $instance['post_tag'] ) ? $instance['post_tag'] : null ); ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e('Date (Show posts associated with a certain time, (yy-mm-dd)):', 'videotube-core'); ?></label>
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
		    <label for="<?php echo $this->get_field_id( 'ids' ); ?>"><?php _e('Post IDs:', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'ids' ); ?>" name="<?php echo $this->get_field_name( 'ids' ); ?>" value="<?php echo ( isset( $instance['ids'] ) ) ? $instance['ids'] : null; ?>" style="width:100%;" />
		</p>										 
		<p>  
		    <label for="<?php echo $this->get_field_id( 'post_shows' ); ?>"><?php _e('Shows:', 'videotube-core'); ?></label>
		    <input type="number" id="<?php echo $this->get_field_id( 'post_shows' ); ?>" name="<?php echo $this->get_field_name( 'post_shows' ); ?>" value="<?php echo (isset( $instance['post_shows'] )) ? (int)$instance['post_shows'] : 16; ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'post_sticky' ); ?>"><?php _e('Show Sticky Posts:', 'videotube-core'); ?></label>
		    <input type="checkbox" id="<?php echo $this->get_field_id( 'post_sticky' ); ?>" name="<?php echo $this->get_field_name( 'post_sticky' ); ?>" <?php  print isset( $instance['post_sticky'] ) && $instance['post_sticky'] =='on' ? 'checked' : null;?> />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php _e('Desktop Columns:', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>" value="<?php echo (isset( $instance['columns'] )) ? (int)$instance['columns'] : 3; ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'tablet_columns' ); ?>"><?php _e('Tablet Columns:', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'tablet_columns' ); ?>" name="<?php echo $this->get_field_name( 'tablet_columns' ); ?>" value="<?php echo (isset( $instance['tablet_columns'] )) ? (int)$instance['tablet_columns'] : 2; ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'mobile_columns' ); ?>"><?php _e('Mobile Columns:', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'mobile_columns' ); ?>" name="<?php echo $this->get_field_name( 'mobile_columns' ); ?>" value="<?php echo (isset( $instance['mobile_columns'] )) ? (int)$instance['mobile_columns'] : 1; ?>" style="width:100%;" />
		</p>	
		<p>  
		    <label for="<?php echo $this->get_field_id( 'rows' ); ?>"><?php _e('Rows:', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'rows' ); ?>" name="<?php echo $this->get_field_name( 'rows' ); ?>" value="<?php echo (isset( $instance['rows'] )) ? (int)$instance['rows'] : 1; ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'auto' ); ?>"><?php _e('Auto Carousel:', 'videotube-core'); ?></label>
		    <input type="checkbox" id="<?php echo $this->get_field_id( 'auto' ); ?>" name="<?php echo $this->get_field_name( 'auto' ); ?>" <?php  print isset( $instance['auto'] ) && $instance['auto'] =='on' ? 'checked' : null;?> />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'view_more' ); ?>"><?php _e('View more link', 'videotube-core'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'view_more' ); ?>" name="<?php echo $this->get_field_name( 'view_more' ); ?>" value="<?php echo ( isset( $instance['view_more'] ) ? $instance['view_more'] : null ); ?>" style="width:100%;" />
		</p>		
	<?php		
	}
	function widget_post_order(){
		return array(
			'ASC'	=>	__('ASC','videotube-core'),
			'DESC'	=>	__('DESC','videotube-core')
		);
	}		
}