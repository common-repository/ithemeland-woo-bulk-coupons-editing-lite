<?php
/*
Plugin Name: iThemeland WooCommerce Bulk Coupons Editing Lite
Plugin URI: https://ithemelandco.com/plugins/woocommerce-bulk-coupons-editing
Description: Editing Date in WordPress is very painful. Be professionals with managing data in the reliable and flexible way by WooCommerce Bulk Coupon Editor.
Author: iThemelandco
Tested up to: WP 5.3
Requires PHP: 5.4
Tags: woocommerce,woocommerce bulk edit,bulk edit,bulk,coupons bulk editor
Text Domain: ithemeland-woocommerce-bulk-coupons-editing-lite
Domain Path: /languages
Requires Plugins: woocommerce
WC requires at least: 3.9
WC tested up to: 8.9
Requires at least: 4.4
Version: 2.3.1
Author URI: https://www.ithemelandco.com
*/

defined('ABSPATH') || exit();

if (defined('WCCBEL_NAME')) {
    return false;
}

require_once __DIR__ . '/vendor/autoload.php';

define('WCCBEL_NAME', 'ithemeland-woocommerce-bulk-coupons-editing-lite');
define('WCCBEL_LABEL', 'Ithemeland Woocommerce Bulk Coupons Editing Lite');
define('WCCBEL_DESCRIPTION', 'Be professionals with managing data in the reliable and flexible way!');
define('WCCBEL_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('WCCBEL_PLUGIN_MAIN_PAGE', admin_url('admin.php?page=wccbel'));
define('WCCBEL_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('WCCBEL_LIB_DIR', trailingslashit(WCCBEL_DIR . 'classes/lib'));
define('WCCBEL_VIEWS_DIR', trailingslashit(WCCBEL_DIR . 'views'));
define('WCCBEL_LANGUAGES_DIR', dirname(plugin_basename(__FILE__)) . '/languages/');
define('WCCBEL_ASSETS_DIR', trailingslashit(WCCBEL_DIR . 'assets'));
define('WCCBEL_ASSETS_URL', trailingslashit(WCCBEL_URL . 'assets'));
define('WCCBEL_CSS_URL', trailingslashit(WCCBEL_ASSETS_URL . 'css'));
define('WCCBEL_IMAGES_URL', trailingslashit(WCCBEL_ASSETS_URL . 'images'));
define('WCCBEL_JS_URL', trailingslashit(WCCBEL_ASSETS_URL . 'js'));
define('WCCBEL_VERSION', '2.3.1');
define('WCCBEL_UPGRADE_URL', 'https://ithemelandco.com/plugins/woocommerce-bulk-coupons-editing?utm_source=free_plugins&utm_medium=plugin_links&utm_campaign=user-lite-buy');
define('WCCBEL_UPGRADE_TEXT', 'Download Pro Version');

register_activation_hook(__FILE__, ['wccbel\classes\bootstrap\WCCBEL', 'activate']);
register_deactivation_hook(__FILE__, ['wccbel\classes\bootstrap\WCCBEL', 'deactivate']);

add_action('init', ['wccbel\classes\bootstrap\WCCBEL', 'wccbel_wp_init']);

// compatible with woocommerce custom order tables
add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

add_action('plugins_loaded', function () {
    if (!class_exists('WooCommerce')) {
        wccbel\classes\bootstrap\WCCBEL::wccbel_woocommerce_required();
    } else {
        wccbel\classes\bootstrap\WCCBEL::init();
    }
}, PHP_INT_MAX);
