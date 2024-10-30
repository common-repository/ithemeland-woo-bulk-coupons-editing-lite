<?php

namespace wccbel\classes\bootstrap;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\repositories\Coupon;

class WCCBEL_Custom_Queries
{
    public function init()
    {
        add_filter('posts_where', [$this, 'general_column_filter'], 10, 2);
        add_filter('posts_where', [$this, 'meta_filter'], 10, 2);
    }

    public function general_column_filter($where, $wp_query)
    {
        global $wpdb;
        if ($search_term = $wp_query->get('wccbel_general_column_filter')) {
            if (is_array($search_term) && count($search_term) > 0) {
                foreach ($search_term as $item) {
                    $field = esc_sql($item['field']);
                    $value = (is_array($item['value'])) ? esc_sql($item['value']) : trim(esc_sql($item['value']));
                    switch ($item['operator']) {
                        case 'like':
                            $custom_where = "(posts.{$field} LIKE '%{$value}%')";
                            break;
                        case 'exact':
                            $custom_where = "(posts.{$field} = '{$value}')";
                            break;
                        case 'not':
                            $custom_where = "(posts.{$field} != '{$value}')";
                            break;
                        case 'begin':
                            $custom_where = "(posts.{$field} LIKE '{$value}%')";
                            break;
                        case 'end':
                            $custom_where = "(posts.{$field} LIKE '%{$value}')";
                            break;
                        case 'in':
                            $custom_where = "(posts.{$field} IN ({$value}))";
                            break;
                        case 'or':
                            if (is_array($value)) {
                                $custom_where = "(";
                                $i = 1;
                                foreach ($value as $value_item) {
                                    $custom_where .= "(posts.{$field} = '{$value_item}')";
                                    if (count($value) > $i) {
                                        $custom_where .= " OR ";
                                    }
                                    $i++;
                                }
                                $custom_where .= ")";
                            }
                            break;
                        case 'not_in':
                            $custom_where = "(posts.{$field} NOT IN ({$value}))";
                            break;
                        case 'between':
                            $value = (is_numeric($value[1])) ? "{$value[0]} AND {$value[1]}" : "'{$value[0]}' AND '{$value[1]}'";
                            $custom_where = "(posts.{$field} BETWEEN {$value})";
                            break;
                        case '>':
                            $custom_where = "(posts.{$field} > {$value})";
                            break;
                        case '<':
                            $custom_where = "(posts.{$field} < {$value})";
                            break;
                        case '>_with_quotation':
                            $custom_where = "(posts.{$field} > '{$value}')";
                            break;
                        case '<_with_quotation':
                            $custom_where = "(posts.{$field} < '{$value}')";
                            break;
                    }

                    $coupon_repository = Coupon::get_instance();
                    $type = (isset($item['type'])) ? esc_sql($item['type']) : 'shop_coupon';
                    $coupons_ids = $coupon_repository->get_ids_by_custom_query('', $custom_where, $type);
                    $ids = (!empty($coupons_ids)) ? $coupons_ids : '0';
                    $where .= " AND ({$wpdb->posts}.ID IN ({$ids}))";
                }
            }
        }

        return $where;
    }

