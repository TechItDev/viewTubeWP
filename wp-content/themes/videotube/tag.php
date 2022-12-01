<?php if( !defined('ABSPATH') ) exit;?>
<?php get_header();?>
<main id="site-content">
	<div class="container">
		<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
		} ?>	
		<div class="row">
			<div id="primary" class="content-area col-lg-8 col-md-8 col-sm-12">
            	<div class="section-header">
            		<?php the_archive_title( '<h1 class="page-title">', '</h1>' )?>
                </div>		
				<?php if( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part('loop','post');?>
				<?php endwhile;?>
                <ul class="pager">
                	<?php posts_nav_link(' ','<li class="previous">'.__('&larr; Older','videotube').'</a></li>',' <li class="next">'.__('Newer &rarr;','videotube').'</a></li>'); ?>
                </ul>
				<?php else:?>
					<h3><?php _e('Not found.','videotube');?></h3>
				<?php endif;?>
			</div>
			<?php get_sidebar();?>
		</div><!-- /.row -->
	</div><!-- /.container -->		
</main>
<?php get_footer();?>