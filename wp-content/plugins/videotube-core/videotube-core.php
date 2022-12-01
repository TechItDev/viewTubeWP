<?php
/**
 * Plugin Name: VideoTube Core
 * Plugin URI: http://themeforest.net/item/videotube-a-responsive-video-wordpress-theme/7214445?ref=phpface
 * Description: VideoTube theme core plugin
 * Version: 1.3.4
 * Author: phpface
 * Author URI: http://themeforest.net/user/phpface
 * License: Themeforest Licence
 * License URI: http://themeforest.net/licenses
 * Text Domain: videotube-core
 * Domain Path: /languages
 */

defined('ABSPATH') || exit; // No direct access.

define( 'VIDEOTUBE_CORE_PATH' , plugin_dir_path( __FILE__ ) );

class VideoTubeCore{

	/**
	 * The instance of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 */	
	protected static $_instance = null;

	/**
	 *
	 * Plugin instance
	 * 
	 * 
	 * @since 1.0
	 * 
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}	
	
	public function __construct(){

		$this->load_files();

		$this->load_hooks();
	}

	/**
	 *
	 * Load all files.
	 *
	 * 
	 * @since 1.0
	 */
	private function load_files(){

		require_once VIDEOTUBE_CORE_PATH . 'includes/functions.php';

		require_once VIDEOTUBE_CORE_PATH . 'includes/class-custom-post-type.php';
		require_once VIDEOTUBE_CORE_PATH . 'includes/class-custom-taxonomy.php';
		require_once VIDEOTUBE_CORE_PATH . 'includes/class-shortcode.php';

		require_once VIDEOTUBE_CORE_PATH . 'includes/class-widget-featured-posts.php';
		require_once VIDEOTUBE_CORE_PATH . 'includes/class-widget-featured-videos.php';
		require_once VIDEOTUBE_CORE_PATH . 'includes/class-widget-tags-cloud.php';
		require_once VIDEOTUBE_CORE_PATH . 'includes/class-widget-login-form.php';

		require_once VIDEOTUBE_CORE_PATH . 'includes/class-widget-main-posts.php';

		require_once VIDEOTUBE_CORE_PATH . 'includes/class-widget-main-videos.php';

		require_once VIDEOTUBE_CORE_PATH . 'includes/class-widget-large-video.php';

		require_once VIDEOTUBE_CORE_PATH . 'includes/class-widget-aside-posts.php';

		require_once VIDEOTUBE_CORE_PATH . 'includes/class-widget-aside-videos.php';

		require_once VIDEOTUBE_CORE_PATH . 'includes/class-widget-related-posts.php';

		require_once VIDEOTUBE_CORE_PATH . 'includes/class-widget-related-videos.php';

		require_once VIDEOTUBE_CORE_PATH . 'includes/class-widget-socials.php';

		require_once VIDEOTUBE_CORE_PATH . 'includes/class-widget-social-count.php';

		require_once VIDEOTUBE_CORE_PATH . 'includes/class-metabox.php';

		require_once VIDEOTUBE_CORE_PATH . 'includes/class-composer.php';

	}

	/**
	 *
	 * Load all hooks.
	 * 
	 * @since 1.0
	 */
	private function load_hooks(){
		add_action('plugins_loaded', array( $this , 'plugins_loaded' ) );
	}
		
	/**
	 * Languages
	 */
	function plugins_loaded() {
		load_plugin_textdomain( 'videotube-core' , false , dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

}

function videotube_core(){
	return VideoTubeCore::get_instance();
}

videotube_core();