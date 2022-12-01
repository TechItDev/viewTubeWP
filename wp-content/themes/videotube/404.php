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
					<div class="post-header">
						<h2><?php _e('This is somewhat embarrassing, isn\'t it?','videotube')?></h2>
					</div>			
                   <div class="post-entry">
                    	<p><?php _e('It seems we can not find what you are looking for. Perhaps searching can help.','videotube')?></p>
                    	<?php 
	             		$tag_cloud = array(
						    'smallest'                  => 8, 
						    'largest'                   => 22,
						    'unit'                      => 'pt', 
						    'number'                    => 45,  
						    'format'                    => 'flat',
						    'separator'                 => ' ',
						    'orderby'                   => 'name', 
						    'order'                     => 'ASC',
						    'exclude'                   => null, 
						    'include'                   => null, 
						    'link'                      => 'view', 
							'taxonomy'  => array('post_tag','video_tag'), 
						    'echo'                      => false
						);       	
                    	print wp_tag_cloud($tag_cloud);
                    	?>
                    </div>				
				 </div><!-- /.post -->
			</div>
			<?php get_sidebar();?>
		</div><!-- /.row -->
	</div><!-- /.container -->
</main>	
<?php get_footer();?>
