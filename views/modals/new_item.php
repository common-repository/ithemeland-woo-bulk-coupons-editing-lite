<?php

use wccbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-modal" id="wccbel-modal-new-item">
    <div class="wccbel-modal-container">
        <div class="wccbel-modal-box wccbel-modal-box-sm">
            <div class="wccbel-modal-content">
                <div class="wccbel-modal-title">
                    <h2 id="wccbel-new-item-title"></h2>
                    <button type="button" class="wccbel-modal-close" data-toggle="modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-modal-body">
                    <div class="wccbel-wrap">
                        <div class="wccbel-form-group">
                            <label class="wccbel-label-big" for="wccbel-new-item-count" id="wccbel-new-item-description"></label>
                            <input type="number" class="wccbel-input-numeric-sm wccbel-m0" id="wccbel-new-item-count" value="1" placeholder="<?php esc_html_e('Number ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>">
                        </div>
                        <div id="wccbel-new-item-extra-fields">
                            <?php if (!empty($new_item_extra_fields)) : ?>
                                <?php foreach ($new_item_extra_fields as $extra_field) : ?>
                                    <div class="wccbel-form-group">
                                        <?php echo wp_kses($extra_field['label'], Sanitizer::allowed_html_tags()); ?>
                                        <?php echo wp_kses($extra_field['field'], Sanitizer::allowed_html_tags()); ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="wccbel-modal-footer">
                    <button type="button" class="wccbel-button wccbel-button-blue" id="wccbel-create-new-item"><?php esc_html_e('Create', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>