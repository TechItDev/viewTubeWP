<?php if( !defined('ABSPATH') ) exit;?>
<?php global $videotube;?>
<?php get_header();?>
<main id="site-content">
	<div class="container">
		<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
		} ?>	
		<div class="row">
			<div id="primary" class="content-area col-lg-8 col-md-8 col-sm-12">
            	<header class="section-header">
            		<?php print apply_filters('videotube_author_header',null);?>
                </header>
				<?php if( have_posts() ) : ?>
					<?php print apply_filters('videotube_author_loop_before', null);?>
						<?php while ( have_posts() ) : the_post();?>
							<?php apply_filters('videotube_author_loop_content',null);?>
						<?php endwhile;?>
					<?php print apply_filters('videotube_author_loop_after', null);?>
					<?php 
						if( $videotube['enable_channelpage'] == 1 ){
							do_action( 'videotube_pagination', null );
						}
						else{?>
			                <ul class="pager">
			                	<?php posts_nav_link(' ','<li class="previous">'.__('&larr; Older','videotube').'</a></li>',' <li class="next">'.__('Newer &rarr;','videotube').'</a></li>'); ?>
			                </ul>							
						<?php }
					?>
				<?php else:?>
					<div class="alert alert-info"><?php _e('No posts were found.','videotube')?></div>
				<?php endif;?>
			</div>
			<?php get_sidebar();?>
		</div><!-- /.row -->
	</div><!-- /.container -->
</main>	
<?php get_footer();?>