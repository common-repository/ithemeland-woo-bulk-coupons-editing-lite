<?php

namespace wccbel\classes\controllers;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\helpers\Render;
use wccbel\classes\helpers\Sanitizer;
use wccbel\classes\helpers\Coupon_Helper;
use wccbel\classes\helpers\Filter_Helper;
use wccbel\classes\helpers\Meta_Fields;
use wccbel\classes\repositories\Column;
use wccbel\classes\repositories\Coupon;
use wccbel\classes\repositories\History;
use wccbel\classes\repositories\Meta_Field;
use wccbel\classes\repositories\Product;
use wccbel\classes\repositories\Search;
use wccbel\classes\repositories\Setting;
use wccbel\classes\services\coupon\update\WCCBEL_Coupon_Update;

class WCCBEL_Ajax
{
    private static $instance;
    private $coupon_repository;
    private $history_repository;

    public static function register_callback()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->coupon_repository = Coupon::get_instance();
        $this->history_repository = History::get_instance();
        add_action('wp_ajax_wccbel_add_meta_keys_by_coupon_id', [$this, 'add_meta_keys_by_coupon_id']);
        add_action('wp_ajax_wccbel_column_manager_add_field', [$this, 'column_manager_add_field']);
        add_action('wp_ajax_wccbel_column_manager_get_fields_for_edit', [$this, 'column_manager_get_fields_for_edit']);
        add_action('wp_ajax_wccbel_coupons_filter', [$this, 'coupons_filter']);
        add_action('wp_ajax_wccbel_save_filter_preset', [$this, 'save_filter_preset']);
        add_action('wp_ajax_wccbel_coupon_edit', [$this, 'coupon_edit']);
        add_action('wp_ajax_wccbel_create_new_coupon', [$this, 'create_new_coupon']);
        add_action('wp_ajax_wccbel_delete_coupons', [$this, 'delete_coupons']);
        add_action('wp_ajax_wccbel_untrash_coupons', [$this, 'untrash_coupons']);
        add_action('wp_ajax_wccbel_empty_trash', [$this, 'empty_trash']);
        add_action('wp_ajax_wccbel_duplicate_coupon', [$this, 'duplicate_coupon']);
        add_action('wp_ajax_wccbel_load_filter_profile', [$this, 'load_filter_profile']);
        add_action('wp_ajax_wccbel_delete_filter_profile', [$this, 'delete_filter_profile']);
        add_action('wp_ajax_wccbel_save_column_profile', [$this, 'save_column_profile']);
        add_action('wp_ajax_wccbel_get_text_editor_content', [$this, 'get_text_editor_content']);
        add_action('wp_ajax_wccbel_change_count_per_page', [$this, 'change_count_per_page']);
        add_action('wp_ajax_wccbel_filter_profile_change_use_always', [$this, 'filter_profile_change_use_always']);
        add_action('wp_ajax_wccbel_get_default_filter_profile_coupons', [$this, 'get_default_filter_profile_coupons']);
        add_action('wp_ajax_wccbel_sort_by_column', [$this, 'sort_by_column']);
        add_action('wp_ajax_wccbel_get_products', [$this, 'get_products']);
        add_action('wp_ajax_wccbel_get_categories', [$this, 'get_categories']);
        add_action('wp_ajax_wccbel_get_coupon_products', [$this, 'get_coupon_products']);
        add_action('wp_ajax_wccbel_get_coupon_categories', [$this, 'get_coupon_categories']);
        add_action('wp_ajax_wccbel_get_coupon_used_in', [$this, 'get_coupon_used_in']);
        add_action('wp_ajax_wccbel_get_coupon_used_by', [$this, 'get_coupon_used_by']);
        add_action('wp_ajax_wccbel_clear_filter_data', [$this, 'clear_filter_data']);
        add_action('wp_ajax_wccbel_history_change_page', [$this, 'history_change_page']);
    }

    public function get_default_filter_profile_coupons()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        $filter_data = Filter_Helper::get_active_filter_data();
        $result = $this->coupon_repository->get_coupons_list($filter_data, 1);
        $this->make_response([
            'success' => true,
            'filter_data' => $filter_data,
            'coupons_list' => $result->coupons_list,
            'product_ids' => $result->product_ids,
            'exclude_product_ids' => $result->exclude_product_ids,
            'product_categories' => $result->product_categories,
            'exclude_product_categories' => $result->exclude_product_categories,
            'pagination' => $result->pagination,
            'status_filters' => $result->status_filters,
            'coupons_count' => $result->count,
        ]);
    }

    public function get_products()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (!isset($_POST['search'])) {
            return false;
        }

        $list = [];
        $product_repository = new Product();
        $products = $product_repository->get_products([
            'posts_per_page' => '-1',
            'post_type' => ['product', 'product_variation'],
            's' => strtolower(sanitize_text_field($_POST['search'])),
        ]);

        if (!empty($products)) {
            foreach ($products as $product) {
                $list['results'][] = [
                    'id' => $product->ID,
                    'text' => $product->post_title,
                ];
            }
        }

        $this->make_response($list);
    }

    public function get_categories()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        $list = [];
        $product_repository = new Product();
        $categories = $product_repository->get_categories_by_name(sanitize_text_field($_POST['search']));
        if (!empty($categories)) {
            foreach ($categories as $category) {
                if ($category instanceof \WP_Term) {
                    $list['results'][] = [
                        'id' => $category->term_id,
                        'text' => $category->name,
                    ];
                }
            }
        }
        $this->make_response($list);
    }

    public function get_coupon_products()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (empty($_POST['coupon_id']) || empty($_POST['field'])) {
            return false;
        }

        $coupon = $this->coupon_repository->get_coupon(intval($_POST['coupon_id']));
        if (!($coupon instanceof \WC_Coupon)) {
            return false;
        }

        $coupon_products = [];
        $getter_method = ($_POST['field'] == 'product_ids') ? 'get_product_ids' : 'get_excluded_product_ids';
        $product_ids = method_exists($coupon, $getter_method) ? $coupon->{$getter_method}() : [];
        $product_repository = new Product();
        $products = $product_repository->get_products([
            'posts_per_page' => '-1',
            'post_status' => 'any',
            'post_type' => ['product', 'product_variation'],
            'post__in' => (!empty($product_ids)) ? array_map('intval', $product_ids) : [0],
        ]);

        if (!empty($products)) {
            foreach ($products as $product) {
                if ($product instanceof \WP_Post) {
                    $coupon_products[$product->ID] = $product->post_title;
                }
            }
        }
        $this->make_response([
            'success' => true,
            'coupon_products' => $coupon_products,
        ]);
    }

    public function get_coupon_categories()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (empty($_POST['coupon_id']) || empty($_POST['field'])) {
            return false;
        }

        $coupon = $this->coupon_repository->get_coupon(intval($_POST['coupon_id']));
        if (!($coupon instanceof \WC_Coupon)) {
            return false;
        }

        $getter_method = ($_POST['field'] == 'product_categories') ? 'get_product_categories' : 'get_excluded_product_categories';
        $category_ids = method_exists($coupon, $getter_method) ? $coupon->{$getter_method}() : [];
        $product_categories = [];
        if (!empty($category_ids) && is_array($category_ids)) {
            $product_categories = get_terms([
                'taxonomy' => 'product_cat',
                'include' => $category_ids,
                'hide_empty' => false,
                'fields' => 'id=>name',
            ]);
        }

        $this->make_response([
            'success' => true,
            'product_categories' => $product_categories,
        ]);
    }

    public function get_coupon_used_in()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (empty($_POST['coupon_code'])) {
            return false;
        }

        $order_ids = $this->coupon_repository->get_order_ids_by_coupon(sanitize_text_field($_POST['coupon_code']));

        $orders = [];
        if (!empty($order_ids) && is_array($order_ids)) {
            foreach ($order_ids as $order) {
                if (!empty($order['order_id'])) {
                    $orders[$order['order_id']] = esc_url(admin_url("post.php?post={$order['order_id']}&action=edit"));
                }
            }
        }

        $this->make_response([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    public function get_coupon_used_by()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (empty($_POST['coupon_id'])) {
            return false;
        }

        $coupon = $this->coupon_repository->get_coupon(intval($_POST['coupon_id']));

        if (!($coupon instanceof \WC_Coupon)) {
            return false;
        }

        $user_ids = $coupon->get_used_by();
        $users = get_users([
            'include' => (!empty($user_ids)) ? $user_ids : [0],
            'fields' => array('ID', 'display_name'),
        ]);

        $used_by = [];
        if (!empty($users) && is_array($users)) {
            foreach ($users as $user) {
                if (!empty($user->display_name)) {
                    $used_by[] = [
                        'link' => esc_url(admin_url("user-edit.php?user_id={$user->ID}")),
                        'name' => $user->display_name,
                    ];
                }
            }
        }

        $this->make_response([
            'success' => true,
            'users' => !empty($used_by) ? $used_by : false,
        ]);
    }

    public function coupons_filter()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (isset($_POST['filter_data'])) {
            $data = Sanitizer::array($_POST['filter_data']);
            $current_page = !empty($_POST['current_page']) ? intval($_POST['current_page']) : 1;
            $filter_result = $this->coupon_repository->get_coupons_list($data, $current_page);
            $this->make_response([
                'success' => true,
                'coupons_list' => $filter_result->coupons_list,
                'product_ids' => $filter_result->product_ids,
                'exclude_product_ids' => $filter_result->exclude_product_ids,
                'product_categories' => $filter_result->product_categories,
                'exclude_product_categories' => $filter_result->exclude_product_categories,
                'pagination' => $filter_result->pagination,
                'status_filters' => $filter_result->status_filters,
                'coupons_count' => $filter_result->count,
            ]);
        }
        return false;
    }

    public function add_meta_keys_by_coupon_id()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        $coupon_id = intval($_POST['coupon_id']);
        $coupon = $this->coupon_repository->get_coupon($coupon_id);
        if (!($coupon instanceof \WC_Coupon)) {
            die();
        }
        $meta_keys = Meta_Fields::remove_default_meta_keys(array_keys(get_post_meta($coupon_id)));
        $output = "";
        if (!empty($meta_keys)) {
            foreach ($meta_keys as $meta_key) {
                $meta_field['key'] = $meta_key;
                $meta_fields_main_types = Meta_Field::get_main_types();
                $meta_fields_sub_types = Meta_Field::get_sub_types();
                $output .= Render::html(WCCBEL_VIEWS_DIR . "meta_field/meta_field_item.php", compact('meta_field', 'meta_fields_main_types', 'meta_fields_sub_types'));
            }
        }

        $this->make_response($output);
        return false;
    }

    public function column_manager_add_field()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (isset($_POST['field_name']) && is_array($_POST['field_name']) && !empty($_POST['field_name'])) {
            $output = '';
            $field_action = sanitize_text_field($_POST['field_action']);
            for ($i = 0; $i < count($_POST['field_name']); $i++) {
                $field_name = sanitize_text_field($_POST['field_name'][$i]);
                $field_label = (!empty($_POST['field_label'][$i])) ? sanitize_text_field($_POST['field_label'][$i]) : $field_name;
                $field_title = (!empty($_POST['field_label'][$i])) ? sanitize_text_field($_POST['field_label'][$i]) : $field_name;
                $output .= Render::html(WCCBEL_VIEWS_DIR . "column_manager/field_item.php", compact('field_name', 'field_label', 'field_action', 'field_title'));
            }
            $this->make_response($output);
        }

        return false;
    }

    public function column_manager_get_fields_for_edit()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (isset($_POST['preset_key'])) {
            $preset = (new Column())->get_preset(sanitize_text_field($_POST['preset_key']));
            if ($preset) {
                $output = '';
                $fields = [];
                if (isset($preset['fields'])) {
                    foreach ($preset['fields'] as $field) {
                        $field_info = [
                            'field_name' => $field['name'],
                            'field_label' => $field['label'],
                            'field_title' => $field['title'],
                            'field_background_color' => $field['background_color'],
                            'field_text_color' => $field['text_color'],
                            'field_action' => "edit",
                        ];
                        $fields[] = sanitize_text_field($field['name']);
                        $output .= Render::html(WCCBEL_VIEWS_DIR . 'column_manager/field_item.php', $field_info);
                    }
                }

                $this->make_response([
                    'html' => $output,
                    'fields' => implode(',', $fields),
                ]);
            }
        }

        return false;
    }

    public function save_filter_preset()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (!empty($_POST['preset_name'])) {
            $data = Sanitizer::array($_POST['filter_data']);
            $filter_item['name'] = sanitize_text_field($_POST['preset_name']);
            $filter_item['date_modified'] = gmdate('Y-m-d H:i:s');
            $filter_item['key'] = 'preset-' . wp_rand(1000000, 9999999);
            $filter_item['filter_data'] = $data;
            $save_result = (new Search())->update($filter_item);
            if (!$save_result) {
                return false;
            }
            $new_item = Render::html(WCCBEL_VIEWS_DIR . 'modals/filter_profile_item.php', compact('filter_item'));
            $this->make_response([
                'success' => $save_result,
                'new_item' => $new_item,
            ]);
        }
        return false;
    }

    public function coupon_edit()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (empty($_POST['coupon_data']) || !is_array($_POST['coupon_data'])) {
            return false;
        }

        if (!empty($_POST['coupon_ids'])) {
            $coupon_ids = array_map('intval', $_POST['coupon_ids']);
        } elseif (!empty($_POST['filter_data'])) {
            $args = Coupon_Helper::set_filter_data_items(Sanitizer::array($_POST['filter_data']), [
                'fields' => 'ids',
            ]);
            $coupon_ids = ($this->coupon_repository->get_coupons($args))->posts;
        } else {
            return false;
        }
        $update_service = WCCBEL_Coupon_Update::get_instance();
        $update_service->set_update_data([
            'coupon_ids' => $coupon_ids,
            'coupon_data' => Sanitizer::array($_POST['coupon_data']),
            'save_history' => true,
        ]);
        $update_result = $update_service->perform();

        $result = $this->coupon_repository->get_coupons_rows($coupon_ids);
        $histories = $this->history_repository->get_histories();
        $history_count = $this->history_repository->get_history_count();
        $reverted = $this->history_repository->get_latest_reverted();
        $histories_rendered = Render::html(WCCBEL_VIEWS_DIR . 'history/history_items.php', compact('histories'));
        $history_pagination = Render::html(WCCBEL_VIEWS_DIR . 'history/history_pagination.php', compact('history_count'));

        $this->make_response([
            'success' => $update_result,
            'coupons' => $result->coupon_rows,
            'coupon_statuses' => $result->coupon_statuses,
            'status_filters' => $result->status_filters,
            'history_items' => $histories_rendered,
            'history_pagination' => $history_pagination,
            'reverted' => !empty($reverted),
        ]);
    }

    public function create_new_coupon()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (isset($_POST) && !empty($_POST['count'])) {
            $coupons = [];
            for ($i = 1; $i <= intval($_POST['count']); $i++) {
                $coupons[] = $this->coupon_repository->create();
            }
            $this->make_response([
                'success' => true,
                'coupon_ids' => $coupons,
            ]);
        }
    }

    public function delete_coupons()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (!empty($_POST['delete_type'])) {
            $coupons_ids = (!empty($_POST['coupon_ids'])) ? array_map('intval', $_POST['coupon_ids']) : [];
            $trashed = [];
            switch ($_POST['delete_type']) {
                case 'trash':
                    if (!empty($coupons_ids)) {
                        foreach ($coupons_ids as $coupon_id) {
                            $trashed[] = intval($coupon_id);
                            wp_trash_post(intval($coupon_id));
                        }
                    }
                    break;
                case 'all':
                    if (isset($_POST['filter_data'])) {
                        $args = Coupon_Helper::set_filter_data_items(Sanitizer::array($_POST['filter_data']), [
                            'fields' => 'ids',
                            'posts_per_page' => -1
                        ]);
                        $coupon_ids = ($this->coupon_repository->get_coupons($args))->posts;
                        if (!empty($coupon_ids)) {
                            foreach ($coupon_ids as $coupon_id) {
                                $trashed[] = intval($coupon_id);
                                wp_trash_post(intval($coupon_id));
                            }
                        }
                    }

                    break;
                case 'permanently':
                    if (!empty($coupons_ids)) {
                        foreach ($coupons_ids as $coupon_id) {
                            wp_delete_post(intval($coupon_id));
                        }
                    }
                    break;
            }

            if (!empty($trashed)) {
                $this->save_history_for_delete($trashed);
            }

            $histories = $this->history_repository->get_histories();
            $history_count = $this->history_repository->get_history_count();
            $reverted = $this->history_repository->get_latest_reverted();
            $histories_rendered = Render::html(WCCBEL_VIEWS_DIR . 'history/history_items.php', compact('histories'));
            $history_pagination = Render::html(WCCBEL_VIEWS_DIR . 'history/history_pagination.php', compact('history_count'));

            $this->make_response([
                'success' => true,
                'message' => __('Success !', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'history_items' => $histories_rendered,
                'history_pagination' => $history_pagination,
                'reverted' => !empty($reverted),
                'edited_ids' => $coupons_ids,
            ]);
        }
        return false;
    }

    public function untrash_coupons()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        $trash = (!empty($_POST['coupon_ids'])) ? array_map('intval', $_POST['coupon_ids']) : $this->coupon_repository->get_trash();

        if (!empty($trash) && is_array($trash)) {
            foreach ($trash as $coupon_id) {
                wp_untrash_post(intval($coupon_id));
            }
        }

        $this->make_response([
            'success' => true,
            'message' => __('Success !', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
        ]);
    }

    public function empty_trash()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        $trash = $this->coupon_repository->get_trash();
        if (!empty($trash)) {
            foreach ($trash as $coupon_id) {
                wp_delete_post(intval($coupon_id), true);
            }
            $this->make_response([
                'success' => true,
                'message' => __('Success !', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            ]);
        }
        return false;
    }

    private function save_history_for_delete($coupons)
    {
        if (empty($coupons) || !is_array($coupons)) {
            return false;
        }

        $create_history = $this->history_repository->create_history([
            'user_id' => intval(get_current_user_id()),
            'fields' => serialize(['coupon_delete']),
            'operation_type' => History::BULK_OPERATION,
            'operation_date' => gmdate('Y-m-d H:i:s'),
        ]);

        if (!$create_history) {
            return false;
        }

        foreach ($coupons as $coupon_id) {
            if (empty($coupon_id)) {
                continue;
            }

            $this->history_repository->save_history_item([
                'history_id' => intval($create_history),
                'historiable_id' => intval($coupon_id),
                'name' => 'coupon_delete',
                'type' => 'coupon_action',
                'prev_value' => 'untrash',
                'new_value' => 'trash',
            ]);
        }

        return true;
    }

    public function duplicate_coupon()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        $message = __('Error !', 'ithemeland-woocommerce-bulk-coupons-editing-lite');
        if (isset($_POST['coupon_ids']) && !empty($_POST['coupon_ids']) && !empty($_POST['duplicate_number'])) {
            $result = $this->coupon_repository->duplicate(array_map('intval', $_POST['coupon_ids']), intval($_POST['duplicate_number']));
            if ($result) {
                $message = __('Success !', 'ithemeland-woocommerce-bulk-coupons-editing-lite');
            }
        }

        $this->make_response([
            'success' => $message,
        ]);
    }

    public function load_filter_profile()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (isset($_POST['preset_key'])) {
            $search_repository = new Search();

            $preset = $search_repository->get_preset(sanitize_text_field($_POST['preset_key']));
            if (!isset($preset['filter_data'])) {
                return false;
            }
            $search_repository = new Search();
            $search_repository->update_current_data([
                'last_filter_data' => $preset['filter_data'],
            ]);
            $result = $this->coupon_repository->get_coupons_list($preset['filter_data'], 1);
            $this->make_response([
                'success' => true,
                'filter_data' => $preset['filter_data'],
                'coupons_list' => $result->coupons_list,
                'product_ids' => $result->product_ids,
                'exclude_product_ids' => $result->exclude_product_ids,
                'product_categories' => $result->product_categories,
                'exclude_product_categories' => $result->exclude_product_categories,
                'pagination' => $result->pagination,
                'status_filters' => $result->status_filters,
                'coupons_count' => $result->count,
            ]);
        }
        return false;
    }

    public function delete_filter_profile()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (isset($_POST['preset_key'])) {
            $search_repository = new Search();
            $delete_result = $search_repository->delete(sanitize_text_field($_POST['preset_key']));
            if (!$delete_result) {
                return false;
            }

            $this->make_response([
                'success' => true,
            ]);
        }
        return false;
    }

    public function save_column_profile()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (isset($_POST['preset_key']) && isset($_POST['type'])) {
            $column_repository = new Column();
            $fields = $column_repository->get_fields();
            $preset['date_modified'] = gmdate('Y-m-d H:i:s', time());

            switch ($_POST['type']) {
                case 'save_as_new':
                    $preset['name'] = "Preset " . wp_rand(100, 999);
                    $preset['key'] = 'preset-' . wp_rand(1000000, 9999999);
                    break;
                case 'update_changes':
                    $preset_item = $column_repository->get_preset(sanitize_text_field($_POST['preset_key']));
                    if (!$preset_item) {
                        return false;
                    }
                    $preset['name'] = sanitize_text_field($preset_item['name']);
                    $preset['key'] = sanitize_text_field($preset_item['key']);
                    break;
            }

            $preset['fields'] = [];
            foreach ($_POST['items'] as $item) {
                $item = sanitize_text_field($item);
                if (isset($fields[$item])) {
                    $preset['fields'][$item] = [
                        'name' => $item,
                        'label' => $fields[$item]['label'],
                        'title' => $fields[$item]['label'],
                        'editable' => $fields[$item]['editable'],
                        'content_type' => $fields[$item]['content_type'],
                        'update_type' => $fields[$item]['update_type'],
                        'allowed_type' => $fields[$item]['allowed_type'],
                        'background_color' => '#fff',
                        'text_color' => '#444',
                    ];
                    if (isset($fields[$item]['sortable'])) {
                        $preset["fields"][$item]['sortable'] = $fields[$item]['sortable'];
                    }
                    if (isset($fields[$item]['options'])) {
                        $preset["fields"][$item]['options'] = $fields[$item]['options'];
                    }
                    if (isset($fields[$item]['field_type'])) {
                        $preset["fields"][$item]['field_type'] = $fields[$item]['field_type'];
                    }
                    $preset['checked'][] = $item;
                }
            }

            $column_repository->update($preset);
            $column_repository->set_active_columns($preset['key'], $preset['fields']);
            $this->make_response([
                'success' => true,
            ]);
        }
        return false;
    }

    public function update_coupon_taxonomy()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (isset($_POST['coupon_ids']) && is_array($_POST['coupon_ids'])) {
            $coupon_ids = array_map('intval', $_POST['coupon_ids']);
            if (is_array($_POST['field']) && isset($_POST['field'][0]) && isset($_POST['field'][1])) {
                $field = sanitize_text_field($_POST['field'][0]);
                $taxonomy = sanitize_text_field($_POST['field'][1]);
            } else {
                $field = 'taxonomy';
                $taxonomy = sanitize_text_field($_POST['field']);
            }

            $this->save_history($coupon_ids, [$field => $taxonomy], Sanitizer::array($_POST['values']), History::INLINE_OPERATION);

            $result = $this->coupon_repository->update($coupon_ids, [
                'field_type' => $field,
                'field' => $taxonomy,
                'value' => Sanitizer::array($_POST['values']),
                'operator' => 'taxonomy_replace',
            ]);
            if (!$result) {
                return false;
            }

            $histories = $this->history_repository->get_histories();
            $history_count = $this->history_repository->get_history_count();
            $reverted = $this->history_repository->get_latest_reverted();
            $histories_rendered = Render::html(WCCBEL_VIEWS_DIR . 'history/history_items.php', compact('histories'));
            $history_pagination = Render::html(WCCBEL_VIEWS_DIR . 'history/history_pagination.php', compact('history_count'));

            $this->make_response([
                'success' => true,
                'message' => __('Success !', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                'history_items' => $histories_rendered,
                'history_pagination' => $history_pagination,
                'reverted' => !empty($reverted),
                'edited_ids' => $coupon_ids,
            ]);
        }
        return false;
    }

    public function get_text_editor_content()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (isset($_POST['coupon_id']) && isset($_POST['field'])) {
            $field = sanitize_text_field($_POST['field']);
            $field_type = sanitize_text_field($_POST['field_type']);

            $coupon_object = $this->coupon_repository->get_coupon(intval($_POST['coupon_id']));
            if (!($coupon_object instanceof \WC_Coupon)) {
                return false;
            }
            $coupon = $this->coupon_repository->coupon_to_array($coupon_object);
            switch ($field_type) {
                case 'meta_field':
                case 'custom_field':
                    $value = (isset($coupon[$field_type][$field])) ? $coupon[$field_type][$field][0] : '';
                    break;
                default:
                    $value = $coupon[$field];
                    break;
            }

            $this->make_response([
                'success' => true,
                'content' => $value,
            ]);
        }
        return false;
    }

    public function history_change_page()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (empty($_POST['page'])) {
            return false;
        }

        $where = [];

        if (isset($_POST['filters'])) {
            if (isset($_POST['filters']['operation']) && !empty($_POST['filters']['operation'])) {
                $where['operation_type'] = sanitize_text_field($_POST['filters']['operation']);
            }
            if (isset($_POST['filters']['author']) && !empty($_POST['filters']['author'])) {
                $where['user_id'] = sanitize_text_field($_POST['filters']['author']);
            }
            if (isset($_POST['filters']['fields']) && !empty($_POST['filters']['fields'])) {
                $where['fields'] = sanitize_text_field($_POST['filters']['fields']);
            }
            if (isset($_POST['filters']['date'])) {
                $where['operation_date'] = sanitize_text_field($_POST['filters']['date']);
            }
        }

        $per_page = 10;
        $history_count = $this->history_repository->get_history_count($where);
        $current_page = intval($_POST['page']);
        $offset = intval($current_page - 1) * $per_page;
        $histories = $this->history_repository->get_histories($where, $per_page, $offset);
        $histories_rendered = Render::html(WCCBEL_VIEWS_DIR . 'history/history_items.php', compact('histories'));
        $history_pagination = Render::html(WCCBEL_VIEWS_DIR . 'history/history_pagination.php', compact('history_count', 'per_page', 'current_page'));

        $this->make_response([
            'success' => true,
            'history_items' => $histories_rendered,
            'history_pagination' => $history_pagination,
        ]);
    }

    public function change_count_per_page()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (isset($_POST['count_per_page'])) {
            $setting_repository = new Setting();
            $setting_repository->update_current_settings([
                'count_per_page' => intval($_POST['count_per_page']),
            ]);
            $this->make_response([
                'success' => true,
            ]);
        }
        return false;
    }

    public function filter_profile_change_use_always()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (isset($_POST['preset_key'])) {
            (new Search())->update_use_always(sanitize_text_field($_POST['preset_key']));
            $this->make_response([
                'success' => true,
            ]);
        }
        return false;
    }

    public function sort_by_column()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        if (!empty($_POST['column_name']) && !empty($_POST['sort_type']) && !empty($_POST['filter_data'])) {
            $setting_repository = new Setting();
            $setting_repository->update_current_settings([
                'sort_by' => sanitize_text_field($_POST['column_name']),
                'sort_type' => sanitize_text_field(strtoupper($_POST['sort_type'])),
            ]);
            $filter_data = Sanitizer::array($_POST['filter_data']);
            $result = $this->coupon_repository->get_coupons_list($filter_data, 1);
            $this->make_response([
                'success' => true,
                'filter_data' => $filter_data,
                'coupons_list' => $result->coupons_list,
                'product_ids' => $result->product_ids,
                'exclude_product_ids' => $result->exclude_product_ids,
                'product_categories' => $result->product_categories,
                'exclude_product_categories' => $result->exclude_product_categories,
                'pagination' => $result->pagination,
                'status_filters' => $result->status_filters,
                'coupons_count' => $result->count,
            ]);
        }
        return false;
    }

    public function clear_filter_data()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wccbel_ajax_nonce')) {
            die();
        }

        $search_repository = new Search();
        $search_repository->delete_current_data();
        $this->make_response([
            'success' => true,
        ]);
    }

    private function save_history($coupon_ids, $fields, $new_value, $operation_type)
    {
        $create_history = $this->history_repository->create_history([
            'user_id' => intval(get_current_user_id()),
            'fields' => serialize($fields),
            'operation_type' => sanitize_text_field($operation_type),
            'operation_date' => gmdate('Y-m-d H:i:s'),
        ]);

        if (!$create_history) {
            return false;
        }

        foreach ($coupon_ids as $coupon_id) {
            $coupon_object = $this->coupon_repository->get_coupon(intval($coupon_id));
            if (!($coupon_object instanceof \WC_Coupon)) {
                return false;
            }
            $coupon_item = $this->coupon_repository->coupon_to_array($coupon_object);
            if (!empty($fields)) {
                foreach ($fields as $field_type => $field) {
                    if (is_array($field)) {
                        $new_val = [];
                        $prev_val = [];
                        foreach ($field as $filed_name) {
                            $encoded_field = strtolower(urlencode($filed_name));
                            switch ($field_type) {
                                case 'custom_field':
                                    $new_val['custom_field'][$encoded_field] = $new_value[$encoded_field];
                                    $prev_val['custom_field'][$encoded_field] = (isset($coupon_item[$field_type][$encoded_field][0])) ? $coupon_item[$field_type][$encoded_field][0] : '';
                                    break;
                                case 'taxonomy':
                                    $new_val['taxonomy'][$encoded_field] = $new_value[$encoded_field];
                                    $prev_val['taxonomy'][$encoded_field] = ($encoded_field == 'coupon_tag') ? wp_get_post_terms($coupon_item['id'], $encoded_field, ['fields' => 'names']) : wp_get_post_terms($coupon_item['id'], $encoded_field, ['fields' => 'ids']);
                                    break;
                                default:
                                    break;
                            }
                        }
                    } else {
                        $encoded_field = strtolower(urlencode($field));
                        if (is_numeric($field_type)) {
                            $prev_val = (isset($coupon_item[$field])) ? $coupon_item[$field] : '';
                            if ($field == '_thumbnail_id') {
                                $new_val = [
                                    'id' => intval($new_value),
                                    'small' => wp_get_attachment_image_src(intval($new_value), [40, 40]),
                                    'big' => wp_get_attachment_image_src(intval($new_value), [600, 600]),
                                ];
                            } else {
                                $new_val = (!empty($new_value[$field])) ? $new_value[$field] : $new_value;
                            }
                        } else {
                            switch ($field_type) {
                                case 'custom_field':
                                    $new_val['custom_field'][$encoded_field] = $new_value;
                                    $prev_val['custom_field'][$encoded_field] = (isset($coupon_item[$field_type][$encoded_field][0])) ? $coupon_item[$field_type][$encoded_field][0] : '';
                                    break;
                                case 'taxonomy':
                                    $new_val['taxonomy'][$encoded_field] = $new_value;
                                    $prev_val['taxonomy'][$encoded_field] = ($encoded_field == 'coupon_tag') ? wp_get_post_terms($coupon_item['id'], $encoded_field, ['fields' => 'names']) : wp_get_post_terms($coupon_item['id'], $encoded_field, ['fields' => 'ids']);
                                    break;
                                default:
                                    break;
                            }
                        }
                    }

                    $this->history_repository->create_history_item([
                        'history_id' => intval($create_history),
                        'historiable_id' => intval($coupon_id),
                        'field' => (!empty($field_type) && !is_numeric($field_type)) ? serialize([$field_type => $field]) : serialize([$field]),
                        'prev_value' => serialize($prev_val),
                        'new_value' => serialize($new_val),
                    ]);
                }
            }
        }
        return true;
    }

    private function make_response($data)
    {
        echo (is_array($data)) ? wp_json_encode($data) : wp_kses($data, Sanitizer::allowed_html_tags());
        die();
    }
}
