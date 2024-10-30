<?php

namespace wccbel\classes\repositories;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\helpers\Others;
use wccbel\classes\helpers\Pagination;
use wccbel\classes\helpers\Render;
use wccbel\classes\providers\column\CouponColumnProvider;
use wccbel\classes\providers\coupon\CouponProvider;

class Coupon
{
    private static $instance;

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

    public function get_coupon($coupon_id)
    {
        return new \WC_Coupon(intval($coupon_id));
    }

    public function get_coupon_as_post($coupon_id, $output = 'object')
    {
        $output = ($output == 'array') ? ARRAY_A : OBJECT;
        return get_post(intval($coupon_id), $output);
    }

    public function get_coupon_statuses()
    {
        $statuses = get_post_statuses();
        $statuses['trash'] = __('Trash', 'ithemeland-woocommerce-bulk-coupons-editing-lite');

        return $statuses;
    }

    public function get_ids_by_custom_query($join, $where, $type = 'shop_coupon')
    {
        global $wpdb;
        $type = ($type == 'product') ? "'product', 'product_variation'" : "'" . sanitize_text_field(esc_sql($type)) . "'";
        $where = (!empty($where)) ? "AND ({$where})" : '';
        $query = "SELECT posts.ID, posts.post_parent FROM $wpdb->posts AS posts {$join} WHERE posts.post_type IN ({$type}) {$where}";
        $coupons = $wpdb->get_results($query, ARRAY_N); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $coupons = array_unique(Others::array_flatten($coupons, 'int'));
        if ($key = array_search(0, $coupons) !== false) {
            unset($coupons[$key]);
        }
        return implode(',', $coupons);
    }

    public function get_coupons($args)
    {
        if (!isset($args['post_type'])) {
            $args['post_type'] = 'shop_coupon';
        }
        if (!isset($args['posts_per_page'])) {
            $args['posts_per_page'] = -1;
        }

        $coupons = new \WP_Query($args);
        return $coupons;
    }

    public function get_order_ids_by_coupon($coupon_code)
    {
        global $wpdb;
        $order_items = $wpdb->prefix . "woocommerce_order_items";
        $order_ids = $wpdb->get_results($wpdb->prepare("SELECT order_id FROM {$order_items} WHERE order_item_type = 'coupon' AND order_item_name = %s", sanitize_text_field($coupon_code)), ARRAY_A);
        return $order_ids;
    }

    public function get_except_columns_for_export()
    {
        return [];
    }

    public function get_coupons_list($data, $active_page)
    {
        $column_repository = new Column();
        $search_repository = new Search();
        $search_repository->update_current_data([
            'last_filter_data' => $data,
        ]);

        $settings_repository = new Setting();
        $settings = $settings_repository->get_settings();
        $current_settings = $settings_repository->get_current_settings();
        $sort_by = isset($current_settings['sort_by']) ? $current_settings['sort_by'] : '';
        $sort_type = isset($current_settings['sort_type']) ? $current_settings['sort_type'] : '';
        $sticky_first_columns = $current_settings['sticky_first_columns'];
        $args = \wccbel\classes\helpers\Setting::get_arg_coupon_by(esc_sql($sort_by), [
            'order' => esc_sql($sort_type),
            'posts_per_page' => $current_settings['count_per_page'],
            'paged' => $active_page,
            'paginate' => true,
            'post_type' => ['shop_coupon'],
            'post_status' => 'any',
            'fields' => 'ids',
        ]);
        $coupons_args = \wccbel\classes\helpers\Coupon_Helper::set_filter_data_items($data, $args);
        $coupons = $this->get_coupons($coupons_args);
        $items = $coupons->posts;
        $item_provider = CouponProvider::get_instance();
        $show_id_column = $column_repository::SHOW_ID_COLUMN;
        $next_static_columns = $column_repository::get_static_columns();
        $columns_title = $column_repository::get_columns_title();
        $columns = $column_repository->get_active_columns()['fields'];
        $display_full_columns_title = $settings['display_full_columns_title'];
        $coupons_list = Render::html(WCCBEL_VIEWS_DIR . 'data_table/items.php', compact('item_provider', 'display_full_columns_title', 'next_static_columns', 'items', 'columns', 'sort_type', 'sort_by', 'show_id_column', 'columns_title', 'sticky_first_columns'));
        if (!empty($coupons) && !empty($active_page)) {
            $pagination = Pagination::init($active_page, $coupons->max_num_pages);
        }

        $result = new \stdClass();
        $result->coupons_list = $coupons_list;
        $result->product_ids = !empty($data['product_ids']['value']) ? $this->get_selected_products($data['product_ids']['value']) : [];
        $result->exclude_product_ids = !empty($data['exclude_product_ids']['value']) ? $this->get_selected_products($data['exclude_product_ids']['value']) : [];
        $result->product_categories = !empty($data['product_categories']['value']) ? $this->get_selected_categories($data['product_categories']['value']) : [];
        $result->exclude_product_categories = !empty($data['exclude_product_categories']['value']) ? $this->get_selected_categories($data['exclude_product_categories']['value']) : [];
        $result->pagination = $pagination;
        $result->status_filters = $this->get_status_filters();
        $result->count = $coupons->found_posts;
        return $result;
    }

