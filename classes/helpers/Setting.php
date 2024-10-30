<?php


namespace wccbel\classes\helpers;

defined('ABSPATH') || exit(); // Exit if accessed directly


class Setting
{
    public static function get_arg_coupon_by($default_sort, $args)
    {
        switch ($default_sort) {
            case 'ID':
            case 'id':
                $args['orderby'] = 'ID';
                break;
            case 'coupon_code':
                $args['orderby'] = 'post_title';
                break;
            case 'post_date':
                $args['orderby'] = 'post_date';
                break;
            case 'post_modified':
                $args['orderby'] = 'post_modified';
                break;
        }

        return $args;
    }
}
