<?php if( !defined('ABSPATH') ) exit;?>
<?php get_header();?>
<main id="site-content">
	<div class="container">
		<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
		} ?>	
		<div class="row">
			<div id="primary" class="content-area col-lg-8 col-md-8 col-sm-12">
				<header class="section-header">
					<?php the_archive_title( '<h1 class="page-title">', '</h1>' )?>
                </header>		
				<?php if( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part('loop','post');?>
				<?php endwhile;?>
                <ul class="pager">
                	<?php posts_nav_link(' ','<li class="previous">'.__('&larr; Previous','videotube').'</a></li>',' <li class="next">'.__('Next &rarr;','videotube').'</a></li>'); ?>
                </ul>
				<?php else:?>
					<div class="alert alert-info"><?php _e('No posts were found.','videotube')?></div>
				<?php endif;?>
			</div>
			<?php get_sidebar();?>
		</div><!-- /.row -->
	</div><!-- /.container -->			
</main>	
<?php get_footer();?>