    public function get_coupons_rows($coupon_ids)
    {
        if (!is_array($coupon_ids)) {
            return false;
        }

        $column_repository = new Column();
        $settings_repository = new Setting();
        $settings = $settings_repository->get_settings();
        $sticky_first_columns = $settings['sticky_first_columns'];
        $column_provider = CouponColumnProvider::get_instance();
        $show_id_column = $column_repository::SHOW_ID_COLUMN;
        $next_static_columns = $column_repository::get_static_columns();
        $columns = $column_repository->get_active_columns()['fields'];

        $coupon_rows = [];
        $includes = [];
        $coupon_statuses = [];

        if (!empty($coupon_ids)) {
            foreach ($coupon_ids as $coupon_id) {
                $coupon_object = $this->get_coupon(intval($coupon_id));
                if (!($coupon_object instanceof \WC_Coupon)) {
                    continue;
                }
                $coupon_as_post = $this->get_coupon_as_post(intval($coupon_id));
                $item_columns = $column_provider->get_item_columns($coupon_object, $columns);
                $coupon_statuses[intval($coupon_id)] = !empty($coupon_as_post->post_status) ? $coupon_as_post->post_status : '';
                if (is_array($item_columns) && isset($item_columns['items'])) {
                    $coupon_rows[intval($coupon_id)] = $item_columns['items'];
                    $includes[] = $item_columns['includes'];
                } else {
                    $coupon_rows[intval($coupon_id)] = $item_columns;
                }
            }
        }

        $result = new \stdClass();
        $result->coupon_rows = $coupon_rows;
        $result->coupon_statuses = $coupon_statuses;
        $result->includes = $includes;
        $result->status_filters = $this->get_status_filters();
        return $result;
    }

    private function get_status_filters()
    {
        $coupon_counts_by_status = $this->get_coupon_counts_group_by_status();
        $coupon_statuses = $this->get_coupon_statuses();
        return Render::html(WCCBEL_VIEWS_DIR . "bulk_edit/status_filters.php", compact('coupon_counts_by_status', 'coupon_statuses'));
    }

    private function get_selected_products($product_ids)
    {
        $selected_products = [];
        if (!empty($product_ids) && is_array($product_ids)) {
            $product_repository = new Product();
            $products = $product_repository->get_products([
                'post__in' => $product_ids,
                'post_type' => ['product', 'product_variation'],
                'post_status' => 'any',
                'posts_per_page' => -1,
            ]);

            if (!empty($products)) {
                foreach ($products as $product) {
                    if ($product instanceof \WP_Post) {
                        $selected_products[$product->ID] = $product->post_title;
                    }
                }
            }
        }
        return $selected_products;
    }

    private function get_selected_categories($ids)
    {
        $selected_categories = [];
        if (!empty($ids) && is_array($ids)) {
            $product_repository = new Product();
            $selected_categories = $product_repository->get_categories_by_id($ids);
        }

        return $selected_categories;
    }

    public function get_taxonomies()
    {
        $taxonomies_value = [];
        $taxonomies = get_taxonomies([], 'objects');
        foreach ($taxonomies as $taxonomy) {
            if (taxonomy_exists($taxonomy->name)) {
                $taxonomies_value[$taxonomy->name] = [
                    'label' => (strpos($taxonomy->name, 'pa_') !== false) ? wc_attribute_label($taxonomy->name) : $taxonomy->label,
                    'terms' => get_terms([
                        'taxonomy' => $taxonomy->name,
                        'hide_empty' => false,
                    ]),
                ];
            }
        }
        return $taxonomies_value;
    }

