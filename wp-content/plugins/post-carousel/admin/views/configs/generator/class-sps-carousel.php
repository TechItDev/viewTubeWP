<?php
/**
 * The Carousel section Meta-box configurations.
 *
 * @package Smart_Post_Show
 * @subpackage Smart_Post_Show/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.

	/**
	 * The Carousel building class.
	 */
class SPS_Carousel {

	/**
	 * Carousel section metabox.
	 *
	 * @param string $prefix The metabox key.
	 * @return void
	 */
	public static function section( $prefix ) {
		SP_PC::createSection(
			$prefix,
			array(
				'title'  => __( 'Carousel Controls', 'post-carousel' ),
				'icon'   => 'fa fa-sliders',
				'fields' => array(
					array(
						'id'       => 'pcp_carousel_mode',
						'type'     => 'button_set',
						'title'    => __( 'Carousel Mode', 'post-carousel' ),
						'subtitle' => __( 'Choose a mode for the carousel.', 'post-carousel' ),
						'options'  => array(
							'standard' => __( 'Standard', 'post-carousel' ),
							'center'   => array(
								'text'     => __( 'Center', 'post-carousel' ),
								'pro_only' => true,
							),
							'ticker'   => array(
								'text'     => __( 'Ticker', 'post-carousel' ),
								'pro_only' => true,
							),
						),
						'default'  => 'standard',
					),
					array(
						'id'       => 'pcp_carousel_speed',
						'type'     => 'spinner',
						'title'    => __( 'Carousel Speed', 'post-carousel' ),
						'subtitle' => __( 'Set carousel speed in millisecond.', 'post-carousel' ),
						'default'  => '600',
						'min'      => 0,
						'max'      => 20000,
						'step'     => 100,
						'unit'     => 'ms',
					),
					array(
						'id'         => 'pcp_autoplay',
						'type'       => 'switcher',
						'title'      => __( 'AutoPlay', 'post-carousel' ),
						'subtitle'   => __( 'Enable/Disable carousel autoplay.', 'post-carousel' ),
						'default'    => true,
						'text_on'    => __( 'Enabled', 'post-carousel' ),
						'text_off'   => __( 'Disabled', 'post-carousel' ),
						'text_width' => 94,
					),
					array(
						'id'         => 'pcp_autoplay_speed',
						'type'       => 'spinner',
						'title'      => __( 'AutoPlay Speed', 'post-carousel' ),
						'subtitle'   => __( 'Set autoplay speed in millisecond.', 'post-carousel' ),
						'default'    => '2000',
						'min'        => 0,
						'max'        => 10000,
						'step'       => 100,
						'unit'       => 'ms',
						'dependency' => array( 'pcp_autoplay', '==', true ),
					),
					array(
						'id'         => 'pcp_pause_hover',
						'type'       => 'switcher',
						'title'      => __( 'Pause on Hover', 'post-carousel' ),
						'subtitle'   => __( 'Enable/Disable carousel stop on hover.', 'post-carousel' ),
						'default'    => true,
						'text_on'    => __( 'Enabled', 'post-carousel' ),
						'text_off'   => __( 'Disabled', 'post-carousel' ),
						'text_width' => 94,
						'dependency' => array( 'pcp_autoplay', '==', true ),

					),
					array(
						'id'         => 'pcp_infinite_loop',
						'type'       => 'switcher',
						'title'      => __( 'Infinite Loop', 'post-carousel' ),
						'subtitle'   => __( 'Enable/Disable carousel infinite loop.', 'post-carousel' ),
						'text_on'    => __( 'Enabled', 'post-carousel' ),
						'text_off'   => __( 'Disabled', 'post-carousel' ),
						'text_width' => 94,
						'default'    => true,
					),
					array(
						'id'         => 'pcp_lazy_load',
						'type'       => 'switcher',
						'title'      => __( 'Lazy Load', 'post-carousel' ),
						'subtitle'   => __( 'Enable/Disable lazy load.', 'post-carousel' ),
						'text_on'    => __( 'Enabled', 'post-carousel' ),
						'text_off'   => __( 'Disabled', 'post-carousel' ),
						'text_width' => 94,
						'default'    => true,
					),
					array(
						'id'       => 'pcp_carousel_direction',
						'type'     => 'button_set',
						'title'    => __( 'Carousel Direction', 'post-carousel' ),
						'subtitle' => __( 'Choose a carousel direction.', 'post-carousel' ),
						'options'  => array(
							'ltr' => __( 'Right to Left', 'post-carousel' ),
							'rtl' => __( 'Left to Right', 'post-carousel' ),
						),
						'default'  => 'ltr',
					),
					array(
						'id'       => 'pcp_slide_effect',
						'type'     => 'select',
						'title'    => __( 'Transition Effect', 'post-carousel' ),
						'subtitle' => __( 'Select a slide transition effect.', 'post-carousel' ),
						'help'     => __( 'Fade, cube, and flip transition effects work only for the single column view.', 'post-carousel' ),
						'options'  => array(
							'slide'     => __( 'Slide', 'post-carousel' ),
							'fade'      => __( 'Fade (Pro)', 'post-carousel' ),
							'coverflow' => __( 'Coverflow (Pro)', 'post-carousel' ),
							'cube'      => __( 'Cube (Pro)', 'post-carousel' ),
							'flip'      => __( 'Flip (Pro)', 'post-carousel' ),
						),
						'default'  => 'slide',
					),
					array(
						'id'       => 'pcp_number_of_row',
						'type'     => 'column',
						'class'    => 'pcp_only_pro',
						'title'    => __( 'Row', 'post-carousel' ),
						'subtitle' => __( 'Set number of row in different devices for responsive view.', 'post-carousel' ),
						'default'  => array(
							'lg_desktop'       => '1',
							'desktop'          => '1',
							'tablet'           => '1',
							'mobile_landscape' => '1',
							'mobile'           => '1',
						),
						'min'      => '1',
					),
					array(
						'type'    => 'subheading',
						'content' => __( 'Navigation', 'post-carousel' ),
					),

					// Navigation Settings.
					array(
						'id'       => 'pcp_navigation',
						'type'     => 'button_set',
						'title'    => __( 'Navigation', 'post-carousel' ),
						'subtitle' => __( 'Show/Hide carousel navigation.', 'post-carousel' ),
						'options'  => array(
							'show'           => __( 'Show', 'post-carousel' ),
							'hide'           => __( 'Hide', 'post-carousel' ),
							'hide_on_mobile' => __( 'Hide on Mobile', 'post-carousel' ),
						),
						'default'  => 'show',
					),
					array(
						'id'         => 'pcp_nav_colors',
						'type'       => 'color_group',
						'title'      => __( 'Navigation Color', 'post-carousel' ),
						'subtitle'   => __( 'Set color for the carousel navigation.', 'post-carousel' ),
						'options'    => array(
							'color'              => __( 'Color', 'post-carousel' ),
							'hover-color'        => __( 'Hover Color', 'post-carousel' ),
							'bg'                 => __( 'Background', 'post-carousel' ),
							'hover-bg'           => __( 'Hover Background', 'post-carousel' ),
							'border-color'       => __( 'Border', 'post-carousel' ),
							'hover-border-color' => __( 'Hover Border', 'post-carousel' ),
						),
						'default'    => array(
							'color'              => '#aaa',
							'hover-color'        => '#fff',
							'bg'                 => '#fff',
							'hover-bg'           => '#D64224',
							'border-color'       => '#aaa',
							'hover-border-color' => '#D64224',
						),
						'dependency' => array( 'pcp_navigation', '!=', 'hide' ),
					),

					// Pagination Settings.
					array(
						'type'    => 'subheading',
						'content' => __( 'Pagination', 'post-carousel' ),
					),
					array(
						'id'       => 'pcp_pagination',
						'type'     => 'button_set',
						'title'    => __( 'Pagination', 'post-carousel' ),
						'subtitle' => __( 'Show/Hide carousel pagination.', 'post-carousel' ),
						'options'  => array(
							'show'           => __( 'Show', 'post-carousel' ),
							'hide'           => __( 'Hide', 'post-carousel' ),
							'hide_on_mobile' => __( 'Hide on Mobile', 'post-carousel' ),
						),
						'default'  => 'show',
					),
					array(
						'id'         => 'pcp_pagination_color_set',
						'type'       => 'fieldset',
						'class'      => 'pcp-pagination-color-set',
						'title'      => __( 'Pagination Color', 'post-carousel' ),
						'subtitle'   => __( 'Set color for the carousel pagination.', 'post-carousel' ),
						'fields'     => array(
							array(
								'id'      => 'pcp_pagination_color',
								'type'    => 'color_group',
								'options' => array(
									'color'        => __( 'Color', 'post-carousel' ),
									'active-color' => __( 'Active Color', 'post-carousel' ),
								),
								'default' => array(
									'color'        => '#cccccc',
									'active-color' => '#D64224',
								),
							),
						),
						'dependency' => array( 'pcp_pagination', '!=', 'hide' ),
					),

					// Miscellaneous Settings.
					array(
						'type'    => 'subheading',
						'content' => __( 'Miscellaneous', 'post-carousel' ),
					),
					array(
						'id'         => 'pcp_adaptive_height',
						'type'       => 'switcher',
						'title'      => __( 'Adaptive Carousel Height', 'post-carousel' ),
						'subtitle'   => __( 'Dynamically adjust post carousel height based on each slide\'s height.', 'post-carousel' ),
						'default'    => false,
						'text_on'    => __( 'Enabled', 'post-carousel' ),
						'text_off'   => __( 'Disabled', 'post-carousel' ),
						'text_width' => 94,
					),
					array(
						'id'         => 'pcp_accessibility',
						'type'       => 'switcher',
						'title'      => __( 'Tab and Key Navigation', 'post-carousel' ),
						'subtitle'   => __( 'Enable/Disable carousel scroll with tab and keyboard.', 'post-carousel' ),
						'default'    => true,
						'text_on'    => __( 'Enabled', 'post-carousel' ),
						'text_off'   => __( 'Disabled', 'post-carousel' ),
						'text_width' => 94,
					),
					array(
						'id'         => 'touch_swipe',
						'type'       => 'switcher',
						'title'      => __( 'Touch Swipe', 'post-carousel' ),
						'subtitle'   => __( 'Enable/Disable touch swipe mode.', 'post-carousel' ),
						'text_on'    => __( 'Enabled', 'post-carousel' ),
						'text_off'   => __( 'Disabled', 'post-carousel' ),
						'text_width' => 94,
						'default'    => true,
					),
					array(
						'id'         => 'slider_draggable',
						'type'       => 'switcher',
						'title'      => __( 'Mouse Draggable', 'post-carousel' ),
						'subtitle'   => __( 'Enable/Disable mouse draggable mode.', 'post-carousel' ),
						'text_on'    => __( 'Enabled', 'post-carousel' ),
						'text_off'   => __( 'Disabled', 'post-carousel' ),
						'text_width' => 94,
						'default'    => true,
					),
					array(
						'id'         => 'free_mode',
						'type'       => 'switcher',
						'title'      => __( 'Free Mode', 'post-carousel' ),
						'subtitle'   => __( 'Enable/Disable free mode.', 'post-carousel' ),
						'text_on'    => __( 'Enabled', 'post-carousel' ),
						'text_off'   => __( 'Disabled', 'post-carousel' ),
						'text_width' => 94,
						'default'    => false,
					),
					array(
						'id'         => 'slider_mouse_wheel',
						'type'       => 'switcher',
						'title'      => __( 'Mouse Wheel', 'post-carousel' ),
						'subtitle'   => __( 'Enable/Disable mouse wheel mode.', 'post-carousel' ),
						'text_on'    => __( 'Enabled', 'post-carousel' ),
						'text_off'   => __( 'Disabled', 'post-carousel' ),
						'text_width' => 94,
						'default'    => false,
					),
				), // End of fields array.
			)
		); // Carousel Controls section end.
	}
}
