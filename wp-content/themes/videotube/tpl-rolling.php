<?php 
/**
 * Template Name: Scrolling Page
 */
if( !defined('ABSPATH') ) exit;

get_header();?>
<main id="site-content">
	<?php dynamic_sidebar('mars-featured-videos-sidebar');?>
	<div class="container">
		<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
		} ?>	
          <div class="row">
               <div id="primary" class="content-area col-lg-8 col-md-8 col-sm-12">
                    <div class="video-section scroll-list mb-4">
                         <?php 
                         $args = array(
                              'post_type'    =>   get_post_meta( get_the_ID(), 'videotube_post_type', true )
                         );

                         if( is_front_page() ){
                              $args['paged'] = get_query_var( 'page' ) ? absint(get_query_var( 'page', '1' )) : 1;
                         }
                         else{
                              $args['paged'] = get_query_var( 'paged' ) ? absint(get_query_var( 'paged', '1' )) : 1;    
                         }

                         echo videotube_get_scroll_posts( $args );?>
                    </div>
               </div>
               <?php get_sidebar();?>
          </div><!-- /.row -->
	</div><!-- /.container -->
</main>     
<?php get_footer();?>