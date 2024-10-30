<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-float-side-modal" id="wccbel-float-side-modal-history">
    <div class="wccbel-float-side-modal-container">
        <div class="wccbel-float-side-modal-box">
            <div class="wccbel-float-side-modal-content">
                <div class="wccbel-float-side-modal-title">
                    <h2><?php esc_html_e('History', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></h2>
                    <button type="button" class="wccbel-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-float-side-modal-body">
                    <div class="wccbel-wrap">
                        <div class="wccbel-alert wccbel-alert-default">
                            <span><?php esc_html_e('List of your changes and possible to roll back to the previous data', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></span>
                        </div>
                        <div class="wccbel-alert wccbel-alert-danger">
                            <span class="wccbel-lh36">This option is not available in Free Version, Please upgrade to Pro Version</span>
                            <a href="<?php echo esc_url(WCCBEL_UPGRADE_URL); ?>"><?php echo esc_html(WCCBEL_UPGRADE_TEXT); ?></a>
                        </div>
                        <div class="wccbel-history-filter">
                            <div class="wccbel-history-filter-fields">
                                <div class="wccbel-history-filter-field-item">
                                    <label for="wccbel-history-filter-operation"><?php esc_html_e('Operation', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                    <select id="wccbel-history-filter-operation">
                                        <option value=""><?php esc_html_e('Select', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></option>
                                        <?php if (!empty($history_types = \wccbel\classes\repositories\History::get_operation_types())) : ?>
                                            <?php foreach ($history_types as $history_type_key => $history_type_label) : ?>
                                                <option value="<?php echo esc_attr($history_type_key); ?>"><?php echo esc_html($history_type_label); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="wccbel-history-filter-field-item">
                                    <label for="wccbel-history-filter-author"><?php esc_html_e('Author', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                    <select id="wccbel-history-filter-author">
                                        <option value=""><?php esc_html_e('Select', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></option>
                                        <?php if (!empty($users)) : ?>
                                            <?php foreach ($users as $user_item) : ?>
                                                <option value="<?php echo esc_attr($user_item->ID); ?>"><?php echo esc_html($user_item->user_login); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="wccbel-history-filter-field-item">
                                    <label for="wccbel-history-filter-fields"><?php esc_html_e('Fields', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                    <input type="text" id="wccbel-history-filter-fields" placeholder="for example: ID">
                                </div>
                                <div class="wccbel-history-filter-field-item wccbel-history-filter-field-date">
                                    <label><?php esc_html_e('Date', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                    <input type="text" id="wccbel-history-filter-date-from" class="wccbel-datepicker wccbel-date-from" data-to-id="wccbel-history-filter-date-to" placeholder="<?php esc_html_e('From ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>">
                                    <input type="text" id="wccbel-history-filter-date-to" class="wccbel-datepicker" placeholder="<?php esc_html_e('To ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>">
                                </div>
                            </div>
                            <div class="wccbel-history-filter-buttons">
                                <div class="wccbel-history-filter-buttons-left">
                                    <button type="button" disabled="disabled" class="wccbel-button wccbel-button-lg wccbel-button-blue">
                                        <i class="wccbel-icon-filter1"></i>
                                        <span><?php esc_html_e('Apply Filters', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></span>
                                    </button>
                                    <button type="button" disabled="disabled" class="wccbel-button wccbel-button-lg wccbel-button-gray">
                                        <i class="wccbel-icon-rotate-cw"></i>
                                        <span><?php esc_html_e('Reset Filters', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></span>
                                    </button>
                                </div>
                                <div class="wccbel-history-filter-buttons-right">
                                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                                        <?php wp_nonce_field('wccbel_post_nonce'); ?>
                                        <button type="button" disabled="disabled" class="wccbel-button wccbel-button-lg wccbel-button-red">
                                            <i class="wccbel-icon-trash-2"></i>
                                            <span><?php esc_html_e('Clear History', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="wccbel-history-items">
                            <h3><?php esc_html_e('Column(s)', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></h3>
                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                                <?php wp_nonce_field('wccbel_post_nonce'); ?>
                                <div class="wccbel-table-border-radius">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><?php esc_html_e('History Name', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></th>
                                                <th><?php esc_html_e('Author', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></th>
                                                <th class="wccbel-mw125"><?php esc_html_e('Date Modified', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></th>
                                                <th class="wccbel-mw250"><?php esc_html_e('Actions', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php include 'history_items.php'; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="wccbel-history-pagination-container">
                                    <?php include 'history_pagination.php'; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>