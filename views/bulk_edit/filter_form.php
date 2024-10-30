<?php

use wccbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-float-side-modal" id="wccbel-float-side-modal-filter">
    <div class="wccbel-float-side-modal-container">
        <div class="wccbel-float-side-modal-box">
            <div class="wccbel-float-side-modal-content">
                <input type="hidden" id="filter-form-changed" value="">
                <div class="wccbel-float-side-modal-title">
                    <h2><?php esc_html_e('Filter Form', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></h2>
                    <button type="button" class="wccbel-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-float-side-modal-body">
                    <div class="wccbel-wrap">
                        <ul class="wccbel-tabs-list" data-content-id="wccbel-bulk-edit-filter-tabs-contents">
                            <?php if (!empty($filter_form_tabs_title) && is_array($filter_form_tabs_title)) : ?>
                                <?php $filter_tab_title_counter = 1; ?>
                                <?php foreach ($filter_form_tabs_title as $tab_key => $tab_label) : ?>
                                    <li><button type="button" class="wccbel-tab-item <?php echo ($filter_tab_title_counter == 1) ? 'selected' : ''; ?>" data-content="<?php echo esc_attr($tab_key); ?>"><?php echo esc_attr($tab_label); ?></button></li>
                                    <?php $filter_tab_title_counter++; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                        <div class="wccbel-tabs-contents" id="wccbel-bulk-edit-filter-tabs-contents">
                            <?php if (!empty($filter_form_tabs_content)) : ?>
                                <?php foreach ($filter_form_tabs_content as $tab_key => $filter_tab) : ?>
                                    <?php echo (!empty($filter_tab['wrapper_start'])) ? wp_kses($filter_tab['wrapper_start'], Sanitizer::allowed_html_tags()) : ''; ?>
                                    <?php
                                    if (!empty($filter_tab['fields_top']) && is_array($filter_tab['fields_top'])) {
                                        foreach ($filter_tab['fields_top'] as $top_item) {
                                            echo wp_kses($top_item, Sanitizer::allowed_html_tags());
                                        }
                                    }
                                    ?>
                                    <?php if (!empty($filter_tab['fields']) && is_array($filter_tab['fields'])) : ?>
                                        <?php foreach ($filter_tab['fields'] as $field_key => $field_items) : ?>
                                            <?php if (!empty($field_items) && is_array($field_items)) : ?>
                                                <div class="wccbel-form-group" data-name="<?php echo esc_attr($field_key); ?>">
                                                    <?php foreach ($field_items as $field_html) : ?>
                                                        <?php echo wp_kses($field_html, Sanitizer::allowed_html_tags()); ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <?php echo (!empty($filter_tab['wrapper_end'])) ? wp_kses($filter_tab['wrapper_end'], Sanitizer::allowed_html_tags()) : ''; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="wccbel-float-side-modal-footer">
                    <div class="wccbel-tab-footer-left">
                        <button type="button" id="wccbel-filter-form-get-coupons" class="wccbel-button wccbel-button-blue wccbel-filter-form-action" data-search-action="pro_search">
                            <?php esc_html_e('Get Coupons', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                        </button>
                        <button type="button" class="wccbel-button wccbel-button-white" id="wccbel-filter-form-reset">
                            <?php esc_html_e('Reset Filters', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                        </button>
                    </div>
                    <div class="wccbel-tab-footer-right">
                        <input type="text" name="save_filter" id="wccbel-filter-form-save-preset-name" placeholder="Filter Name ..." class="" title="Filter Name">
                        <button type="button" id="wccbel-filter-form-save-preset" class="wccbel-button wccbel-button-blue">
                            <?php esc_html_e('Save Profile', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                        </button>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</div>