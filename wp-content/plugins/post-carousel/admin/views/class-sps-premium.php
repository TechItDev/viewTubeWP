<?php
/**
 * The main class for Meta-box configurations.
 *
 * @package Smart_Post_Show
 * @subpackage Smart_Post_Show/admin/assets/assets/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

/**
 * Smart Post Show Help Page.
 */
class SPS_Premium {

	/**
	 * Smart Post Show single instance of the class
	 *
	 * @var null The instance of the class.
	 * @since 2.0
	 *
	 * @return void
	 */
	protected static $instance = null;

	/**
	 * Main Smart_Post_Show_Help Instance
	 *
	 * @since 2.0
	 * @static
	 *
	 * @return self help instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add admin sub-menu.
	 *
	 * @return void
	 */
	public function premium_page_menu() {
		$landing_page = 'https://smartpostshow.com/?ref=1';
		add_submenu_page(
			'edit.php?post_type=sp_post_carousel',
			__( 'Smart Post Show Premium', 'post-carousel' ),
			'<span class="sp-go-pro-icon"></span>' . __( 'Go Pro', 'post-carousel' ),
			'manage_options',
			$landing_page
		);

	}

}
