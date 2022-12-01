<?php if( !defined('ABSPATH') ) exit;?>
<?php 
	the_post_thumbnail( 'blog-large-thumb', array(
		'class'	=>	'img-responsive'
	) );
?>
<div class="post-header mb-3">
	<?php 
	if( is_page() ){
		the_title( '<h1 class="entry-title post-title page-title">', '</h1>' );
	}
	else{
		the_title( '<h2 class="entry-title post-title page-title"><a href="'.esc_url( get_permalink() ).'">', '</a></h2>' );
	}
	?>
</div>
<div class="post-entry">
	<?php the_content();?>
	<div class="clearfix"></div>
	<?php 
		$defaults = array(
			'before' => '<ul class="pagination">',
			'after' => '</ul>',
			'before_link' => '<li>',
			'after_link' => '</li>',
			'current_before' => '<li class="active">',
			'current_after' => '</li>',
			'previouspagelink' => '&laquo;',
			'nextpagelink' => '&raquo;'
		);  
		bootstrap_link_pages( $defaults );
	?>
</div>