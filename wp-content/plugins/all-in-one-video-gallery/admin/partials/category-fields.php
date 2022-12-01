<?php

/**
 * Categories: Custom Fields.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */
?>

<?php if ( 'add' == $form ) : ?>
    <div class="form-field term-image_id-group">
        <label for="aiovg-categories-image-id"><?php esc_html_e( 'Image', 'all-in-one-video-gallery' ); ?></label>
        <input type="hidden" name="image_id" id="aiovg-categories-image-id" />
        <div id="aiovg-categories-image-wrapper"></div>
        <p>
            <input type="button" id="aiovg-categories-upload-image" class="button button-secondary" value="<?php esc_attr_e( 'Add Image', 'all-in-one-video-gallery' ); ?>" />
            <input type="button" id="aiovg-categories-remove-image" class="button button-secondary" value="<?php esc_attr_e( 'Remove Image', 'all-in-one-video-gallery' ); ?>" style="display: none;" />
        </p>
    </div>

    <div class="form-field term-exclude_search_form-group">
        <label>
			<input type="checkbox" name="exclude_search_form" id="aiovg-categories-exclude-search-form" value="1" />
            <?php esc_html_e( 'Exclude in Search Form', 'all-in-one-video-gallery' ); ?>			
		</label>
        <p><?php esc_html_e( 'Exclude this category in the front-end search form.', 'all-in-one-video-gallery' ); ?></p>
    </div>
<?php elseif ( 'edit' == $form ) : ?>
    <tr class="form-field term-image_id-wrap">
        <th scope="row">
            <label for="aiovg-categories-image-id"><?php esc_html_e( 'Image', 'all-in-one-video-gallery' ); ?></label>
        </th>
        <td>
            <input type="hidden" name="image_id" id="aiovg-categories-image-id" value="<?php echo esc_attr( $image_id ); ?>" />
            <div id="aiovg-categories-image-wrapper">
				<?php if ( $image_url ) : ?>
                    <img src="<?php echo esc_url( $image_url ); ?>" />
                <?php endif; ?>
            </div>
            <p>
                <input type="button" id="aiovg-categories-upload-image" class="button button-secondary" value="<?php esc_attr_e( 'Add Image', 'all-in-one-video-gallery' ); ?>" <?php if ( $image_url ) echo 'style="display: none;"'; ?>/>
                <input type="button" id="aiovg-categories-remove-image" class="button button-secondary" value="<?php esc_attr_e( 'Remove Image', 'all-in-one-video-gallery' ); ?>" <?php if ( ! $image_url ) echo 'style="display: none;"'; ?>/>
            </p>
        </td>
    </tr>
    <tr class="form-field term-exclude_search_form-wrap">
        <th scope="row">
            <label for="aiovg-exclude_search_form"><?php esc_html_e( 'Exclude in Search Form', 'all-in-one-video-gallery' ); ?></label>
        </th>
        <td>
            <label>
                <input type="checkbox" name="exclude_search_form" id="aiovg-categories-exclude-search-form" value="1" <?php checked( $exclude_search_form, 1 ); ?> />
                <?php esc_html_e( 'Exclude this category in the front-end search form.', 'all-in-one-video-gallery' ); ?>		
            </label>
        </td>
    </tr>
<?php endif;
// Add a nonce field
wp_nonce_field( 'aiovg_save_category_fields', 'aiovg_category_fields_nonce' );