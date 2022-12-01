<?php if( !defined('ABSPATH') ) exit;?>
<?php
/**
 * Template Name: Blog Page
 */
get_header();?>
<main id="site-content">
	<div class="container">
		<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
		} ?>	
		<div class="row">
			<div class="col-md-8 col-sm-12 main-content">
				<?php 
					$paged = get_query_var('paged') ? get_query_var('paged') : 1;
					$wp_query = new WP_Query(array('post_type'=>'post', 'paged'=>$paged));
					if( $wp_query->have_posts() ) : while ( $wp_query->have_posts() ) : $wp_query->the_post();
							get_template_part('loop','post');
						endwhile;
						?>
		                <ul class="pager">
		                	<?php posts_nav_link(null,'<li class="previous">'.__('&larr; Older','videotube').'</a></li>','<li class="next">'.__('Newer &rarr;','videotube').'</a></li>'); ?>
		                </ul>						
						<?php 
					else:
						print '<div class="alert alert-info">'. __('Oops...nothing.','videotube') .'</div>';
					endif;?>
			</div>
			<?php get_sidebar();?>
		</div><!-- /.row -->
	</div><!-- /.container -->
</main>	
<?php get_footer();?>