<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
    <?php wp_nonce_field('wccbel_post_nonce'); ?>
    <input type="hidden" name="action" value="wccbel_column_manager_edit_preset">
    <input type="hidden" name="preset_key" id="wccbel-column-manager-edit-preset-key" value="">
    <div class="wccbel-modal" id="wccbel-modal-column-manager-edit-preset">
        <div class="wccbel-modal-container">
            <div class="wccbel-modal-box wccbel-modal-box-lg">
                <div class="wccbel-modal-content">
                    <div class="wccbel-modal-title">
                        <h2><?php esc_html_e('Edit Column Preset', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></h2>
                        <button type="button" class="wccbel-modal-close" data-toggle="modal-close">
                            <i class="wccbel-icon-x"></i>
                        </button>
                    </div>
                    <div class="wccbel-modal-body">
                        <div class="wccbel-wrap">
                            <div class="wccbel-column-manager-new-profile wccbel-mt0">
                                <div class="wccbel-column-manager-new-profile-left">
                                    <label class="wccbel-column-manager-check-all-fields-btn" data-action="edit">
                                        <input type="checkbox" class="wccbel-column-manager-check-all-fields">
                                        <span><?php esc_html_e('Select All', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></span>
                                    </label>
                                    <input type="text" title="Search Field" data-action="edit" placeholder="<?php esc_attr_e('Search Field ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>" class="wccbel-column-manager-search-field">
                                    <div class="wccbel-column-manager-available-fields" data-action="edit">
                                        <ul>
                                            <?php if (!empty($column_items)) : ?>
                                                <?php foreach ($column_items as $column_key => $column_field) : ?>
                                                    <li data-name="<?php echo esc_attr($column_key); ?>">
                                                        <label>
                                                            <input type="checkbox" data-name="<?php echo esc_attr($column_key); ?>" data-type="field" value="<?php echo esc_attr($column_field['label']); ?>">
                                                            <?php echo esc_html($column_field['label']); ?>
                                                        </label>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="wccbel-column-manager-new-profile-middle">
                                    <div class="wccbel-column-manager-middle-buttons">
                                        <div>
                                            <button type="button" data-action="edit" class="wccbel-button wccbel-button-lg wccbel-button-square-lg wccbel-button-blue wccbel-column-manager-add-field">
                                                <i class="wccbel-icon-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="wccbel-column-manager-new-profile-right">
                                    <div class="wccbel-column-manager-right-top">
                                        <input type="text" title="Profile Name" class="wccbel-w100p" id="wccbel-column-manager-edit-preset-name" name="preset_name" placeholder="<?php esc_attr_e('Profile name ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>">
                                    </div>
                                    <div class="wccbel-column-manager-added-fields wccbel-table-border-radius wccbel-mt10" data-action="edit">
                                        <div class="items"></div>
                                        <img src="<?php echo esc_url(WCCBEL_IMAGES_URL . 'loading.gif'); ?>" alt="" class="wccbel-box-loading wccbel-hide">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wccbel-modal-footer">
                        <button type="submit" name="edit_preset" class="wccbel-button wccbel-button-blue"><?php esc_html_e('Save Changes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>