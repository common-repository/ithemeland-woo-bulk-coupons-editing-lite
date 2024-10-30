<?php

namespace wccbel\classes\services\coupon\update;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\repositories\History;
use wccbel\classes\services\coupon\update\handlers\Coupon_Action_Handler;
use wccbel\classes\services\coupon\update\handlers\Meta_Field_Handler;
use wccbel\classes\services\coupon\update\handlers\Woocommerce_Handler;
use wccbel\classes\services\coupon\update\handlers\WP_Posts_Handler;

class WCCBEL_Coupon_Update
{
    private static $instance;
    private $coupon_ids;
    private $coupon_data;
    private $update_classes;
    private $save_history;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->update_classes = $this->get_update_classes();
    }

    public function set_update_data($data)
    {
        if (!isset($data['coupon_ids']) || empty($data['coupon_data']) || !is_array($data['coupon_data'])) {
            return false;
        }
        $this->coupon_ids = array_unique($data['coupon_ids']);
        $this->coupon_data = $data['coupon_data'];
        $this->save_history = (!empty($data['save_history']));
    }

    public function perform()
    {
        // save history
        if ($this->save_history) {
            $history_id = $this->save_history();
            if (empty($history_id)) {
                return false;
            }
        }

        foreach ($this->coupon_data as $update_item) {
            if (!empty($history_id)) {
                // set history id for save history item
                $update_item['history_id'] = intval($history_id);
            }

            // check items
            if (!$this->is_valid_update_item($update_item)) {
                continue;
            }

            $class = $this->update_classes[$update_item['type']];
            $instance = $class::get_instance();
            $update_result = $instance->update($this->coupon_ids, $update_item);
            if (!$update_result) {
                return false;
            }
        }

        return true;
    }

    private function is_valid_update_item($update_item)
    {
        // has require item ?
        if (
            empty($update_item['name'])
            || empty($update_item['type'])
            || (empty($update_item['value']) && (!empty($update_item['operator']) &&  !in_array($update_item['operator'], ['text_remove_duplicate', 'text_replace']) && $update_item['operation'] != 'inline_edit'))
        ) {
            return false;
        }

        // has update method ?
        if (!isset($this->update_classes[$update_item['type']]) || !class_exists($this->update_classes[$update_item['type']])) {
            return false;
        }

        return true;
    }

    private function get_update_classes()
    {
        return [
            'woocommerce_field' => Woocommerce_Handler::class,
            'wp_posts_field' => WP_Posts_Handler::class,
            'meta_field' => Meta_Field_Handler::class,
            'coupon_action' => Coupon_Action_Handler::class,
        ];
    }

    private function save_history()
    {
        $history_repository = History::get_instance();
        $fields = array_column($this->coupon_data, 'name');
        $history_id = $history_repository->create_history([
            'user_id' => intval(get_current_user_id()),
            'fields' => serialize($fields),
            'operation_type' => (count($this->coupon_data) > 1) ? History::BULK_OPERATION : History::INLINE_OPERATION,
            'operation_date' => gmdate('Y-m-d H:i:s'),
        ]);

        return $history_id;
    }
}
