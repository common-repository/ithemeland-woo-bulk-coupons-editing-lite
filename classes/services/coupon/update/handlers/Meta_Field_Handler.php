<?php

namespace wccbel\classes\services\coupon\update\handlers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\helpers\Coupon_Helper;
use wccbel\classes\repositories\Coupon;
use wccbel\classes\repositories\History;
use wccbel\classes\services\coupon\update\Handler_Interface;

class Meta_Field_Handler implements Handler_Interface
{
    private static $instance;

    private $coupon_ids;
    private $coupon_repository;
    private $coupon;
    private $setter_method;
    private $update_data;
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
    }

    public function update($coupon_ids, $update_data)
    {
        $this->setter_method = $this->get_setter($update_data['name']);
        if (empty($this->setter_method) && empty($coupon_ids) && !is_array($coupon_ids)) {
            return false;
        }

        // has update method ?
        if (!method_exists($this, $this->setter_method)) {
            return false;
        };

        $this->update_data = $update_data;
        $this->coupon_ids = $coupon_ids;

        foreach ($coupon_ids as $coupon_id) {
            if (!isset($this->update_data['value'])) {
                $this->update_data['value'] = '';
            }

            $this->coupon_repository = Coupon::get_instance();
            $this->coupon = $this->coupon_repository->get_coupon(intval($coupon_id));
            if (!($this->coupon instanceof \WC_Coupon)) {
                return false;
            }

            $this->current_field_value = (!empty($this->update_data['name'])) ? get_post_meta($this->coupon->get_id(), $this->update_data['name'], true) : '';

            // run update method
            $this->{$this->setter_method}();

            // save history item
            if (!empty($this->update_data['history_id'])) {
                $history_repository = History::get_instance();
                $history_item_result = $history_repository->save_history_item([
                    'history_id' => $this->update_data['history_id'],
                    'historiable_id' => $this->coupon->get_id(),
                    'name' => $this->update_data['name'],
                    'sub_name' => (!empty($this->update_data['sub_name'])) ? $this->update_data['sub_name'] : '',
                    'type' => $this->update_data['type'],
                    'prev_value' => $this->current_field_value,
                    'new_value' => $this->update_data['value'],
                ]);
                if (!$history_item_result) {
                    return false;
                }
            }
        }

        return true;
    }

    private function get_setter($field_name)
    {
        $setter_methods = $this->get_setter_methods();
        return (!empty($setter_methods[$field_name])) ? $setter_methods[$field_name] : $setter_methods['default_meta_field'];
    }

    private function get_setter_methods()
    {
        return [
            'default_meta_field' => 'set_default_meta_field',
        ];
    }

    private function set_default_meta_field()
    {
        // set value with operator
        if (!empty($this->update_data['operator'])) {
            $this->update_data['value'] = Coupon_Helper::apply_operator($this->current_field_value, $this->update_data);
        }
        return update_post_meta($this->coupon->get_id(), esc_sql($this->update_data['name']), esc_sql($this->update_data['value']));
    }
}
