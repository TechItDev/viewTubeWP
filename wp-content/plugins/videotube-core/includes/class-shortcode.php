<?php
if( !defined('ABSPATH') ) exit;
class VideoTube_Core_ShortCode {

	function __construct() {
		add_action( 'init', array( $this,'add_shortcode') );

		add_filter( 'single_template', array( $this , 'load_playlist_template' ), 100, 1 );
	}

	function add_shortcode(){
		add_shortcode('videotube', array($this,'videotube'));

		add_shortcode('videotube_upload', array($this,'videotube_upload'));

		add_shortcode( 'videotube_login', array( $this , 'videotube_login' ) );

		add_shortcode( 'videotube_liked', array( $this , 'videotube_liked' ) );		
	}


	/**
	 * Display the video, filted by the condition
	 * @param array $attr
	 * @param string $content
	 */
	function videotube( $attr, $content ) {
		ob_start();
		wp_reset_postdata();wp_reset_query();
		extract(shortcode_atts(array(
			'title'					=>	'',
			'cat'					=>	'', // video category
			'post_category'			=>	'', // regular post category
			'tag'					=>	'', // video tag.
			'post_tags'				=>	'', // regular post tag.
			'date'					=>	'',
			'today'					=>	'',
			'thisweek'				=>	'',
			'orderby'				=>	'ID',
			'order'					=>	'DESC',
			'show'					=>	get_option('posts_per_page'),
			'ids'					=>	'',
			'id'					=>	'pagebuilder' . rand(1000, 9999),
			'author'				=>	'',
			'current_author'		=>	'',
			'current_logged_in'		=>	'',
			'rows'					=>	1,
			'columns'				=>	3,
			'tablet_columns'		=>	2,
			'mobile_columns'		=>	1,
			'navigation'			=>	'off',
			'sort'					=>	'off',
			'hide_empty_thumbnail'	=>	'',
			'thumbnail_size'		=>	'video-featured',
			'type'					=>	'main',
			'carousel'				=>	'off',
			'autoplay'				=>	'off',
			'el_class'				=>	'',
			'post_type'				=>	'video',
			'icon'					=>	'',
			'excerpt'				=>	'off'
		), $attr));

	
		if( is_front_page() ){
			$paged = get_query_var( 'page' ) ? intval( get_query_var( 'page' ) ) : 1;
		}
		else{
			$paged = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
		}
		
		$title = isset( $attr['title'] ) ? trim( $attr['title'] ) : null;
		$cat = !empty( $attr['cat'] ) ? explode(',', $attr['cat'] )  : null;
		$post_category = !empty( $attr['post_category'] ) ? explode(',', $attr['post_category'] )  : null;
		$tags = !empty( $attr['tag'] ) ? explode(',', $attr['tag'] )  : null;
		$post_tags = !empty( $attr['post_tags'] ) ? explode(',', $attr['post_tags'] )  : null;
		$ids = !empty( $attr['ids'] ) ? explode(',', $attr['ids'] )  : null;
		$author__in = !empty( $attr['author'] ) ? explode(',', $attr['author'] )  : null;
		
		$class_columns = ceil( 12/$columns );
		
		$tablet_columns= ceil( 12/$tablet_columns);
		
		$mobile_columns = ceil( 12/$mobile_columns );
		
		$post_query = array(
			'post_type'				=> $post_type,
			'showposts'				=>	$show,
			'post_status'			=>	'publish',
			'order'					=>	$order,
			'no_found_rows'			=>	true,
			'ignore_sticky_posts'	=>	true,
			'meta_query'			=>	array()
		);
		if( $type == 'main' ){
			$post_query['paged']	=	$paged;
			$post_query['no_found_rows'] = false;
		}
		
		if( $post_type == 'video' ){
			// check the video category.
			if( !empty( $cat ) && is_array( $cat ) ){
				$post_query['tax_query'] = array(
					array(
						'taxonomy' => 'categories',
						'field' => 'id',
						'terms' => $cat,
						'operator' => 'IN'	
					)
				);					
			}
			// check the video tag.
			if( ! empty( $tags ) && is_array( $tags ) ){

				$parsed_tags = array();

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

				$post_query['tax_query'] = array(
					array(
						'taxonomy'	=> 'video_tag',
						'field' 	=> 'slug',
						'terms' 	=>	$parsed_tags
					)
				);
			}
		}
		
		if( $post_type == 'post' ){
			// check the regular category
			if( ! empty( $post_category ) ){
				$post_query['category__in'] = $post_category;
			}
			if( ! empty( $post_tags ) ){

				$parsed_tags = array();

				for ( $i=0;  $i < count( $post_tags );  $i++) { 
					if( absint( $post_tags[$i] ) > 0 ){
						$term = get_term_by( 'id', $post_tags[$i], 'post_tag' );

						if( $term ){
							$parsed_tags[] = $term->slug;	
						}
					}
					else{
						$parsed_tags[] = $post_tags[$i];
					}
				}	

				$post_query['tag_slug__in'] = $parsed_tags;
			}
		}
					
		if( ! empty( $author__in ) ){
			$post_query['author__in'] = $author__in;
		}


		if( $current_author == 'on' ){
			if( is_singular() ){
				$post_query['author'] = get_the_author_meta( 'ID' );
			}

			if( is_author() ){
				$post_query['author'] = get_queried_object_id();	
			}
		}

		if( $current_logged_in == 'on' ){
			$post_query['author'] = get_current_user_id();
		}

		if( $hide_empty_thumbnail == 'on' ){
			$post_query['meta_query'][] = array(
				'key'		=>	'_thumbnail_id',
				'compare'	=>	'EXISTS'
			);
		}

		if( $post_type == 'video' && $orderby == 'views' ){
			$post_query['meta_key'] = 'count_viewed';
			$post_query['orderby']	=	'meta_value_num';
		}
		elseif ( $post_type == 'video' && $orderby == 'likes' ){
			$post_query['meta_key'] = 'like_key';
			$post_query['orderby']	=	'meta_value_num';				
		}			
		else{
			$post_query['orderby'] = $orderby;
		}
					
		### Custom Video ID
		if( $ids && is_array( $ids ) ){
			unset( $post_query['tax_query'] );
			unset( $post_query['author__in'] );
			$post_query['post__in']	=	$ids;
		}
		
		if( !empty( $date ) ){
			$dateime = explode("-", $date);
			$post_query['date_query'] = array(
				array(
					'year'  => isset( $dateime[0] ) ? $dateime[0] : null,
					'month' => isset( $dateime[1] ) ? $dateime[1] : null,
					'day'   => isset( $dateime[2] ) ? $dateime[2] : null,
				)
			);
		}
		
		if( !empty( $today ) ){
			$is_today = getdate();
			$post_query['date_query'][]	= array(
				'year'  => $is_today['year'],
				'month' => $is_today['mon'],
				'day'   => $is_today['mday']
			);
		}
		if( !empty( $thisweek ) ){
			$post_query['date_query'][]	= 	array(
				'year' => date( 'Y' ),
				'week' => date( 'W' )
			);
		}
		
		$post_query	=	apply_filters( 'videotube_sc_videotube_args' , $post_query, $id );

		$hover_image = '';
		$wpquery = new WP_Query( $post_query );
		if( $wpquery->have_posts() ):
			$carousel_setup = array(
				'interval' => $autoplay == 'on' ?  5000 : false
			);								
			if( $type == 'widget' && $carousel == 'on' ):
				?>
	          		<div data-setup="<?php echo esc_attr( json_encode( $carousel_setup ) )?>" id="<?php echo $id;?>" class="carousel slide video-section page-builder<?php echo $id;?> <?php echo $el_class;?>" data-ride="carousel">
	                    <header class="section-header">
	                    	<?php if( ! empty( $title ) ):?>
	                        	<h4 class="widget-title">
	                        		<?php if( ! empty( $icon ) && $icon != 'none' ):?>
	                        			<i class="<?php echo $icon;?>"></i>
	                        		<?php endif;?>
	                        		<?php echo $title;?>
	                        	</h4>
	                        <?php endif;?>
				            <?php if( $show > $columns*$rows ):?>
					            <ul class="carousel-indicators section-nav">
					            	<li data-target="#<?php echo $id;?>" data-slide-to="0" class="bullet active"></li>
					                <?php 
					                	$c = 0;
					                	for ($j = 1; $j < $wpquery->post_count; $j++) {
		                					if ( $j % ($columns*$rows) == 0 && $j < $show ){
						                    	$c++;
						                    	echo '<li data-target="#'.$id.'" data-slide-to="'.$c.'" class="bullet"></li> '; 
						                    }
					                	}
					                ?>				          
					            </ul>
				            <?php endif;?>
	                    </header>

	                    <div class="clearfix"></div>
	                    
                     <div class="carousel-inner">
                       	<?php
                       	if( $wpquery->have_posts() ) : 
                       		$i =0;
	                       	while ( $wpquery->have_posts() ) : $wpquery->the_post();
	                       	$i++;
	                       	?>
	                       	<?php if( $i == 1 ):?>
	                       		<div class="carousel-item item active"><div class="row row-5">
	                       	<?php endif;?>	
	                       		<div id="video-<?php the_ID();?>" class="col-xl-<?php echo $class_columns;?> col-lg-<?php echo $class_columns;?> col-md-<?php echo esc_attr( $tablet_columns );?> col-<?php echo esc_attr( $mobile_columns );?> item responsive-height post video-<?php echo get_the_ID();?>">
	                       			<article <?php post_class();?>>
		                                <div class="item-img">
		                                	<a href="<?php the_permalink();?>">
												<?php the_post_thumbnail( $thumbnail_size, array('class'=>'img-responsive') );?>
		                                	</a>
											<a href="<?php echo get_permalink(get_the_ID()); ?>"><?php if( $post_type == 'video' ):?><div class="img-hover"></div><?php endif;?></a>
										</div>

										<div class="post-header">
											<?php the_title( '<h3 class="post-title h2"><a href="'.esc_url( get_permalink() ).'">', '</a></h3>' );?>

											<?php if( get_post_type() == 'video' ):?>
												<?php do_action( 'videotube_video_meta' );?>
											<?php else:?>
												<?php printf(
													'<span class="post-meta"><i class="far fa-clock"></i> %s</span>',
													get_the_date( '', get_the_ID())
												);?>
											<?php endif;?>
											
											<?php if( isset( $excerpt ) && $excerpt == 'on' ):?>
						                        <div class="post-excerpt my-1">
						                        	<?php echo wp_trim_words( get_the_excerpt( get_the_ID() ), 20, null )?> 
						                        </div>
											<?php endif;?>
										</div>
									</article>
                                 </div> 
		                    <?php
		                    if ( $i % ($columns * $rows) == 0 && $i < $show ){
		                    	?></div></div><div class="carousel-item item"><div class="row row-5"><?php 
		                    }
	                       	endwhile;
	                      ?></div></div><?php 
                       	endif;
                       	?> 
                        </div>
	                 </div>
				<?php 
			else:
				if( !empty( $title ) ):
					?>
						<header class="section-header">
                        	<h4 class="widget-title">
                        		<?php if( ! empty( $icon ) && $icon != 'none' ):?>
                        			<i class="<?php print $icon;?>"></i>
                        		<?php endif;?>
                        		<?php print $title;?>
                        	</h4>
						</header>
						<div class="clearfix"></div>
					<?php 
				endif;		
				// default
				?>
				<div class="video-section">
					<div id="<?php print esc_attr( $id );?>" class="row row-5 columns-<?php print $columns;?> <?php print $el_class;?>"> 
						<?php while ( $wpquery->have_posts() ): $wpquery->the_post(); ?>
							<div class="col-xl-<?php print $class_columns;?> col-lg-<?php print $class_columns;?> col-md-<?php echo esc_attr( $tablet_columns );?> col-<?php echo esc_attr( $mobile_columns );?> item responsive-height post">
								<article <?php post_class();?>>
									<div class="item-img">
	                                	<a href="<?php the_permalink();?>">
											<?php the_post_thumbnail( $thumbnail_size, array('class'=>'img-responsive') );?>
	                                	</a>									
										<?php 
										$hover_image = ( $post_type == 'video' ) ? '<div class="img-hover"></div>' : null;
										?>
										<a href="<?php print get_permalink( get_the_ID() );?>"><?php print $hover_image;?></a>
									</div>
									<div class="post-header">
										<?php the_title( '<h3 class="post-title h2"><a href="'.esc_url( get_permalink() ).'">', '</a></h3>' );?>

										<?php if( get_post_type() == 'video' ):?>
											<?php do_action( 'videotube_video_meta' );?>
										<?php else:?>
											<?php printf(
												'<span class="post-meta"><i class="far fa-clock"></i> %s</span>',
												get_the_date( '', get_the_ID())
											);?>
										<?php endif;?>
										
										<?php if( isset( $excerpt ) && $excerpt == 'on' ):?>
					                        <div class="post-excerpt my-1">
					                        	<?php echo wp_trim_words( get_the_excerpt( get_the_ID() ), 20, null )?> 
					                        </div>
										<?php endif;?>
									</div>
								</article>
							</div>
						<?php endwhile;?>
					</div>
				</div>
			<?php endif;?>
			<?php 
			// display the navigation
			if( $type == 'main' ):
				do_action( 'videotube_pagination', $wpquery );
			endif;
		else:
			?><div class="alert alert-info"><?php _e('No posts were found.','mars')?></div><?php 
		endif;
		wp_reset_postdata();wp_reset_query();
		
		return sprintf(
			'<div class="widget widget-shortcode widget-builder"><div class="widget-content">%s</div></div>',
			ob_get_clean()
		);
	}

