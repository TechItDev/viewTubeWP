<?php if( !defined('ABSPATH') ) exit;?>
<?php 
if( is_active_sidebar( 'mars-inner-page-right-sidebar' ) ){
	?>
	<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 sidebar">
		<?php 
			dynamic_sidebar( 'mars-inner-page-right-sidebar' );
		?>
	</div>
	<?php 
}
