<?php
/*
Plugin Name: WP Easy Review
Plugin URI: https://1.envato.market/DdaAG
Description: A simple post review plugin
Version: 1.3
Author: phpface
Author URI: http://themeforest.net/user/phpface?ref=phpface
License: Themeforest Licence
License URI: http://themeforest.net/licenses
Text Domain: wp-easy-review
*/
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'WP_Easy_Review' ) ){
	
	class WP_Easy_Review {
		
		/**
		 * Holds the plugin url
		 * @var string
		 */
		var $plugin_dir_url	=	'';
		
		/**
		 * Holds the prefix name
		 * @var string
		 */
		var $prefix		=	'wp_easy_review_';		
		
		/**
		 * Holds the form field name
		 * @var string
		 */
		var $form		=	'wp_easy_review_form';
		
		/**
		 * Holds the nonce name
		 * @var string
		 */
		var $nonce		=	'wp_easy_review_nonce';
		
		/**
		 * Holds the action name
		 * @var string
		 */
		var $action		=	'wp_easy_review_action';

		/**
		 * Holds the option key,
		 * @var string
		 */
		var $options	=	'wp_easy_review_option';
		
		/**
		 * Holds the plugin setting.
		 * @var array
		 */
		var $settings = array();
		
		function __construct() {
			
			$this->plugin_dir_url = plugin_dir_url(__FILE__);
			
			$this->settings	=	apply_filters( 'wp-easy-review_metaboxes' , array(
				'screen'	=>	array( 'post' ),
				'context'	=>	'advanced',
				'priority'	=>	'high',
				'content_priority'	=>	10
			));
			
			add_action('plugins_loaded', array( $this , 'plugins_loaded' ) );
			add_action( 'wp_enqueue_scripts' , array( $this , 'wp_enqueue_scripts' ), 9999 );
			add_action( 'admin_enqueue_scripts', array( $this , 'wp_admin_enqueue_scripts' ) );
			add_shortcode( 'wp_easy_review' , array( $this , 'wp_easy_review' ) );
			add_action( 'add_meta_boxes', array( $this, 'review_box' ) );
			add_action( 'save_post', array( $this, 'review_save' ) );
			
			add_action( 'the_content' , array( $this , 'the_content' ), $this->settings['content_priority'] , 1 );
			
			add_filter( 'score_format' , array( $this , 'set_score_format' ), 10, 1 );
			
		}
		
		/**
		 * Languages
		 * @since WP Easy Review 1.0
		 */
		function plugins_loaded() {
			load_plugin_textdomain( 'wp-easy-review' , false , dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}
		
		function wp_enqueue_scripts(){
			wp_enqueue_script( $this->prefix . 'scripts', $this->plugin_dir_url . 'scripts.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_style( $this->prefix . 'style', $this->plugin_dir_url . 'style.css', array(), '1.3' );
		}
		
		function wp_admin_enqueue_scripts(){
			wp_enqueue_style( $this->prefix . 'admin-style', $this->plugin_dir_url . 'admin-style.css', array(), '1.0' );
			wp_enqueue_script( $this->prefix . 'admin-script', $this->plugin_dir_url . 'admin-script.js', array( 'jquery' ), '1.0', true );
			
			$wp_easy_review_js_vars = array(
				'form'			=>	$this->form,
				'name'			=>	esc_html__('Name','safir'),
				'score'			=>	esc_html__('Score','safir'),
				'addtext'		=>	esc_html__('Add','safir'),
				'deletetext'	=>	esc_html__('Delete','safir')
			);
			$wp_easy_review_js_vars	=	apply_filters( 'wp_easy_review_js_vars' , $wp_easy_review_js_vars );
			
			wp_localize_script( $this->prefix . 'admin-script', 'wp_easy_review_js_vars' , $wp_easy_review_js_vars );
		}
		
		/**
		 * Attach the review into the end of post content.
		 * @param unknown_type $content
		 */
		function the_content( $content ){
			global $post;
			
			if( is_single() || is_page() ){
				
				if(  post_password_required( $post ) ){
					return $content;
				}				
				
				if( $this->has_review( $post->ID ) && ! has_shortcode( $post->post_content , 'wp_easy_review') ){
					if( apply_filters( 'wp_easy_review_head' , false ) === true ){
						return $this->wp_easy_review( array( 'id' => $post->ID ) ) . $content;
					}
					else{
						return $content . $this->wp_easy_review( array( 'id' => $post->ID ) );
					}
				}				
			}

			return $content;
		}
		
		/**
		 * Add wp_easy_review shortcode
		 * @param unknown_type $atts
		 * @param unknown_type $content
		 */
		function wp_easy_review( $atts = null, $content = null ){
			$output = '';
			$is_empty_score = true;
			$post_id = '';

			global $post;
			
			extract(shortcode_atts(array(
				'id'	=>	''
			), $atts));
			
			if( ! empty( $id ) ){
				$post_id = $id;
			}
			
			if( empty( $post_id ) && ! empty( $post->ID ) ){
				$post_id = $post->ID;
			}

			$review = get_post_meta( $post_id , $this->options , true );
			if( empty( $review ) ){
				return;
			}

			$review_data = get_post_meta( $post_id, $this->options, true );
			
			$review_heading = isset( $review_data['review']['review_heading'] ) ? esc_attr( $review_data['review']['review_heading'] ) : '';
			$summary_text = isset( $review_data['review']['summary_text'] ) ? esc_attr( $review_data['review']['summary_text'] ) : '';

			$review_criterias = isset( $review_data['review']['review_criterias'] ) ? $review_data['review']['review_criterias'] : null;		

			$output .= '<div class="post-review">';
			
			if( ! empty( $review_heading ) || ! empty( $summary_text ) ){
				$output .= '
					<div class="review-header">
						<div class="review-summary">';
							if( !empty( $review_heading ) ){
								$output .= '<h4>'. esc_html( $review_heading ) .'</h4>';
							}
							if( !empty( $summary_text ) ){
								$output .= '<p>'. $summary_text .'</p>';
							}
						$output .= '
						</div>
						<div class="review-score">
							<span data-score="'. esc_attr( apply_filters( 'score_format' , $this->get_total_score( $post_id ) ) ) .'">'. apply_filters( 'score_format' , $this->get_total_score( $post_id ) ) .'</span>
						</div>
					</div>
				';
				if( is_array( $review_criterias ) && !empty( $review_criterias ) ){
					if( isset( $review_criterias['name'] ) && is_array( $review_criterias['name'] ) ){
						$output .= '<div class="review-criteria">';
						for ($i = 0; $i < count( $review_criterias['name'] ); $i++) {
							if( !empty( $review_criterias['name'][$i] ) && !empty( $review_criterias['score'][$i] ) && absint( $review_criterias['score'][$i] ) && $review_criterias['score'][$i] >=0 && $review_criterias['score'][$i] <=100 ){
								$is_empty_score = false;
								// esc_attr( $review_criterias['score'][$i] )
								$output .= '
									<div class="criteria">
										<div class="thescore" data-score="'. esc_attr( $review_criterias['score'][$i] ) .'" style="width:0%;">
											<span class="criteria-name">'. esc_html( $review_criterias['name'][$i] ) .'</span>
										</div>
										<span class="criteria-score">'.apply_filters( 'score_format' , $review_criterias['score'][$i] ).'</span>
									</div>
								';
							}
						}
						$output .= '</div>';
					}
				}
			}
			$output .= '</div>';
			
			if( $is_empty_score !== true ){
				return do_shortcode( $output );
			}
		}
		
		/**
		 * @param unknown_type $post_id
		 * @return string
		 */
		function has_review( $post_id ){
			if( empty( $post_id ) ){
				return;
			}
			return $this->wp_easy_review( array( 'id' => $post_id ) );
		}
		
		/**
		 * Get total score
		 * @param unknown_type $post_id
		 * @return void|boolean|number
		 */
		function get_total_score( $post_id ) {
			if( ! $post_id || ! get_post_meta( $post_id, $this->options , true )){
				return false;
			}
			$total = null;
			$is_empty_score = 0;
		
			$review_data = get_post_meta( $post_id, $this->options , true );
			$review_criterias = isset( $review_data['review']['review_criterias'] ) ? $review_data['review']['review_criterias'] : null;
			if( is_array( $review_criterias ) && !empty( $review_criterias ) ){
				if( isset( $review_criterias['name'] ) && is_array( $review_criterias['name'] ) ){
					for ($i = 0; $i < count( $review_criterias['name'] ); $i++) {
						if( !empty( $review_criterias['name'][$i] ) && !empty( $review_criterias['score'][$i] ) && absint( $review_criterias['score'][$i] ) ){
							if( $review_criterias['score'][$i] >= 0 && $review_criterias['score'][$i] <= 100 ){
								$is_empty_score++;
								$total += (int)$review_criterias['score'][$i];
							}
						}
					}
				}
			}
			if( $is_empty_score > 0 ){
				return ceil( $total/$is_empty_score );
			}
			return;
		}
		
		/**
		 * @param unknown_type $number
		 * @return void|number|Ambigous <unknown, string>
		 */
		function set_score_format( $number ) {
			if( empty( $number ) )
				return;
			if( $number%10 == 0 ){
				return $number/10;
			}
			else{
				return function_exists( 'number_format' ) ? number_format( $number/10, '1' ) : $number;
			}
		}		
		
		function review_box(){

			$this->settings = apply_filters( 'wp-easy-review_metaboxes_pre', $this->settings );

			add_meta_box( $this->prefix . 'review' , esc_html__( 'Review' , 'wp-easy-review' ), array( $this, 'review_box_callback' ), $this->settings['screen'] , $this->settings['context'] , $this->settings['priority'] );
		}
		
		/**
		 * @param object $post
		 *  @since WP Easy Review 1.0
		 */
		function review_box_callback( $post ) {
			$output = '';
			
			$is_empty_score = true;
			
			wp_nonce_field( $this->action, $this->nonce );		
				
			$review = get_post_meta( $post->ID, $this->options , true );
			
			$review_heading = isset( $review['review']['review_heading'] ) ? esc_attr( $review['review']['review_heading'] ) : '';
			$summary_text = isset( $review['review']['summary_text'] ) ? esc_attr( $review['review']['summary_text'] ) : '';
		
			if( shortcode_exists( 'wp_easy_review' ) && isset( $_GET['post'] ) ){
				$output .= sprintf( esc_html__('Shortcode: %s','safir'), '<strong>[wp_easy_review post_id="'. $_GET['post'] .'"]</strong>' );
			}
			
			$output .= '
				<table class="form-table review">
					<tbody>
						<tr>
							<th scope="row"><label for="review_heading">'.esc_html__('Review Heading','safir').'</label></th>
							<td>
								<input value="'.$review_heading.'" name="'. $this->form .'[review][review_heading]" type="text" id="review_heading" class="regular-text">
								<p class="description">'.esc_attr( esc_html__('Short review heading (e.g.Excellent!)','safir') ).'</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="summary_text">'.esc_html__('Summary Text','safir').'</label></th>
							<td>';
								$output .= '<textarea name="'. esc_attr( $this->form .'[review][summary_text]' ) .'" id="summary_text">'. esc_textarea( $summary_text ) .'</textarea>
							</td>
						</tr>
						
						<tr>
							<table class="form-table review_criterias" id="wp_easy_review_item_table">
								<tr>
									<th colspan=5><h4>'.__('Review Criterias','safir').'</h4></th>
								</tr>
								<tbody class="review_criterias_body">';
									$review_criterias = isset( $review['review']['review_criterias'] ) ? $review['review']['review_criterias'] : null;
									if( is_array( $review_criterias ) && !empty( $review_criterias ) ):
										if( isset( $review_criterias['name'] ) && is_array( $review_criterias['name'] ) ):
											for ($i = 0; $i < count( $review_criterias['name'] ); $i++) {
												
												if( !empty( $review_criterias['name'][$i] ) && !empty( $review_criterias['score'][$i] ) && absint( $review_criterias['score'][$i] ) && $review_criterias['score'][$i] >= 0 && $review_criterias['score'][$i] <=100  ):
													$is_empty_score = false;
													$output .= '<tr class="review_criterias_item">';
													$output .= '
														<td scope="row">
															<label for="review_criterias_name">'.esc_html__('Name','safir').'</label>
														</td>
														<td>
															<input placeholder="'. esc_attr( esc_html__( 'Gameplay', 'safir' ) ) .'" value="'.esc_attr( $review_criterias['name'][$i] ).'" name="'. $this->form .'[review][review_criterias][name][]" type="text" id="review_criterias_name" class="regular-text">
														</td>
													';
													$output .= '
														<td scope="row"><label for="review_criterias_score">'.esc_html__('Score','safir').'</label></td>
														<td>
															<input placeholder="85" value="'.esc_attr( $review_criterias['score'][$i] ).'" name="'. $this->form .'[review][review_criterias][score][]" type="text" id="review_criterias_score" class="regular-text">
														</td>
														<td>
															<input class="button addfield" type="button" name="addfield" value="'.esc_attr( esc_html__('Add','safir') ).'">
															<input class="button deletefield" type="button" name="deletefield" value="'.esc_attr( esc_html__('Delete','safir') ).'">
														</td>
													';
													$output .= '</tr>';
												
												endif; // end loop.
											}	
										endif;
									endif;
									if( $is_empty_score ):
									
										$output .= '<tr class="review_criterias_item">';
											$output .= '
												<td scope="row">
													<label for="review_criterias_name">'.esc_html__('Name','safir').'</label>
												</td>
												<td>
													<input placeholder="'. esc_attr( esc_html__( 'Gameplay', 'safir' ) ) .'" name="'. $this->form .'[review][review_criterias][name][]" type="text" id="review_criterias_name" class="regular-text">
												</td>
											';
											$output .= '
												<td scope="row"><label for="review_criterias_score">'.esc_html__('Score','safir').'</label></td>
												<td>
													<input placeholder="85" name="'. $this->form . '[review][review_criterias][score][]" type="text" id="review_criterias_score" class="regular-text">
												</td>
												<td colspan="4">
													<input class="button addfield" type="button" name="addfield" value="'.esc_attr( esc_html__('Add','safir') ).'">
													<input class="button deletefield" type="button" name="deletefield" value="'.esc_attr( esc_html__('Delete','safir') ).'">
												</td>
											';
										$output .= '</tr>';												
									
									endif;
									$output .= '
								</tbody>
							</table>
						</tr>
					</tbody>
				</table>
			';
			
			print $output;
		}
		
		/**
		 * Save the review.
		 * @param int $post_id
		 */
		function review_save( $post_id ) {

			// Check if our nonce is set.
			if ( ! isset( $_POST[ $this->nonce ] ) ){
				return;
			}
			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST[ $this->nonce ], $this->action ) ){
				return;
			}
			// Check the user's permissions.
			if ( ! current_user_can( 'edit_post', $post_id ) || ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
			/* OK, it's safe for us to save the data now. */
			// Make sure that it is set.
			if( isset( $_POST[ $this->form ] ) ){

				update_post_meta( $post_id , $this->options , $_POST[ $this->form ] );
			}
		}
	}
	
	$wp_easy_review = new WP_Easy_Review();
}