	function videotube_upload( $attr, $content = null){
		global $videotube;
		global $post;	
		$html = null;
		extract(shortcode_atts(array(
			'id'			=>	'',
			'vcategory'		=>	'on',
			'vtag'			=>	'on',
			'cat_exclude'	=>	'',
			'cat_include'	=>	'',
			'cat_orderby'	=>	'name',
			'cat_order'		=>	'DESC'
		), $attr));
		$video_type = isset( $videotube['video-type'] ) ? $videotube['video-type'] : null;
		if( !is_array( $video_type ) ){
			$video_type = (array)$video_type;
		}
		$submit_roles = isset( $videotube['submit_roles'] ) ? (array)$videotube['submit_roles'] : array( 'author' );
		if( is_array( $submit_roles ) && count( $submit_roles ) == 1 ){
			$submit_roles = (array)$submit_roles;
		}	
		//print_r($submit_roles);
		### 0 is not allow guest, 1 is only register.
		$submit_permission = isset( $videotube['submit_permission'] ) ? $videotube['submit_permission'] : 0;
		$user_id = get_current_user_id();
		$current_user_role = videotube_get_user_role( $user_id );
		### Check if Admin does not allow Visitor submit the video.
		if( $submit_permission == 0 && !$user_id ){
			
			$html .= $this->videotube_login( array() );
		}
		elseif( $submit_permission == 0 && ! array_intersect( $current_user_role, $submit_roles)){
			$html .= '
				<div class="alert alert-warning">'.__('You have no permission to access this feature.','mars').'</div>';		
		}
		else{
			
			$edit = false;
			$post_data = array(
				'post_title'		=>	'',
				'post_content'		=>	'',
				'post_categories'	=>	array(),
				'post_tags'			=>	''
			);
			
			if( ! empty( $id ) && get_post_type( $id ) == 'video' ){
				$post_data = array_merge( $post_data, get_post( $id, ARRAY_A ) );
				
				if( has_term( null, 'video_tag', $id ) ){
					$tags = get_the_terms( $id, 'video_tag' );
					$post_data['post_tags'] = join(",", wp_list_pluck( $tags , 'name') );
				}
				
				if( has_term( null, 'categories', $id ) ){
					$categories= get_the_terms( $id, 'categories' );
					$post_data['post_categories'] = wp_list_pluck( $categories, 'term_id');
				}
				
				$edit = true;
			}
			
			$categories_html = null;
			$category_array = array(
				'hide_empty'=>0,
				'order'	=>	$cat_order,
				'orderby'	=>	$cat_orderby,
			);
			if( ! empty( $cat_exclude ) ){
				$cat_exclude = explode(",", $cat_exclude);
				if( is_array( $cat_exclude ) ){
					$category_array['exclude']	= $cat_exclude;
				}
			}
			if( ! empty( $cat_include ) ){
				$cat_include = explode(",", $cat_include);
				if( is_array( $cat_include ) ){
					$category_array['include']	= $cat_include;
				}
			}		
			
			$categories = get_terms('categories', $category_array);
			
			 if ( ! empty( $categories ) && ! is_wp_error( $categories ) ){
				foreach ( $categories as $category ){
				 	$categories_html .= sprintf(
				 		'<div class="custom-control custom-checkbox"><input class="custom-control-input" id="category-%1$s" name="video_category[]" value="%1$s" type="checkbox" %3$s><label class="custom-control-label" for="category-%1$s">%2$s</label></div>',
				 		$category->term_id,
				 		$category->name,
				 		$id && has_term( $category->term_id, 'categories', $id ) ? 'checked' : ''
				 	);
				}
			 }
			 
			$html .= '<form role="form" action="" method="post" id="mars-submit-video-form" enctype="multipart/form-data">';
				if( isset( $_GET['resp'] ) && $_GET['resp'] == 'success' ){
					$html .= '<div class="alert alert-success" role="alert">'.esc_html__( 'Updated!', 'mars' ).'</div>';
				}
				 $html .= '<div class="form-group post_title">
				    <label for="post_title">'.__('Title','mars').'</label>
				    <span class="badge badge-danger">'.__('Required','mars').'</span>
				    <input type="text" class="form-control form-control-sm" name="post_title" id="post_title" value="'.esc_attr( $post_data['post_title'] ).'">
				    <span class="help-block"></span>
				  </div>
				  <div class="form-group post_content">
				    <label for="post_content">'.__('Description','mars').'</label>';
					if( $videotube['submit_editor'] == 1 ){
						$html .= videotube_get_editor( $post_data['post_content'], 'post_content', 'post_content');	
					}
					else{
						$html .= '<textarea name="post_content" id="post_content" class="form-control form-control-sm" rows="3">'.esc_textarea( $post_data['post_content'] ).'</textarea>';
					}
				  $html .= '<span class="help-block"></span>';
				  
				  $html .= '</div>';
				  
				  if( ! $edit ){
					  $html .= '<div class="form-group video-types">';

					  	$html .= '<div class="types-group">';

					  		$html .= '<ul id="tab-video-types" class="nav nav-tabs" role="tablist">';

							  	if( in_array( 'videolink', $video_type ) ){
							  		$html .= sprintf(
							  			'<li class="nav-item" role="presentation"><a class="nav-link" data-href="video_link_type" href="#video-link" aria-controls="video-link" role="tab" data-toggle="tab">%s</a></li>',
							  			esc_html__( 'Embed a link', 'mars' )
							  		);
							  	}

							  	if( in_array( 'embedcode', $video_type ) ){
							  		$html .= sprintf(
							  			'<li class="nav-item" role="presentation"><a class="nav-link" data-href="embed_code_type" href="#video-embed-iframe" aria-controls="video-embed-iframe" role="tab" data-toggle="tab">%s</a></li>',
							  			esc_html__( 'Embed an iframe', 'mars' )
							  		);
							  	}

							  	if( in_array( 'videofile', $video_type ) ){
							  		$html .= sprintf(
							  			'<li class="nav-item" role="presentation"><a class="nav-link" data-href="file_type" href="#video-file" aria-controls="video-file" role="tab" data-toggle="tab">%s</a></li>',
							  			esc_html__( 'Upload a video file', 'mars' )
							  		);
							  		$html .= sprintf(
							  			'<li class="nav-item" role="presentation"><a class="nav-link" data-href="file_type" href="#record-video-file" aria-controls="record-video-file" role="tab" data-toggle="tab">%s</a></li>',
							  			esc_html__( 'Record a video', 'mars' )
							  		);					  		
							  	}

						  	$html .= '</ul>';

						  	$html .= '<div class="tab-content">';

							  	if( in_array( 'videolink', $video_type ) ){
							  		$html .= ' <div role="tabpanel" class="tab-pane" id="video-link">
							  			<input type="text" class="form-control form-control-sm" name="video_url" id="video_url">
							  		</div>';
							  	}

							  	if( in_array( 'embedcode', $video_type ) ){
							  		$html .= '<div role="tabpanel" class="tab-pane" id="video-embed-iframe">
							  			<textarea class="form-control form-control-sm" name="embed_code" id="embed_code"></textarea>
							  		</div>';
							  	}

							  	if( in_array( 'videofile', $video_type ) ){
							  		$html .= '<div role="tabpanel" class="tab-pane" id="video-file">';
									  	$html .= '<label><div class="btn-group">';
									  		$html .= '<a class="btn btn-white border btn-sm upload-file upload-video-file"><i class="fas fa-cloud-upload-alt mr-2"></i> '. __( 'Upload a video', 'mars' ) .'</a>';
									  		$html .= '<input style="display:none;" accept="video/*" type="file" type="text" class="form-control form-control-sm" name="video_file" id="video_file">';
									  	$html .= '</div><label>';
								  	$html .= '</div>';
							  		$html .= '<div role="tabpanel" class="tab-pane" id="record-video-file">';
									  	$html .= '<label><div class="btn-group">';
									  		$html .= '<a class="btn btn-white border btn-sm upload-file upload-video-file"><i class="fas fa-video mr-2"></i>'. __( 'Start recording', 'mars' ) .'</a>';
									  		$html .= '<input style="display:none;" accept="video/*" capture="camera" type="file" type="text" class="form-control form-control-sm" name="video_file" id="record_video_file">';
									  	$html .= '</div><label>';
								  	$html .= '</div>';
							  	}

							$html .= '</div>';						  	

					  	$html .= '</div>';
					  $html .= '</div>';
				  }

				  $html .= '<div class="form-group video_thumbnail">';
					  $html .= '<label><div class="btn-group">';
						  $html .= '<a class="btn btn-white border btn-sm upload-file upload-image-file"><i class="fas fa-image mr-2"></i>'. __( 'Upload a thumbnail image', 'mars' ) .'</a>';
						  $html .= '<input style="display:none;" accept="image/*" type="file" type="text" class="form-control form-control-sm" name="video_thumbnail" id="video_thumbnail">';
					  $html .= '</div></label>';
					  if( $edit && has_post_thumbnail( $id ) ){
					  	$html .= '<div class="thumbnail-image">';
					  		$html .= get_the_post_thumbnail( $id, 'thumbnail' );
					  	$html .= '</div>';
					  }
				  $html .= '</div>';

				  if( $vtag == 'on' ):

					  $html .= '<div class="form-group video-tag">
					    <label for="key">'.__('Tags','mars').'</label>
					    <input value="'.esc_attr( $post_data['post_tags'] ).'" type="text" class="form-control form-control-sm" name="video_tag" id="video_tag">
						<span class="help-block">'.
							__( 'Enter tags, separated by commas(,), e.g: travel,vblog,tutorial', 'mars' )
						.'</span>
					  </div>';
				  endif;
				  if( $vcategory == 'on' ):
				  	$html .= '<div class="form-group categories-video">
					    <label for="category">'.__('Category','mars').'</label>';
					    $html .= '<div class="category-checkboxes">'.$categories_html.'</div>';
					  $html .= '</div>';
				  endif;
				  $videolayout = isset( $videotube['videolayout'] ) ? $videotube['videolayout'] : 'yes';
				  if( $videolayout == 'yes' ){
					$html .= '
 						<div class="form-group layout"> 	
						  	<label for="layout">'.__('Layout','mars') . '</label>';
								if( function_exists( 'videotube_videolayout' ) ){
										$html .= '<select name="layout" class="form-control form-control-sm">';
										foreach ( videotube_videolayout() as $key => $value ){
											$html .= sprintf(
												'<option %s value="%s">%s</option>',
												$id ? selected( $key, get_post_meta( $id, 'layout', true ), false ) : '',
												esc_attr( $key ),
												esc_html( $value )
											);
										}
									$html .= '</select>';
								}
						  	$html .= '<span class="help-block"></span>
					  	</div>						
					';
				  }
				  
				  $html .= '<div class="form-group group-submit">';
					  if( $edit ){
					  	$html .= '<button type="submit" class="btn btn-primary"">'.__('Update','mars').'</button>';
					  	$html .= '<button id="delete-video" data-id="'.esc_attr( $id ).'" href="'.esc_url( get_permalink( $id ) ).'" class="btn btn-danger"">'.__('Delete video','mars').'</button>';
					  	$html .= '<a href="'.esc_url( get_permalink( $id ) ).'" class="btn btn-default"">'.__('Go to video','mars').'</a>';
					  }
					  else{
					  	$html .= '<button type="submit" class="btn btn-primary btn-sm px-3"">'.__('Submit','mars').'</button>';
					  }
					  $html .= '
						  <input type="hidden" name="current_page" value="'.$post->ID.'">
						  <input type="hidden" name="attachment_id">
						  <input type="hidden" name="_thumbnail_id">
						  <input type="hidden" name="post_id" value="'.esc_attr( $id ).'">
						  <input type="hidden" name="action" value="videotube_submit_video">
					  ';

					  $html .= sprintf(
					  	'<input type="hidden" name="video_type" value="%s">',
					  	$edit ? get_post_meta( $id, 'video_type', true ) : ''
					  );

				  $html .= '</div>';
				$html .= '
				</form>
			';
			
		}
		return do_shortcode( $html );
	}

