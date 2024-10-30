<?php

use wccbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

function wccbel_woocommerce_required_error()
{
    echo wp_kses('<div class="notice notice-error"><p>' . esc_html__('"iThemeland WooCommerce Bulk Coupons Editing" Plugin needs "WooCommerce" Plugin, Please Install/Activate that.') . '</p></div>', Sanitizer::allowed_html_tags());
}

add_action('admin_notices', 'wccbel_woocommerce_required_error');
