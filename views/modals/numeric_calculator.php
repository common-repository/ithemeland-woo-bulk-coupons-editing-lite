<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-modal" id="wccbel-modal-numeric-calculator">
    <div class="wccbel-modal-container">
        <div class="wccbel-modal-box wccbel-modal-box-sm">
            <div class="wccbel-modal-content">
                <div class="wccbel-modal-title">
                    <h2><?php esc_html_e('Calculator', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?> - <span id="wccbel-modal-numeric-calculator-item-title" class="wccbel-modal-product-title"></span></h2>
                    <button type="button" class="wccbel-modal-close" data-toggle="modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-modal-body">
                    <div class="wccbel-wrap">
                        <select id="wccbel-numeric-calculator-operator" title="<?php esc_html_e('Select Operator', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>">
                            <option value="+">+</option>
                            <option value="-">-</option>
                            <option value="replace"><?php esc_html_e('replace', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></option>
                        </select>
                        <input type="number" placeholder="<?php esc_html_e('Enter Value ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>" id="wccbel-numeric-calculator-value" title="<?php esc_html_e('Value', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>">
                    </div>
                </div>
                <div class="wccbel-modal-footer">
                    <button type="button" data-item-id="" data-field="" data-field-type="" data-toggle="modal-close" class="wccbel-button wccbel-button-blue wccbel-edit-action-numeric-calculator">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>