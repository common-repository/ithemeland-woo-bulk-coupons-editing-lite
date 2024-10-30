<?php

namespace wccbel\classes\helpers;

defined('ABSPATH') || exit(); // Exit if accessed directly

class Pagination
{
    public static function init($current_page, $max_num_pages)
    {
        $current_page = intval($current_page);
        $max_num_pages = intval($max_num_pages);
        $prev = max(1, $current_page - 1);
        $next = min($max_num_pages, $current_page + 1);
        $max_display = 3;
        $output = "<div class='wccbel-top-nav-filters-paginate'>";
        if (isset($max_num_pages)) {
            $output .= "<button type='button' data-index='" . esc_attr($prev) . "' class='wccbel-filter-form-action' data-search-action='pagination'><</a>";
            if ($current_page < $max_display) {
                for ($i = 1; $i <= min($max_display, $max_num_pages); $i++) {
                    $current = ($i == $current_page) ? 'current' : '';
                    $output .= "<button type='button' data-index='" . esc_attr($i) . "' class='wccbel-filter-form-action " . esc_attr($current) . "' data-search-action='pagination'>" . esc_html($i) . "</button>";
                }
                if ($max_num_pages > ($max_display + 1)) {
                    $output .= "<span>...</span>";
                }
                if ($max_num_pages > $max_display) {
                    $output .= "<button type='button' data-index='" . esc_attr($max_num_pages) . "' class='wccbel-filter-form-action' data-search-action='pagination'>" . esc_html($max_num_pages) . "</button>";
                }
            } elseif ($current_page == $max_display) {
                $max_num = ($max_display < $max_num_pages) ? $max_display + 1 : $max_display;
                for ($i = 1; $i <= $max_num; $i++) {
                    $current = ($i == $current_page) ? 'current' : '';
                    $output .= "<button type='button' data-index='" . esc_attr($i) . "' class='wccbel-filter-form-action " . esc_attr($current) . "' data-search-action='pagination'>" . esc_html($i) . "</button>";
                }
                if ($max_num_pages > ($current_page + 1)) {
                    $output .= "<span>...</span>";
                    $output .= "<button type='button' data-index='" . esc_attr($max_num_pages) . "' class='wccbel-filter-form-action' data-search-action='pagination'>" . esc_html($max_num_pages) . "</button>";
                }
            } else {
                $output .= "<button type='button' data-index='1' class='wccbel-filter-form-action' data-search-action='pagination'>1</button>";
                if ($max_num_pages > $max_display) {
                    $output .= "<span>...</span>";
                }
                for ($i = $current_page - 1; $i <= min($current_page + 1, $max_num_pages); $i++) {
                    $current = ($i == $current_page) ? 'current' : '';
                    $output .= "<button type='button' data-index='" . esc_attr($i) . "' class='wccbel-filter-form-action " . esc_attr($current) . "' data-search-action='pagination'>" . esc_html($i) . "</button>";
                }
                if ($current_page + 1 < $max_num_pages) {
                    $output .= "<span>...</span>";
                    $output .= "<button type='button' data-index='" . esc_attr($max_num_pages) . "' class='wccbel-filter-form-action' data-search-action='pagination'>" . esc_html($max_num_pages) . "</button>";
                }
            }
            $output .= "<button type='button' data-index='" . esc_attr($next) . "' class='wccbel-filter-form-action' data-search-action='pagination'>></button>";
        }
        $output .= "</div>";
        $output .= '<div class="wccbel-pagination-loading"><img src="' . WCCBEL_IMAGES_URL . 'loading.gif" alt="Loading" width="20" height="20"></div>';
        return $output;
    }
}
