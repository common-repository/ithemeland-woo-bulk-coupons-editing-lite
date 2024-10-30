<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-modal" id="wccbel-modal-image">
    <div class="wccbel-modal-container">
        <div class="wccbel-modal-box wccbel-modal-box-sm">
            <div class="wccbel-modal-content">
                <div class="wccbel-modal-title">
                    <h2><?php esc_html_e('Image Edit', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?> - <span id="wccbel-modal-image-item-title" class="wccbel-modal-item-title"></span></h2>
                    <button type="button" class="wccbel-modal-close" data-toggle="modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-modal-body">
                    <div class="wccbel-wrap">
                        <div class="wccbel-inline-image-edit">
                            <button type="button" class="wccbel-inline-uploader wccbel-open-uploader" data-target="inline-edit" data-type="single" data-id="" data-item-id="">
                                <i class="wccbel-icon-pencil"></i>
                            </button>
                            <div class="wccbel-inline-image-preview" data-image-preview-id=""></div>
                            <input type="hidden" id="" class="wccbel-image-preview-hidden-input">
                        </div>
                    </div>
                </div>
                <div class="wccbel-modal-footer">
                    <button type="button" data-item-id="" data-field="" data-button-type="save" data-content-type="image" class="wccbel-button wccbel-button-blue wccbel-edit-action-with-button" data-toggle="modal-close" data-image-url="" data-image-id="">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                    </button>
                    <button type="button" class="wccbel-button wccbel-button-red wccbel-edit-action-with-button" data-button-type="remove" data-item-id="" data-image-url="<?php echo esc_url(WCCBEL_IMAGES_URL . "no-image.png"); ?>" data-field="" data-image-id="0" data-toggle="modal-close">
                        <?php esc_html_e('Remove Image', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>