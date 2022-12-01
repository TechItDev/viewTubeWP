<?php if( !defined('ABSPATH') ) exit;?>
<?php 
/**
 * Template Name: Page Builder
 */
?>
<?php get_header(); ?>
<main id="site-content">
	<div class="container">
		<?php if( have_posts() ) : the_post();?>
			<?php the_content();?>
		<?php endif;?>
	</div>
</main>	
<?php get_footer();?>