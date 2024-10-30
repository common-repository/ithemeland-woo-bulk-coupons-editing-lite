<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div id="wccbel-loading" class="wccbel-loading">
    <?php esc_html_e('Loading ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
</div>

<?php
if (!empty($flush_message)) {
    include WCCBEL_VIEWS_DIR . "alerts/flush_message.php";
}
?>

<div id="wccbel-main">
    <div id="wccbel-header">
        <div class="wccbel-plugin-title">
            <span class="wccbel-plugin-name"><img src="<?php echo esc_url(WCCBEL_IMAGES_URL . 'wccbel_icon_original.svg'); ?>" alt=""><?php echo esc_html($title); ?></span>
        </div>
        <ul class="wccbel-header-left">
            <li title="Help">
                <a href="<?php echo (!empty($doc_link)) ? esc_url($doc_link) : '#'; ?>">
                    <i class="wccbel-icon-book"></i>
                </a>
            </li>
            <li id="wccbel-full-screen" title="Full screen">
                <i class="wccbel-icon-enlarge"></i>
            </li>
            <li class="wccbel-get-pro-button" title="Get Pro">
                <a target="_blank" href="<?php echo esc_url(WCCBEL_UPGRADE_URL); ?>">
                    <i class="wccbel-icon-star-full"></i> Get Pro
                </a>
            </li>
        </ul>
    </div>