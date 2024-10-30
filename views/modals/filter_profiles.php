<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-float-side-modal" id="wccbel-float-side-modal-filter-profiles">
    <div class="wccbel-float-side-modal-container">
        <div class="wccbel-float-side-modal-box">
            <div class="wccbel-float-side-modal-content">
                <div class="wccbel-float-side-modal-title">
                    <h2><?php esc_html_e('Filter Profiles', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></h2>
                    <button type="button" class="wccbel-float-side-modal-close" data-toggle="float-side-modal-close">
                        <i class="wccbel-icon-x"></i>
                    </button>
                </div>
                <div class="wccbel-float-side-modal-body">
                    <div class="wccbel-wrap">
                        <div class="wccbel-filter-profiles-items wccbel-pb30">
                            <div class="wccbel-table-border-radius">
                                <table>
                                    <thead>
                                        <tr>
                                            <th><?php esc_html_e('Profile Name', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></th>
                                            <th><?php esc_html_e('Date Modified', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></th>
                                            <th><?php esc_html_e('Use Always', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></th>
                                            <th><?php esc_html_e('Actions', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($filters_preset)) : ?>
                                            <?php foreach ($filters_preset as $filter_item) : ?>
                                                <?php include "filter_profile_item.php"; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>