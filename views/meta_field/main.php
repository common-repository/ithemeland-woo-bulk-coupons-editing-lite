<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-float-side-modal" id="wccbel-float-side-modal-meta-fields">
    <div class="wccbel-float-side-modal-container">
        <div class="wccbel-float-side-modal-box">
            <div class="wccbel-float-side-modal-content">
                <div class="wccbel-float-side-modal-title">
                    <h2><?php esc_html_e('Meta Fields', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></h2>
                    <button type="button" class="wccbel-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-float-side-modal-body" style="height: calc(100% - 45px);">
                    <div class="wccbel-wrap">
                        <div class="wccbel-tab-middle-content">
                            <div class="wccbel-alert wccbel-alert-default">
                                <span><?php esc_html_e('You can add new coupons meta fields in two ways: 1- Individually 2- Get from other coupon.', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></span>
                            </div>
                            <div class="wccbel-alert wccbel-alert-danger">
                                <span class="wccbel-lh36">This option is not available in Free Version, Please upgrade to Pro Version</span>
                                <a href="<?php echo esc_url(WCCBEL_UPGRADE_URL); ?>"><?php echo esc_html(WCCBEL_UPGRADE_TEXT); ?></a>
                            </div>
                            <div class="wccbel-meta-fields-left">
                                <div class="wccbel-meta-fields-manual">
                                    <label for="wccbel-meta-fields-manual_key_name"><?php esc_html_e('Manually', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                    <div class="wccbel-meta-fields-manual-field">
                                        <input type="text" id="wccbel-meta-fields-manual_key_name" disabled="disabled" placeholder="<?php esc_attr_e('Enter Meta Key ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>">
                                        <button type="button" disabled="disabled" class="wccbel-button wccbel-button-square wccbel-button-blue">
                                            <i class="wccbel-icon-plus1 wccbel-m0"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="wccbel-meta-fields-automatic">
                                    <label for="wccbel-add-meta-fields-coupon-id"><?php esc_html_e('Automatically From Coupon', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></label>
                                    <div class="wccbel-meta-fields-automatic-field">
                                        <input type="text" id="wccbel-add-meta-fields-coupon-id" disabled="disabled" placeholder="<?php esc_attr_e('Enter Coupon ID ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>">
                                        <button type="button" disabled="disabled" class="wccbel-button wccbel-button-square wccbel-button-blue">
                                            <i class="wccbel-icon-plus1 wccbel-m0"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                                <?php wp_nonce_field('wccbel_post_nonce'); ?>
                                <input type="hidden" name="action" value="wccbel_meta_fields">
                                <div class="wccbel-meta-fields-right" id="wccbel-meta-fields-items">
                                    <p class="wccbel-meta-fields-empty-text" <?php echo (!empty($meta_fields)) ? 'style="display:none";' : ''; ?>><?php esc_html_e("Please add your meta key manually", 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?><br> <?php esc_html_e("OR", 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?><br><?php esc_html_e("From another coupon", 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></p>
                                    <?php if (!empty($meta_fields)) : ?>
                                        <?php foreach ($meta_fields as $meta_field) : ?>
                                            <?php include WCCBEL_VIEWS_DIR . 'meta_field/meta_field_item.php'; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <div class="droppable-helper"></div>
                                </div>
                                <div class="wccbel-meta-fields-buttons">
                                    <div class="wccbel-meta-fields-buttons-left">
                                        <button type="button" disabled="disabled" class="wccbel-button wccbel-button-lg wccbel-button-blue">
                                            <img src="<?php echo esc_url(WCCBEL_IMAGES_URL . 'save.svg'); ?>" alt="">
                                            <span><?php esc_html_e('Save Fields', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></span>
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