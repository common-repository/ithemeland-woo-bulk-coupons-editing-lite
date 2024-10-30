<?php

namespace wccbel\classes\helpers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\helpers\Formula;

class Coupon_Helper
{
    public static function round($value, $round_item)
    {
        $division = intval('1' . str_repeat('0', wc_get_price_decimals()));
        switch ($round_item) {
            case 5:
            case 10:
                $value += floatval($round_item / $division);
                $decimals = floatval($value - floor($value));
                $value = floor($value) + ($decimals - floatval(intval(($decimals * $division) . '') % $round_item) / $division);
                break;
            case 9:
            case 19:
            case 29:
            case 39:
            case 49:
            case 59:
            case 69:
            case 79:
            case 89:
            case 99:
                $value = intval($value) + floatval($round_item / $division);
                break;
            default:
                break;
        }

        return $value;
    }

    public static function coupons_id_parser($ids)
    {
        $output = '';
        $ids_array = explode('|', $ids);
        if (is_array($ids_array) && !empty($ids_array)) {
            foreach ($ids_array as $item) {
                $output .= self::parser($item);
            }
        } else {
            $output .= self::parser($ids_array);
        }

        return rtrim($output, ',');
    }

    private static function parser($ids_string)
    {
        $output = '';
        if (strpos($ids_string, '-') > 0) {
            $from_to = explode('-', $ids_string);
            if (isset($from_to[0]) && isset($from_to[1])) {
                for ($i = intval($from_to[0]); $i <= intval($from_to[1]); $i++) {
                    $output .= $i . ',';
                }
            }
        } else {
            $output = $ids_string . ',';
        }

        return $output;
    }

    public static function get_tax_query($taxonomy, $terms, $operator = null, $field = null)
    {
        $field = !empty($field) ? $field : 'slug';
        $values = (is_array($terms)) ? array_map('urldecode', $terms) : $terms;
        switch ($operator) {
            case null:
                $tax_item = [
                    'taxonomy' => urldecode($taxonomy),
                    'field' => $field,
                    'terms' => $values,
                    'operator' => 'AND'
                ];
                break;
            case 'or':
                $tax_item = [
                    'taxonomy' => urldecode($taxonomy),
                    'field' => $field,
                    'terms' => $values,
                    'operator' => 'IN'
                ];
                break;
            case 'and':
                $tax_item['relation'] = 'AND';
                if (is_array($values) && !empty($values)) {
                    foreach ($values as $value) {
                        $tax_item[] = [
                            'taxonomy' => urldecode($taxonomy),
                            'field' => $field,
                            'terms' => [$value],
                        ];
                    }
                }
                break;
            case 'not_in':
                $tax_item = [
                    'taxonomy' => urldecode($taxonomy),
                    'field' => $field,
                    'terms' => $values,
                    'operator' => 'NOT IN'
                ];
                break;
        }
        return $tax_item;
    }

