<?php

namespace wccbel\classes\repositories;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\helpers\Meta_Fields;

class Column
{
    const SHOW_ID_COLUMN = true;
    const DEFAULT_PROFILE_NAME = 'default';

    private $columns_option_name;
    private $active_columns_option_name;

    public function __construct()
    {
        $this->columns_option_name = "wccbel_column_fields";
        $this->active_columns_option_name = 'wccbel_active_columns';
    }

    public function update(array $data)
    {
        if (!isset($data['key'])) {
            return false;
        }

        $presets = $this->get_presets();
        $presets[$data['key']] = $data;
        return update_option($this->columns_option_name, $presets);
    }

    public function delete($preset_key)
    {
        $presets = $this->get_presets();
        if (is_array($presets) && array_key_exists($preset_key, $presets)) {
            unset($presets[$preset_key]);
        }
        return update_option($this->columns_option_name, $presets);
    }

    public function get_preset($preset_key)
    {
        $presets = $this->get_presets();
        return (isset($presets[$preset_key])) ? $presets[$preset_key] : false;
    }

    public function get_presets()
    {
        return get_option($this->columns_option_name);
    }

    public function get_presets_fields()
    {
        $presets_fields = [];
        $presets = $this->get_presets();
        if (!empty($presets)) {
            foreach ($presets as $key => $preset) {
                $presets_fields[$key] = (!empty($preset['checked'])) ? $preset['checked'] : [];
            }
        }

        return $presets_fields;
    }

    public function set_active_columns(string $profile_name, array $columns, string $option_name = "")
    {
        $option_name = (!empty($option_name)) ? esc_sql($option_name) : $this->active_columns_option_name;
        return update_option($option_name, ['name' => $profile_name, 'fields' => $columns]);
    }

    public function get_active_columns()
    {
        return get_option($this->active_columns_option_name);
    }

    public function delete_active_columns()
    {
        return delete_option($this->active_columns_option_name);
    }

    public function has_column_fields()
    {
        $columns = get_option($this->columns_option_name);
        return !empty($columns['default']['fields']);
    }

    public static function get_columns_title()
    {
        return [
            'coupon_amount' => "Value of the coupon",
            'date_expires' => "The coupon will expire <br> at 00:00:00 of this date",
            'minimum_amount' => "This field allows you to set <br> the minimum spend (subtotal) allowed <br> to use the coupon.",
            'maximum_amount' => "This field allows you to set <br> the maximum spend (subtotal) allowed <br> when using the coupon.",
            'product_ids' => "Products that the coupon will <br> be applied to, or that need to be in <br> the cart in order for the <br> 'Fixed cart discount to be applied.",
            'exclude_product_ids' => "Products that the coupon will not <br> be applied to, or that cannot to be in <br> the cart in order for the <br> 'Fixed cart discount to be applied.",
            'product_categories' => "Product categories that the <br> coupon will be applied to, or that need to <br> be in the cart in order for the <br> 'Fixed cart discount' to be applied.",
            'exclude_product_categories' => "Product categories that the coupon will <br> not be applied to, or that cannot be in the <br> cart in order for the <br> 'Fixed cart discount' to be applied.",
            'customer_email' => "List of allowed billing emails <br> to check against when an order is placed. <br> Separate email addresses with commas.<br> You can also use an asterisk (*) to <br> match parts of an email",
            'usage_limit' => "How many times this coupon <br> can be used before it is void.",
            'usage_limit_per_user' => "How many times this coupon <br> can be used by an individual user. <br> Uses billing email for guests, and user ID <br> for logged in users.",
        ];
    }

