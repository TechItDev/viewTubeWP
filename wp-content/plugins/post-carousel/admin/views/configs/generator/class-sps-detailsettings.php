<?php
/**
 * The Popup Settings Meta-box configurations.
 *
 * @package Smart_Post_Show
 * @subpackage Smart_Post_Show/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.

/**
 * The Popup settings class.
 */
class SPS_DetailSettings {

	/**
	 * Popup settings section metabox.
	 *
	 * @param string $prefix The metabox key.
	 * @return void
	 */
	public static function section( $prefix ) {
		SP_PC::createSection(
			$prefix,
			array(
				'title'  => __( 'Detail page Settings', 'post-carousel' ),
				'icon'   => 'fa fa-external-link-square',
				'fields' => array(
					array(
						'id'       => 'pcp_page_link_type',
						'class'    => 'pcp_page_link_type',
						'type'     => 'radio',
						'title'    => __( 'Detail Page Link Type', 'post-carousel' ),
						'subtitle' => __( 'Choose a link type for the (item) detail page.', 'post-carousel' ),
						'options'  => array(
							'popup'       => __( 'Popup (Pro)', 'post-carousel' ),
							'single_page' => __( 'Single Page', 'post-carousel' ),
							'none'        => __( 'None (no link action)', 'post-carousel' ),
						),
						'default'  => 'single_page',
					),
					array(
						'id'         => 'pcp_link_target',
						'type'       => 'radio',
						'title'      => __( 'Target', 'post-carousel' ),
						'subtitle'   => __( 'Set a target for the item link.', 'post-carousel' ),
						'options'    => array(
							'_self'   => __( 'Current Tab', 'post-carousel' ),
							'_blank'  => __( 'New Tab', 'post-carousel' ),
							'_parent' => __( 'Parent', 'post-carousel' ),
							'_top'    => __( 'Top', 'post-carousel' ),
						),
						'default'    => '_self',
						'dependency' => array( 'pcp_page_link_type', '==', 'single_page' ),
					),
					array(
						'id'      => 'pcp_link_rel',
						'type'    => 'checkbox',
						'title'   => __( 'Add rel="nofollow" to item links', 'post-carousel' ),
						'default' => false,
					),
					array(
						'type'    => 'notice',
						'content' => __( 'To unlock the following amazing Popup Settings</b>, <a href="//smartpostshow.com" target="_blank"><b>Upgrade To Pro!</b></a>', 'post-carousel' ),
					),
					array(
						'id'     => 'pcp_popup_settings',
						'class'  => 'pcp_popup_settings_field',
						'type'   => 'fieldset',
						'fields' => array(
							array(
								'id'       => 'pcp_popup_type',
								'type'     => 'radio',
								'title'    => __( 'Popup Type', 'post-carousel' ),
								'subtitle' => __( 'Choose a popup type.', 'post-carousel' ),
								'options'  => array(
									'single_popup' => __( 'Single Popup', 'post-carousel' ),
									'multi_popup'  => __( 'Multi Popup (Preview with navigation)', 'post-carousel' ),
								),
								'default'  => 'single_popup',
							),
							array(
								'id'       => 'popup_width',
								'type'     => 'spacing',
								'title'    => __( 'Maximum Width', 'post-carousel' ),
								'subtitle' => __( 'Set maximum popup width.', 'post-carousel' ),
								'all'      => true,
								'min'      => 0,
								'max'      => 3000,
								'all_icon' => '<i class="fa fa-arrows-h"></i>',
								'units'    => array( 'px', '%' ),
								'default'  => array(
									'all' => '1050',
								),
							),
							array(
								'id'       => 'popup_height',
								'type'     => 'spacing',
								'title'    => __( 'Maximum Height', 'post-carousel' ),
								'subtitle' => __( 'Set maximum popup height.', 'post-carousel' ),
								'all'      => true,
								'min'      => 0,
								'max'      => 3000,
								'all_icon' => '<i class="fa fa-arrows-v"></i>',
								'units'    => array( 'vh', 'px', '%' ),
								'default'  => array(
									'all' => '700',
								),
							),
							array(
								'id'       => 'popup_content_color',
								'type'     => 'color_group',
								'title'    => __( 'Content Color', 'post-carousel' ),
								'subtitle' => __( 'Set the popup content color.', 'post-carousel' ),
								'options'  => array(
									'post-title'    => __( 'Post Title', 'post-carousel' ),
									'post-meta'     => __( 'Post Meta', 'post-carousel' ),
									'post-content'  => __( 'Post Content', 'post-carousel' ),
									'custom-fields' => __( 'Custom Fields', 'post-carousel' ),
								),
								'default'  => array(
									'post-title'    => '#111',
									'post-meta'     => '#888',
									'post-content'  => '#444',
									'custom-fields' => '#888',
								),
							),
							array(
								'id'       => 'popup_bg_color',
								'type'     => 'color',
								'title'    => __( 'Background Color', 'post-carousel' ),
								'subtitle' => __( 'Change the popup background color.', 'post-carousel' ),
								'default'  => '#fff',
							),
							array(
								'id'       => 'popup_overlay_color',
								'type'     => 'color',
								'title'    => __( 'Overlay Color', 'post-carousel' ),
								'subtitle' => __( 'Change the popup overlay color.', 'post-carousel' ),
								'default'  => 'rgba(11,11,11,0.8)',
							),
							array(
								'id'         => 'popup_close_button',
								'type'       => 'switcher',
								'title'      => __( 'Close Button', 'post-carousel' ),
								'subtitle'   => __( 'Show/Hide popup close button.', 'post-carousel' ),
								'text_on'    => __( 'Show', 'post-carousel' ),
								'text_off'   => __( 'Hide', 'post-carousel' ),
								'text_width' => 80,
								'default'    => true,
							),
							array(
								'id'       => 'popup_close_button_color',
								'type'     => 'color_group',
								'title'    => __( 'Close Button Color', 'post-carousel' ),
								'subtitle' => __( 'Change the popup close button color.', 'post-carousel' ),
								'options'  => array(
									'color'       => __( 'Color', 'post-carousel' ),
									'hover-color' => __( 'Hover Color', 'post-carousel' ),
								),
								'default'  => array(
									'color'       => '#fc0c0c',
									'hover-color' => '#e1624b',
								),
							),
							array(
								'id'       => 'popup_nav_color',
								'type'     => 'color_group',
								'title'    => __( 'Navigation Color', 'post-carousel' ),
								'subtitle' => __( 'Change the popup navigation color.', 'post-carousel' ),
								'options'  => array(
									'color'       => __( 'Color', 'post-carousel' ),
									'hover-color' => __( 'Hover Color', 'post-carousel' ),
									'bg'          => __( 'Background', 'post-carousel' ),
									'hover-bg'    => __( 'Hover Background', 'post-carousel' ),
								),
								'default'  => array(
									'color'       => '#aaa',
									'hover-color' => '#fff',
									'bg'          => 'rgba(0,0,0,0.5)',
									'hover-bg'    => '#e1624b',
								),
							),
							array(
								'id'       => 'popup_post_content_sorter',
								'type'     => 'sortable',
								'title'    => __( 'Popup Fields', 'post-carousel' ),
								'subtitle' => __( 'Show/Hide content fields in the popup. These fields are also sortable.', 'post-carousel' ),
								'class'    => 'post_content_sorter_sortable',
								'default'  => array(
									'popup_show_post_thumb' => true,
									'popup_show_post_title' => true,
									'popup_show_post_meta' => true,
									'popup_show_post_content' => true,
									'popup_show_social_share' => true,
									'popup_show_custom_fields' => false,
								),
								'fields'   => array(
									array(
										'id'         => 'popup_show_post_thumb',
										'type'       => 'switcher',
										'title'      => __( 'Thumbnail', 'post-carousel' ),
										'text_on'    => __( 'Show', 'post-carousel' ),
										'text_off'   => __( 'Hide', 'post-carousel' ),
										'text_width' => 80,
									),
									array(
										'id'         => 'popup_show_post_title',
										'type'       => 'switcher',
										'title'      => __( 'Title', 'post-carousel' ),
										'text_on'    => __( 'Show', 'post-carousel' ),
										'text_off'   => __( 'Hide', 'post-carousel' ),
										'text_width' => 80,
									),
									array(
										'id'         => 'popup_show_post_meta',
										'type'       => 'switcher',
										'title'      => __( 'Meta Fields', 'post-carousel' ),
										'text_on'    => __( 'Show', 'post-carousel' ),
										'text_off'   => __( 'Hide', 'post-carousel' ),
										'text_width' => 80,
									),
									array(
										'id'         => 'popup_show_post_content',
										'type'       => 'switcher',
										'title'      => __( 'Content', 'post-carousel' ),
										'text_on'    => __( 'Show', 'post-carousel' ),
										'text_off'   => __( 'Hide', 'post-carousel' ),
										'text_width' => 80,
									),
									array(
										'id'         => 'popup_show_social_share',
										'type'       => 'switcher',
										'title'      => __( 'Social Share', 'post-carousel' ),
										'text_on'    => __( 'Show', 'post-carousel' ),
										'text_off'   => __( 'Hide', 'post-carousel' ),
										'text_width' => 80,
									),
									array(
										'id'         => 'popup_show_custom_fields',
										'type'       => 'switcher',
										'title'      => __( 'Custom Fields', 'post-carousel' ),
										'text_on'    => __( 'Show', 'post-carousel' ),
										'text_off'   => __( 'Hide', 'post-carousel' ),
										'text_width' => 80,
									),

								),
							),
						),
					),

				), // End of fields array.
			)
		); // Display settings section end.
	}
}