	public function videotube_login( $attr, $content = '' ){
		return sprintf(
			'<div class="videotube-login-form">%s</div>',
			wp_login_form( array(
				'echo'	=>	false
			) )
		);
	}

	public function videotube_liked( $attr, $content = '' ){

		$current = is_singular( 'video' ) ? get_the_ID() : 0;

		$output = '';

		$attr = wp_parse_args( $attr, array(
			'orderby'			=>	'post__in',
			'order'				=>	'desc',
			'posts_per_page'	=>	get_option( 'posts_per_page' ),
			'thumbnail_size'	=>	'video-featured',
			'excerpt_length'	=>	20
		) );

		if( ! is_user_logged_in() ){
			return sprintf(
				'<div class="alert alert-warning">%s</div>',
				sprintf(
					esc_html__( 'Please %s to view your liked videos', 'videotube-core' ),
					sprintf(
						'<a href="%s">%s</a>',
						esc_url( wp_login_url( get_permalink() ) ),
						esc_html__( 'log in', 'videotube-core' )
					)
				)
			);
		}

		$liked = videotube_core_get_user_liked_videos();

		if( ! $liked ){
			return sprintf(
				'<div class="alert alert-info">%s</div>',
				esc_html__( 'You have not liked any videos yet.', 'videotube-core' )
			);
		}

		ob_start();

		$query_args = array(
			'post_type'			=>	'video',
			'posts_per_page'	=>	$attr['posts_per_page'],
			'post__in'			=>	$liked,
			'post_status'		=>	'publish',
			'orderby'			=>	$attr['orderby'],
			'order'				=>	$attr['order'],
			'paged'				=>	get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1
		);

		$query_posts = new WP_Query( $query_args );

		if( $query_posts->have_posts() ):

			while ( $query_posts->have_posts() ):

				$query_posts->the_post();

				add_filter( 'post_type_link', array( $this , 'add_permalink_list_param' ), 9999, 4 );

				$current_class = get_the_ID() == $current ? 'current' : '';

				?>
					<article <?php post_class( 'post ' . $current_class )?>>

						<div class="row row-5">

							<?php printf(
								'<div class="%s item list">',
								is_singular( 'video' ) ? 'col-xl-5 col-lg-5 col-md-4 col-5' : 'col-xl-5 col-lg-5 col-12'
							)?>

								<?php if( has_post_thumbnail() ):?>
									<div class="item-img">
										<a href="<?php the_permalink();?>">
											<?php the_post_thumbnail( $attr['thumbnail_size'], array('class'=>'img-responsive') );?>
										</a>
										<a href="<?php echo get_permalink(get_the_ID()); ?>"><div class="img-hover"></div></a>
									</div>
								<?php endif;?>
							</div>

							<?php printf(
								'<div class="%s item list">',
								is_singular( 'video' ) ? 'col-xl-7 col-lg-7 col-md-8 col-7' : 'col-xl-7 col-lg-7 col-12'
							)?>

								<div class="post-header">
									<?php the_title( '<h3 class="post-title mt-0 mb-2"><a href="'.esc_url( get_permalink() ).'">', '</a></h3>' );?>
									<?php do_action( 'videotube_video_meta' );?>

									<?php 
									if( absint( $attr['excerpt_length'] ) > 0 ){
										$more = sprintf(
											'<a class="read-more watch-video-link d-block" href="%s"><i class="fa fa-play-circle"></i> %s</a>',
											esc_url( get_permalink() ),
											esc_html__( 'watch video', 'videotube-core' )
										);
										printf(
											'<div class="post-excerpt my-1"><p>%s</p></div>',
											wp_trim_words( get_the_excerpt( get_the_ID() ), $attr['excerpt_length'], $more )
										);
									}
									
									?>
								</div>
							</div>

						</div>

					</article>		
				<?php

				remove_filter( 'post_type_link', array( $this , 'add_permalink_list_param' ), 9999, 4 );

			endwhile;

			do_action( 'videotube_pagination', $query_posts );

		endif;

		wp_reset_postdata();

		$output = ob_get_clean();

		if( ! empty( $output ) ){

			return sprintf(
				'<div class="widget widget-main widget-liked-videos"><div class="widget-content">%s</div></div>',
				$output
			);
		}
	}

	public function add_permalink_list_param( $post_link, $post, $leavename, $sample ){

		return add_query_arg( array(
			'list'	=>	'liked'
		), $post_link );
	}

	public function load_playlist_template( $single_template ){
		global $post;
		if( is_user_logged_in() ){

			$liked = videotube_core_get_user_liked_videos();

			if( $liked && in_array( $post->ID, $liked ) && 'video' === $post->post_type && isset( $_GET['list'] ) && $_GET['list'] == 'liked' ){
				$single_template = locate_template( 'page-templates/single-playlist.php' );
			}
		}

		return $single_template;
	}

}
new VideoTube_Core_ShortCode();