    public static function get_static_columns()
    {
        return [
            'coupon_code' => [
                'field' => 'coupon_code',
                'update_type' => 'woocommerce_field',
                'title' => __('Coupon Code', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            ],
        ];
    }

    public function update_meta_field_items()
    {
        $presets = $this->get_presets();
        $meta_fields = (new Meta_Field())->get();
        if (!empty($presets)) {
            foreach ($presets as $preset) {
                if (!empty($preset['fields'])) {
                    foreach ($preset['fields'] as $field) {
                        if (isset($field['field_type'])) {
                            if (isset($meta_fields[$field['name']])) {
                                $preset['fields'][$field['name']]['content_type'] = Meta_Fields::get_meta_field_type($meta_fields[$field['name']]['main_type'], $meta_fields[$field['name']]['sub_type']);
                                $this->update($preset);
                            }
                        }
                    }
                }
            }
        }
    }

    public function set_default_columns()
    {
        $fields['default'] = [
            'name' => 'Default',
            'date_modified' => gmdate('Y-m-d H:i:s', time()),
            'key' => 'default',
            'fields' => $this->get_default_columns_default(),
            'checked' => array_keys($this->get_default_columns_default()),
        ];
        return update_option('wccbel_column_fields', $fields);
    }

    public function get_grouped_fields()
    {
        $grouped_fields = [];
        $fields = $this->get_fields();
        if (!empty($fields)) {
            foreach ($fields as $key => $field) {
                if (isset($field['field_type'])) {
                    switch ($field['field_type']) {
                        case 'general':
                            $grouped_fields['General'][$key] = $field;
                            break;
                        case 'usage_limits':
                            $grouped_fields['Usage limits'][$key] = $field;
                            break;
                        case 'usage_restriction':
                            $grouped_fields['Usage restriction'][$key] = $field;
                            break;
                        case 'custom_field':
                            $grouped_fields['Custom Fields'][$key] = $field;
                            break;
                    }
                } else {
                    $grouped_fields['General'][$key] = $field;
                }
            }
        }
        return $grouped_fields;
    }

    public function get_fields()
    {
        $discount_types = wc_get_coupon_types();
        $coupon_repository = Coupon::get_instance();
        $coupon_statuses = $coupon_repository->get_coupon_statuses();

        return apply_filters('wccbel_column_fields', [
            'description' => [
                'name' => 'description',
                'label' => __('Description', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'text',
                'update_type' => 'woocommerce_field',
                'field_type' => 'general',
            ],
            'date_created' => [
                'name' => 'date_created',
                'label' => __('Published on', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => true,
                'content_type' => 'date_time_picker',
                'update_type' => 'woocommerce_field',
                'field_type' => 'general',
            ],
            'post_modified' => [
                'name' => 'post_modified',
                'label' => __('Modification date', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => false,
                'sortable' => true,
                'content_type' => 'date',
                'update_type' => 'wp_posts_field',
                'field_type' => 'general',
            ],
            'date_expires' => [
                'name' => 'date_expires',
                'label' => __('Coupon expiry date', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'date',
                'update_type' => 'woocommerce_field',
                'field_type' => 'general',
            ],
            'product_ids' => [
                'name' => 'product_ids',
                'label' => __('Products', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'products',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_restriction',
            ],
            'exclude_product_ids' => [
                'name' => 'exclude_product_ids',
                'label' => __('Exclude products', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'products',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_restriction',
            ],
            'product_categories' => [
                'name' => 'product_categories',
                'label' => __('Product categories', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'product_categories',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_restriction',
            ],
            'exclude_product_categories' => [
                'name' => 'exclude_product_categories',
                'label' => __('Exclude categories', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'product_categories',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_restriction',
            ],
            'coupon_amount' => [
                'name' => 'coupon_amount',
                'label' => __('Coupon amount', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'numeric',
                'update_type' => 'woocommerce_field',
                'field_type' => 'general',
            ],
            'minimum_amount' => [
                'name' => 'minimum_amount',
                'label' => __('Minimum spend', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'numeric',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_restriction',
            ],
            'maximum_amount' => [
                'name' => 'maximum_amount',
                'label' => __('Maximum spend', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'numeric',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_restriction',
            ],
            'usage_limit' => [
                'name' => 'usage_limit',
                'label' => __('Usage limit per coupon', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'numeric',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_limits',
            ],
            'usage_limit_per_user' => [
                'name' => 'usage_limit_per_user',
                'label' => __('Usage limit per user', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'numeric',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_limits',
            ],
            'limit_usage_to_x_items' => [
                'name' => 'limit_usage_to_x_items',
                'label' => __('Usage limit to x items', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'numeric',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_limits',
            ],
            'discount_type' => [
                'name' => 'discount_type',
                'label' => __('Discount type', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'select',
                'update_type' => 'woocommerce_field',
                'options' => $discount_types,
                'field_type' => 'general',
            ],
            'post_status' => [
                'name' => 'post_status',
                'label' => __('Status', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'select',
                'update_type' => 'wp_posts_field',
                'options' => $coupon_statuses,
                'field_type' => 'general',
            ],
            'free_shipping' => [
                'name' => 'free_shipping',
                'label' => __('Allow free shipping', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'checkbox_dual_mode',
                'update_type' => 'woocommerce_field',
                'field_type' => 'general',
            ],
            'individual_use' => [
                'name' => 'individual_use',
                'label' => __('Individual use only', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'checkbox_dual_mode',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_restriction',
            ],
            'exclude_sale_items' => [
                'name' => 'exclude_sale_items',
                'label' => __('Exclude sale items', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'checkbox_dual_mode',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_restriction',
            ],
            'customer_email' => [
                'name' => 'customer_email',
                'label' => __('Allowed Emails', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'text',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_restriction',
            ],
            'usage_count' => [
                'name' => 'usage_count',
                'label' => __('Usage count', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => false,
                'sortable' => false,
                'content_type' => 'text',
                'update_type' => 'woocommerce_field',
                'field_type' => 'general',
            ],
            '_used_by' => [
                'name' => '_used_by',
                'label' => __('Used by', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'used_by',
                'update_type' => 'woocommerce_field',
                'field_type' => 'general',
            ],
            'used_in' => [
                'name' => 'used_in',
                'label' => __('Used in', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'used_in',
                'update_type' => 'woocommerce_field',
                'field_type' => 'general',
            ],
        ]);
    }

    public function set_default_active_columns()
    {
        return $this->set_active_columns(self::DEFAULT_PROFILE_NAME, self::get_default_columns_default());
    }

    public static function get_default_columns_name()
    {
        return [
            'default',
        ];
    }

    public static function get_default_columns_default()
    {
        $discount_types = wc_get_coupon_types();

        return [
            'discount_type' => [
                'name' => 'discount_type',
                'label' => __('Discount type', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'title' => __('Discount type', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'options' => $discount_types,
                'content_type' => 'select',
                'update_type' => 'woocommerce_field',
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
            ],
            'coupon_amount' => [
                'name' => 'coupon_amount',
                'label' => __('Coupon amount', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'title' => __('Coupon amount', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'numeric',
                'update_type' => 'woocommerce_field',
                'field_type' => 'general',
                'background_color' => '#fff',
                'text_color' => '#444',
            ],
            'description' => [
                'name' => 'description',
                'label' => __('Description', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'title' => __('Description', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'text',
                'update_type' => 'woocommerce_field',
                'field_type' => 'general',
                'background_color' => '#fff',
                'text_color' => '#444',
            ],
            'product_ids' => [
                'name' => 'product_ids',
                'label' => __('Products', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'title' => __('Products', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'products',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_restriction',
                'background_color' => '#fff',
                'text_color' => '#444',
            ],
            'usage_limit' => [
                'name' => 'usage_limit',
                'label' => __('Usage limit per coupon', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'title' => __('Usage limit per coupon', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'sortable' => false,
                'content_type' => 'numeric',
                'update_type' => 'woocommerce_field',
                'field_type' => 'usage_limits',
                'text_color' => '#444',
            ],
            'date_expires' => [
                'name' => 'date_expires',
                'label' => __('Coupon expiry date', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'title' => __('Coupon expiry date', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'editable' => true,
                'content_type' => 'date',
                'update_type' => 'woocommerce_field',
                'background_color' => '#fff',
                'text_color' => '#444',
                'field_type' => 'general',
            ],
        ];
    }
}
