<?php

namespace wccbel\classes\bootstrap;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\helpers\Others;

class WCCBEL_Verification
{
    public static function is_active()
    {
        if (Others::isAllowedDomain()) {
            return 'yes';
        }

        $is_active = get_option('wccbel_is_active', 'no');
        return ($is_active == 'yes' || $is_active == 'skipped');
    }

    public static function skipped()
    {
        $skipped = get_option('wccbel_is_active', 'no');
        return $skipped == 'skipped';
    }
}
