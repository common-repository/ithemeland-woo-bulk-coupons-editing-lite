<?php

namespace wccbel\classes\services\coupon\update\handlers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\repositories\History;
use wccbel\classes\services\coupon\update\Handler_Interface;

class WP_Posts_Handler implements Handler_Interface
{
    private static $instance;

    private $post;
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
        if (empty($coupon_ids) && !is_array($coupon_ids)) {
            return false;
        }

        $this->update_data = $update_data;

        foreach ($coupon_ids as $coupon_id) {
            if (!isset($this->update_data['value'])) {
                $this->update_data['value'] = '';
            }

            $post = get_post(intval($coupon_id));
            if (!($post instanceof \WP_Post)) {
                return false;
            }

            $this->post = $post;
            $this->current_field_value = (!empty($this->post->{$this->update_data['name']})) ? $this->post->{$this->update_data['name']} : '';

            $update_result = wp_update_post([
                'ID' => intval($coupon_id),
                sanitize_text_field($this->update_data['name']) => sanitize_text_field($this->update_data['value'])
            ]);

            if (!$update_result) {
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

    private function save_history()
    {
        $history_repository = History::get_instance();
        return $history_repository->save_history_item([
            'history_id' => $this->update_data['history_id'],
            'historiable_id' => $this->post->ID,
            'name' => $this->update_data['name'],
            'sub_name' => (!empty($this->update_data['sub_name'])) ? $this->update_data['sub_name'] : '',
            'type' => $this->update_data['type'],
            'prev_value' => $this->current_field_value,
            'new_value' => $this->update_data['value'],
        ]);
    }
}
