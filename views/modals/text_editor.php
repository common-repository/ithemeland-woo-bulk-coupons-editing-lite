<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-modal" id="wccbel-modal-text-editor">
    <div class="wccbel-modal-container">
        <div class="wccbel-modal-box wccbel-modal-box-lg">
            <div class="wccbel-modal-content">
                <div class="wccbel-modal-title">
                    <h2><?php esc_html_e('Content Edit', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?> - <span id="wccbel-modal-text-editor-item-title" class="wccbel-modal-item-title"></span></h2>
                    <button type="button" class="wccbel-modal-close" data-toggle="modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-modal-body">
                    <div class="wccbel-wrap">
                        <?php wp_editor("", 'wccbel-text-editor'); ?>
                    </div>
                </div>
                <div class="wccbel-modal-footer">
                    <button type="button" data-field="" data-item-id="" data-content-type="textarea" id="wccbel-text-editor-apply" class="wccbel-button wccbel-button-blue wccbel-edit-action-with-button" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>