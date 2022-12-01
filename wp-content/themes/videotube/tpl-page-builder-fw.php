<?php 
/**
 * Template Name: Page Builder - Fullwidth
 */
?>
<?php get_header(); ?>
<main id="site-content">
	<div class="container-fluid">
		<?php if( have_posts() ) : the_post();?>
			<?php the_content();?>
		<?php endif;?>
	</div>
</main>	
<?php get_footer();?>