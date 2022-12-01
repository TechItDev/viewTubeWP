<?php 

if( ! defined('ABSPATH') ) exit;

get_header();

the_post();

?>

<main id="site-content">

	<div class="video-wrapper">
		<div class="container">
			<div class="row">
				<div class="col-xl-8 col-lg-8 col-md-12 col-12">

					<div class="player-container">
						<div class="video-info large d-md-flex">
			                <?php 
			                /**
			                 * videotube_before_video_title action.
			                 */
			                do_action( 'videotube_before_video_title' );
			                ?>				
			                <h1><?php the_title();?></h1>
			                <?php 
			                /**
			                 * videotube_after_video_title action.
			                 */
			                do_action( 'videotube_after_video_title' );
			                ?>
			                <div class="info-meta ml-auto">
				                <?php if( videotube_get_count_viewed() > 1 ):?>
				                	<span class="views mr-3"><i class="fa fa-eye"></i><?php print apply_filters( 'postviews' , videotube_get_count_viewed() );?>
				                	</span>
				                <?php endif;?>

				                <span class="like-button">
					                <a href="#" class="likes-dislikes" data-action="like" id="video-<?php print get_the_ID();?>" data-post-id="<?php echo get_the_ID()?>">
					                	<span class="likes"><i class="fa fa-thumbs-up"></i>
					                		<label class="like-count like-count-<?php print get_the_ID();?>">
					                			<?php if(function_exists('videotube_get_like_count')) {
				                            		echo apply_filters( 'postlikes' , videotube_get_like_count(get_the_ID()) );
				                            	} ?>
					                		</label>
					                	</span>
					                </a>
				            	</span>
			            	</div>
			            </div>
			            <?php 
			            /**
			             * videotube_before_video action.
			             */
			            do_action( 'videotube_before_video' );
			            ?>
			            <div class="<?php echo esc_attr( join( ' ',videotube_get_player_wrap_classes() ) )?>">
			                <div class="<?php echo esc_attr( videotube_get_video_aspect_ratio() );?>">
			                	<div class="embed-responsive-item">
			                	<?php 
								/**
								 * videotube_media action.
								 * hooked videotube_get_media_object, 10, 1
								 */
								do_action( 'videotube_media', get_the_ID() );
								?>
								</div>
			                </div>
			        	</div>
						<?php
						/**
						 * videotube_media_pagination action.
						 * hooked videotube_get_media_pagination, 10, 1
						 */
						do_action( 'videotube_media_pagination', get_the_ID() );
						?>
			            <?php 
			            /**
			             * videotube_after_video action.
			             */
			            do_action( 'videotube_after_video' );
			            ?>

		        	</div>
		            <div id="lightoff"></div>

           		</div>

  				<div class="col-xl-4 col-lg-4 col-md-12 col-12">
  					<div id="liked-list" class="d-none">
  						<?php echo do_shortcode( '[videotube_liked posts_per_page=-1 excerpt_length=0]' );?>
  					</div>
  				</div>
        	</div>
		</div>
	</div>

	<div class="container">
		<div class="row">
			<div class="col-md-8 col-12 main-content">
				<?php 
					$defaults = array(
						'before' => '<ul class="pagination">',
						'after' => '</ul>',
						'before_link' => '<li>',
						'after_link' => '</li>',
						'current_before' => '<li class="active">',
						'current_after' => '</li>',
						'previouspagelink' => '&laquo;',
						'nextpagelink' => '&raquo;'
					);  
					bootstrap_link_pages( $defaults );
				?>				
            	<div class="row row-5 video-options">
                    <div class="col-xl-3 col-lg-3 col-md-3 col-6 mb-2 box-comment">
                        <a href="javascript:void(0)" class="option comments-scrolling">
                            <i class="fa fa-comments"></i>
                            <span class="option-text"><?php _e('Comments','videotube')?></span>
                        </a>
                    </div>
                    
                    <div class="col-xl-3 col-lg-3 col-md-3 col-6 mb-2 box-share">
                        <a href="javascript:void(0)" class="option share-button" id="off">
                            <i class="fa fa-share"></i>
                            <span class="option-text"><?php _e('Share','videotube')?></span>
                        </a>
                    </div>
                    
                    <div class="col-xl-3 col-lg-3 col-md-3 col-6 mb-2 box-like">
                        <a class="option likes-dislikes" href="#" data-action="like" id="video-<?php echo get_the_ID();?>" data-post-id="<?php echo get_the_ID();?>">
                            <i class="fa fa-thumbs-up"></i>
                            <span class="option-text like-count like-count-<?php echo get_the_ID();?>">
                        		<?php if(function_exists('videotube_get_like_count')) {
                        			echo apply_filters( 'postlikes' , videotube_get_like_count(get_the_ID()) );
                        		} ?>
                            </span>
                        </a>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-6 mb-2 box-turn-off-light">
						<!-- LIGHT SWITCH -->
						<a href="javascript:void(0)" class="option switch-button">
                            <i class="far fa-lightbulb"></i>
							<span class="option-text"><?php _e('Turn off Light','videotube')?></span>
                        </a>	
                    </div>
                </div>	

               	<div class="social-share-buttons">
					<?php videotube_socials_share();?>
				</div>

				<div class="video-details">
					<span class="date">
						<?php printf(
							__('Published on %s by %s','videotube'), 
							get_the_date(), 
							'<a class="post-author" href="'.get_author_posts_url(get_the_author_meta('ID')).'">'. get_the_author_meta( 'display_name' ) .'</a>'
						);?>
					</span>
                    <div class="post-entry">
                    	<?php 
                    		$r = array(
                    			'embedCSS'			=>	false,
                    			'collapsedHeight'	=>	40,
                    			'moreLink'			=>	'<a class="read-more-js btn btn-sm btn-block btn-white border-top" href="#">
								'.esc_html__( 'Read more', 'videotube' ).'
								<i class="fas fa-angle-down"></i>
								</a>',
                    			'lessLink'			=>	'<a class="read-less-js btn btn-sm btn-block btn-white border-top" href="#">
								'.esc_html__( 'Read less', 'videotube' ).'
								<i class="fas fa-angle-up"></i>
								</a>'
                    		);
                    		$r = apply_filters( 'read_more_js' , $r );
                    	?>
                    	<?php if( $r ):?>
						<div class="content-more-js" data-settings="<?php echo esc_attr( json_encode( $r ) );?>">
							<?php the_content();?>
						</div>
						<?php else:?>
							<?php the_content();?>
						<?php endif;?>

						<?php
						if( shortcode_exists( 'wp_easy_review' ) ){
							printf(
								'<div class="mt-5">%s</div><div class="clearfix"></div>',
								do_shortcode( '[wp_easy_review]' )
							);
						}
						?>

                    </div>
                    
                    <?php if( videotube_can_user_edit_video( get_the_ID() ) ):?>
                    	<div class="edit-post">
							<div class="btn-group">
								<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'edit-video' ), get_permalink() ) );?>" class="btn btn-primary btn-sm">
									<i class="fa fa-cog" aria-hidden="true"></i>
									<?php esc_html_e( 'Edit', 'videotube' );?>
								</a>
							</div>
                    	</div>
                    <?php endif;?>
                    
                    <span class="meta"><?php print the_terms( get_the_ID(), 'categories', '<span class="meta-info">'.__('Category','videotube').'</span> ', ' ' ); ?></span>
                    <span class="meta"><?php print the_terms( get_the_ID(), 'video_tag', '<span class="meta-info">'.__('Tag','videotube').'</span> ', ' ' ); ?></span>
                </div>
				<?php dynamic_sidebar('mars-video-single-below-sidebar');?>
				<?php 
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
				?>
			</div>
			<?php get_sidebar();?>
		</div><!-- /.row -->
	</div><!-- /.container -->	

</main>

<?php
get_footer();