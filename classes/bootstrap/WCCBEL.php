<?php

namespace wccbel\classes\bootstrap;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\controllers\WCCBEL_Ajax;
use wccbel\classes\controllers\WCCBEL_Post;
use wccbel\classes\repositories\Option;
use wccbel\classes\repositories\Setting;

class WCCBEL
{
    private static $instance;

    public static function init()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
    }

    private function __construct()
    {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        add_filter('safe_style_css', function ($styles) {
            $styles[] = 'display';
            return $styles;
        });

        WCCBEL_Top_Banners::register();

        WCCBEL_Ajax::register_callback();
        WCCBEL_Post::register_callback();
        (new WCCBEL_Meta_Fields())->init();
        (new WCCBEL_Custom_Queries())->init();

        // update all options
        (new Option())->update_options('wccbel', ['wccbel_meta_fields']);

        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public static function wccbel_woocommerce_required()
    {
        include WCCBEL_VIEWS_DIR . 'alerts/wccbel_woocommerce_required.php';
    }

    public static function wccbel_wp_init()
    {
        $version = get_option('wccbel-version');
        if (empty($version) || $version != WCCBEL_VERSION) {
            update_option('wccbel-version', WCCBEL_VERSION);
        }

        // load textdomain
        load_plugin_textdomain('ithemeland-woocommerce-bulk-coupons-editing-lite', false, WCCBEL_LANGUAGES_DIR);
    }

    public function add_menu()
    {
        if (defined('WBEBL_NAME')) {
            add_submenu_page('wbebl', esc_html__('Woo Coupons', 'ithemeland-woocommerce-bulk-coupons-editing-lite'), esc_html__('Woo Coupons', 'ithemeland-woocommerce-bulk-coupons-editing-lite'), 'manage_woocommerce', 'wccbel', ['wccbel\classes\controllers\Woo_Coupon_Controller', 'init'], 1);
        } else {
            add_menu_page(esc_html__('iT Woo Coupons', 'ithemeland-woocommerce-bulk-coupons-editing-lite'), sprintf('%s', '<span style="color: #627ddd;font-weight: 900;">iT</span> Woo Coupons'), 'manage_woocommerce', 'wccbel', ['wccbel\classes\controllers\Woo_Coupon_Controller', 'init'], WCCBEL_IMAGES_URL . 'wccbel_icon.svg', 2);
        }
    }

    public function enqueue_scripts($page)
    {
        if (!empty($_GET['page']) && $_GET['page'] == 'wccbel') {
            if (WCCBEL_Verification::is_active() || defined('WBEBL_NAME')) {
                $this->main_enqueue_scripts();
            } else {
                $this->activation_enqueue_scripts();
            }
        }
    }

    private function main_enqueue_scripts()
    {
        $setting_repository = new Setting();
        // Styles
        wp_enqueue_style('wccbel-reset', WCCBEL_CSS_URL . 'reset.css', [], WCCBEL_VERSION);
        wp_enqueue_style('wccbel-icomoon', WCCBEL_CSS_URL . 'icomoon.css', [], WCCBEL_VERSION);
        wp_enqueue_style('wccbel-datepicker', WCCBEL_CSS_URL . 'bootstrap-material-datetimepicker.css', [], WCCBEL_VERSION);
        wp_enqueue_style('wccbel-select2', WCCBEL_CSS_URL . 'select2.min.css', [], WCCBEL_VERSION);
        wp_enqueue_style('wccbel-sweetalert', WCCBEL_CSS_URL . 'sweetalert.css', [], WCCBEL_VERSION);
        wp_enqueue_style('wccbel-jquery-ui', WCCBEL_CSS_URL . 'jquery-ui.min.css', [], WCCBEL_VERSION);
        wp_enqueue_style('wccbel-tipsy', WCCBEL_CSS_URL . 'jquery.tipsy.css', [], WCCBEL_VERSION);
        wp_enqueue_style('wccbel-datetimepicker', WCCBEL_CSS_URL . 'jquery.datetimepicker.css', [], WCCBEL_VERSION);
        wp_enqueue_style('wccbel-main', WCCBEL_CSS_URL . 'style.css', [], WCCBEL_VERSION);
        wp_enqueue_style('wp-color-picker');

        // Scripts
        wp_enqueue_script('wccbel-datetimepicker', WCCBEL_JS_URL . 'jquery.datetimepicker.js', ['jquery'], WCCBEL_VERSION);
        wp_enqueue_script('wccbel-functions', WCCBEL_JS_URL . 'functions.js', ['jquery'], WCCBEL_VERSION);
        wp_enqueue_script('wccbel-select2', WCCBEL_JS_URL . 'select2.min.js', ['jquery'], WCCBEL_VERSION);
        wp_enqueue_script('wccbel-moment', WCCBEL_JS_URL . 'moment-with-locales.min.js', ['jquery'], WCCBEL_VERSION);
        wp_enqueue_script('wccbel-tipsy', WCCBEL_JS_URL . 'jquery.tipsy.js', ['jquery'], WCCBEL_VERSION);
        wp_enqueue_script('wccbel-bootstrap_datepicker', WCCBEL_JS_URL . 'bootstrap-material-datetimepicker.js', ['jquery'], WCCBEL_VERSION);
        wp_enqueue_script('wccbel-sweetalert', WCCBEL_JS_URL . 'sweetalert.min.js', ['jquery'], WCCBEL_VERSION);
        wp_enqueue_script('wccbel-main', WCCBEL_JS_URL . 'main.js', ['jquery'], WCCBEL_VERSION);
        wp_localize_script('wccbel-main', 'WCCBEL_DATA', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('wccbel_ajax_nonce'),
            'strings' => [
                'please_select_one_item' => __('Please select one coupon', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
            ],
            'wccbel_settings' => $setting_repository->get_settings()
        ]);
        wp_enqueue_media();
        wp_enqueue_editor();
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('wp-color-picker');
    }

    private function activation_enqueue_scripts()
    {
        wp_enqueue_style('wccbel-reset', WCCBEL_CSS_URL . 'reset.css', [], WCCBEL_VERSION);
        wp_enqueue_style('wccbel-sweetalert', WCCBEL_CSS_URL . 'sweetalert.css', [], WCCBEL_VERSION);
        wp_enqueue_style('wccbel-main', WCCBEL_CSS_URL . 'style.css', [], WCCBEL_VERSION);
        wp_enqueue_style('wccbel-activation', WCCBEL_CSS_URL . 'activation.css', [], WCCBEL_VERSION);

        wp_enqueue_script('wccbel-sweetalert', WCCBEL_JS_URL . 'sweetalert.min.js', ['jquery'], WCCBEL_VERSION);
        wp_enqueue_script('wccbel-activation', WCCBEL_JS_URL . 'activation.js', ['jquery'], WCCBEL_VERSION);
    }

    private static function create_tables()
    {
        global $wpdb;
        $history_table_name = esc_sql($wpdb->prefix . 'itbbc_history');
        $history_items_table_name = esc_sql($wpdb->prefix . 'itbbc_history_items');
        $query = '';
        $history_table = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($history_table_name)); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        if (!$wpdb->get_var($history_table) == $history_table_name) { // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $query .= "CREATE TABLE {$history_table_name} (
                  id int(11) NOT NULL AUTO_INCREMENT,
                  user_id int(11) NOT NULL,
                  fields text NOT NULL,
                  operation_type varchar(32) NOT NULL,
                  operation_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  reverted tinyint(1) NOT NULL DEFAULT '0',
                  sub_system varchar(64) NOT NULL,
                  PRIMARY KEY (id),
                  INDEX (user_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        }

        $history_items_table = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($history_items_table_name)); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        if (!$wpdb->get_var($history_items_table) == $history_items_table_name) { // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            $query .= "CREATE TABLE {$history_items_table_name} (
                      id int(11) NOT NULL AUTO_INCREMENT,
                      history_id int(11) NOT NULL,
                      historiable_id int(11) NOT NULL,
                      field longtext,
                      prev_value longtext,
                      new_value longtext,
                      PRIMARY KEY (id),
                      INDEX (history_id),
                      INDEX (historiable_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"; // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

            $query .= "ALTER TABLE {$history_items_table_name} ADD CONSTRAINT itbbc_history_items_history_id_relation FOREIGN KEY (history_id) REFERENCES {$history_table_name} (id) ON DELETE CASCADE ON UPDATE CASCADE;"; // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        } else {
            $result = $wpdb->get_results("SELECT DATA_TYPE as itbbc_field_type FROM information_schema.columns WHERE table_name = '{$history_items_table_name}' AND column_name = 'field'"); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            if (!empty($result[0]->itbbc_field_type) && $result[0]->itbbc_field_type != 'longtext') {
                $wpdb->query("ALTER TABLE {$history_items_table_name} MODIFY field longtext"); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            }
        }

        if (!empty($query)) {
            require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
            dbDelta($query); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        }
    }

    public static function activate()
    {
        if (!defined('WBEBL_NAME')) {
            update_option('wccbel-version', WCCBEL_VERSION);

            self::create_tables();
        }
    }

    public static function deactivate()
    {
        // 
    }
}
