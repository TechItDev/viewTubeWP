<?php
/**
 * The Display options Meta-box configurations.
 *
 * @package Smart_Post_Show
 * @subpackage Smart_Post_Show/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.

/**
 * The Layout building class.
 */
class SPS_Display {

	/**
	 * Display options section metabox.
	 *
	 * @param string $prefix The metabox key.
	 * @return void
	 */
	public static function section( $prefix ) {
		SP_PC::createSection(
			$prefix,
			array(
				'title'  => __( 'DISPLAY OPTIONS', 'post-carousel' ),
				'icon'   => 'fa fa-th-large',
				'fields' => array(
					array(
						'id'         => 'section_title',
						'type'       => 'switcher',
						'title'      => __( 'Section Title ', 'post-carousel' ),
						'subtitle'   => __( 'Show/Hide item showcase section title.', 'post-carousel' ),
						'default'    => false,
						'text_on'    => __( 'Show', 'post-carousel' ),
						'text_off'   => __( 'Hide', 'post-carousel' ),
						'text_width' => 75,
					),
					array(
						'id'              => 'section_title_margin',
						'type'            => 'spacing',
						'title'           => __( 'Section Title Margin', 'post-carousel' ),
						'subtitle'        => __( 'Set margin for the section title.', 'post-carousel' ),
						'all_icon'        => '<i class="fa fa-long-arrow-down"></i>',
						'units'           => array(
							'px',
						),
						'all_placeholder' => 'margin',
						'default'         => array(
							'top'    => '0',
							'right'  => '0',
							'bottom' => '30',
							'top'    => '0',
						),
						'dependency'      => array(
							'section_title',
							'==',
							'true',
							true,
						),
					),
					array(
						'id'       => 'pcp_number_of_columns',
						'type'     => 'column',
						'title'    => __( 'Column(s)', 'post-carousel' ),
						'subtitle' => __( 'Set number of column(s) in different devices for responsive view.', 'post-carousel' ),
						'default'  => array(
							'lg_desktop'       => '4',
							'desktop'          => '4',
							'tablet'           => '3',
							'mobile_landscape' => '2',
							'mobile'           => '1',
						),
						'min'      => '1',
						'help'     => '<i class="fa fa-television"></i> Large Desktop - is larger than 1200px,<br><i class="fa fa-desktop"></i> Desktop - size is larger than 992px,<br> <i class="fa fa-tablet"></i> Tablet - Size is larger than 768,<br> <i class="fa fa-mobile"></i> Mobile Landscape- size is larger than 576px.,<br> <i class="fa fa-mobile"></i> Mobile - size is smaller than 576px.',
					),
					array(
						'id'              => 'margin_between_post',
						'type'            => 'spacing',
						'title'           => __( 'Margin Between Columns', 'post-carousel' ),
						'subtitle'        => __( 'Set margin between columns (items).', 'post-carousel' ),
						'all'             => true,
						'all_icon'        => '<i class="fa fa-arrows-h"></i>',
						'units'           => array(
							'px',
						),
						'all_placeholder' => 'margin',
						'default'         => array(
							'all' => '20',
						),
					),
					array(
						'id'       => 'post_content_orientation',
						'type'     => 'layout_preset',
						'title'    => __( 'Content Orientation ', 'post-carousel' ),
						'subtitle' => __( 'Set a position for the item content.', 'post-carousel' ),
						'desc'     => __( 'To unlock more amazing Content Orientation and Layout based Settings, <a href="https://smartpostshow.com/" target="_blank"><strong>Upgrade To Pro!</strong></a>', 'post-carousel' ),
						'class'    => 'pcp-content-orientation',
						'options'  => array(
							'default'     => array(
								'image' => SP_PC_URL . 'admin/views/sp-framework/assets/img/default.svg',
								'text'  => __( 'Default', 'post-carousel' ),
							),
							'left-thumb'  => array(
								'image'    => SP_PC_URL . 'admin/views/sp-framework/assets/img/left-image.svg',
								'text'     => __( 'Left Image', 'post-carousel' ),
								'pro_only' => true,
							),
							'right-thumb' => array(
								'image'    => SP_PC_URL . 'admin/views/sp-framework/assets/img/right-image.svg',
								'text'     => __( 'Right Image', 'post-carousel' ),
								'pro_only' => true,
							),
							'overlay'     => array(
								'image'    => SP_PC_URL . 'admin/views/sp-framework/assets/img/overlay.svg',
								'text'     => __( 'Overlay', 'post-carousel' ),
								'pro_only' => true,
							),
							'card'        => array(
								'image'    => SP_PC_URL . 'admin/views/sp-framework/assets/img/card.svg',
								'text'     => __( 'Card', 'post-carousel' ),
								'pro_only' => true,
							),
							'overlay-box' => array(
								'image'    => SP_PC_URL . 'admin/views/sp-framework/assets/img/overlay-box.svg',
								'text'     => __( 'Overlay Box', 'post-carousel' ),
								'pro_only' => true,
							),
						),
						'default'  => 'default',
					),
					array(
						'id'       => 'post_border',
						'type'     => 'border',
						'title'    => __( 'Border', 'post-carousel' ),
						'subtitle' => __( 'Set border for the item.', 'post-carousel' ),
						'all'      => true,
						'default'  => array(
							'all'   => '0',
							'style' => 'solid',
							'color' => '#e2e2e2',
						),
					),
					array(
						'id'       => 'post_border_radius_property',
						'type'     => 'spacing',
						'title'    => __( 'Border Radius', 'post-carousel' ),
						'subtitle' => __( 'Set border radius for item.', 'post-carousel' ),
						'all'      => true,
						'units'    => array( 'px', '%' ),
						'default'  => array(
							'all' => '0',
						),
					),
					array(
						'id'       => 'post_background_property',
						'type'     => 'color',
						'title'    => __( 'Background', 'post-carousel' ),
						'subtitle' => __( 'Set background color for the item.', 'post-carousel' ),
						'default'  => 'transparent',
					),
					array(
						'id'       => 'post_inner_padding_property',
						'type'     => 'spacing',
						'title'    => __( 'Inner Padding', 'post-carousel' ),
						'subtitle' => __( 'Set inner padding for  item.', 'post-carousel' ),
						'default'  => array(
							'top'    => '0',
							'right'  => '0',
							'bottom' => '0',
							'left'   => '0',
							'unit'   => 'px',
						),
						'help'     => "<img src='" . SP_PC_URL . "/admin/assets/img/inner_padding.jpg'>",
					),
					array(
						'id'       => 'post_content_sorter',
						'type'     => 'sortable',
						'title'    => __( 'Content Fields', 'post-carousel' ),
						'subtitle' => __( 'Item content fields which are draggable to change display order and it\'s settings.', 'post-carousel' ),
						'desc'     => __( 'To show Social Share and Custom Fields, <a href="https://smartpostshow.com/" target="_blank"><strong>Upgrade To Pro!</strong></a>', 'post-carousel' ),
						'class'    => 'post_content_sorter',
						'fields'   => array(
							array(
								'id'         => 'pcp_post_thumb',
								'type'       => 'accordion',
								'accordions' => array(
									array(
										'title'  => __( 'Thumbnail', 'post-carousel' ),
										'fields' => array(
											array(
												'id'       => 'post_thumb_show',
												'type'     => 'switcher',
												'title'    => __( 'Thumbnail', 'post-carousel' ),
												'text_on'  => __( 'Show', 'post-carousel' ),
												'text_off' => __( 'Hide', 'post-carousel' ),
												'default'  => true,
												'text_width' => 80,
											),
											array(
												'id'      => 'pcp_thumb_sizes',
												'type'    => 'image_sizes',
												'title'   => __( 'Size', 'post-carousel' ),
												'default' => 'full',
												'dependency' => array( 'post_thumb_show', '==', 'true', true ),
											),
											array(
												'id'       => 'pcp_image_crop_size',
												'type'     => 'dimensions_advanced',
												'title'    => __( 'Custom Size (Pro)', 'post-carousel' ),
												'chosen'   => true,
												'bottom'   => false,
												'left'     => false,
												'color'    => false,
												'top_icon' => '<i class="fa fa-arrows-h"></i>',
												'right_icon' => '<i class="fa fa-arrows-v"></i>',
												'top_placeholder' => 'width',
												'right_placeholder' => 'height',
												'styles'   => array(
													'Soft-crop',
													'Hard-crop',
												),
												'default'  => array(
													'top'  => '600',
													'right' => '400',
													'style' => 'Soft-crop',
													'unit' => 'px',
												),
												'attributes' => array(
													'min' => 0,
												),
												'dependency' => array( 'post_thumb_show|pcp_thumb_sizes', '==|==', 'true|custom' ),
											),
											array(
												'id'       => 'load_2x_image',
												'type'     => 'switcher',
												'title'    => __( 'Load 2x Resolution Image in Retina Display (Pro)', 'post-carousel' ),
												'text_on'  => __( 'Enabled', 'post-carousel' ),
												'text_off' => __( 'Disabled', 'post-carousel' ),
												'text_width' => 94,
												'default'  => false,
												'dependency' => array( 'post_thumb_show|pcp_thumb_sizes', '==|==', 'true|custom' ),
											),
										),
									),
								),
							),
							array(
								'id'         => 'pcp_post_title',
								'type'       => 'accordion',
								'accordions' => array(
									array(
										'title'  => 'Title',
										'fields' => array(
											array(
												'id'       => 'show_post_title',
												'type'     => 'switcher',
												'title'    => __( 'Title', 'post-carousel' ),
												'text_on'  => __( 'Show', 'post-carousel' ),
												'text_off' => __( 'Hide', 'post-carousel' ),
												'default'  => true,
												'text_width' => 80,
											),
											array(
												'id'      => 'post_title_tag',
												'type'    => 'select',
												'title'   => __( 'Title HTML Tag', 'post-carousel' ),
												'options' => array(
													'h1'  => __( 'h1', 'post-carousel' ),
													'h2'  => __( 'h2', 'post-carousel' ),
													'h3'  => __( 'h3', 'post-carousel' ),
													'h4'  => __( 'h4', 'post-carousel' ),
													'h5'  => __( 'h5', 'post-carousel' ),
													'h6'  => __( 'h6', 'post-carousel' ),
													'p'   => __( 'p', 'post-carousel' ),
													'div' => __( 'div', 'post-carousel' ),
												),
												'default' => 'h2',
												'class'   => 'chosen',
												'dependency' => array( 'show_post_title', '==', 'true', true ),
											),
										),
									),
								),
							),
							array(
								'id'         => 'pcp_post_meta',
								'type'       => 'accordion',
								'accordions' => array(
									array(
										'title'  => __( 'Meta Fields', 'post-carousel' ),
										'fields' => array(
											array(
												'id'       => 'show_post_meta',
												'type'     => 'switcher',
												'title'    => __( 'Meta Fields', 'post-carousel' ),
												'text_on'  => __( 'Show', 'post-carousel' ),
												'text_off' => __( 'Hide', 'post-carousel' ),
												'default'  => true,
												'text_width' => 80,
											),
											array(
												'id'      => 'pcp_post_meta_group',
												'class'   => 'pcp_custom_group_design',
												'type'    => 'group',
												'button_title' => __( 'Add New Meta', 'post-carousel' ),
												'dependency' => array( 'show_post_meta', '==', 'true' ),
												'fields'  => array(
													array(
														'id'       => 'select_post_meta',
														'type'     => 'select',
														'title'    => __( 'Select Meta', 'post-carousel' ),
														'placeholder' => __( 'Select a meta', 'post-carousel' ),
														'class' => 'pcp-meta-select',
														'options'  => array(
															'author'  => __( 'Author', 'post-carousel' ),
															'date'   => __( 'Date', 'post-carousel' ),
															'comment_count'  => __( 'Comment Count', 'post-carousel' ),
															'taxonomy'   => __( 'Taxonomy (Pro)', 'post-carousel' ),
															'view_count' => __( 'View Count (Pro)', 'post-carousel' ),
															'like'     => __( 'Like (Pro)', 'post-carousel' ),
															'reading_time'     => __( 'Reading Time (Pro)', 'post-carousel' ),
														),
													),
													array(
														'id'      => 'post_meta_author_avatar',
														'type'    => 'select',
														'title'   => __( 'Avatar', 'post-carousel' ),
														'class'   => 'post_meta_author_avatar',
														'options' => array(
															'show_name' => __( 'Show name', 'post-carousel' ),
															'show_gravatar' => __( 'Show gravatar', 'post-carousel' ),
															'name_with_gravatar' => __( 'Author name & gravatar', 'post-carousel' ),
															'name_with_icon' => __( 'Author name with an icon', 'post-carousel' ),

														),
														'desc' => __( 'To show gravatar, you must <a href="https://wordpress.org/support/article/how-to-use-gravatars/" target="_blank" rel="noopener noreferrer nofollow"><em>enable it</em></a>.', 'post-carousel' ),
														'default' => 'name_with_icon',
														'dependency' => array( 'select_post_meta', '==', 'author' ),
													),
													array(
														'id'      => 'post_meta_date_format',
														'type'    => 'select',
														'title'   => __( 'Date Format', 'post-carousel' ),
														'class'   => 'post_meta_date_format',
														'options' => array(
															'default' => __( 'Default', 'post-carousel' ),
															'time_ago' => __( 'Time ago(human time)', 'post-carousel' ),
															'custom' => __( 'Custom', 'post-carousel' ),
														),
														'default' => 'default',
														'dependency' => array( 'select_post_meta', '==', 'date' ),
													),
													array(
														'id'    => 'pcp_custom_meta_date_format',
														'type'  => 'text',
														'title' => ' ',
														'class' => 'pcp_custom_meta_date_format',
														'placeholder' => __( 'F j, Y', 'post-carousel' ),
														'default'   => __( 'F j, Y', 'post-carousel' ),
														'desc' => __( 'To define format, check <a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank" rel="noopener noreferrer nofollow"><em>this doc</em></a>.', 'post-carousel' ),
														'dependency' => array( 'select_post_meta|post_meta_date_format', '==|==', 'date|custom' ),
													),
													array(
														'id'      => 'post_meta_taxonomy',
														'type'    => 'select',
														'title'   => __( 'Select Taxonomy', 'post-carousel' ),
														'class'   => 'post_meta_taxonomy',
														'options' => 'taxonomy',
														'query_args' => array(
															'type' => 'any',
														),
														'attributes' => array(
															'style' => 'width: 200px;',
														),
														'empty_message' => __( 'No taxonomies found.', 'post-carousel' ),
														'dependency' => array( 'select_post_meta', '==', 'taxonomy' ),
													),
													array(
														'id'      => 'pcp_meta_position',
														'class'      => 'pcp_meta_position',
														'type'    => 'button_set',
														'title'   => __( 'Position', 'post-carousel' ),
														'options' => array(
															'beside_meta' => __( 'Beside Other Meta', 'post-carousel' ),
															'above_title' => __( 'Above Title', 'post-carousel' ),
															'over_thumb' => __( 'Over Thumbnail', 'post-carousel' ),

														),
														'default' => 'beside_meta',
														'dependency' => array( 'select_post_meta', '==', 'taxonomy' ),
													),
													array(
														'id'      => 'pcp_meta_over_thump_position',
														'type'    => 'select',
														'title'   => __( 'Over Thumbnail Position', 'post-carousel' ),
														'options' => array(
															'top_left' => __( 'Top Left', 'post-carousel' ),
															'top_right' => __( 'Top Right', 'post-carousel' ),
															'bottom_left' => __( 'Bottom Left', 'post-carousel' ),
															'bottom_right' => __( 'Bottom Right', 'post-carousel' ),
														),
														'default' => 'top_left',
														'dependency' => array( 'select_post_meta|pcp_meta_position', '==|==', 'taxonomy|over_thumb' ),
													),
													array(
														'id'      => 'pcp_meta_pill_color',
														'type'    => 'color_group',
														'title'   => __( 'Meta Color', 'post-carousel' ),
														'options' => array(
															'text' => __( 'Text', 'post-carousel' ),
															'bg' => __( 'Background', 'post-carousel' ),
														),
														'default' => array(
															'text' => '#fff',
															'bg' => '#e53935',
														),
														'dependency' => array( 'select_post_meta|pcp_meta_position', '==|!=', 'taxonomy|beside_meta' ),
													),
													array(
														'id'      => 'pcp_word_per_minute',
														'type'    => 'number',
														'title'   => __( 'Per Minute', 'post-carousel' ),
														'class'   => 'pcp_reading_time_meta',
														'title_help'   => __( 'Default 300 words, the average reading speed for adults.', 'post-carousel' ),
														'sanitize'        => 'pcp_sanitize_number_field',
														'unit'   => __( 'words', 'post-carousel' ),
														'default' => '300',
														'dependency' => array( 'select_post_meta', '==', 'reading_time' ),
														'default' => 300,
													),
													array(
														'id'      => 'select_meta_icon',
														'class'      => 'select_meta_icon',
														'type'    => 'icon',
														'title'   => __( 'Meta Icon', 'post-carousel' ),
														'default' => 'fa fa-folder-o',
														'dependency' => array( 'pcp_meta_position|select_post_meta', '==|!=', 'beside_meta|like' ),
													),
													array(
														'id'      => 'reading_time_postfix',
														'type'    => 'text',
														'title'   => __( 'Reading Time Postfix', 'post-carousel' ),
														'class'   => 'pcp_reading_time_postfix',
														'title_help'   => __( 'Text after time. Leave empty for nothing.', 'post-carousel' ),
														'default' => ' Min Read',
														'dependency' => array( 'select_post_meta', '==', 'reading_time' ),
													),
												),
												'default' => array(
													array(
														'select_post_meta'    => 'author',
														'post_meta_author_avatar'    => 'name_with_icon',
														'select_meta_icon'    => 'fa fa-user',
														'pcp_meta_position'     => 'beside_meta',
													),
													array(
														'select_post_meta'     => 'date',
														'select_meta_icon'    => 'fa fa-calendar',
														'pcp_meta_position'     => 'beside_meta',
													),
													array(
														'select_post_meta'     => 'comment_count',
														'select_meta_icon'    => 'fa fa-comments-o',
														'pcp_meta_position'     => 'beside_meta',
													),
												),
											),
										),
									),
								),
							),
							array(
								'id'         => 'pcp_post_content',
								'type'       => 'accordion',
								'accordions' => array(
									array(
										'title'  => 'Content',
										'fields' => array(
											array(
												'id'       => 'show_post_content',
												'type'     => 'switcher',
												'title'    => __( 'Content', 'post-carousel' ),
												'text_on'  => __( 'Show', 'post-carousel' ),
												'text_off' => __( 'Hide', 'post-carousel' ),
												'default'  => true,
												'text_width' => 80,
											),
											array(
												'id'      => 'post_content_type',
												'type'    => 'select',
												'class'   => 'pcp-post-content-type',
												'title'   => __( 'Content Display Type', 'post-carousel' ),
												'options' => array(
													'excerpt'      => __( 'Excerpt', 'post-carousel' ),
													'full_content' => __( 'Full Content', 'post-carousel' ),
													'limit_content' => __( 'Content with Limit (Pro)', 'post-carousel' ),
												),
												'default' => 'excerpt',
												'dependency' => array( 'show_post_content', '==', 'true', true ),
											),

											// ReadMore settings.
											array(
												'id'       => 'show_read_more',
												'type'     => 'switcher',
												'title'    => __( 'Read More', 'post-carousel' ),
												'text_on'  => __( 'Show', 'post-carousel' ),
												'text_off' => __( 'Hide', 'post-carousel' ),
												'default'  => true,
												'text_width' => 80,
												'dependency' => array( 'post_content_type', '!=', 'full_content', true ),
											),
											array(
												'id'      => 'pcp_read_label',
												'type'    => 'text',
												'title'   => __( 'Read More Label', 'post-carousel' ),
												'default' => 'Read More',
												'dependency' => array( 'post_content_type|show_read_more', '!=|==', 'full_content|true', true ),
											),
											array(
												'id'      => 'readmore_color_button',
												'type'    => 'color_group',
												'title'   => __( 'Read More Color', 'post-carousel' ),
												'options' => array(
													'standard' => __( 'Text Color', 'post-carousel' ),
													'hover' => __( 'Text Hover Color', 'post-carousel' ),
													'bg' => __( 'Background', 'post-carousel' ),
													'hover_bg' => __( 'Hover Background', 'post-carousel' ),
													'border' => __( 'Border', 'post-carousel' ),
													'hover_border' => __( 'Hover Border', 'post-carousel' ),
												),
												'default' => array(
													'standard' => '#111',
													'hover' => '#fff',
													'bg' => 'transparent',
													'hover_bg' => '#e1624b',
													'border' => '#888',
													'hover_border' => '#e1624b',
												),
												'dependency' => array( 'post_content_type|show_read_more', '!=|==', 'full_content|true', true ),
											),

										),
									),
								),
							),
							array(
								'id'         => 'pcp_social_share',
								'type'       => 'accordion',
								'class'      => 'pcp-pro-only',
								'accordions' => array(
									array(
										'title'  => __( 'Social Share (Pro)', 'post-carousel' ),
										'fields' => array(
											array(
												'id'      => 'social_position',
												'type'    => 'button_set',
												'title'   => __( 'Alignment', 'post-carousel' ),
												'options' => array(
													'left' => '<i class="fa fa-align-left" title="Left"></i>',
													'center' => '<i class="fa fa-align-center" title="Center"></i>',
													'right' => '<i class="fa fa-align-right" title="Right"></i>',
												),
												'default' => 'left',
												'dependency' => array( 'social_sharing_media', '!=', '' ),
											),
										),
									),
								),
							), // PCP Post Social.
							array(
								'id'         => 'pcp_custom_fields',
								'type'       => 'accordion',
								'class'      => 'pcp-pro-only',
								'accordions' => array(
									array(
										'title'  => __( 'Custom Fields (Pro)', 'post-carousel' ),
										'fields' => array(
											// The Group Fields.
											array(
												'id'      => 'pcp_custom_meta_icon',
												'type'    => 'icon',
												'title'   => 'Icon Before Name',
												'default' => 'fa fa-calendar-o',
											),

										), // End Fields array.
									),
								), // Accordion.
							), // Custom fields end.
						),
					),
					array(
						'id'         => 'show_post_pagination',
						'type'       => 'switcher',
						'text_on'    => __( 'Enabled', 'post-carousel' ),
						'text_off'   => __( 'Disabled', 'post-carousel' ),
						'text_width' => 100,
						'title'      => __( 'Pagination', 'post-carousel' ),
						'subtitle'   => __( 'Enabled/Disabled item pagination.', 'post-carousel' ),
						'default'    => true,
						'dependency' => array( 'pcp_layout_preset', 'not-any', 'carousel_layout', 'true' ),
					),
					array(
						'id'         => 'post_pagination_type',
						'type'       => 'radio',
						'title'      => __( 'Pagination Type', 'post-carousel' ),
						'subtitle'   => __( 'Choose a pagination type.', 'post-carousel' ),
						'desc'       => __( 'More amazing Ajax Pagination Settings are available in <a href="https://smartpostshow.com/" target="_blank"><strong>Pro</strong></a>!', 'post-carousel' ),
						'class'      => 'pcp-pagination-type',
						'options'    => array(
							'no_ajax'         => __( 'Normal Pagination', 'post-carousel' ),
							'ajax_load_more'  => __( 'Ajax Load More Button (Pro)', 'post-carousel' ),
							'ajax_pagination' => __( 'Ajax Number Pagination (Pro)', 'post-carousel' ),
							'infinite_scroll' => __( 'Ajax Infinite Scroll (Pro)', 'post-carousel' ),
						),
						'default'    => 'no_ajax',
						'dependency' => array( 'pcp_layout_preset|show_post_pagination', 'not-any|==', 'carousel_layout|true', true ),
						// 'dependency' => array( 'pcp_layout_preset', 'not-any', 'grid_layout', 'true' ),
					),
					array(
						'id'         => 'pcp_pagination_btn_color',
						'type'       => 'color_group',
						'title'      => __( 'Pagination  Color', 'post-carousel' ),
						'subtitle'   => __( 'Set Pagination color', 'post-carousel' ),
						'options'    => array(
							'text_color'        => __( 'Text Color', 'post-carousel' ),
							'text_acolor'       => __( 'Text Active Color', 'post-carousel' ),
							'border_color'      => __( 'Border Color', 'post-carousel' ),
							'border_acolor'     => __( 'Border Active Color', 'post-carousel' ),
							'background'        => __( 'Background', 'post-carousel' ),
							'active_background' => __( 'Active BG', 'post-carousel' ),
						),
						'default'    => array(
							'text_color'        => '#5e5e5e',
							'text_acolor'       => '#ffffff',
							'border_color'      => '#bbbbbb',
							'border_acolor'     => '#e1624b',
							'background'        => '#ffffff',
							'active_background' => '#e1624b',
						),
						'dependency' => array( 'pcp_layout_preset|show_post_pagination|post_pagination_type', '!=|==|any', 'carousel_layout|true|ajax_pagination,no_ajax', true ),
					),
					array(
						'id'         => 'post_per_page',
						'type'       => 'spinner',
						'title'      => __( 'Items Per Page', 'post-carousel' ),
						'subtitle'   => __( 'Set number of items to show per page.', 'post-carousel' ),
						'help'       => __( 'This value should be lesser than that <strong> Limit </strong> from <strong>Filter Content  </strong> tab.', 'post-carousel' ),
						'default'    => 12,
						'dependency' => array( 'pcp_layout_preset|show_post_pagination', '==|==', 'grid_layout|true' ),
					),

					array(
						'id'         => 'show_preloader',
						'type'       => 'switcher',
						'title'      => __( 'Preloader', 'post-carousel' ),
						'subtitle'   => __( 'Items will be hidden until page load completed.', 'post-carousel' ),
						'text_on'    => __( 'Enabled', 'post-carousel' ),
						'text_off'   => __( 'Disabled', 'post-carousel' ),
						'text_width' => 94,
						'default'    => false,
					),
				), // End of fields array.
			)
		); // Display settings section end.
	}
}
