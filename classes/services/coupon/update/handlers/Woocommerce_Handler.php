<?php

namespace wccbel\classes\services\coupon\update\handlers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\helpers\Coupon_Helper;
use wccbel\classes\repositories\Coupon;
use wccbel\classes\repositories\History;
use wccbel\classes\services\coupon\update\Handler_Interface;

class Woocommerce_Handler implements Handler_Interface
{
    private static $instance;

    private $coupon;
    private $update_data;
    private $setter_method;
    private $deleted_ids;
    private $created_ids;
    private $coupon_repository;
    private $current_field_value;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->coupon_repository = Coupon::get_instance();
    }

    public function update($coupon_ids, $update_data)
    {
        $this->setter_method = $this->get_setter($update_data['name']);
        if (empty($this->setter_method) && empty($coupon_ids) && !is_array($coupon_ids)) {
            return false;
        }

        foreach ($coupon_ids as $coupon_id) {
            $coupon = $this->coupon_repository->get_coupon(intval($coupon_id));
            if (!($coupon instanceof \WC_Coupon)) {
                return false;
            }

            $this->coupon = $coupon;
            $this->update_data = $update_data;

            // has update method ?
            if (!is_object(${$this->setter_method['object']}) || !method_exists(${$this->setter_method['object']}, $this->setter_method['method'])) {
                return false;
            };

            $coupon_array = $this->coupon_repository->coupon_to_array($this->coupon);
            $this->current_field_value = (isset($coupon_array[$this->update_data['name']])) ? $coupon_array[$this->update_data['name']] : '';

            // set value with operator
            if (!empty($this->update_data['operator'])) {
                $this->set_value_with_operator();
            }

            // replace text variable
            if (!is_numeric($this->update_data['value']) && !is_array($this->update_data['value'])) {
                $this->update_data['value'] = Coupon_Helper::apply_variable($coupon, $this->update_data['value']);
            }
            if (!empty($this->update_data['replace'])) {
                $this->update_data['replace'] = Coupon_Helper::apply_variable($coupon, $this->update_data['replace']);
            }

            // run update method
            try {
                ${$this->setter_method['object']}->{$this->setter_method['method']}($this->update_data['value']);
                if (method_exists(${$this->setter_method['object']}, 'save')) {
                    ${$this->setter_method['object']}->save();
                }
            } catch (\Exception $e) {
                return false;
            }

            // save history item
            if (!empty($this->update_data['history_id'])) {
                $result = $this->save_history();
                if (!$result) {
                    return false;
                }
            }
        }

        return true;
    }

    private function get_setter($field_name)
    {
        $methods = $this->get_setter_methods();
        return (!empty($methods[$field_name])) ? $methods[$field_name] : null;
    }

    private function set_free_shipping()
    {
        $this->coupon->set_free_shipping(!empty($this->update_data['value']) && in_array($this->update_data['value'], ['yes', 1]));
        $this->coupon->save();
    }

    private function set_individual_use()
    {
        $this->coupon->set_individual_use(!empty($this->update_data['value']) && in_array($this->update_data['value'], ['yes', 1]));
        $this->coupon->save();
    }

    private function set_exclude_sale_items()
    {
        $this->coupon->set_exclude_sale_items(!empty($this->update_data['value']) && in_array($this->update_data['value'], ['yes', 1]));
        $this->coupon->save();
    }

    private function get_setter_methods()
    {
        return [
            'coupon_code' => [
                'object' => 'coupon',
                'method' => 'set_code',
            ],
            'description' => [
                'object' => 'coupon',
                'method' => 'set_description',
            ],
            'date_created' => [
                'object' => 'coupon',
                'method' => 'set_date_created'
            ],
            'date_expires' => [
                'object' => 'coupon',
                'method' => 'set_date_expires'
            ],
            'product_ids' => [
                'object' => 'coupon',
                'method' => 'set_product_ids'
            ],
            'exclude_product_ids' => [
                'object' => 'coupon',
                'method' => 'set_excluded_product_ids'
            ],
            'product_categories' => [
                'object' => 'coupon',
                'method' => 'set_product_categories'
            ],
            'exclude_product_categories' => [
                'object' => 'coupon',
                'method' => 'set_excluded_product_categories'
            ],
            'coupon_amount' => [
                'object' => 'coupon',
                'method' => 'set_amount'
            ],
            'minimum_amount' => [
                'object' => 'coupon',
                'method' => 'set_minimum_amount'
            ],
            'maximum_amount' => [
                'object' => 'coupon',
                'method' => 'set_maximum_amount'
            ],
            'usage_limit' => [
                'object' => 'coupon',
                'method' => 'set_usage_limit'
            ],
            'limit_usage_to_x_items' => [
                'object' => 'coupon',
                'method' => 'set_limit_usage_to_x_items'
            ],
            'usage_limit_per_user' => [
                'object' => 'coupon',
                'method' => 'set_usage_limit_per_user'
            ],
            'discount_type' => [
                'object' => 'coupon',
                'method' => 'set_discount_type'
            ],
            'free_shipping' => [
                'object' => 'this',
                'method' => 'set_free_shipping'
            ],
            'individual_use' => [
                'object' => 'this',
                'method' => 'set_individual_use'
            ],
            'exclude_sale_items' => [
                'object' => 'this',
                'method' => 'set_exclude_sale_items'
            ],
            'usage_count' => [
                'object' => 'coupon',
                'method' => 'set_usage_count'
            ],
            'customer_email' => [
                'object' => 'coupon',
                'method' => 'set_email_restrictions'
            ],
            '_used_by' => [
                'object' => 'coupon',
                'method' => 'set_used_by'
            ],
        ];
    }

    private function set_value_with_operator()
    {
        $this->update_data['value'] = Coupon_Helper::apply_operator($this->current_field_value, $this->update_data);
    }

    private function save_history()
    {
        $history_repository = History::get_instance();
        return $history_repository->save_history_item([
            'history_id' => $this->update_data['history_id'],
            'historiable_id' => $this->coupon->get_id(),
            'name' => $this->update_data['name'],
            'sub_name' => (!empty($this->update_data['sub_name'])) ? $this->update_data['sub_name'] : '',
            'type' => $this->update_data['type'],
            'deleted_ids' => $this->deleted_ids,
            'created_ids' => $this->created_ids,
            'prev_value' => $this->current_field_value,
            'new_value' => $this->update_data['value'],
        ]);
    }
}
