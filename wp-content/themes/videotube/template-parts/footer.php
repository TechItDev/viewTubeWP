<?php if( !defined( 'ABSPATH' ) ) exit();?>
<div id="footer" class="mt-auto">
	<div class="container">
		<?php if( is_active_sidebar( 'mars-footer-sidebar' ) ):?>
			<div class="footer-sidebar">
				<div class="row">
					<?php dynamic_sidebar('mars-footer-sidebar');?>
				</div>
			</div>
		<?php endif;?>
		<div class="copyright">
			<?php do_action('videotube_copyright');?>
  		</div>
	</div>
</div><!-- /#footer -->