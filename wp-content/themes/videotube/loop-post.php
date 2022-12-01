<?php if( ! defined('ABSPATH') ) exit;?>
<article data-id="post-<?php the_ID(); ?>" <?php post_class( 'mb-4 pb-1' ); ?>>
    <?php if( has_post_thumbnail() ):?>
    	<a href="<?php the_permalink();?>">
            <?php the_post_thumbnail( apply_filters( 'get_the_post_thumbnail/size' , 'blog-large-thumb'), array('class'=>'img-responsive') );?>
    <?php endif;?>
	</a>		
    <div class="post-header my-3">
		<?php the_title( '<h2 class="entry-title post-title h2"><a href="'.esc_url( get_permalink() ).'">', '</a></h2>' );?>
        <?php do_action( 'videotube_blog_metas' );?>
    </div>
    <div class="post-entry">
    	<?php if( !is_single() ):?>
            <?php the_excerpt();?>
            <a href="<?php esc_url( the_permalink() );?>" class="readmore"><?php _e('Read More','videotube');?></a>
            <?php else: the_content();?>
            <div class="clearfix"></div>
            <?php wp_link_pages();?>
        <?php endif;?>
    </div>
</article>