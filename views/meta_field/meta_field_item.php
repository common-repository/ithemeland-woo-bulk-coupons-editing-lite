<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-meta-fields-right-item">
    <span class="wccbel-meta-fields-name"><?php echo (!empty($meta_field['key'])) ? esc_attr($meta_field['key']) : 'No Name!'; ?></span>
    <input type="hidden" name="meta_field_key[]" value="<?php echo (!empty($meta_field['key'])) ? esc_attr($meta_field['key']) : ''; ?>">
    <input type="text" name="meta_field_title[]" class="wccbel-meta-fields-title" placeholder="<?php esc_html_e('Enter field title ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>" value="<?php echo (isset($meta_field['title'])) ? esc_attr($meta_field['title']) : ''; ?>">
    <?php if (isset($meta_field['type_disabled']) && $meta_field['type_disabled'] === true) : ?>
        <span class="wccbel-meta-fields-type wccbel-meta-fields-main-type"><?php echo esc_html(ucfirst($meta_field['main_type'])); ?></span>
        <span class="wccbel-meta-fields-type wccbel-meta-fields-sub-type"><?php echo esc_html(ucfirst($meta_field['sub_type'])); ?></span>
        <input type="hidden" name="meta_field_main_type[]" value="<?php echo esc_attr($meta_field['main_type']); ?>">
        <input type="hidden" name="meta_field_sub_type[]" value="<?php echo esc_attr($meta_field['sub_type']); ?>">
    <?php else : ?>
        <select class="wccbel-meta-fields-type wccbel-meta-fields-main-type" data-id="<?php echo (!empty($meta_field['key'])) ? esc_attr($meta_field['key']) : ''; ?>" name="meta_field_main_type[]" title="<?php esc_html_e('Select Type', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>">
            <?php if (!empty($meta_fields_main_types)) : ?>
                <?php foreach ($meta_fields_main_types as $main_type_name => $main_type_label) : ?>
                    <option value="<?php echo esc_attr($main_type_name); ?>" <?php echo (isset($meta_field['main_type']) && $meta_field['main_type'] == $main_type_name) ? 'selected' : ''; ?>>
                        <?php echo esc_html($main_type_label); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <select class="wccbel-meta-fields-type wccbel-meta-fields-sub-type <?php echo (isset($meta_field['main_type']) && $meta_field['main_type'] != 'textinput') ? 'wccbel-hide' : ''; ?>" data-id="<?php echo (!empty($meta_field['key'])) ? esc_attr($meta_field['key']) : ''; ?>" name="meta_field_sub_type[]" title="<?php esc_html_e('Select Type', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>">
            <?php if (!empty($meta_fields_sub_types)) : ?>
                <?php foreach ($meta_fields_sub_types as $sub_type_name => $sub_type_label) : ?>
                    <option value="<?php echo esc_attr($sub_type_name); ?>" <?php echo (isset($meta_field['sub_type']) && $meta_field['sub_type'] == $sub_type_name) ? 'selected' : ''; ?>>
                        <?php echo esc_html($sub_type_label); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <div class="wccbel-meta-fields-key-value <?php echo (empty($meta_field['main_type']) || (isset($meta_field['main_type']) && !in_array($meta_field['main_type'], ['select', 'array']))) ? 'wccbel-hide' : ''; ?>" data-id="<?php echo (!empty($meta_field['key'])) ? esc_attr($meta_field['key']) : ''; ?>">
            <input type="text" name="meta_field_key_value[]" value="<?php echo (!empty($meta_field['key_value'])) ? esc_attr($meta_field['key_value']) : '' ?>">
            <p><?php esc_html_e('key1=label1|key2=label2', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></p>
        </div>
    <?php endif; ?>
    <button type="button" class="wccbel-button wccbel-button-flat wccbel-meta-field-item-sortable-btn" title="<?php esc_html_e('Drag', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>">
        <i class="wccbel-icon-menu1"></i>
    </button>
    <button type="button" class="wccbel-button wccbel-button-flat wccbel-meta-field-remove">
        <i class="wccbel-icon-x"></i>
    </button>
</div>