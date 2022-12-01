<?php if( !defined('ABSPATH') ) exit;?>
<?php 
/**
 * Template Name: Main Homepage
 */
?>
<?php get_header();?>
<main id="site-content">
	<?php dynamic_sidebar('mars-featured-videos-sidebar');?>
	<div class="container">
		<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
		} ?>	
		<div class="row">
			<div id="primary" class="content-area col-lg-8 col-md-8 col-sm-12">
				<?php dynamic_sidebar('mars-home-videos-sidebar');?>
			</div><!-- /.video-section -->
			<?php get_sidebar();?>
		</div><!-- /.row -->
	</div><!-- /.container -->
</main>	
<?php get_footer();?>