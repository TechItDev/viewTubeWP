<?php
if( !defined('ABSPATH') ) exit;
if( !function_exists( 'videotube_core_home_4columns_featured_widget' ) ){
	function videotube_core_home_4columns_featured_widget($data) {
		$template               = array();
		$template['name']       = __( 'Homepage 4 Columns and Featured Widget', 'videotube-core' );
		$template['custom_class'] = 'videotube_core_home_v5_1col_right_sidebar';
		$template['content']    = <<<CONTENT
[vc_row full_width="stretch_row_content_no_spaces" css=".vc_custom_1591957250294{margin-top: -30px !important;}"][vc_column][videotube_core_vc_featured_videos auto="on" video_shows="12" rows="1" columns="3" tablet_columns="3" mobile_columns="1" title="Featured Videos" video_tag="featuredvid"][/vc_column][/vc_row][vc_row][vc_column][videotube type="widget" carousel="on" show="16" id="video-widget-1037" rows="2" columns="4" tablet_columns="2" mobile_columns="1" orderby="views" title="Most Viewed Videos" icon="fas fa-play"][/vc_column][/vc_row][vc_row][vc_column][videotube show="8" id="video-widget-1714" columns="4" tablet_columns="2" mobile_columns="1" navigation="on" title="Latest Videos" icon="fab fa-youtube"][/vc_column][/vc_row]

CONTENT;
		array_unshift($data, $template);
		return $data;
	}
	add_filter( 'vc_load_default_templates', 'videotube_core_home_4columns_featured_widget', 70, 1 );
}