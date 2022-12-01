<?php if( !defined('ABSPATH') ) exit;?>
<?php get_header(); ?>
<main id="site-content">
	<div class="container">
		<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
		} ?>	
		<div class="row">
			<div id="primary" class="content-area col-lg-8 col-md-8 col-sm-12">
				 <div <?php post_class();?>>
				 	<?php 
				 		echo do_shortcode( '[videotube_upload id="'.get_the_ID().'"]' );
				 	?>
                </div><!-- /.post -->	
			</div>
			<?php get_sidebar();?>
		</div><!-- /.row -->
	</div><!-- /.container -->
</main>	
<?php get_footer();?>