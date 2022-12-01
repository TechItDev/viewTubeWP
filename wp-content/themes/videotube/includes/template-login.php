<?php
if( !defined( 'ABSPATH' ) ) exit();
/**
 * 
 * Filter the login body class
 * 
 * @param  array $classes
 * @param  string $action
 * @return array
 *
 * @since 1.0.0
 * 
 */
function videotube_filter_login_body_class( $classes, $action ){
	return array_merge( $classes, array(
		'videotube-login d-flex flex-column h-100'
	) );
}
add_filter( 'login_body_class', 'videotube_filter_login_body_class', 10, 2 );

add_action( 'login_enqueue_scripts', 'videotube_enqueue_scripts' );
add_action( 'login_enqueue_scripts', 'videotube_custom_option_style', 100 );
add_action( 'login_enqueue_scripts', 'videotube_load_custom_stylesheet', 100 );
add_action( 'login_enqueue_scripts', 'videotube_load_custom_code_style', 100 );

/**
 *
 * Load the login header
 *
 * @since 1.0.0
 * 
 */
function videotube_load_login_header(){
	get_template_part( 'template-parts/header' );
}
add_action( 'login_header', 'videotube_load_login_header', 10, 1 );


/**
 *
 * Load the login footer
 *
 * @since 1.0.0
 * 
 */
function videotube_load_login_footer(){
	get_template_part( 'template-parts/footer' );
}
add_action( 'login_footer', 'videotube_load_login_footer', 10, 9999 );

/**
 *
 * Add login/logout menu items
 */
function videotube_add_loginout_menu_link( $items, $args ){

	global $videotube;

	if( isset( $videotube['loginmenu'] ) && $videotube['loginmenu'] == '1' && $args->theme_location == 'header_main_navigation' ){
		if( ! is_user_logged_in() ){
			$items .= sprintf(
				'<li class="menu-item nav-item login-item"><a class="menu-link nav-link" href="%1$s" title="%2$s"><i class="fa fa-sign-in-alt"></i> %2$s</a></li>',
				esc_url( wp_login_url( home_url('/') ) ),
				esc_html__( 'Log in', 'videotube' )
			);
		}
		else{

			ob_start();

			?>
			<li class="menu-item nav-item login-item logged-item">
				<div class="dropdown">
					<?php 
					printf(
						'<a data-toggle="dropdown" class="menu-link nav-link dropdown-toggle" href="%1$s">%2$s</a>',
						esc_url( get_author_posts_url( get_current_user_id() ) ),
						get_avatar( get_current_user_id(), 32 )
					);
					?>

					<div class="dropdown-menu dropdown-menu-right">

						<?php printf(
							'<a class="dropdown-item profile-item" href="%1$s" title="%2$s">%2$s</a>',
							esc_url( get_author_posts_url( get_current_user_id() ) ),
							esc_attr__( 'Profile', 'videotube' )

						);?>						

						<?php if( has_nav_menu( 'user_nav' ) ):?>

							<?php
						  		wp_nav_menu(array(
						  			'theme_location'	=>	'user_nav',
						  			'menu_id'			=>	'user-menu',
						  			'menu_class'		=>	'nav flex-column',
						  			'walker' 			=>	new VideoTube_Walker_Nav_Menu(),
						  		));
							?>

						<?php endif;?>

						<?php printf(
							'<a class="dropdown-item" href="%1$s" title="%2$s">%2$s</a>',
							esc_url( wp_logout_url( home_url('/') ) ),
							esc_attr__( 'Log out', 'videotube' )

						);?>

					</div>

				</div>
			</li>
			<?php
			$items .= ob_get_clean();

		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_items', 'videotube_add_loginout_menu_link', 10, 2 );