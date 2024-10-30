<?php

use wccbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-float-side-modal" id="wccbel-float-side-modal-bulk-edit">
    <div class="wccbel-float-side-modal-container">
        <div class="wccbel-float-side-modal-box">
            <div class="wccbel-float-side-modal-content">
                <div class="wccbel-float-side-modal-title">
                    <h2><?php esc_html_e('Bulk Edit Form', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></h2>
                    <button type="button" class="wccbel-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-float-side-modal-body">
                    <div class="wccbel-wrap">
                        <div class="wccbel-tabs">
                            <div class="wccbel-tabs-navigation">
                                <nav class="wccbel-tabs-navbar">
                                    <ul class="wccbel-tabs-list" data-content-id="wccbel-bulk-edit-tabs">
                                        <?php if (!empty($bulk_edit_form_tabs_title) && is_array($bulk_edit_form_tabs_title)) : ?>
                                            <?php $bulk_edit_tab_title_counter = 1; ?>
                                            <?php foreach ($bulk_edit_form_tabs_title as $tab_key => $tab_label) : ?>
                                                <li><button type="button" class="wccbel-tab-item <?php echo ($bulk_edit_tab_title_counter == 1) ? 'selected' : ''; ?>" data-content="<?php echo esc_attr($tab_key); ?>"><?php echo esc_attr($tab_label); ?></button></li>
                                                <?php $bulk_edit_tab_title_counter++; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                            <div class="wccbel-tabs-contents wccbel-mt30" id="wccbel-bulk-edit-tabs">
                                <?php if (!empty($bulk_edit_form_tabs_content)) : ?>
                                    <?php foreach ($bulk_edit_form_tabs_content as $tab_key => $bulk_edit_tab) : ?>
                                        <?php echo (!empty($bulk_edit_tab['wrapper_start'])) ? wp_kses($bulk_edit_tab['wrapper_start'], Sanitizer::allowed_html_tags()) : ''; ?>
                                        <?php
                                        if (!empty($bulk_edit_tab['fields_top']) && is_array($bulk_edit_tab['fields_top'])) {
                                            foreach ($bulk_edit_tab['fields_top'] as $top_item) {
                                                echo wp_kses($top_item, Sanitizer::allowed_html_tags());
                                            }
                                        }
                                        ?>
                                        <?php if (!empty($bulk_edit_tab['tabs_titles'])) : ?>
                                            <ul class="wccbel-sub-tab-titles">
                                                <?php foreach ($bulk_edit_tab['tabs_titles'] as $sub_tab_key => $sub_tab_title) : ?>
                                                    <li><button type="button" class="wccbel-sub-tab-title" data-content="<?php echo esc_attr($sub_tab_key); ?>"><?php echo esc_html($sub_tab_title); ?></button></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                        <?php if (!empty($bulk_edit_tab['tabs_content'])) : ?>
                                            <div class="wccbel-sub-tab-contents">
                                                <?php foreach ($bulk_edit_tab['tabs_content'] as $sub_tab_content_key => $fields_group) : ?>
                                                    <div class="wccbel-sub-tab-content" data-content="<?php echo esc_attr($sub_tab_content_key); ?>">
                                                        <?php
                                                        if (!empty($fields_group) && is_array($fields_group)) :
                                                            foreach ($fields_group as $group_key => $fields) :
                                                                if (!empty($fields) && is_array($fields)) :
                                                                    if (!empty($fields['header']) && is_array($fields['header'])) {
                                                                        foreach ($fields['header'] as $header_item) {
                                                                            echo wp_kses($header_item, Sanitizer::allowed_html_tags());
                                                                        }
                                                                    }
                                                                    foreach ($fields as $field_items) : ?>
                                                                        <div class="wccbel-form-group" <?php echo (!empty($field_items['wrap_attributes'])) ? wp_kses($field_items['wrap_attributes'], Sanitizer::allowed_html_tags()) : ''; ?>>
                                                                            <?php
                                                                            if (!empty($field_items['html']) && is_array($field_items['html'])) :
                                                                                foreach ($field_items['html'] as $field) {
                                                                                    echo wp_kses($field, Sanitizer::allowed_html_tags());
                                                                                }
                                                                            endif;
                                                                            ?>
                                                                        </div>
                                                        <?php
                                                                    endforeach;
                                                                    if (!empty($fields['footer']) && is_array($fields['footer'])) {
                                                                        foreach ($fields['footer'] as $footer_item) {
                                                                            echo wp_kses($footer_item, Sanitizer::allowed_html_tags());
                                                                        }
                                                                    }
                                                                endif;
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($bulk_edit_tab['fields']) && is_array($bulk_edit_tab['fields'])) : ?>
                                            <?php foreach ($bulk_edit_tab['fields'] as $field_key => $field_items) : ?>
                                                <?php
                                                if (!empty($field_items) && is_array($field_items)) : ?>
                                                    <div class="wccbel-form-group" <?php echo (!empty($field_items['wrap_attributes'])) ? wp_kses($field_items['wrap_attributes'], Sanitizer::allowed_html_tags()) : ''; ?>>
                                                        <div>
                                                            <?php
                                                            if (!empty($field_items['header']) && is_array($field_items['header'])) {
                                                                foreach ($field_items['header'] as $header_item) {
                                                                    echo wp_kses($header_item, Sanitizer::allowed_html_tags());
                                                                }
                                                            }

                                                            if (!empty($field_items['html']) && is_array($field_items['html'])) :
                                                                foreach ($field_items['html'] as $field_html) :
                                                                    echo wp_kses($field_html, Sanitizer::allowed_html_tags());
                                                                endforeach;
                                                            endif;

                                                            if (!empty($field_items['footer']) && is_array($field_items['footer'])) {
                                                                foreach ($field_items['footer'] as $footer_item) {
                                                                    echo wp_kses($footer_item, Sanitizer::allowed_html_tags());
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <?php echo (!empty($bulk_edit_tab['wrapper_end'])) ? wp_kses($bulk_edit_tab['wrapper_end'], Sanitizer::allowed_html_tags()) : ''; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wccbel-float-side-modal-footer">
                    <button type="button" class="wccbel-button wccbel-button-blue" id="wccbel-bulk-edit-form-do-bulk-edit">
                        <?php esc_html_e('Do Bulk Edit', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                    </button>
                    <button type="button" class="wccbel-button wccbel-button-white" id="wccbel-bulk-edit-form-reset">
                        <?php esc_html_e('Reset Form', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>