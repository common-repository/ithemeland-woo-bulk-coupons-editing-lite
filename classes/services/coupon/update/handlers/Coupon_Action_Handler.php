<?php

namespace wccbel\classes\services\coupon\update\handlers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\services\coupon\update\Handler_Interface;

class Coupon_Action_Handler implements Handler_Interface
{
    private static $instance;

    private $coupon_id;

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
        $methods = $this->get_methods();
        $method = (!empty($methods[$update_data['value']])) ? $methods[$update_data['value']] : '';
        if (empty($method) || !method_exists($this, $method)) {
            return false;
        }

        foreach ($coupon_ids as $coupon_id) {
            $this->coupon_id = intval($coupon_id);
            $this->{$method}();
        }

        return true;
    }

    private function get_methods()
    {
        return [
            'trash' => 'delete_coupon',
            'untrash' => 'restore_coupon'
        ];
    }

    private function delete_coupon()
    {
        return wp_trash_post($this->coupon_id);
    }

    private function restore_coupon()
    {
        return wp_untrash_post($this->coupon_id);
    }
}
