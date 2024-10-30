<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wccbel-top-nav-filters">
    <div class="wccbel-top-nav-filters-left">
        <?php $quick_search_input = (isset($last_filter_data) && !empty($last_filter_data['search_type']) && $last_filter_data['search_type'] == 'quick_search') ? $last_filter_data : ''; ?>
        <div class="wccbel-top-nav-filters-search">
            <input type="text" id="wccbel-quick-search-text" placeholder="<?php esc_html_e('Quick Search ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>" title="Quick Search" value="<?php echo (isset($quick_search_input['quick_search_text'])) ? esc_attr($quick_search_input['quick_search_text']) : '' ?>">
            <select id="wccbel-quick-search-field" title="Select Field">
                <option value="title" <?php echo (isset($quick_search_input['quick_search_field']) && $quick_search_input['quick_search_field'] == 'title') ? 'selected' : '' ?>>
                    <?php esc_html_e('Title', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                </option>
                <option value="id" <?php echo (isset($quick_search_input['quick_search_field']) && $quick_search_input['quick_search_field'] == 'id') ? 'selected' : '' ?>>
                    <?php esc_html_e('ID', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                </option>
            </select>
            <select id="wccbel-quick-search-operator" title="Select Operator">
                <option value="like" <?php echo (isset($quick_search_input['quick_search_operator']) && $quick_search_input['quick_search_operator'] == 'like') ? 'selected' : '' ?>>
                    <?php esc_html_e('Like', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                </option>
                <option value="exact" <?php echo (isset($quick_search_input['quick_search_operator']) && $quick_search_input['quick_search_operator'] == 'exact') ? 'selected' : '' ?>>
                    <?php esc_html_e('Exact', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                </option>
                <option value="not" <?php echo (isset($quick_search_input['quick_search_operator']) && $quick_search_input['quick_search_operator'] == 'not') ? 'selected' : '' ?>>
                    <?php esc_html_e('Not', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                </option>
                <option value="begin" <?php echo (isset($quick_search_input['quick_search_operator']) && $quick_search_input['quick_search_operator'] == 'begin') ? 'selected' : '' ?>>
                    <?php esc_html_e('Begin', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                </option>
                <option value="end" <?php echo (isset($quick_search_input['quick_search_operator']) && $quick_search_input['quick_search_operator'] == 'end') ? 'selected' : '' ?>>
                    <?php esc_html_e('End', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                </option>
            </select>
            <button type="button" id="wccbel-quick-search-button" class="wccbel-filter-form-action" data-search-action="quick_search">
                <i class="wccbel-icon-filter1"></i>
            </button>
            <button type="button" id="wccbel-quick-search-reset" class="wccbel-button wccbel-button-blue" style="<?php echo (empty($quick_search_input)) ? 'display:none' : 'display:inline-table'; ?>">Reset Filter</button>
        </div>
        <div class="wccbel-top-nav-divider"></div>

        <div class="wccbel-status-filter-container">
            <button type="button" class="wccbel-status-filter-button" title="<?php esc_attr_e('Status Filter', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>">
                <?php esc_html_e('Statuses', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?> <span class="wccbel-status-filter-selected-name"></span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z" />
                </svg>
            </button>

            <div class="wccbel-top-nav-status-filter"></div>
        </div>
    </div>
</div>