<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-float-side-modal" id="wccbel-float-side-modal-import-export">
    <div class="wccbel-float-side-modal-container">
        <div class="wccbel-float-side-modal-box">
            <div class="wccbel-float-side-modal-content">
                <div class="wccbel-float-side-modal-title">
                    <h2><?php esc_html_e('Import/Export', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></h2>
                    <button type="button" class="wccbel-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-float-side-modal-body" style="height: calc(100% - 45px);">
                    <div class="wccbel-wrap">
                        <div class="wccbel-tab-middle-content">
                            <div class="wccbel-alert wccbel-alert-default">
                                <span><?php esc_html_e('Import/Export coupons as CSV files', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>.</span>
                            </div>
                            <div class="wccbel-export">
                                <form action="<?php echo esc_url(admin_url("admin-post.php")); ?>" method="post">
                                    <?php wp_nonce_field('wccbel_post_nonce'); ?>
                                    <input type="hidden" name="action" value="wccbel_export_coupons">
                                    <div id="wccbel-export-items-selected"></div>
                                    <div class="wccbel-export-fields">
                                        <div class="wccbel-export-field-item">
                                            <strong class="label"><?php esc_html_e('Coupons', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></strong>
                                            <label class="wccbel-export-radio">
                                                <input type="radio" name="coupons" value="all" checked="checked" id="wccbel-export-all-items-in-table">
                                                <?php esc_html_e('All Coupons In Table', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                            </label>
                                            <label class="wccbel-export-radio">
                                                <input type="radio" name="coupons" id="wccbel-export-only-selected-items" value="selected" disabled="disabled">
                                                <?php esc_html_e('Only Selected coupons', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                            </label>
                                        </div>
                                        <div class="wccbel-export-field-item">
                                            <strong class="label"><?php esc_html_e('Fields', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></strong>
                                            <label class="wccbel-export-radio">
                                                <input type="radio" name="fields" value="all" checked="checked">
                                                <?php esc_html_e('All Fields', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                            </label>
                                            <label class="wccbel-export-radio">
                                                <input type="radio" name="fields" value="visible">
                                                <?php esc_html_e('Only Visible Fields', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                                            </label>
                                        </div>
                                        <div class="wccbel-export-field-item">
                                            <label class="label" for="wccbel-export-delimiter"><?php esc_html_e('Delimiter', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                            <select name="wccbel-export-delimiter" id="wccbel-export-delimiter">
                                                <option value=",">,</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="wccbel-export-buttons">
                                        <div class="wccbel-export-buttons-left">
                                            <button type="submit" class="wccbel-button wccbel-button-lg wccbel-button-blue" id="wccbel-export-coupons">
                                                <i class="wccbel-icon-filter1"></i>
                                                <span><?php esc_html_e('Export Now', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="wccbel-import">
                                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
                                    <?php wp_nonce_field('wccbel_post_nonce'); ?>
                                    <input type="hidden" name="action" value="wccbel_import_coupons">
                                    <div class="wccbel-import-content">
                                        <p><?php esc_html_e("If you have coupons in another system, you can import those into this site. ", 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></p>
                                        <input type="file" name="import_file" required>
                                    </div>
                                    <div class="wccbel-import-buttons">
                                        <div class="wccbel-import-buttons-left">
                                            <button type="submit" name="import" class="wccbel-button wccbel-button-lg wccbel-button-blue">
                                                <i class="wccbel-icon-filter1"></i>
                                                <span><?php esc_html_e('Import Now', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></span>
                                            </button>
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