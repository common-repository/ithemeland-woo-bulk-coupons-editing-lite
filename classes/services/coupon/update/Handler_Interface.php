<?php

namespace wccbel\classes\services\coupon\update;

defined('ABSPATH') || exit(); // Exit if accessed directly

interface Handler_Interface
{
    public static function get_instance();

    public function update($coupon_ids, $update_data);
}