    public static function set_filter_data_items($data, $others = null)
    {
        $args = [];
        if (!is_null($others)) {
            $args = $others;
        }
        if (isset($data['search_type']) && $data['search_type'] == 'quick_search') {
            if (isset($data['quick_search_text']) && !empty($data['quick_search_text'])) {
                switch ($data['quick_search_field']) {
                    case 'id':
                        $ids = self::coupons_id_parser($data['quick_search_text']);
                        $args['wccbel_general_column_filter'][] = [
                            'field' => 'ID',
                            'value' => $ids,
                            'operator' => "in"
                        ];
                        break;
                    case 'title':
                        $ids = self::coupons_id_parser($data['quick_search_text']);
                        $args['wccbel_general_column_filter'][] = [
                            'field' => 'post_title',
                            'value' => sanitize_text_field($data['quick_search_text']),
                            'operator' => sanitize_text_field($data['quick_search_operator'])
                        ];
                        break;
                }
            }
        } else {
            if (isset($data['coupon_ids']) && !empty($data['coupon_ids']['value'])) {
                $ids = self::coupons_id_parser($data['coupon_ids']['value']);
                $args['wccbel_general_column_filter'][] = [
                    'field' => 'ID',
                    'value' => $ids,
                    'operator' => "in"
                ];
            }
            if (isset($data['coupon_code']) && !empty($data['coupon_code']['value'])) {
                $args['wccbel_general_column_filter'][] = [
                    'field' => 'post_title',
                    'value' => esc_sql($data['coupon_code']['value']),
                    'operator' => esc_sql($data['coupon_code']['operator'])
                ];
            }
            if (isset($data['description']) && !empty($data['description']['value'])) {
                $args['wccbel_general_column_filter'][] = [
                    'field' => 'post_excerpt',
                    'value' => esc_sql($data['description']['value']),
                    'operator' => esc_sql($data['description']['operator'])
                ];
            }
            if (isset($data['post_status']) && !empty($data['post_status']['value'])) {
                $args['post_status'] = esc_sql($data['post_status']['value']);
            }
            if (isset($data['date_created']) && (!empty($data['date_created']['from']) || !empty(!empty($data['date_created']['to'])))) {
                $from = (!empty($data['date_created']['from'])) ? gmdate('Y-m-d H:i:s', strtotime($data['date_created']['from'])) : null;
                $to = (!empty($data['date_created']['to'])) ? gmdate('Y-m-d H:i:s', strtotime($data['date_created']['to'])) : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<_with_quotation';
                }
                $args['wccbel_general_column_filter'][] = [
                    'field' => 'post_date',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (isset($data['post_modified']) && (!empty($data['post_modified']['from']) || !empty(!empty($data['post_modified']['to'])))) {
                $from = (!empty($data['post_modified']['from'])) ? gmdate('Y-m-d H:i:s', strtotime($data['post_modified']['from'])) : null;
                $to = (!empty($data['post_modified']['to'])) ? gmdate('Y-m-d H:i:s', strtotime($data['post_modified']['to'])) : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<_with_quotation';
                }
                $args['wccbel_general_column_filter'][] = [
                    'field' => 'post_modified',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (!empty($data['discount_type']['value'])) {
                $args['wccbel_meta_filter'][] = [
                    'key' => 'discount_type',
                    'value' => esc_sql($data['discount_type']['value']),
                    'operator' => 'or',
                ];
            }
            if (!empty($data['coupon_amount']['from']) || !empty($data['coupon_amount']['to'])) {
                $from = (!empty($data['coupon_amount']['from'])) ? $data['coupon_amount']['from'] : null;
                $to = (!empty($data['coupon_amount']['to'])) ? $data['coupon_amount']['to'] : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<_with_quotation';
                }
                $args['wccbel_meta_filter'][] = [
                    'key' => 'coupon_amount',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (!empty($data['date_expires']['from']) || !empty($data['date_expires']['to'])) {
                $from = (!empty($data['date_expires']['from'])) ? strtotime($data['date_expires']['from']) : null;
                $to = (!empty($data['date_expires']['to'])) ? strtotime($data['date_expires']['to']) : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>=';
                } else {
                    $value = $to;
                    $operator = '<=';
                }
                $args['wccbel_meta_filter'][] = [
                    'key' => 'date_expires',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (!empty($data['free_shipping']['value'])) {
                $args['wccbel_meta_filter'][] = [
                    'key' => 'free_shipping',
                    'value' => sanitize_text_field($data['free_shipping']['value']),
                ];
            }
            if (!empty($data['exclude_sale_items']['value'])) {
                $args['wccbel_meta_filter'][] = [
                    'key' => 'exclude_sale_items',
                    'value' => sanitize_text_field($data['exclude_sale_items']['value']),
                ];
            }
            if (!empty($data['individual_use']['value'])) {
                $args['wccbel_meta_filter'][] = [
                    'key' => 'individual_use',
                    'value' => sanitize_text_field($data['individual_use']['value']),
                ];
            }
            if (!empty($data['minimum_amount']['from']) || !empty($data['minimum_amount']['to'])) {
                $from = (!empty($data['minimum_amount']['from'])) ? $data['minimum_amount']['from'] : null;
                $to = (!empty($data['minimum_amount']['to'])) ? $data['minimum_amount']['to'] : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<_with_quotation';
                }
                $args['wccbel_meta_filter'][] = [
                    'key' => 'minimum_amount',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (!empty($data['maximum_amount']['from']) || !empty($data['maximum_amount']['to'])) {
                $from = (!empty($data['maximum_amount']['from'])) ? $data['maximum_amount']['from'] : null;
                $to = (!empty($data['maximum_amount']['to'])) ? $data['maximum_amount']['to'] : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<_with_quotation';
                }
                $args['wccbel_meta_filter'][] = [
                    'key' => 'maximum_amount',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (!empty($data['product_ids']['value'])) {
                $args['wccbel_meta_filter'][] = [
                    'key' => 'product_ids',
                    'value' => esc_sql($data['product_ids']['value']),
                    'operator' => sanitize_text_field($data['product_ids']['operator']),
                ];
            }
            if (!empty($data['exclude_product_ids']['value'])) {
                $args['wccbel_meta_filter'][] = [
                    'key' => 'exclude_product_ids',
                    'value' => esc_sql($data['exclude_product_ids']['value']),
                    'operator' => sanitize_text_field($data['exclude_product_ids']['operator']),
                ];
            }
            if (!empty($data['product_categories']['value'])) {
                $args['wccbel_meta_filter'][] = [
                    'key' => 'product_categories',
                    'value' => esc_sql($data['product_categories']['value']),
                    'operator' => sanitize_text_field($data['product_categories']['operator']),
                ];
            }
            if (!empty($data['exclude_product_categories']['value'])) {
                $args['wccbel_meta_filter'][] = [
                    'key' => 'exclude_product_categories',
                    'value' => esc_sql($data['exclude_product_categories']['value']),
                    'operator' => sanitize_text_field($data['exclude_product_categories']['operator']),
                ];
            }
            if (!empty($data['customer_email']['value'])) {
                $args['wccbel_meta_filter'][] = [
                    'key' => 'customer_email',
                    'value' => sanitize_text_field($data['customer_email']['value']),
                    'operator' => sanitize_text_field($data['customer_email']['operator']),
                ];
            }
            if (!empty($data['usage_limit']['from']) || !empty($data['usage_limit']['to'])) {
                $from = (!empty($data['usage_limit']['from'])) ? $data['usage_limit']['from'] : null;
                $to = (!empty($data['usage_limit']['to'])) ? $data['usage_limit']['to'] : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<_with_quotation';
                }
                $args['wccbel_meta_filter'][] = [
                    'key' => 'usage_limit',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (!empty($data['limit_usage_to_x_items']['from']) || !empty($data['limit_usage_to_x_items']['to'])) {
                $from = (!empty($data['limit_usage_to_x_items']['from'])) ? $data['limit_usage_to_x_items']['from'] : null;
                $to = (!empty($data['limit_usage_to_x_items']['to'])) ? $data['limit_usage_to_x_items']['to'] : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<_with_quotation';
                }
                $args['wccbel_meta_filter'][] = [
                    'key' => 'limit_usage_to_x_items',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (!empty($data['usage_limit_per_user']['from']) || !empty($data['usage_limit_per_user']['to'])) {
                $from = (!empty($data['usage_limit_per_user']['from'])) ? $data['usage_limit_per_user']['from'] : null;
                $to = (!empty($data['usage_limit_per_user']['to'])) ? $data['usage_limit_per_user']['to'] : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<_with_quotation';
                }
                $args['wccbel_meta_filter'][] = [
                    'key' => 'usage_limit_per_user',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (isset($data['custom_fields']) && !empty($data['custom_fields'])) {
                foreach ($data['custom_fields'] as $custom_field_item) {
                    switch ($custom_field_item['type']) {
                        case 'from-to-date':
                            $from = (!empty($custom_field_item['value'][0])) ? gmdate('Y-m-d H:i:s', strtotime($custom_field_item['value'][0])) : null;
                            $to = (!empty($custom_field_item['value'][1])) ? gmdate('Y-m-d H:i:s', strtotime($custom_field_item['value'][1])) : null;
                            if (empty($from) && empty($to)) {
                                $value = null;
                                $operator = null;
                                break;
                            }
                            if (!empty($from) & !empty($to)) {
                                $value = [$from, $to];
                                $operator = 'between_with_quotation';
                            } else if (!empty($from)) {
                                $value = $from;
                                $operator = '>=_with_quotation';
                            } else {
                                $value = $to;
                                $operator = '<=_with_quotation';
                            }
                            break;
                        case 'from-to-time':
                            $from = (!empty($custom_field_item['value'][0])) ? gmdate('H:i', strtotime($custom_field_item['value'][0])) : null;
                            $to = (!empty($custom_field_item['value'][1])) ? gmdate('H:i', strtotime($custom_field_item['value'][1])) : null;
                            if (empty($from) && empty($to)) {
                                $value = null;
                                $operator = null;
                                break;
                            }
                            if (!empty($from) & !empty($to)) {
                                $value = [$from, $to];
                                $operator = 'between_with_quotation';
                            } else if (!empty($from)) {
                                $value = $from;
                                $operator = '>=_with_quotation';
                            } else {
                                $value = $to;
                                $operator = '<=_with_quotation';
                            }
                            break;
                        case 'from-to-number':
                            $from = (!empty($custom_field_item['value'][0])) ? floatval($custom_field_item['value'][0]) : null;
                            $to = (!empty($custom_field_item['value'][1])) ? floatval($custom_field_item['value'][1]) : null;
                            if (empty($from) && empty($to)) {
                                $value = null;
                                $operator = null;
                                break;
                            }
                            if (!empty($from) & !empty($to)) {
                                $value = [$from, $to];
                                $operator = 'between';
                            } else if (!empty($from)) {
                                $value = $from;
                                $operator = '>=';
                            } else {
                                $value = $to;
                                $operator = '<=';
                            }
                            break;
                        case 'text':
                            $operator = $custom_field_item['operator'];
                            $value = $custom_field_item['value'];
                            break;
                        case 'select':
                            $operator = "like";
                            $value = $custom_field_item['value'];
                            break;
                    }

                    if (!empty($value)) {
                        if (is_array($value) && $custom_field_item['type'] == 'select') {
                            $values = [];
                            foreach ($value as $value_item) {
                                if (!empty($value_item)) {
                                    $values[] = $value_item;
                                }
                            }
                            if (!empty($values)) {
                                $args['wccbel_meta_filter'][] = [
                                    'key' => $custom_field_item['taxonomy'],
                                    'value' => $values,
                                    'operator' => $operator,
                                ];
                            }
                        } else {
                            $args['wccbel_meta_filter'][] = [
                                'key' => $custom_field_item['taxonomy'],
                                'value' => $value,
                                'operator' => $operator,
                            ];
                        }
                    }
                }
            }
        }

        return $args;
    }

    public static function apply_operator($old_value, $data)
    {
        if (empty($data['operator'])) {
            return $data['value'];
        }

        $data['value'] = (!empty($data['operator_type'])) ? self::apply_calculator_operator($old_value, $data) : self::apply_default_operator($old_value, $data);
        $data['value'] = (isset($data['round']) && !empty($data['round'])) ? self::round($data['value'], $data['round']) : $data['value'];

        return $data['value'];
    }

    private static function apply_calculator_operator($old_value, $data)
    {
        $old_value = floatval($old_value);
        $data['value'] = floatval($data['value']);
        $data['sale_price'] = (isset($data['sale_price'])) ? floatval($data['sale_price']) : 0;
        $data['regular_price'] = (isset($data['regular_price'])) ? floatval($data['regular_price']) : 0;

        switch ($data['operator_type']) {
            case 'n':
                switch ($data['operator']) {
                    case '+':
                        $data['value'] += $old_value;
                        break;
                    case '-':
                        $data['value'] = $old_value - $data['value'];
                        break;
                    case 'sp+':
                        $data['value'] += $data['sale_price'];
                        break;
                    case 'rp-':
                        $data['value'] = $data['regular_price'] - $data['value'];
                        break;
                }
                break;
            case '%':
                switch ($data['operator']) {
                    case '+':
                        $data['value'] = $old_value + ($old_value * $data['value'] / 100);
                        break;
                    case '-':
                        $data['value'] = $old_value - ($old_value * $data['value'] / 100);
                        break;
                    case 'sp+':
                        $data['value'] = $data['sale_price'] + ($data['sale_price'] * $data['value'] / 100);
                        break;
                    case 'rp-':
                        $data['value'] = $data['regular_price'] - ($data['regular_price'] * $data['value'] / 100);
                        break;
                }
                break;
        }

        return $data['value'];
    }

    private static function apply_default_operator($old_value, $data)
    {
        switch ($data['operator']) {
            case 'text_append':
                $data['value'] = $old_value . $data['value'];
                break;
            case 'text_prepend':
                $data['value'] = $data['value'] . $old_value;
                break;
            case 'text_new':
                $data['value'] = $data['value'];
                break;
            case 'text_delete':
                $data['value'] = str_replace($data['value'], '', $old_value);
                break;
            case 'text_replace':
                if (isset($data['value'])) {
                    $data['value'] = ($data['sensitive'] == 'yes') ? str_replace($data['value'], $data['replace'], $old_value) : str_ireplace($data['value'], $data['replace'], $old_value);
                } else {
                    $data['value'] = $old_value;
                }
                break;
            case 'text_remove_duplicate':
                $data['value'] = $old_value;
                break;
            case 'taxonomy_append':
                $data['value'] = array_unique(array_merge($old_value, $data['value']));
                break;
            case 'taxonomy_replace':
                $data['value'] = $data['value'];
                break;
            case 'taxonomy_delete':
                $data['value'] = array_values(array_diff($old_value, $data['value']));
                break;
            case 'number_new':
                $data['value'] = $data['value'];
                break;
            case 'number_delete':
                $data['value'] = str_replace($data['value'], '', $old_value);
                break;
            case 'number_clear':
                $data['value'] = '';
                break;
            case 'number_formula':
                $formulaCalculator = new Formula();
                $data['value'] = $formulaCalculator->calculate($data['value'], ['X' => $old_value]);
                break;
            case 'increase_by_value':
                $data['value'] = floatval($old_value) + floatval($data['value']);
                break;
            case 'decrease_by_value':
                $data['value'] = floatval($old_value) - floatval($data['value']);
                break;
            case 'increase_by_percent':
                $data['value'] = floatval($old_value) + floatval(floatval($old_value) * floatval($data['value']) / 100);
                break;
            case 'decrease_by_percent':
                $data['value'] = floatval($old_value) - floatval(floatval($old_value) * floatval($data['value']) / 100);
                break;
            case 'increase_by_value_from_sale':
                $data['value'] = (isset($data['sale_price'])) ? floatval($data['sale_price']) + floatval($data['value']) : $data;
                break;
            case 'increase_by_percent_from_sale':
                $data['value'] = (isset($data['sale_price'])) ? floatval($data['sale_price']) + floatval(floatval($data['sale_price']) * floatval($data['value']) / 100) : $data;
                break;
            case 'decrease_by_value_from_regular':
                $data['value'] = (isset($data['regular_price'])) ? floatval($data['regular_price']) - floatval($data['value']) : $data;
                break;
            case 'decrease_by_percent_from_regular':
                $data['value'] = (isset($data['regular_price'])) ? floatval($data['regular_price']) - (floatval($data['regular_price']) * floatval($data['value']) / 100) : $data;
                break;
        }

        return $data['value'];
    }

    public static function apply_variable($coupon, $value)
    {
        if (!($coupon instanceof \WC_Coupon)) {
            return $value;
        }

        $value = str_replace('{coupon_code}', $coupon->get_code(), $value);
        $value = str_replace('{id}', $coupon->get_id(), $value);
        return $value;
    }
}
