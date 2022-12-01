<?php if( !defined( 'ABSPATH' ) ) exit();?>
<?php global $videotube;?>
<div id="header" class="border-bottom">
	<div class="container">
		<div class="row d-flex align-items-center">
			<div class="col-xl-3 col-lg-3 col-md-3 col-4 d-flex align-items-center" id="logo">
				<a title="<?php bloginfo('description');?>" href="<?php print home_url();?>">
					<?php
						$logo_image = isset( $videotube['logo']['url'] ) ? $videotube['logo']['url'] : get_template_directory_uri() . '/img/logo.png'; 
					?>
					<img src="<?php print esc_url( $logo_image ); ?>" alt="<?php esc_attr( bloginfo('description') );?>" />
				</a>
			</div>
			<div class="col-xl-6 col-lg-6 col-md-6 col-8 d-flex align-items-center m-0" id="site-search">
				<form class="w-100 search-form" method="get" action="<?php print home_url();?>">	
					<div id="header-search" class="d-flex align-items-center">
						<button type="submit" class="btn btn-text btn-sm">
							<span class="fa fa-search"></span>	
						</button>
						<?php if( isset( $videotube['video_search'] ) && $videotube['video_search'] == 1 ):?>
							<input type="hidden" name="post_type" value="video">
						<?php endif;?>
						<input class="form-control form-control-sm shadow-none" value="<?php print get_search_query();?>" name="s" type="text" placeholder="<?php esc_attr_e( 'Search here...','videotube')?>" id="search">
					</div>
				</form>
			</div>
			<div class="col-xl-3 col-lg-3 col-md-3 col-12 d-flex align-items-center justify-content-end" id="header-social">
				<?php 
					global $videotube;
					$social_array = videotube_socials_url();
					if( is_array( $social_array ) ){
						foreach ( $social_array as $key=>$value ){
							if( ! empty( $videotube[$key] ) ){
								print '<a href="'. esc_url( $videotube[$key] ) .'"><i class="fab fa-'. esc_attr( $key ) .'"></i></a>';
							}							
						}							
					}
				?>
				<a href="<?php bloginfo('rss_url');?>"><i class="fa fa-rss"></i></a>
			</div>
		</div>
	</div>
</div><!-- /#header -->
<?php if( has_nav_menu('header_main_navigation') ):?>
	<div id="navigation-wrapper" class="sticky-top">
		<div class="container">
			<nav class="navbar navbar-expand-md navbar-dark m-0 p-0">
				<button class="navbar-toggler btn btn-sm border-0" type="button" data-toggle="collapse" data-target="#site-nav" aria-controls="primary-navigation-container" aria-expanded="false">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<div class="collapse navbar-collapse" id="site-nav">
				<!-- menu -->
				  	<?php 
				  		wp_nav_menu(array(
				  			'theme_location'=>	'header_main_navigation',
				  			'menu_id'		=>	'main-menu',
				  			'menu_class'	=>	'navbar-nav mr-auto main-navigation header-navigation menu',
				  			'walker' 		=>	new VideoTube_Walker_Nav_Menu(),
				  			'container'		=>	null
				  		));
				  	?>
				</div>
			</nav>
		</div>
	</div><!-- /#navigation-wrapper -->	
<?php endif;?>