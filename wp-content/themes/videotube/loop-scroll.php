<article data-id="<?php the_ID();?>" <?php post_class( 'post pb-3 mb-3');?>>
	<div class="row row-5">
		<div class="col-xl-5 col-lg-5 col-md-6 col-12 item list">
			<?php if( get_post_type() == 'video' ):?>
			<div class="item-img">
			<?php endif;?>
	               <a href="<?php the_permalink();?>">
	                    <?php the_post_thumbnail( 'video-category-featured', array('class'=>'img-responsive') );?>
	               </a>          
	           	<?php if( get_post_type() == 'video' ):?>
	           	<a href="<?php echo get_permalink(get_the_ID()); ?>"><div class="img-hover"></div></a>
	 		</div>
	 		<?php endif;?>
		</div>
		<div class="col-xl-7 col-lg-7 col-md-6 col-12 item list">
			<div class="post-header my-md-0 my-3">
				<?php the_title( '<h3 class="post-title"><a href="'.esc_url( get_permalink() ).'">', '</a></h3>' );?>

				<?php if( get_post_type() == 'video' ):?>
					<?php do_action( 'videotube_video_meta' );?>
				<?php else:?>
					<?php do_action( 'videotube_blog_metas' );?>
				<?php endif;?>

				<div class="post-excerpt mb-2">
	            	<?php
	            	echo wp_trim_words( get_the_excerpt( get_the_ID() ), videotube_get_post_excerpt_length(), '' );
	            	?> 
	            </div>

	            <?php if( get_post_type() == 'video' ):?>
	            	<?php printf(
	            		'<a class="read-more watch-video-link" href="%s"><i class="fa fa-play-circle"></i> %s</a>',
	            		esc_url( get_permalink() ),
	            		esc_html__( 'watch video', 'videotube' )
	            	);?>
	            <?php endif;?>
			</div>
		</div>
	</div>
</article>