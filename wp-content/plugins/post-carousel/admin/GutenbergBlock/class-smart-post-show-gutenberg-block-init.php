<?php
/**
 * The plugin gutenberg block Initializer.
 *
 * @link       https://shapedplugin.com/
 * @since      2.4.2
 *
 * @package    Smart_Post_Show
 * @subpackage Smart_Post_Show/Admin
 * @author     ShapedPlugin <support@shapedplugin.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Smart_Post_Show_Gutenberg_Block_Init' ) ) {
	/**
	 * Smart_Post_Show_Gutenberg_Block_Init class.
	 */
	class Smart_Post_Show_Gutenberg_Block_Init {
		/**
		 * Script and style suffix
		 *
		 * @since 2.4.2
		 * @access protected
		 * @var string
		 */
		protected $suffix;
		/**
		 * Custom Gutenberg Block Initializer.
		 */
		public function __construct() {
			$this->suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';
			add_action( 'init', array( $this, 'spsp_gutenberg_shortcode_block' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'spsp_block_editor_assets' ) );
		}

		/**
		 * Register block editor script for backend.
		 */
		public function spsp_block_editor_assets() {
			wp_enqueue_script(
				'smart-post-show-shortcode-block',
				plugins_url( '/GutenbergBlock/build/index.js', dirname( __FILE__ ) ),
				array( 'jquery' ),
				SP_PC_VERSION,
				true
			);

			/**
			 * Register block editor css file enqueue for backend.
			 */
			$pcp_settings = get_option( 'sp_post_carousel_settings' );
			if ( $pcp_settings['pcp_fontawesome_css'] ) {
				wp_enqueue_style( 'font-awesome', SP_PC_URL . 'public/assets/css/font-awesome.min.css', array(), SP_PC_VERSION, 'all' );
			}
			if ( $pcp_settings['pcp_swiper_css'] ) {
				wp_enqueue_style( 'pcp_swiper', SP_PC_URL . 'public/assets/css/swiper-bundle' . $this->suffix . '.css', array(), SP_PC_VERSION, 'all' );
			}
			wp_enqueue_style( 'pcp-style', SP_PC_URL . 'public/assets/css/style' . $this->suffix . '.css', array(), SP_PC_VERSION, 'all' );
		}
		/**
		 * Shortcode list.
		 *
		 * @return array
		 */
		public function spsp_post_list() {
			$shortcodes = get_posts(
				array(
					'post_type'      => 'sp_post_carousel',
					'post_status'    => 'publish',
					'posts_per_page' => 9999,
				)
			);

			if ( count( $shortcodes ) < 1 ) {
				return array();
			}

			return array_map(
				function ( $shortcode ) {
						return (object) array(
							'id'    => absint( $shortcode->ID ),
							'title' => esc_html( $shortcode->post_title ),
						);
				},
				$shortcodes
			);
		}

		/**
		 * Register Gutenberg shortcode block.
		 */
		public function spsp_gutenberg_shortcode_block() {
			/**
			 * Register block editor js file enqueue for backend.
			 */
			wp_register_script( 'pcp_swiper', SP_PC_URL . 'public/assets/js/swiper-bundle' . $this->suffix . '.js', array( 'jquery' ), SP_PC_VERSION, true );
			wp_register_script( 'pcp_script', SP_PC_URL . 'public/assets/js/scripts' . $this->suffix . '.js', array( 'jquery' ), SP_PC_VERSION, true );

			wp_localize_script(
				'pcp_script',
				'smartPostShowGbScript',
				array(
					'url'           => SP_PC_URL,
					'loadScript'    => SP_PC_URL . 'public/assets/js/scripts.js',
					'link'          => admin_url( 'post-new.php?post_type=sp_post_carousel' ),
					'shortCodeList' => $this->spsp_post_list(),
				)
			);
			/**
			 * Register Gutenberg block on server-side.
			 */
			register_block_type(
				'smart-post-show-pro/shortcode',
				array(
					'attributes'      => array(
						'shortcodelist'      => array(
							'type'    => 'object',
							'default' => '',
						),
						'shortcode'          => array(
							'type'    => 'string',
							'default' => '',
						),
						'showInputShortcode' => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'preview'            => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'is_admin'           => array(
							'type'    => 'boolean',
							'default' => is_admin(),
						),
					),
					'example'         => array(
						'attributes' => array(
							'preview' => true,
						),
					),
					// Enqueue blocks.editor.build.js in the editor only.
					'editor_script'   => array(
						'pcp_swiper',
						'pcp_script',
					),
					// Enqueue blocks.editor.build.css in the editor only.
					'editor_style'    => array(),
					'render_callback' => array( $this, 'smart_post_show_render_shortcode' ),
				)
			);
		}

		/**
		 * Render callback.
		 *
		 * @param string $attributes Shortcode.
		 * @return string
		 */
		public function smart_post_show_render_shortcode( $attributes ) {

			$class_name = '';
			if ( ! empty( $attributes['className'] ) ) {
				$class_name = 'class="' . $attributes['className'] . '"';
			}

			if ( ! $attributes['is_admin'] ) {
				return '<div ' . $class_name . '>' . do_shortcode( '[smart_post_show id="' . sanitize_text_field( $attributes['shortcode'] ) . '"]' ) . '</div>';
			}

			$pcp_settings   = get_option( 'sp_post_carousel_settings' );
			$pcp_id         = $attributes['shortcode'];
			$custom_css     = '';
			$pcp_custom_css = $pcp_settings['pcp_custom_css'];
			$view_options   = get_post_meta( $pcp_id, 'sp_pcp_view_options', true );
			$layouts        = get_post_meta( $pcp_id, 'sp_pcp_layouts', true );
			// Include dynamic style file.
			include SP_PC_PATH . 'public/dynamic-css/dynamic-css.php';
			if ( ! empty( $pcp_custom_css ) ) {
				$custom_css .= $pcp_custom_css;
			}
			// Add dynamic style.
			$style = '<style>' . $custom_css . '</style>';

			return $style . '<div id="' . uniqid() . '" ' . $class_name . ' >' . do_shortcode( '[smart_post_show id="' . sanitize_text_field( $attributes['shortcode'] ) . '"]' ) . '</div>';
		}
	}
}
