<?php

namespace wccbel\classes\controllers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\helpers\Sanitizer;
use wccbel\classes\repositories\Flush_Message;
use wccbel\classes\repositories\Column;
use wccbel\classes\repositories\Coupon;
use wccbel\classes\repositories\Search;
use wccbel\classes\repositories\Setting;
use wccbel\classes\services\activation\Activation_Service;

class WCCBEL_Post
{
    private static $instance;

    public static function register_callback()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_post_wccbel_load_column_profile', [$this, 'load_column_profile']);
        add_action('admin_post_wccbel_settings', [$this, 'settings']);
        add_action('admin_post_wccbel_export_coupons', [$this, 'export_coupons']);
        add_action('admin_post_wccbel_import_coupons', [$this, 'import_coupons']);
        add_action('admin_post_wccbel_save_column_profile', [$this, 'save_column_profile']);
        add_action('admin_post_wccbel_activation_plugin', [$this, 'activation_plugin']);
    }

    public function load_column_profile()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wccbel_post_nonce')) {
            die('403 Forbidden');
        }

        if (isset($_POST['preset_key'])) {
            $preset_key = sanitize_text_field($_POST['preset_key']);
            $checked_columns = Sanitizer::array($_POST["columns"]);
            $checked_columns = array_combine($checked_columns, $checked_columns);
            $column_repository = new Column();
            $columns = [];
            $fields = $column_repository->get_fields();
            $preset_columns = $column_repository->get_preset($preset_key);
            if (!empty($checked_columns) && is_array($checked_columns)) {
                if (!empty($preset_columns['fields'])) {
                    foreach ($preset_columns['fields'] as $column_key => $preset_column) {
                        if (isset($checked_columns[$column_key])) {
                            $columns[$column_key] = $preset_column;
                            unset($checked_columns[$column_key]);
                        }
                    }
                }

                if (!empty($checked_columns)) {
                    foreach ($checked_columns as $column_item) {
                        if (isset($fields[$column_item])) {
                            $checked_column = [
                                'name' => $column_item,
                                'label' => $fields[$column_item]['label'],
                                'title' => $fields[$column_item]['label'],
                                'editable' => $fields[$column_item]['editable'],
                                'content_type' => $fields[$column_item]['content_type'],
                                'update_type' => $fields[$column_item]['update_type'],
                                'allowed_type' => $fields[$column_item]['allowed_type'],
                                'background_color' => '#fff',
                                'text_color' => '#444',
                            ];
                            if (isset($fields[$column_item]['sortable'])) {
                                $checked_column['sortable'] = ($fields[$column_item]['sortable']);
                            }
                            if (isset($fields[$column_item]['options'])) {
                                $checked_column['options'] = $fields[$column_item]['options'];
                            }
                            if (isset($fields[$column_item]['field_type'])) {
                                $checked_column['field_type'] = $fields[$column_item]['field_type'];
                            }
                            $columns[$column_item] = $checked_column;
                        }
                    }
                }
            }

            $column_repository->set_active_columns($preset_key, $columns);
        }
        $this->redirect();
    }

    public function settings()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wccbel_post_nonce')) {
            die('403 Forbidden');
        }

        if (isset($_POST['settings'])) {
            $setting_repository = new Setting();
            $setting_repository->update(Sanitizer::array($_POST['settings']));
        }

        $this->redirect([
            'message' => __('Success !', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'type' => 'success'
        ]);
    }

    public function export_coupons()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wccbel_post_nonce')) {
            die('403 Forbidden');
        }

        $file_name = "wccbel-coupon-export-" . time() . '.csv';
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename={$file_name}");
        header("Pragma: no-cache");
        header("Expires: 0");
        $file = fopen('php://output', 'w');
        fwrite($file, chr(239) . chr(187) . chr(191)); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
        $search_repository = new Search();
        $current_data = $search_repository->get_current_data();
        $last_filter_data = !empty($current_data['last_filter_data']) ? $current_data['last_filter_data'] : null;
        $column_repository = new Column();
        $coupon_repository = Coupon::get_instance();

        if (isset($_POST['coupons'])) {
            switch ($_POST['coupons']) {
                case 'all':
                    $args = \wccbel\classes\helpers\Coupon_Helper::set_filter_data_items($last_filter_data, [
                        'posts_per_page' => '-1',
                        'post_type' => 'shop_coupon',
                        'post_status' => 'any',
                        'fields' => 'ids',
                    ]);
                    $coupons = $coupon_repository->get_coupons($args);
                    $coupon_ids = $coupons->posts;
                    break;
                case 'selected':
                    $coupon_ids = isset($_POST['item_ids']) ? array_map('intval', $_POST['item_ids']) : [];
                    break;
            }

            switch ($_POST['fields']) {
                case 'all':
                    $columns = $column_repository->get_fields();
                    break;
                case 'visible':
                    $columns = $column_repository->get_active_columns()['fields'];
                    break;
            }

            $except_columns = $coupon_repository->get_except_columns_for_export();
            if (!empty($except_columns) && is_array($except_columns)) {
                foreach ($except_columns as $except_column) {
                    if (isset($columns[$except_column])) {
                        unset($columns[$except_column]);
                    }
                }
            }

            if (!empty($coupon_ids)) {
                $header[] = "id";
                $header[] = "coupon_code";
                if (!empty($columns)) {
                    foreach ($columns as $column_key => $column) {
                        $header[] = $column_key;
                    }
                }
                fputcsv($file, $header);

                foreach ($coupon_ids as $coupon_id) {
                    $output = [];
                    $coupon_object = $coupon_repository->get_coupon(intval($coupon_id));
                    if ($coupon_object instanceof \WC_Coupon) {
                        $coupon = $coupon_repository->coupon_to_array($coupon_object);
                        if (!empty($coupon) && is_array($coupon)) {
                            $output[] = $coupon['id'];
                            $output[] = $coupon['coupon_code'];
                            if (!empty($columns)) {
                                foreach ($columns as $column_key => $column_item) {
                                    if (!isset($column_item['field_type'])) {
                                        if (isset($coupon[$column_key])) {
                                            $output[] = (is_array($coupon[$column_key])) ? (string)implode(',', $coupon[$column_key]) : (string)$coupon[$column_key];
                                        } else {
                                            $output[] = " ";
                                        }
                                    } else {
                                        switch ($column_item['field_type']) {
                                            case 'custom_field':
                                                if (isset($coupon['custom_field'][$column_key])) {
                                                    $output[] = (is_array($coupon['custom_field'][$column_key])) ? (string)implode(',', $coupon['custom_field'][$column_key]) : (string)$coupon['custom_field'][$column_key];
                                                } else {
                                                    $output[] = " ";
                                                }
                                                break;
                                            default:
                                                if (isset($coupon[$column_key])) {
                                                    $output[] = (is_array($coupon[$column_key])) ? (string)implode(',', $coupon[$column_key]) : (string)$coupon[$column_key];
                                                } else {
                                                    $output[] = " ";
                                                }
                                                break;
                                        }
                                    }
                                }
                                fputcsv($file, $output);
                            }
                        }
                    }
                }
            }
        }
        die();
    }

    public function import_coupons()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wccbel_post_nonce')) {
            die('403 Forbidden');
        }

        ini_set('max_execution_time', 900);
        $wccbel_upload_dir = wp_upload_dir('wccbel')['path'];
        $notice = [
            'message' => __('Error ! Please Try again', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'type' => 'danger'
        ];
        if (isset($_FILES["import_file"]) && !empty($_FILES['import_file']['type']) && in_array($_FILES['import_file']['type'], ['text/csv', 'application/vnd.ms-excel'])) {
            $target_file = $wccbel_upload_dir . '/' . time() . wp_rand(100, 999) . '.csv';
            $result = move_uploaded_file($_FILES["import_file"]["tmp_name"], $target_file); //  phpcs:ignore Generic.PHP.ForbiddenFunctions.Found 
            if ($result) {
                $notice = [
                    'message' => __('Success !', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                    'type' => 'success'
                ];
                $coupon_repository = Coupon::get_instance();
                $coupon_repository->import_from_csv($target_file);
            }
        }

        $this->redirect($notice);
    }

    public function activation_plugin()
    {
        if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'wccbel_post_nonce')) {
            die('403 Forbidden');
        }

        $message = "Error! Try again";

        if (isset($_POST['activation_type'])) {
            if ($_POST['activation_type'] == 'skip') {
                update_option('wccbel_is_active', 'skipped');
                return $this->redirect('bulk-edit');
            } else {
                if (!empty($_POST['email']) && !empty($_POST['industry'])) {
                    $activation_service = new Activation_Service();
                    $info = $activation_service->activation([
                        'email' => sanitize_email($_POST['email']),
                        'domain' => $_SERVER['SERVER_NAME'],
                        'product_id' => 'wccbel',
                        'product_name' => WCCBEL_LABEL,
                        'industry' => sanitize_text_field($_POST['industry']),
                        'multi_site' => is_multisite(),
                        'core_version' => null,
                        'subsystem_version' => WCCBEL_VERSION,
                    ]);

                    if (!empty($info) && is_array($info)) {
                        if (!empty($info['result']) && $info['result'] == true) {
                            update_option('wccbel_is_active', 'yes');
                            $message = esc_html__('Success !', 'ithemeland-woocommerce-bulk-coupons-editing-lite');
                        } else {
                            update_option('wccbel_is_active', 'no');
                            $message = (!empty($info['message'])) ? esc_html($info['message']) : esc_html__('System Error !', 'ithemeland-woocommerce-bulk-coupons-editing-lite');
                        }
                    } else {
                        update_option('wccbel_is_active', 'no');
                        $message = esc_html__('Connection Timeout! Please Try Again', 'ithemeland-woocommerce-bulk-coupons-editing-lite');
                    }
                }
            }
        }

        $this->redirect($message);
    }

    private function redirect($notice = [])
    {
        if (!empty($notice) && isset($notice['message'])) {
            $flush_message_repository = new Flush_Message();
            $flush_message_repository->set($notice);
        }

        return wp_redirect(WCCBEL_PLUGIN_MAIN_PAGE);
    }
}
