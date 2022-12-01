<?php if( ! defined('ABSPATH') ) exit;?>
<?php
if ( post_password_required() ) {
	return;
}
?>
<div class="comments">
<?php 
	$commenter = wp_get_current_commenter();
	$required_text = null;
	$args = array(
	  'id_form'           	=> 'commentform',
	  'id_submit'         	=> 'submit',
	  'class_submit'		=>	'btn-1 btn btn-primary btn-sm px-3',
	  'title_reply'       	=> __( 'Add your comment','videotube' ),
	  'title_reply_to'    	=> __( 'Leave a Reply to %s','videotube' ),
	  'cancel_reply_link' 	=> __( 'Cancel Reply','videotube' ),
	  'label_submit'      	=> __( 'Submit','videotube' ),
	  'submit_button'		=>	'<button name="%1$s" type="submit" id="%2$s" class="%3$s" />%4$s</button>',
	  'comment_field'		=>	'
		  <div class="form-group">
			<textarea placeholder="'.esc_html__( 'Leave your comment', 'videotube' ).'" required="required" class="autosize form-control form-control-sm" aria-required="true" id="comment" name="comment" rows="6"></textarea>
		  </div>
	  ',
	  'must_log_in' => '<p class="must-log-in">' .
		sprintf(
		  __( 'You must be <a href="%s">logged in</a> to post a comment.','videotube' ),
		  wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
		) . '</p>',
	
	  'logged_in_as' => '<p class="logged-in-as">' .
		sprintf(
		__( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>','videotube' ),
		  admin_url( 'profile.php' ),
		  $user_identity,
		  wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) )
		) . '</p>',
	
	  'comment_notes_before' => '<p class="comment-notes text-muted">' .
		__( 'Your email address will not be published.','videotube' ) . ( $req ? $required_text : '' ) .
		'</p>',
	
	  'comment_notes_after' => null,
	
	  'fields' => apply_filters( 'comment_form_default_fields', array(
		'author'	=>	'
		  <div class="row row-5"><div class="col-lg-4"><div class="form-group">
			<input required="required" type="text" class="form-control form-control-sm" id="author" name="author" placeholder="'.__('Enter name','videotube').'" value="' . esc_attr( $commenter['comment_author'] ) .'">
		  </div></div>
		',
		'email'	=>	'<div class="col-lg-4">
		  <div class="form-group">
			<input required="required" type="email" class="form-control form-control-sm" id="email" name="email" placeholder="'.__('Enter Email','videotube').'" value="' . esc_attr(  $commenter['comment_author_email'] ) .'">
		  </div></div>
		',
		'url'	=>	'<div class="col-lg-4">
		  <div class="form-group">
			<input type="text" class="form-control form-control-sm" id="url" name="url" placeholder="'.__('Enter Website','videotube').'" value="' . esc_attr( $commenter['comment_author_url'] ) .'">
		  </div></div></div>
		'
		)
	  ),
	);

	comment_form($args);
	?>

	<?php if( get_comments_number() > 0 ):?>
		<div class="section-header border-bottom pb-2">
			<h3 class="comment-count"><?php comments_number(); ?></h3>
		</div>
	<?php endif;?>

	<ul id="comment-list" class="list-unstyled comment-list">
		<?php wp_list_comments( videotube_comments_list_args() );?>
    </ul>

	<?php
		$comment_pagination = paginate_comments_links(
			array(
				'echo'      => false,
				'end_size'  => 0,
				'mid_size'  => 0,
				'next_text' => __( 'Newer Comments', 'videotube' ) . ' <span aria-hidden="true">&rarr;</span>',
				'prev_text' => '<span aria-hidden="true">&larr;</span> ' . __( 'Older Comments', 'videotube' ),
			)
		);

		if ( $comment_pagination ) {
			$pagination_classes = '';

			// If we're only showing the "Next" link, add a class indicating so.
			if ( false === strpos( $comment_pagination, 'prev page-numbers' ) ) {
				$pagination_classes = ' only-next';
			}
			?>

			<nav class="comments-pagination pagination<?php echo esc_attr($pagination_classes); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static output ?>" aria-label="<?php esc_attr_e( 'Comments', 'videotube' ); ?>">
				<?php echo wp_kses_post( $comment_pagination ); ?>
			</nav>

			<?php
		}
	?>    

	<?php if ( ! comments_open() ) : ?>
		<p class="no-comments text-info"><?php _e( 'Comments are closed.', 'videotube' ); ?></p>
	<?php endif; ?>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<?php

		global $videotube;

		$button_classes = array(
			'btn',
			'btn-outline-secondary',
			'btn-sm',
			'btn-block',
			'mx-auto',
			'w-auto',
			'px-5'
		);

		if( ! isset( $videotube['load_comments'] ) ){
			$videotube['load_comments'] = 'click';
		}

		$button_classes[] = 'load-comments-' . sanitize_html_class( $videotube['load_comments'] );

		printf(
			'<button data-post-id="%1$s" data-comment-paged="%2$s" id="load-comments" class="%3$s" data-text-loading="%4$s" data-text-load="%5$s">%5$s</button>',
			esc_attr( get_the_ID() ),
			esc_attr( get_query_var( 'cpage', 1 ) ),
			esc_attr( join(' ', $button_classes ) ),
			esc_html__( 'loading', 'videotube' ),
			esc_html__( 'load more', 'videotube' )
		);?>
	<?php endif; // check for comment navigation ?>	
</div>