    public function meta_filter($where, $wp_query)
    {
        global $wpdb;
        $coupon_repository = Coupon::get_instance();
        $join = "LEFT JOIN $wpdb->postmeta AS postmeta ON (posts.ID = postmeta.post_id)";
        if ($search_term = $wp_query->get('wccbel_meta_filter')) {
            if (is_array($search_term) && count($search_term) > 0) {
                foreach ($search_term as $item) {
                    $key = esc_sql($item['key']);
                    $value = esc_sql($item['value']);
                    switch ($item['operator']) {
                        case 'like':
                            if (is_array($value)) {
                                $custom_where = "(";
                                $i = 1;
                                foreach ($value as $value_item) {
                                    $custom_where .= "(postmeta.meta_key = '{$key}' AND postmeta.meta_value LIKE '%{$value_item}%')";
                                    if (count($value) > $i) {
                                        $custom_where .= " OR ";
                                    }
                                    $i++;
                                }
                                $custom_where .= ")";
                            } else {
                                $custom_where = "(postmeta.meta_key = '{$key}' AND postmeta.meta_value LIKE '%{$value}%')";
                            }
                            break;
                        case 'or':
                            if (is_array($value)) {
                                $custom_where = "(";
                                $i = 1;
                                foreach ($value as $value_item) {
                                    $custom_where .= "(postmeta.meta_key = '{$key}' AND (postmeta.meta_value = '{$value_item}' OR postmeta.meta_value LIKE '%,{$value_item}%'))";
                                    if (count($value) > $i) {
                                        $custom_where .= " OR ";
                                    }
                                    $i++;
                                }
                                $custom_where .= ")";
                            } else {
                                $custom_where = "(postmeta.meta_key = '{$key}' AND postmeta.meta_value LIKE '%{$value}%')";
                            }
                            break;
                        case 'and':
                        case 'not_in':
                            if (is_array($value)) {
                                $custom_where = "(";
                                $i = 1;
                                foreach ($value as $value_item) {
                                    $custom_where .= "(postmeta.meta_key = '{$key}' AND (postmeta.meta_value = {$value_item} OR postmeta.meta_value LIKE '%,{$value_item}%'))";
                                    if (count($value) > $i) {
                                        $custom_where .= " AND ";
                                    }
                                    $i++;
                                }
                                $custom_where .= ")";
                            } else {
                                $custom_where = "(postmeta.meta_key = '{$key}' AND postmeta.meta_value LIKE '%{$value}%')";
                            }
                            break;
                        case 'exact':
                            $custom_where = "(postmeta.meta_key = '{$key}' AND postmeta.meta_value = '{$value}')";
                            break;
                        case 'not':
                            $custom_where = "(postmeta.meta_key = '{$key}' AND postmeta.meta_value != '{$value}')";
                            break;
                        case 'begin':
                            $custom_where = "(postmeta.meta_key = '{$key}' AND postmeta.meta_value LIKE '{$value}%')";
                            break;
                        case 'end':
                            $custom_where = "(postmeta.meta_key = '{$key}' AND postmeta.meta_value LIKE '%{$value}')";
                            break;
                        case 'in':
                            $custom_where = "(postmeta.meta_key = '{$key}' AND postmeta.meta_value IN ({$value}))";
                            break;
                        case 'between':
                            $custom_where = "(postmeta.meta_key = '$key' AND postmeta.meta_value BETWEEN {$value[0]} AND {$value[1]})";
                            break;
                        case 'between_with_quotation':
                            $custom_where = "(postmeta.meta_key = '$key' AND postmeta.meta_value BETWEEN '{$value[0]}' AND '{$value[1]}')";
                            break;
                        case '<=':
                            $custom_where = "(postmeta.meta_key = '$key' AND postmeta.meta_value <= {$value})";
                            break;
                        case '>=':
                            $custom_where = "(postmeta.meta_key = '$key' AND postmeta.meta_value >= {$value})";
                            break;
                        case '<=_with_quotation':
                            $custom_where = "(postmeta.meta_key = '$key' AND postmeta.meta_value <= '{$value}')";
                            break;
                        case '>=_with_quotation':
                            $custom_where = "(postmeta.meta_key = '$key' AND postmeta.meta_value >= '{$value}')";
                            break;
                        default:
                            $custom_where = "(postmeta.meta_key = '$key' AND postmeta.meta_value = '{$value}')";
                            break;
                    }
                    $type = (isset($item['type'])) ? esc_sql($item['type']) : 'shop_coupon';
                    $coupons_ids = $coupon_repository->get_ids_by_custom_query($join, $custom_where, $type);
                    $ids = (!empty($coupons_ids)) ? $coupons_ids : '0';
                    $operator = ($item['operator'] == 'not_in') ? 'NOT IN' : 'IN';
                    $where .= " AND ({$wpdb->posts}.ID {$operator} ({$ids}))";
                }
            }
        }
        return $where;
    }
}
