<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-modal" id="wccbel-modal-file">
    <div class="wccbel-modal-container">
        <div class="wccbel-modal-box wccbel-modal-box-lg">
            <div class="wccbel-modal-content">
                <div class="wccbel-modal-title">
                    <h2><?php esc_html_e('Select File', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?> - <span id="wccbel-modal-select-file-item-title" class="wccbel-modal-item-title"></span></h2>
                    <button type="button" class="wccbel-modal-close" data-toggle="modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-modal-body">
                    <div class="wccbel-wrap">
                        <div class="wccbel-inline-select-files">
                            <div class="wccbel-modal-select-files-file-item">
                                <input type="text" class="wccbel-inline-edit-file-url wccbel-w60p" id="wccbel-file-url" placeholder="File Url ..." value="">
                                <button type="button" class="wccbel-button wccbel-button-white wccbel-open-uploader wccbel-inline-edit-choose-file" data-type="single" data-target="inline-file-custom-field"><?php esc_html_e('Choose File', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></button>
                                <input type="hidden" id="wccbel-file-id" value="">
                                <button type="button" class="wccbel-button wccbel-button-white" id="wccbel-modal-file-clear"><?php esc_html_e('Clear', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wccbel-modal-footer">
                    <button type="button" id="wccbel-modal-file-apply" data-item-id="" data-field="" data-content-type="file" class="wccbel-button wccbel-button-blue wccbel-edit-action-with-button" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>