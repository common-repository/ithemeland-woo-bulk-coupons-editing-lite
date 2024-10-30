<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-modal" id="wccbel-modal-item-duplicate">
    <div class="wccbel-modal-container">
        <div class="wccbel-modal-box wccbel-modal-box-sm">
            <div class="wccbel-modal-content">
                <div class="wccbel-modal-title">
                    <h2><?php esc_html_e('Duplicate', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></h2>
                    <button type="button" class="wccbel-modal-close" data-toggle="modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-modal-body">
                    <div class="wccbel-wrap">
                        <div class="wccbel-form-group">
                            <label class="wccbel-label-big" for="wccbel-bulk-edit-duplicate-number">
                                <?php esc_html_e('Enter how many item(s) to Duplicate!', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                            </label>
                            <input type="number" class="wccbel-input-numeric-sm" id="wccbel-bulk-edit-duplicate-number" value="1" placeholder="<?php esc_html_e('Number ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>">
                        </div>
                    </div>
                </div>
                <div class="wccbel-modal-footer">
                    <button type="button" class="wccbel-button wccbel-button-blue" id="wccbel-bulk-edit-duplicate-start">
                        <?php esc_html_e('Start Duplicate', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>