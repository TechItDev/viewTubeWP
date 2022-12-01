<?php if( !defined('ABSPATH') ) exit;?>
<?php get_header(); ?>
<main id="site-content">
	<div class="container">
		<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
		} ?>	
		<div class="row">
			<div id="primary" class="content-area col-lg-8 col-md-8 col-sm-12">
				<?php if( have_posts() ):the_post();?>
				<div id="post-<?php the_ID(); ?>" <?php post_class( 'single-entry' ); ?>>
                	<?php 
                		if( has_post_thumbnail() ){
                			the_post_thumbnail( apply_filters( 'get_the_post_thumbnail/size' , 'blog-large-thumb'), array('class'=>'img-responsive') );
                		}
                	?>

                    <div class="post-header my-3">
                    	<?php the_title( '<h1 class="entry-title post-title h1">', '</h1>' );?>
                        <?php do_action( 'videotube_blog_metas' );?>
                    </div>
                    
                    <div class="post-entry">
                    	<?php the_content();?>
                    	<div class="clearfix"></div>

						<?php
						if( shortcode_exists( 'wp_easy_review' ) ){
							printf(
								'<div class="mt-5">%s</div><div class="clearfix"></div>',
								do_shortcode( '[wp_easy_review]' )
							);
						}
						?>         	
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
                    </div>

                    <div class="post-info">
                    	<span class="meta"><?php print the_terms( $post->ID, 'category', '<span class="meta-info">'.__('Category','videotube').'</span> ', ' ' ); ?></span>
                        <?php the_tags('<span class="meta"><span class="meta-info">'.__('Tag','videotube').'</span> ',' ','</span>');?>
                    </div>

                    <div class="post-share social-share-buttons d-block my-5">
						<?php videotube_socials_share();?>
					</div>
                    
                    <?php 
                    
	                    if ( ( get_previous_post() || get_next_post() ) && apply_filters( 'videotube_prev_next_post' , true ) === true ):
	                    
	                    	?>
								<nav class="posts-pager my-4">
								  <ul class="pager list-unstyled">
								  	<?php 
								  	
									  	if( get_previous_post() ):
									  		$prev_post = get_previous_post();
									  		
									  		echo '<li class="previous"><a href="'. esc_url( get_permalink( $prev_post->ID ) ) .'">'. sprintf( '&larr; %s', $prev_post->post_title ) .'</a></li>';
									  	
									    endif;
									    
									    if( get_next_post() ):
									    	$next_post = get_next_post();
									    	
									    	echo '<li class="next"><a href="'. esc_url( get_permalink( $next_post->ID ) ) .'">'. sprintf( '%s &rarr;', $next_post->post_title ) .'</a></li>';
									    endif;
								  	
								  	?>
								  </ul>
								  <div class="clearfix"></div>
								</nav>                    
	                    	<?php 
	                    
	                    endif;
                    
                    ?>
                    
                </div><!-- /.post -->     
				<?php dynamic_sidebar('mars-post-single-below-content-sidebar');?>
				<?php 
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
				?>	
				<?php endif;?>
			</div>
			<?php get_sidebar();?>
		</div><!-- /.row -->
	</div><!-- /.container -->
</main>	
<?php get_footer();?>
