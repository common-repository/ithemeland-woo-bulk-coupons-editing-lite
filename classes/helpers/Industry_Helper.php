<?php

namespace wccbel\classes\helpers;

defined('ABSPATH') || exit(); // Exit if accessed directly

class Industry_Helper
{
    public static function get_industries()
    {
        return [
            'Automotive and Transportation' => __('Automotive', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'AdTech and AdNetwork' => __('AdTech and AdNetwork', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Agency' => __('Agency', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'B2B Software' => __('B2B Software', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'B2C Internet Services' => __('B2C Internet Services', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Classifieds' => __('Classifieds', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Consulting and Market Research' => __('Consulting and Market Research', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'CPG, Food and Beverages' => __('CPG', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Education' => __('Education', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Education (student)' => __('Education (Student)', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Equity Research' => __('Equity Research', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Financial services' => __('Financial Services', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Gambling / Gaming' => __('Gambling and Gaming', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Hedge Funds and Asset Management' => __('Hedge Funds and Asset Management', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Investment Banking' => __('Investment Banking', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Logistics and Shipping' => __('Logistics and Shipping', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Payments' => __('Payments', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Pharma and Healthcare' => __('Pharma and Healthcare', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Private Equity and Venture Capital' => __('Private Equity and Venture Capital', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Media and Entertainment' => __('Publishers and Media', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Government Public Sector & Non Profit' => __('Public Sector, Non Profit, Fraud and Compliance', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Retail / eCommerce' => __('Retail and eCommerce', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Telecom and Hardware' => __('Telecom', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Travel and Hospitality' => __('Travel', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'Other' => __('Other', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
        ];
    }
}