    public function coupon_to_array($coupon_object)
    {
        if (!($coupon_object instanceof \WC_Coupon)) {
            return false;
        }

        $coupon_as_post = $this->get_coupon_as_post($coupon_object->get_id());
        $post_meta = get_post_meta($coupon_object->get_id());

        return [
            'id' => $coupon_object->get_id(),
            'post_status' => $coupon_as_post->post_status,
            'coupon_code' => $coupon_object->get_code(),
            'description' => $coupon_object->get_description(),
            'date_created' => (!empty($coupon_object->get_date_created()) && !empty($coupon_object->get_date_created()->date('Y/m/d H:i'))) ? $coupon_object->get_date_created()->format('Y/m/d H:i') : '',
            'post_modified' => (!empty($coupon_object->get_date_modified()) && !empty($coupon_object->get_date_modified()->date('Y/m/d H:i'))) ? $coupon_object->get_date_modified()->format('Y/m/d H:i') : '',
            'date_expires' => (!empty($coupon_object->get_date_expires()) && !empty($coupon_object->get_date_expires()->date('Y/m/d'))) ? $coupon_object->get_date_expires()->format('Y/m/d') : '',
            'product_ids' => $coupon_object->get_product_ids(),
            'exclude_product_ids' => $coupon_object->get_excluded_product_ids(),
            'product_categories' => $coupon_object->get_product_categories(),
            'exclude_product_categories' => $coupon_object->get_excluded_product_categories(),
            'coupon_amount' => $coupon_object->get_amount(),
            'minimum_amount' => $coupon_object->get_minimum_amount(),
            'maximum_amount' => $coupon_object->get_maximum_amount(),
            'usage_limit' => $coupon_object->get_usage_limit(),
            'limit_usage_to_x_items' => $coupon_object->get_limit_usage_to_x_items(),
            'usage_limit_per_user' => $coupon_object->get_usage_limit_per_user(),
            'discount_type' => $coupon_object->get_discount_type(),
            'free_shipping' => $coupon_object->get_free_shipping(),
            'individual_use' => $coupon_object->get_individual_use(),
            'exclude_sale_items' => $coupon_object->get_exclude_sale_items(),
            'usage_count' => $coupon_object->get_usage_count(),
            'customer_email' => $coupon_object->get_email_restrictions(),
            '_used_by' => $coupon_object->get_used_by(),
            'custom_field' => $post_meta,
        ];
    }

    public function create($data = [])
    {
        $coupon_code = $this->generate_coupon_code();
        $coupon = new \WC_Coupon($coupon_code);
        $result = $coupon->save();
        WC()->session->set('unique_coupon', $coupon_code);
        return $result;
    }

    private function coupon_code_exists($coupon_code)
    {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'shop_coupon' AND post_name = %s", sanitize_text_field($coupon_code)));
    }

    public function generate_coupon_code()
    {
        for ($i = 0; $i < 1; $i++) {
            $coupon_code = strtolower(wp_generate_password(8, false));
            if ($this->coupon_code_exists($coupon_code)) {
                $i--;
            } else {
                break;
            }
        }
        return $coupon_code;
    }

