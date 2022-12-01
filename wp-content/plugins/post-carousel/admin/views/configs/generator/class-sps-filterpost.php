<?php
/**
 * The Filter Post Meta-box configurations.
 *
 * @package Smart_Post_Show
 * @subpackage Smart_Post_Show/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access pages directly.

/**
 * The Filter post building class.
 */
class SPS_FilterPost {

	/**
	 * Filter Post section metabox.
	 *
	 * @param string $prefix The metabox key.
	 * @return void
	 */
	public static function section( $prefix ) {
		SP_PC::createSection(
			$prefix,
			array(
				'title'  => __( 'Filter content', 'post-carousel' ),
				'icon'   => 'fa fa-filter',
				'fields' => array(
					array(
						'id'            => 'pcp_select_post_type',
						'type'          => 'select',
						'title'         => __( 'Post Type(s)', 'post-carousel' ),
						'subtitle'      => __( 'Select post type(s).', 'post-carousel' ),
						'desc'          => __( 'To filter custom post type (product, portfolio, event...), <a href="https://smartpostshow.com/" target="_blank"><strong>Upgrade To Pro!</strong></a>', 'post-carousel' ),
						'options'       => array(
							'post'               => __( 'Posts', 'post-carousel' ),
							'page'               => __( 'Pages', 'post-carousel' ),
							'product'            => __( 'Products', 'post-carousel' ),
							'multiple_post_type' => __( 'Multiple Post Types (Pro)', 'post-carousel' ),

						),
						'multiple_type' => true,
						'class'         => 'sp_pcp_post_type',
						'default'       => 'post',
						'attributes'    => array(
							'style' => 'min-width: 150px;',
						),
					),
					array(
						'id'         => 'pcp_select_filter_product_type',
						'type'       => 'select',
						'title'      => __( 'Filter Products', 'post-carousel' ),
						'subtitle'   => __( 'Select a product type for filtering.', 'post-carousel' ),
						'options'    => array(
							'recent'       => __( 'Recent', 'post-carousel' ),
							'featured'     => __( 'Featured (Pro)', 'post-carousel' ),
							'on_sale'      => __( 'On Sale (Pro)', 'post-carousel' ),
							'best_selling' => __( 'Best Selling (Pro)', 'post-carousel' ),
							'top_rated'    => __( 'Top Rated (Pro)', 'post-carousel' ),
							'out_of_stock' => __( 'Out of Stock (Pro)', 'post-carousel' ),
							'none'         => __( 'None of Above (Pro)', 'post-carousel' ),
						),
						'default'    => 'Recent',
						'dependency' => array( 'pcp_select_post_type', '==', 'product' ),
					),
					array(
						'type'    => 'subheading',
						'content' => __( 'Common Filtering', 'post-carousel' ),
					),
					array(
						'id'          => 'pcp_include_only_posts',
						'type'        => 'select',
						'title'       => __( 'Include Only', 'post-carousel' ),
						'subtitle'    => __( 'Enter post IDs, or type to search by title.', 'post-carousel' ),
						'options'     => 'posts',
						'ajax'        => true,
						'sortable'    => true,
						'chosen'      => true,
						'class'       => 'sp_pcp_include_only_posts',
						'multiple'    => true,
						'placeholder' => __( 'Choose posts', 'post-carousel' ),
						'query_args'  => array(
							'cache_results' => false,
							'no_found_rows' => true,
						),
					),
					array(
						'id'       => 'pcp_exclude_post_set',
						'type'     => 'fieldset',
						'title'    => __( 'Exclude', 'post-carousel' ),
						'subtitle' => __( 'Enter post IDs, or type to search by title.', 'post-carousel' ),
						'class'    => 'sp_pcp_exclude_post_set',
						'fields'   => array(
							array(
								'id'          => 'pcp_exclude_posts',
								'type'        => 'select',
								'options'     => 'posts',
								'chosen'      => true,
								'class'       => 'sp_pcp_exclude_posts',
								'multiple'    => true,
								'ajax'        => true,
								'placeholder' => __( 'Choose posts to exclude', 'post-carousel' ),
								'query_args'  => array(
									'cache_results' => false,
									'no_found_rows' => true,
								),
								'dependency'  => array( 'pcp_include_only_posts', '==', '', true ),
							),
							array(
								'id'      => 'pcp_exclude_too',
								'type'    => 'checkbox',
								'class'   => 'sp_pcp_exclude_too',
								'options' => array(
									'current'            => __( 'Current Post', 'post-carousel' ),
									'password_protected' => __( 'Password Protected Posts', 'post-carousel' ),
									'children'           => __( 'Children Posts', 'post-carousel' ),
								),
							),
						),
					),
					array(
						'id'       => 'pcp_post_limit',
						'title'    => __( 'Limit', 'post-carousel' ),
						'type'     => 'spinner',
						'subtitle' => __( 'Number of total items to display. Leave it empty to show all found items.', 'post-carousel' ),
						'default'  => '20',
						'min'      => 1,
					),
					array(
						'type'    => 'subheading',
						'content' => __( 'Advanced Filtering', 'post-carousel' ),
					),
					array(
						'id'       => 'pcp_advanced_filter',
						'type'     => 'checkbox',
						'class'    => 'spf_column_2 pcp_advanced_filter',
						'title'    => __( 'Filter by', 'post-carousel' ),
						'subtitle' => __( 'Check the option(s) to filter by.', 'post-carousel' ),
						'options'  => array(
							'taxonomy'     => __( 'Taxonomy', 'post-carousel' ),
							'author'       => __( 'Author', 'post-carousel' ),
							'sortby'       => __( 'Sort By', 'post-carousel' ),
							'custom_field' => __( 'Custom Fields (Pro)', 'post-carousel' ),
							'status'       => __( 'Status', 'post-carousel' ),
							'date'         => __( 'Date (Pro)', 'post-carousel' ),
							'keyword'      => __( 'Keyword', 'post-carousel' ),
						),
					),
					array(
						'id'         => 'pcp_filter_by_taxonomy',
						'type'       => 'accordion',
						'class'      => 'padding-t-0 pcp-opened-accordion',
						'accordions' => array(
							array(
								'title'  => __( 'Taxonomy', 'post-carousel' ),
								'icon'   => 'fa fa-folder-open',
								'fields' => array(
									// The Group Fields.
									array(
										'id'     => 'pcp_taxonomy_and_terms',
										'type'   => 'group',
										'class'  => 'pcp_taxonomy_terms_group pcp_custom_group_design',
										'accordion_title_auto' => true,
										'fields' => array(
											array(
												'id'      => 'pcp_select_taxonomy',
												'type'    => 'select',
												'title'   => __( 'Select Taxonomy', 'post-carousel' ),
												'class'   => 'sp_pcp_post_taxonomy',
												'options' => 'taxonomy',
												'query_args' => array(
													'type' => 'post',
												),
												'attributes' => array(
													'style' => 'width: 200px;',
												),
												'empty_message' => __( 'No taxonomies found.', 'post-carousel' ),
											),
											array(
												'id'       => 'pcp_select_terms',
												'type'     => 'select',
												'title'    => __( 'Choose Term(s)', 'post-carousel' ),
												'help'     => __( 'Choose the taxonomy term(s) to show the posts from.', 'post-carousel' ),
												'options'  => 'terms',
												'class'    => 'sp_pcp_taxonomy_terms',
												'width'    => '300px',
												'multiple' => true,
												'sortable' => true,
												'empty_message' => __( 'No terms found.', 'post-carousel' ),
												'placeholder' => __( 'Select Term(s)', 'post-carousel' ),
												'chosen'   => true,
											),
											array(
												'id'      => 'pcp_taxonomy_term_operator',
												'type'    => 'select',
												'title'   => __( 'Operator', 'post-carousel' ),
												'options' => array(
													'IN'  => __( 'IN', 'post-carousel' ),
													'AND' => __( 'AND', 'post-carousel' ),
													'NOT IN' => __( 'NOT IN', 'post-carousel' ),
												),
												'default' => 'IN',
												'help'    => __( 'IN - Show posts which associate with one or more terms<br>AND - Show posts which match all terms<br>NOT IN - Show posts which don\'t match the terms', 'post-carousel' ),
											),
											array(
												'id'    => 'add_filter_post',
												'class' => 'pcp_disabled',
												'type'  => 'checkbox',
												'title' => __( 'Add to Ajax Live Filters (Pro)', 'post-carousel' ),
											),

										),
									), // Group field end.
									array(
										'id'      => 'pcp_taxonomies_relation',
										'type'    => 'select',
										'title'   => __( 'Relation', 'post-carousel' ),
										'class'   => 'pcp_relate_among_taxonomies',
										'options' => array(
											'AND' => __( 'AND', 'post-carousel' ),
											'OR'  => __( 'OR', 'post-carousel' ),
										),
										'default' => 'AND',
										'help'    => __( 'The logical relationship between/among above taxonomies.', 'post-carousel' ),
									),

								), // Fields array.
							),
						), // Accordions end.
						'dependency' => array( 'pcp_advanced_filter', 'not-any', 'author,sortby,custom_field,status,date,keyword' ),
					),
					array(
						'id'         => 'pcp_filter_by_author',
						'type'       => 'accordion',
						'class'      => 'padding-t-0 pcp-opened-accordion',
						'accordions' => array(
							array(
								'title'  => 'Author',
								'icon'   => 'fa fa-user',
								'fields' => array(
									array(
										'id'      => 'pcp_select_author_by',
										'type'    => 'checkbox',
										'title'   => __( 'Post by Author', 'post-carousel' ),
										'options' => 'users',
									),
									array(
										'id'      => 'pcp_select_author_not_by',
										'type'    => 'checkbox',
										'title'   => __( 'Post Not by Author ', 'post-carousel' ),
										'options' => 'users',
									),
								),
							),
						),
						'dependency' => array( 'pcp_advanced_filter', 'not-any', 'taxonomy,sortby,custom_field,status,date,keyword' ),
					),
					array(
						'id'         => 'pcp_filter_by_order',
						'type'       => 'accordion',
						'class'      => 'padding-t-0 pcp-opened-accordion',
						'accordions' => array(
							array(
								'title'  => 'Sort By',
								'icon'   => 'fa fa-sort',
								'fields' => array(
									array(
										'id'      => 'pcp_select_filter_orderby',
										'type'    => 'select',
										'title'   => __( 'Order by', 'post-carousel' ),
										'options' => array(
											'ID'         => __( 'ID', 'post-carousel' ),
											'title'      => __( 'Title', 'post-carousel' ),
											'date'       => __( 'Date', 'post-carousel' ),
											'modified'   => __( 'Modified date', 'post-carousel' ),
											'post__in'   => __( 'Post in (Drag & Drop) (Pro)', 'post-carousel' ),
											'post_slug'  => __( 'Post slug (Pro)', 'post-carousel' ),
											'post_type'  => __( 'Post type (Pro)', 'post-carousel' ),
											'rand'       => __( 'Random (Pro)', 'post-carousel' ),
											'comment_count' => __( 'Comment count (Pro)', 'post-carousel' ),
											'menu_order' => __( 'Menu order (Pro)', 'post-carousel' ),
											'author'     => __( 'Author (Pro)', 'post-carousel' ),
										),
										'default' => 'date',
									),
									array(
										'id'         => 'pcp_select_filter_order',
										'type'       => 'radio',
										'title'      => __( 'Order', 'post-carousel' ),
										'options'    => array(
											'ASC'  => __( 'Ascending', 'post-carousel' ),
											'DESC' => __( 'Descending', 'post-carousel' ),
										),
										'default'    => 'DESC',
										'dependency' => array( 'pcp_select_filter_orderby', '!=', 'post__in' ),
									),
								),
							),
						),
						'dependency' => array( 'pcp_advanced_filter', 'not-any', 'taxonomy,author,custom_field,status,date,keyword' ),
					),
					array(
						'id'         => 'pcp_filter_by_status',
						'type'       => 'accordion',
						'class'      => 'padding-t-0 pcp-opened-accordion',
						'accordions' => array(
							array(
								'title'  => __( 'Status', 'post-carousel' ),
								'icon'   => 'fa fa-lock',
								'fields' => array(
									array(
										'id'       => 'pcp_select_post_status',
										'type'     => 'select',
										'title'    => __( 'Post Status', 'post-carousel' ),
										'options'  => 'post_statuses',
										'multiple' => true,
										'chosen'   => true,
									),
								),
							),
						),
						'dependency' => array( 'pcp_advanced_filter', 'not-any', 'taxonomy,author,custom_field,sortby,date,keyword' ),
					),
					array(
						'id'         => 'pcp_filter_by_keyword',
						'type'       => 'accordion',
						'class'      => 'padding-t-0 pcp-opened-accordion',
						'accordions' => array(
							array(
								'title'  => __( 'Keyword', 'post-carousel' ),
								'icon'   => 'fa fa-key',
								'fields' => array(
									array(
										'id'      => 'pcp_set_post_keyword',
										'type'    => 'text',
										'title'   => __( 'Type Keyword', 'post-carousel' ),
										'help'    => __( 'Enter keyword(s) for searching the posts.', 'post-carousel' ),
										'options' => 'post_statuses',
									),
								),
							),
						),
						'dependency' => array( 'pcp_advanced_filter', 'not-any', 'taxonomy,author,custom_field,sortby,date,status' ),
					),
				),
			)
		); // Filter settings section end.
	}
}
