<?php

namespace wccbel\classes\helpers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\repositories\Meta_Field;

class Meta_Fields
{
    public static function get_default_meta_key()
    {
        return [];
    }

    public static function get_default_taxonomies()
    {
        return [];
    }

    public static function remove_default_meta_keys(array $meta_keys)
    {
        return array_diff($meta_keys, self::get_default_meta_key());
    }

    public static function get_meta_field_type($main_type, $sub_type)
    {
        $type = '';
        switch ($main_type) {
            case Meta_Field::TEXTINPUT:
                switch ($sub_type) {
                    case Meta_Field::STRING_TYPE:
                        $type = 'text';
                        break;
                    case Meta_Field::NUMBER:
                        $type = 'numeric';
                        break;
                }
                break;
            case Meta_Field::TEXTAREA:
                $type = 'textarea';
                break;
            case Meta_Field::ARRAY_TYPE:
                $type = 'text';
                break;
            case Meta_Field::CHECKBOX:
                $type = 'checkbox';
                break;
            case Meta_Field::CALENDAR:
                $type = 'date';
                break;
        }
        return $type;
    }
}
