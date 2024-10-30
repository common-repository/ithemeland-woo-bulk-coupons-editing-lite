<?php

namespace wccbel\classes\providers\coupon;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\providers\column\CouponColumnProvider;
use wccbel\classes\repositories\Column;
use wccbel\classes\repositories\Coupon;

class CouponProvider
{
    private static $instance = null;
    private $coupon_repository;

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

    public function get_items($items, $columns)
    {
        if (!empty($items)) {
            $column_provider = CouponColumnProvider::get_instance();
            $show_id_column = Column::SHOW_ID_COLUMN;
            $next_static_columns = Column::get_static_columns();
            foreach ($items as $coupon_id) {
                $item = $this->coupon_repository->get_coupon(intval($coupon_id));
                include WCCBEL_VIEWS_DIR . "data_table/row.php";
            }
        }
    }
}
