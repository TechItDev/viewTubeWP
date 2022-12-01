<?php
if( !defined('ABSPATH') ) exit;
function videotube_core_main_posts_widget_register() {
	register_widget('VideoTube_Core_Widget_MainPosts');
}
add_action('widgets_init', 'videotube_core_main_posts_widget_register');

class VideoTube_Core_Widget_MainPosts extends WP_Widget{
	var $title_length = 31;
	
	function __construct(){
		$widget_ops = array( 'classname' => 'mars-mainpost-widgets', 'description' => __('[VideoTube] Main Posts', 'videotube-core') );
	
		parent::__construct( 'mars-mainpost-widgets' , __('[VideoTube] Main Posts', 'videotube-core') , $widget_ops);
	}	
	
	function widget($args, $instance){
		extract( $args );
		wp_reset_postdata();wp_reset_query();
		$instance = wp_parse_args( $instance, array(
			'title'					=>	__('Latest Posts', 'mars'),
			'icon'					=>	'fa fa-pencil-alt',
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
		$widget_column = isset( $instance['widget_column'] ) ? $instance['widget_column'] : 3;
		
		$tablet_columns = isset( $instance['tablet_columns'] ) ? (int)$instance['tablet_columns'] : 2;
		
		$tablet_columns = ceil(12/$tablet_columns);
		
		$mobile_columns = isset( $instance['mobile_columns'] ) ? (int)$instance['mobile_columns'] : 1;
		
		$mobile_columns = ceil(12/$mobile_columns);
		
		$thumbnail_size = isset( $instance['thumbnail_size'] ) ? $instance['thumbnail_size'] : 'video-category-featured';
		
		if( empty( $thumbnail_size ) ){
			$thumbnail_size  = 'video-category-featured';
		}
		
		$class_columns = ( 12%$widget_column == 0 ) ? 12/$widget_column : 4;
		$post_shows = isset( $instance['post_shows'] ) ? (int)$instance['post_shows'] : 16;  
		$post_rows = isset( $instance['rows'] ) ? (int)$instance['rows'] : 1;
		$autoplay = isset( $instance['auto'] ) ? $instance['auto'] : null;
		$i=0;

		$posts_query = array(
			'post_type'	=>	'post',
			'posts_per_page'	=>	$post_shows,
			'ignore_sticky_posts'	=>	true,
			'no_found_rows'	=>	true,
			'meta_query'	=>	array()
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
				'terms' => explode( ',', $post_category )
				)		                       		
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

		$posts_query	=	apply_filters( 'mars_main_widget_args' , $posts_query, $this->id);		
		
		$posts_query	=	apply_filters( 'videotube_main_widget_args' , $posts_query, $this->id);
		
		$wp_query = new WP_Query( $posts_query );

		if( ! $wp_query->have_posts() ){
			return;
		}

		$colum = $widget_column;

		ob_start();

		$carousel_setup = array(
			'interval' => $autoplay ? 5000 : false
		);			

		?>
			<?php if( $widget_column == 3 ):?>
          		<div data-setup="<?php echo esc_attr( json_encode( $carousel_setup ) )?>" id="carousel-latest-<?php print $this->id; ?>" class="carousel carousel-<?php print $this->id?> slide video-section" data-ride="carousel">
          	<?php elseif ( $widget_column ==2 ):?>
          		<div class="row row-5 video-section post-section">
          	<?php else:?>
          		<div data-setup="<?php echo esc_attr( json_encode( $carousel_setup ) )?>" id="carousel-latest-<?php print $this->id; ?>" class="carousel carousel-<?php print esc_attr( $this->id )?> slide video-section" <?php if($post_shows>3):?> data-ride="carousel"<?php endif;?>>
          	<?php endif;?>
          	
          		<?php if( ! empty( $instance['title'] ) ):?>
					<div class="section-header d-flex align-items-center justify-content-between">
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

                        <?php if( $widget_column != 2 ):?>
				            <?php if( $post_shows >= $wp_query->post_count && $post_shows > $colum*$post_rows):?>
					            <ol class="carousel-indicators section-nav">
					            	<li data-target="#carousel-latest-<?php print $this->id; ?>" data-slide-to="0" class="bullet active"></li>
					                <?php 
					                	$c = 0;
					                	for ($j = 1; $j < $wp_query->post_count; $j++) {
					                		if ( $j % ($colum*$post_rows) == 0 && $j < $post_shows ){
						                    	$c++;
						                    	print '<li data-target="#carousel-latest-'.$this->id.'" data-slide-to="'.$c.'" class="bullet"></li> '; 
						                    }	
					                	}
					                ?>
					            </ol>
				            <?php endif;?>
	                    <?php else:?>
	                    	<?php if( ! empty( $instance['view_more'] ) ):?>
								<div class="section-nav">
									<a href="<?php print esc_url( $instance['view_more'] );?>" class="viewmore"><?php _e('View More','mars');?> <i class="fa fa-angle-double-right"></i></a>
								</div>
							<?php endif;?>
						<?php endif;?>

                    </div>
                <?php endif;?>
                    
               	<?php if( $widget_column == 2 ):?>
               		<?php while ( $wp_query->have_posts() ) : $wp_query->the_post();?>
						<div id="post-main-<?php print $this->id; ?>-<?php the_ID();?>" class="col-xl-<?php print $class_columns;?> col-lg-<?php print $class_columns;?> col-md-<?php echo esc_attr( $tablet_columns );?> col-<?php echo esc_attr( $mobile_columns );?>">
							<article <?php post_class();?>>
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
							</article>
						</div>
					<?php endwhile;?>
											
                <?php elseif( $widget_column ==2) :?>
					<!-- 1 colum -->

                    <div class="carousel-inner">
                       	<?php while ( $wp_query->have_posts() ) : $wp_query->the_post();
	                       	$i++;
	                       	?>
	                       	<?php if( $i ==1 ):?>
	                       		<div class="carousel-item item active">
	                       	<?php endif;?>
								<?php get_template_part( 'loop', 'scroll' );?>	                       	
			                    <?php
			                    //if ( $i % 3 == 0 && $i < 18 ){
			                    if ( $i % $post_rows == 0 && $i < $post_shows ){
			                    	?></div><div class="carousel-item item"><?php 
			                    } 
		                endwhile;?></div>
				    </div>
				<!-- end 1 colum -->
					
                <?php else:?>
                     <div class="carousel-inner">
                       	<?php
                       		$i =0;
	                       	while ( $wp_query->have_posts() ) : $wp_query->the_post();
	                       	$i++;
	                       	?>
	                       	<?php if( $i == 1 ):?>
	                       		<div class="carousel-item item active"><div class="row-5 row">
	                       	<?php endif;?>	
	                       		<div id="post-main-<?php print $this->id; ?>-<?php the_ID();?>" class="col-xl-<?php print $class_columns;?> col-lg-<?php print $class_columns;?> col-md-<?php echo esc_attr( $tablet_columns );?> col-<?php echo esc_attr( $mobile_columns );?>" >
	                       			<article <?php post_class();?>>
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
                                	</article>
                                 </div> 
		                    <?php
		                    if ( $i % ($widget_column*$post_rows) == 0 && $i < $post_shows ){
		                    	?></div></div><div class="carousel-item item"><div class="row-5 row"><?php 
		                    } 	             
	                       	endwhile;
	                      ?></div></div>
                    </div>
                <?php endif;?>
                </div><!-- /#carousel-->
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
			'title' 				=> __('Latest Posts', 'mars'),
			'icon'					=>	'fa fa-pencil-alt',
			'view_more'				=>	'',
			'widget_column'			=>	3,
			'today'					=>	'',
			'thisweek'				=>	'',
			'tablet_columns'		=>	2,
			'mobile_columns'		=>	1,
			'thumbnail_size'		=>	'',
			'hide_empty_thumbnail'	=>	''
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'mars'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'icon' ); ?>"><?php _e('Icon:', 'mars'); ?></label>
			<input id="<?php echo $this->get_field_id( 'icon' ); ?>" name="<?php echo $this->get_field_name( 'icon' ); ?>" value="<?php echo $instance['icon']; ?>" style="width:100%;" />
		</p>		
		<p>
		    <label for="<?php echo $this->get_field_id( 'post_category' ); ?>"><?php _e('Category:', 'mars'); ?></label>
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
		    <label for="<?php echo $this->get_field_id( 'post_tag' ); ?>"><?php _e('Post Tag:', 'mars'); ?></label>
		    <input placeholder="<?php _e('Eg: tag1,tag2,tag3','mars');?>" id="<?php echo $this->get_field_id( 'post_tag' ); ?>" name="<?php echo $this->get_field_name( 'post_tag' ); ?>" value="<?php echo ( isset( $instance['post_tag'] ) ? $instance['post_tag'] : null ); ?>" style="width:100%;" />
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
		    <label for="<?php echo $this->get_field_id( 'post_orderby' ); ?>"><?php _e('Orderby:', 'mars'); ?></label>
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
		    <label for="<?php echo $this->get_field_id( 'post_order' ); ?>"><?php _e('Order:', 'mars'); ?></label>
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
		    <label for="<?php echo $this->get_field_id( 'widget_column' ); ?>"><?php _e('Desktop Column:', 'mars'); ?></label>
		    <select style="width:100%;" id="<?php echo $this->get_field_id( 'widget_column' ); ?>" name="<?php echo $this->get_field_name( 'widget_column' ); ?>">
		    	<?php 
		    		foreach ( $this->widget_post_column() as $key=>$value ){
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
		    		foreach ( $this->widget_post_column() as $key=>$value ){
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
		    		foreach ( $this->widget_post_column() as $key=>$value ){
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
		    <label for="<?php echo $this->get_field_id( 'post_shows' ); ?>"><?php _e('Shows:', 'mars'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'post_shows' ); ?>" name="<?php echo $this->get_field_name( 'post_shows' ); ?>" value="<?php echo (isset( $instance['post_shows'] )) ? (int)$instance['post_shows'] : 16; ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'rows' ); ?>"><?php _e('Rows (Available for 3 or 1 Column):', 'mars'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'rows' ); ?>" name="<?php echo $this->get_field_name( 'rows' ); ?>" value="<?php echo (isset( $instance['rows'] )) ? (int)$instance['rows'] : 1; ?>" style="width:100%;" />
		</p>
		<p>  
		    <label for="<?php echo $this->get_field_id( 'hide_empty_thumbnail' ); ?>"><?php _e('Hide empty thumbnail posts:', 'mars'); ?></label>
		    <input type="checkbox" id="<?php echo $this->get_field_id( 'hide_empty_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty_thumbnail' ); ?>" <?php  print isset( $instance['hide_empty_thumbnail'] ) && $instance['hide_empty_thumbnail'] =='on' ? 'checked' : null;?> />
		</p>		
		<p>  
		    <label for="<?php echo $this->get_field_id( 'auto' ); ?>"><?php _e('Auto Carousel:', 'mars'); ?></label>
		    <input type="checkbox" id="<?php echo $this->get_field_id( 'auto' ); ?>" name="<?php echo $this->get_field_name( 'auto' ); ?>" <?php  print isset( $instance['auto'] ) && $instance['auto'] =='on' ? 'checked' : null;?> />
		</p>				
		<p>  
		    <label for="<?php echo $this->get_field_id( 'view_more' ); ?>"><?php _e('View more link', 'mars'); ?></label>
		    <input id="<?php echo $this->get_field_id( 'view_more' ); ?>" name="<?php echo $this->get_field_name( 'view_more' ); ?>" value="<?php echo ( isset( $instance['view_more'] ) ? $instance['view_more'] : null ); ?>" style="width:100%;" />
		</p>
	<?php
	}
	function widget_post_column(){
		return array(
			'6'	=>	__('6 Columns','mars'),
			'4'	=>	__('4 Columns','mars'),
			'3'	=>	__('3 Columns','mars'),
			'2'	=>	__('2 Columns','mars'),
			'1'	=>	__('1 Column','mars'),
		);
	}
	function widget_video_order(){
		return array(
			'DESC'	=>	__('DESC','mars'),
			'ASC'	=>	__('ASC','mars')
		);
	}	
}