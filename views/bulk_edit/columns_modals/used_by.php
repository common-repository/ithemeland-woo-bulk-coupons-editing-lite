<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-modal" id="wccbel-modal-used-by">
    <div class="wccbel-modal-container">
        <div class="wccbel-modal-box wccbel-modal-box-sm">
            <div class="wccbel-modal-content">
                <div class="wccbel-modal-title">
                    <h2><?php esc_html_e('Used by', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?> <span id="wccbel-modal-used-by-item-title" class="wccbel-modal-item-title"></span></h2>
                    <button type="button" class="wccbel-modal-close" data-toggle="modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-modal-body">
                    <div class="wccbel-wrap">
                        <div class="wccbel-col-full" id="wccbel-modal-coupon-used-by-items">

                        </div>
                    </div>
                </div>
                <div class="wccbel-modal-footer">
                    <button type="button" class="wccbel-button wccbel-button-gray wccbel-float-right" data-toggle="modal-close">
                        <?php esc_html_e('Close', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>