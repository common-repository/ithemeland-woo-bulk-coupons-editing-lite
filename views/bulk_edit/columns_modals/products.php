<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-modal" id="wccbel-modal-products">
    <div class="wccbel-modal-container">
        <div class="wccbel-modal-box wccbel-modal-box-sm">
            <div class="wccbel-modal-content">
                <div class="wccbel-modal-title">
                    <h2><?php esc_html_e('Products', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?> <span id="wccbel-modal-products-item-title" class="wccbel-modal-item-title"></span></h2>
                    <button type="button" class="wccbel-modal-close" data-toggle="modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-modal-body">
                    <div class="wccbel-wrap">
                        <div class="wccbel-col-full">
                            <label for="wccbel-modal-products-items"><strong><?php esc_html_e('Select Products', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></strong></label>
                            <select id="wccbel-modal-products-items" class="wccbel-select2-products" data-placeholder="<?php esc_attr_e('Select Products', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?> ..." multiple></select>
                        </div>
                    </div>
                </div>
                <div class="wccbel-modal-footer">
                    <button type="button" class="wccbel-button wccbel-button-blue wccbel-float-left wccbel-modal-products-save-changes" data-item-id="" data-field="" data-toggle="modal-close" data-update-type="woocommerce_field">
                        <?php esc_html_e('Save Changes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                    </button>
                    <button type="button" class="wccbel-button wccbel-button-gray wccbel-float-right" data-toggle="modal-close">
                        <?php esc_html_e('Close', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>