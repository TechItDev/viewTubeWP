<?php if( !defined('ABSPATH') ) exit;?>
<?php get_header(); ?>
<main id="site-content">
	<div class="container">
		<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
		} ?>	
		<div class="row">
			<div id="primary" class="content-area col-lg-8 col-md-8 col-sm-12">
				<header class="section-header d-md-flex align-items-center justify-content-between">
					<?php the_archive_title( '<h1 class="page-title">', '</h1>' )?>
                    <?php do_action('videotube_orderblock_videos');?>
                </header>				
				<?php if( have_posts() ):?>
					<div class="video-section">
						<div class="row row-5">	
				
							<?php 
								while ( have_posts() ) : the_post();
								
									get_template_part( 'loop', 'video' );
								
								endwhile;
							?>
						</div>
						<?php do_action( 'videotube_pagination', null );?>
					</div>
                <?php else:?>
                	<div class="alert alert-info"><?php _e('No posts were found.','videotube')?></div>
                <?php endif;?>
			</div>
			<?php get_sidebar();?>
		</div><!-- /.row -->
	</div><!-- /.container -->
</main>
<?php get_footer();?>