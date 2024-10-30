<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-float-side-modal" id="wccbel-float-side-modal-column-manager">
    <div class="wccbel-float-side-modal-container">
        <div class="wccbel-float-side-modal-box">
            <div class="wccbel-float-side-modal-content">
                <div class="wccbel-float-side-modal-title">
                    <h2><?php esc_html_e('Column Manager', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></h2>
                    <button type="button" class="wccbel-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-float-side-modal-body" style="height: calc(100% - 45px);">
                    <div class="wccbel-wrap">
                        <div class="wccbel-tab-middle-content">
                            <div class="wccbel-alert wccbel-alert-default">
                                <span><?php esc_html_e('Mange columns of table. You can Create your customize presets and use them in column profile section.', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></span>
                            </div>
                            <div class="wccbel-column-manager-items">
                                <h3><?php esc_html_e('Column Profiles', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></h3>
                                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="wccbel-column-manager-delete-preset-form">
                                    <?php wp_nonce_field('wccbel_post_nonce'); ?>
                                    <input type="hidden" name="action" value="wccbel_column_manager_delete_preset">
                                    <input type="hidden" name="delete_key" id="wccbel_column_manager_delete_preset_key">
                                    <div class="wccbel-table-border-radius">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th><?php esc_html_e('Profile Name', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></th>
                                                    <th><?php esc_html_e('Date Modified', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></th>
                                                    <th><?php esc_html_e('Actions', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($column_manager_presets)) : ?>
                                                    <?php $i = 1 ?>
                                                    <?php foreach ($column_manager_presets as $key => $column_manager_preset) : ?>
                                                        <tr>
                                                            <td><?php echo intval($i); ?></td>
                                                            <td>
                                                                <span class="wccbel-history-name"><?php echo (isset($column_manager_preset['name'])) ? esc_html($column_manager_preset['name']) : ''; ?></span>
                                                            </td>
                                                            <td><?php echo (isset($column_manager_preset['date_modified'])) ? esc_html(gmdate('d M Y', strtotime($column_manager_preset['date_modified']))) : ''; ?></td>
                                                            <td>
                                                                <?php if (!in_array($key, \wccbel\classes\repositories\Column::get_default_columns_name())) : ?>
                                                                    <button type="button" class="wccbel-button wccbel-button-blue wccbel-column-manager-edit-field-btn" data-toggle="modal" data-target="#wccbel-modal-column-manager-edit-preset" value="<?php echo esc_attr($key); ?>" data-preset-name="<?php echo (isset($column_manager_preset['name'])) ? esc_attr($column_manager_preset['name']) : ''; ?>">
                                                                        <i class="wccbel-icon-pencil"></i>
                                                                        <?php esc_html_e('Edit', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                                                    </button>
                                                                    <button type="button" name="delete_preset" class="wccbel-button wccbel-button-red wccbel-column-manager-delete-preset" value="<?php echo esc_attr($key); ?>">
                                                                        <i class="wccbel-icon-trash-2"></i>
                                                                        <?php esc_html_e('Delete', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                                                    </button>
                                                                <?php else : ?>
                                                                    <i class="wccbel-icon-lock1"></i>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <?php $i++; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                            <div class="wccbel-column-manager-new-profile">
                                <h3 class="wccbel-column-manager-section-title"><?php esc_html_e('Create New Profile', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></h3>
                                <div class="wccbel-column-manager-new-profile-left">
                                    <input type="text" title="<?php esc_attr_e('Search Field', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>" data-action="new" placeholder="<?php esc_attr_e('Search Field ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>" class="wccbel-column-manager-search-field">
                                    <div class="wccbel-column-manager-available-fields" data-action="new">
                                        <label class="wccbel-column-manager-check-all-fields-btn" data-action="new">
                                            <input type="checkbox" class="wccbel-column-manager-check-all-fields">
                                            <span><?php esc_html_e('Select All', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></span>
                                        </label>
                                        <ul>
                                            <?php if (!empty($column_items)) : ?>
                                                <?php foreach ($column_items as $column_key => $column_field) : ?>
                                                    <li data-name="<?php echo esc_attr($column_key); ?>" data-added="false">
                                                        <label>
                                                            <input type="checkbox" data-type="field" data-name="<?php echo esc_attr($column_key); ?>" value="<?php echo esc_attr($column_field['label']); ?>">
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
                                            <button type="button" data-action="new" data-type="checked" class="wccbel-button wccbel-button-lg wccbel-button-square-lg wccbel-button-blue wccbel-column-manager-add-field">
                                                <i class="wccbel-icon-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="wccbel-column-manager-add-new-preset">
                                    <?php wp_nonce_field('wccbel_post_nonce'); ?>
                                    <input type="hidden" name="action" value="wccbel_column_manager_new_preset">
                                    <div class="wccbel-column-manager-new-profile-right">
                                        <div class="wccbel-column-manager-right-top">
                                            <input type="text" title="Profile Name" id="wccbel-column-manager-new-preset-name" name="preset_name" placeholder="Profile name ..." required>
                                            <button type="submit" name="save_preset" id="wccbel-column-manager-new-preset-btn" class="wccbel-button wccbel-button-lg wccbel-button-blue">
                                                <img src="<?php echo esc_url(WCCBEL_IMAGES_URL . 'save.svg'); ?>" alt="">
                                                <?php esc_html_e('Save Preset', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                            </button>
                                        </div>
                                        <div class="wccbel-column-manager-added-fields-wrapper">
                                            <p class="wccbel-column-manager-empty-text"><?php esc_html_e('Please add your columns here', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></p>
                                            <div class="wccbel-column-manager-added-fields" data-action="new">
                                                <div class="items"></div>
                                                <img src="<?php echo esc_url(WCCBEL_IMAGES_URL . 'loading.gif'); ?>" alt="" class="wccbel-box-loading wccbel-hide">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>