<?php
if( !defined('ABSPATH') ) exit;
$item_classes = array( 'item' );
$item_classes[] = 'col-xl-' . videotube_get_columns( 'desktop' );
$item_classes[] = 'col-lg-' . videotube_get_columns( 'desktop' );
$item_classes[] = 'col-md-' . videotube_get_columns( 'desktop' );
$item_classes[] = 'col-sm-' . videotube_get_columns( 'tablet' );
$item_classes[] = 'col-' . videotube_get_columns( 'mobile' );

$thumbnail_size = videotube_convert_columns_to_thumbnail_size();
?>

<div class="<?php echo esc_attr( join(" ", $item_classes ) ); ?>">
	<article <?php post_class()?>>
		<?php if( has_post_thumbnail() ):?>
			<div class="item-img">
				<a href="<?php the_permalink();?>">
					<?php the_post_thumbnail( $thumbnail_size, array('class'=>'img-responsive') );?>
				</a>
				<?php if( get_post_type() == 'video' ):?>
					<a href="<?php echo get_permalink(get_the_ID()); ?>"><div class="img-hover"></div></a>
				<?php endif;?>
			</div>
		<?php endif;?>
		<div class="post-header">
			<?php the_title( '<h3 class="post-title"><a href="'.esc_url( get_permalink() ).'">', '</a></h3>' );?>
			<?php do_action( 'videotube_video_meta' );?>
		</div>
	</article>
</div>