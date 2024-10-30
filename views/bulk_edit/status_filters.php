<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<?php if (!empty($coupon_statuses) && is_array($coupon_statuses) && !empty($coupon_counts_by_status) && is_array($coupon_counts_by_status)) : ?>
    <ul>
        <li><button type="button" data-status="all" class="wccbel-bulk-edit-status-filter-item all"><?php esc_html_e('All', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?> (<?php echo isset($coupon_counts_by_status['all']) ? intval($coupon_counts_by_status['all']) : 0 ?>)</button></li>
        <?php foreach ($coupon_statuses as $status_key => $status_label) : ?>
            <?php if (isset($coupon_counts_by_status[$status_key])) : ?>
                <li><button type="button" data-status="<?php echo esc_attr($status_key); ?>" class="wccbel-bulk-edit-status-filter-item <?php echo esc_attr($status_key); ?>"><?php echo esc_html($status_label . ' (' . $coupon_counts_by_status[$status_key] . ')'); ?></button></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>