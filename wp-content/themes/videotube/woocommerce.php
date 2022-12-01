<?php if( !defined('ABSPATH') ) exit;?>
<?php get_header(); ?>
<main id="site-content">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-sm-12 main-content">
				<?php if(have_posts()):?>
				 <div <?php post_class()?>>
                    <?php woocommerce_content(); ?>
                </div><!-- /.post -->
				<?php endif;?>
			</div>
			<?php get_sidebar();?>
		</div><!-- /.row -->
	</div><!-- /.container -->
</main>
<?php get_footer();?>