    public function duplicate($coupon_ids, $number = 1)
    {
        if (empty($coupon_ids) || !is_array($coupon_ids)) {
            return false;
        }

        foreach ($coupon_ids as $coupon_id) {
            $original_coupon = $this->get_coupon_as_post(intval($coupon_id), 'array');
            if (isset($original_coupon['ID'])) {
                unset($original_coupon['ID']);
                unset($original_coupon['post_title']);
                unset($original_coupon['tags_input']);
                unset($original_coupon['post_category']);
                unset($original_coupon['ancestors']);
                unset($original_coupon['page_template']);
                unset($original_coupon['filter']);
                for ($i = 1; $i <= $number; $i++) {
                    $original_coupon['post_title'] = $this->generate_coupon_code();
                    $new_coupon_id = wp_insert_post($original_coupon);
                    if (!empty($new_coupon_id)) {
                        $original_meta_keys = get_post_meta(intval($coupon_id));
                        if (!empty($original_meta_keys) && is_array($original_meta_keys)) {
                            foreach ($original_meta_keys as $original_meta_key => $original_meta_value) {
                                if (isset($original_meta_value[0])) {
                                    update_post_meta(intval($new_coupon_id), $original_meta_key, $original_meta_value[0]);
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    public function get_coupon_setter_methods()
    {
        return [
            'coupon_code' => 'set_code',
            'description' => 'set_description',
            'date_created' => 'set_date_created',
            'date_expires' => 'set_date_expires',
            'product_ids' => 'set_product_ids',
            'exclude_product_ids' => 'set_excluded_product_ids',
            'product_categories' => 'set_product_categories',
            'exclude_product_categories' => 'set_excluded_product_categories',
            'coupon_amount' => 'set_amount',
            'minimum_amount' => 'set_minimum_amount',
            'maximum_amount' => 'set_maximum_amount',
            'usage_limit' => 'set_usage_limit',
            'limit_usage_to_x_items' => 'set_limit_usage_to_x_items',
            'usage_limit_per_user' => 'set_usage_limit_per_user',
            'discount_type' => 'set_discount_type',
            'free_shipping' => 'set_free_shipping',
            'individual_use' => 'set_individual_use',
            'exclude_sale_items' => 'set_exclude_sale_items',
            'usage_count' => 'set_usage_count',
            'customer_email' => 'set_email_restrictions',
            '_used_by' => 'set_used_by',
        ];
    }

    public function import_from_csv($csv_path)
    {
        if (!file_exists($csv_path)) {
            return false;
        }
        $coupon_setter_methods = $this->get_coupon_setter_methods();

        if (($handle = fopen($csv_path, "r")) !== false) { //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
            $columns = fgetcsv($handle);
            if (empty($columns)) {
                return false;
            }

            while (($data = fgetcsv($handle)) !== false) {
                $num = count($data);
                $new_coupon = new \WC_Coupon();
                for ($c = 0; $c < $num; $c++) {
                    $method = isset($coupon_setter_methods[$columns[$c]]) ? $coupon_setter_methods[$columns[$c]] : '';
                    $value = $data[$c];

                    if (in_array($method, [
                        'set_used_by',
                        'set_product_ids',
                        'set_excluded_product_ids',
                        'set_product_categories',
                        'set_excluded_product_categories',
                        'set_exclude_sale_items',
                        'set_email_restrictions',
                    ])) {
                        if (empty($value)) {
                            $value = [];
                        } else {
                            $value_array = [];
                            if (!is_array($value)) {
                                if (!empty($value)) {
                                    $value_array = explode(',', $value);
                                }
                                $value = $value_array;
                            }
                        }
                    }

                    if (!empty($method) && method_exists($new_coupon, $method)) {
                        $new_coupon->{$method}($value);
                    } else {
                        if ($columns[$c] == 'post_status') {
                            $post_status = $value;
                        } else {
                            update_post_meta($new_coupon->get_id(), $columns[$c], $value);
                        }
                    }
                }
                $new_coupon->save();
                if (isset($post_status)) {
                    wp_update_post([
                        'ID' => $new_coupon->get_id(),
                        'post_status' => $post_status,
                    ]);
                }
            }
            fclose($handle); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
            return true;
        }
        return false;
    }

    public function get_coupon_counts_group_by_status()
    {
        global $wpdb;
        $output = [];
        $all = 0;
        $result = $wpdb->get_results("SELECT post_status AS 'status',COUNT(*) AS 'count' FROM {$wpdb->posts} WHERE post_type = 'shop_coupon' AND post_status NOT IN ('auto-draft') GROUP BY post_status", ARRAY_A);
        if (!empty($result) && is_array($result)) {
            foreach ($result as $item) {
                if (isset($item['status']) && isset($item['count'])) {
                    if ($item['status'] !== 'trash') {
                        $all += $item['count'];
                    }
                    $output[$item['status']] = $item['count'];
                }
            }
        }
        $output['all'] = intval($all);
        return $output;
    }

    public function get_trash()
    {
        $args = [
            'post_type' => ['shop_coupon'],
            'post_status' => 'trash',
            'fields' => 'ids',
        ];

        $coupons = $this->get_coupons($args);
        return $coupons->posts;
    }

    public function get_status_color($status)
    {
        $status_colors = $this->get_status_colors();
        return (isset($status_colors[$status])) ? $status_colors[$status] : null;
    }

    private function get_status_colors()
    {
        return [
            'draft' => '#a3b7a3',
            'pending' => '#80e045',
            'private' => '#f9c662',
            'publish' => '#6ca9d6',
            'trash' => '#808080',
        ];
    }
}
