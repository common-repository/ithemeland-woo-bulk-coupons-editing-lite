<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-float-side-modal" id="wccbel-float-side-modal-settings">
    <div class="wccbel-float-side-modal-container">
        <div class="wccbel-float-side-modal-box">
            <div class="wccbel-float-side-modal-content">
                <div class="wccbel-float-side-modal-title">
                    <h2><?php esc_html_e('Settings', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></h2>
                    <button type="button" class="wccbel-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" style="width: 100%; float: left; height: 100%;">
                    <?php wp_nonce_field('wccbel_post_nonce'); ?>
                    <input type="hidden" name="action" value="wccbel_settings">
                    <div class="wccbel-float-side-modal-body">
                        <div class="wccbel-wrap">
                            <div class="wccbel-tab-middle-content">
                                <div class="wccbel-alert wccbel-alert-default">
                                    <span><?php esc_html_e('You can set bulk editor settings', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></span>
                                </div>
                                <div class="wccbel-form-group">
                                    <label for="wccbel-settings-count-per-page"><?php esc_html_e('Count Per Page', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                    <select name="settings[count_per_page]" id="wccbel-quick-per-page" title="The number of coupons per page">
                                        <?php
                                        if (!empty($count_per_page_items)) :
                                            foreach ($count_per_page_items as $count_per_page_item) :
                                        ?>
                                                <option value="<?php echo intval(esc_attr($count_per_page_item)); ?>" <?php if (isset($settings['count_per_page']) && $settings['count_per_page'] == intval($count_per_page_item)) : ?> selected <?php endif; ?>>
                                                    <?php echo esc_html($count_per_page_item); ?>
                                                </option>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </div>
                                <div class="wccbel-form-group">
                                    <label for="wccbel-settings-default-sort-by"><?php esc_html_e('Default Sort By', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                    <select id="wccbel-settings-default-sort-by" class="wccbel-input-md" name="settings[default_sort_by]">
                                        <option value="id" <?php echo (isset($settings['default_sort_by']) && $settings['default_sort_by'] == 'id') ? 'selected' : ''; ?>>
                                            <?php esc_html_e('ID', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="wccbel-form-group">
                                    <label for="wccbel-settings-default-sort"><?php esc_html_e('Default Sort', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                    <select name="settings[default_sort]" id="wccbel-settings-default-sort" class="wccbel-input-md">
                                        <option value="ASC" <?php echo (isset($settings['default_sort']) && $settings['default_sort'] == 'ASC') ? 'selected' : ''; ?>>
                                            <?php esc_html_e('ASC', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                        </option>
                                        <option value="DESC" <?php echo (isset($settings['default_sort']) && $settings['default_sort'] == 'DESC') ? 'selected' : ''; ?>>
                                            <?php esc_html_e('DESC', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="wccbel-form-group">
                                    <label for="wccbel-settings-close-popup-after-applying"><?php esc_html_e('Close popup after applying', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                    <select name="settings[close_popup_after_applying]" id="wccbel-settings-close-popup-after-applying" class="wccbel-input-md">
                                        <option value="yes" <?php echo (isset($settings['close_popup_after_applying']) && $settings['close_popup_after_applying'] == 'yes') ? 'selected' : ''; ?>>
                                            <?php esc_html_e('Yes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                        </option>
                                        <option value="no" <?php echo (isset($settings['close_popup_after_applying']) && $settings['close_popup_after_applying'] == 'no') ? 'selected' : ''; ?>>
                                            <?php esc_html_e('No', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="wccbel-form-group">
                                    <label for="wccbel-settings-sticky-first-columns"><?php esc_html_e("Sticky 'ID' & 'Coupon Code' Columns", 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                    <select name="settings[sticky_first_columns]" id="wccbel-settings-sticky-first-columns" class="wccbel-input-md">
                                        <option value="yes" <?php echo (isset($settings['sticky_first_columns']) && $settings['sticky_first_columns'] == 'yes') ? 'selected' : ''; ?>>
                                            <?php esc_html_e('Yes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                        </option>
                                        <option value="no" <?php echo (isset($settings['sticky_first_columns']) && $settings['sticky_first_columns'] == 'no') ? 'selected' : ''; ?>>
                                            <?php esc_html_e('No', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="wccbel-form-group">
                                    <label for="wccbel-settings-display-full-columns-title"><?php esc_html_e('Display Columns Label', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                    <select name="settings[display_full_columns_title]" id="wccbel-settings-display-full-columns-title" class="wccbel-input-md">
                                        <option value="yes" <?php echo (isset($settings['display_full_columns_title']) && $settings['display_full_columns_title'] == 'yes') ? 'selected' : ''; ?>>
                                            <?php esc_html_e('Completely', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                        </option>
                                        <option value="no" <?php echo (isset($settings['display_full_columns_title']) && $settings['display_full_columns_title'] == 'no') ? 'selected' : ''; ?>>
                                            <?php esc_html_e('In short', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="wccbel-form-group">
                                    <label for="wccbel-settings-keep-filled-data-in-bulk-edit-form"><?php esc_html_e('Keep filled data in bulk edit form', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                    <select name="settings[keep_filled_data_in_bulk_edit_form]" id="wccbel-settings-keep-filled-data-in-bulk-edit-form" class="wccbel-input-md">
                                        <option value="yes" <?php echo (isset($settings['keep_filled_data_in_bulk_edit_form']) && $settings['keep_filled_data_in_bulk_edit_form'] == 'yes') ? 'selected' : ''; ?>>
                                            <?php esc_html_e('Yes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                        </option>
                                        <option value="no" <?php echo (isset($settings['keep_filled_data_in_bulk_edit_form']) && $settings['keep_filled_data_in_bulk_edit_form'] == 'no') ? 'selected' : ''; ?>>
                                            <?php esc_html_e('No', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wccbel-float-side-modal-footer">
                        <button type="submit" class="wccbel-button wccbel-button-blue">
                            <img src="<?php echo esc_url(WCCBEL_IMAGES_URL . 'save.svg'); ?>" alt="">
                            <span><?php esc_html_e('Save Changes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>