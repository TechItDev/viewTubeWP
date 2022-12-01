<?php
/**
 * VideoTube Required Plugin
 * Display the notifcation about the required plugins, TGM_Plugin_Activation class is required.
 * @author 		Toan Nguyen
 * @category 	Core
 * @version     1.0.0
 */
if( !defined('ABSPATH') ) exit;

if( ! function_exists( 'videotube_register_required_plugins' ) ):

	function videotube_register_required_plugins() {

		/**
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(
			array(
				'name'					=>	'VideoTube Core',
				'slug'					=>	'videotube-core',
				'version'				=>	'1.3.4',
				'source'				=>	get_template_directory() . '/plugins/videotube-core.zip',
				'required'				=>	true
			),
			array(
				'name'					=>	'WP Easy Review',
				'slug'					=>	'wp-easy-review',
				'version'				=>	'1.3',
				'source'				=>	get_template_directory() . '/plugins/wp-easy-review.zip',
				'required'				=>	true
			),
			array(
				'name'					=>	'Redux Framework',
				'slug'					=>	'redux-framework'
			),
			array(
				'name'					=>	'Advanced Custom Fields',
				'slug'					=>	'advanced-custom-fields',
				'required'				=>	true
			),
			array(
				'name'					=>	'Advanced Custom Fields: Gallery Field',
				'slug'					=>	'acf-gallery',
				'source'					=>	get_template_directory() . '/plugins/acf-gallery.zip',
				'version'				=>	'2.1.0',
				'required'				=>	true
			),
			array(
				'name'					=>	'Video Thumbnails',
				'slug'					=>	'video-thumbnails'
			),
			array(
				'name'					=>	'WPBakery Page Builder',
				'slug'					=>	'js_composer',
				'version'				=>	'6.6',
				'source'					=>	get_template_directory() . '/plugins/js_composer.zip'
			),
			array(
				'name'					=>	'Social Count Plus',
				'slug'					=>	'social-count-plus',
				'version'				=>	'3.4.1',
				'source'					=>	get_template_directory() . '/plugins/social-count-plus.zip'
			)
		);

		$config = array(
			'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'themes.php',            // Parent menu slug.
			'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => true,                   // Automatically activate plugins after installation or not.
			'message'      => ''
		);

		tgmpa( $plugins, $config );
	}
	add_action( 'tgmpa_register', 'videotube_register_required_plugins' );

endif;
