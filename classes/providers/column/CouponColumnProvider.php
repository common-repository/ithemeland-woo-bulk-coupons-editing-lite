<?php

namespace wccbel\classes\providers\column;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\helpers\Meta_Field as Meta_Field_Helper;
use wccbel\classes\helpers\Sanitizer;
use wccbel\classes\repositories\Column;
use wccbel\classes\repositories\Coupon;
use wccbel\classes\repositories\Setting;

class CouponColumnProvider
{
    private static $instance;
    private $sticky_first_columns;
    private $coupon_repository;
    private $coupon;
    private $column_key;
    private $decoded_column_key;
    private $column_data;
    private $field_type;
    private $fields_method;
    private $settings;

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
        $setting_repository = new Setting();
        $this->settings = $setting_repository->get_settings();
        $this->sticky_first_columns = isset($this->settings['sticky_first_columns']) ? $this->settings['sticky_first_columns'] : 'yes';

        $this->field_type = "";

        $this->fields_method = $this->get_fields_method();
    }

    public function get_item_columns($item, $columns)
    {
        if ($item instanceof \WC_Coupon) {
            $this->coupon = $this->coupon_repository->coupon_to_array($item);
            $output = '<tr data-item-id="' . esc_attr($this->coupon['id']) . '">';
            $output .= $this->get_static_columns();
            if (!empty($columns) && is_array($columns)) {
                foreach ($columns as $column_key => $column_data) {
                    $this->column_key = $column_key;
                    $this->column_data = $column_data;
                    $this->decoded_column_key = urlencode($this->column_key);
                    $field_data = $this->get_field();
                    $output .= (!empty($field_data['field'])) ? $field_data['field'] : '';
                    if (isset($field_data['includes']) && is_array($field_data['includes'])) {
                        foreach ($field_data['includes'] as $include) {
                            if (file_exists($include)) {
                                include $include;
                            }
                        }
                    }
                }
            }
            $output .= "</tr>";
            return $output;
        }
    }

    private function get_field()
    {
        $output['field'] = '';
        $output['includes'] = [];
        $this->field_type = '';

        $this->set_coupon_field();
        $color = $this->get_column_colors_style();

        $editable = ($this->column_data['editable']) ? 'yes' : 'no';
        $sub_name = (!empty($this->column_data['sub_name'])) ? $this->column_data['sub_name'] : '';
        $update_type = (!empty($this->column_data['update_type'])) ? $this->column_data['update_type'] : '';
        $output['field'] .= '<td data-item-id="' . esc_attr($this->coupon['id']) . '" data-editable="' . $editable . '" data-item-title="#' . esc_attr($this->coupon['id']) . '" data-col-title="' . esc_attr($this->column_data['title']) . '" data-field="' . esc_attr($this->column_key) . '" data-field-type="' . esc_attr($this->field_type) . '" data-name="' . esc_attr($this->column_data['name']) . '" data-sub-name="' . esc_attr($sub_name) . '" data-update-type="' . esc_attr($update_type) . '" style="' . esc_attr($color['background']) . ' ' . esc_attr($color['text']) . '" ';
        if ($this->column_data['editable'] === true && !in_array($this->column_data['content_type'], ['multi_select', 'multi_select_attribute'])) {
            $output['field'] .= 'data-content-type="' . esc_attr($this->column_data['content_type']) . '" data-action="inline-editable"';
        }
        $output['field'] .= '>';

        if ($this->column_data['editable'] === true) {
            $generated = $this->generate_field();
            if (is_array($generated) && isset($generated['field']) && isset($generated['includes'])) {
                $output['field'] .= $generated['field'];
                $output['includes'][] = $generated['includes'];
            } else {
                $output['field'] .= $generated;
            }
        } else {
            if (isset($this->coupon[$this->decoded_column_key])) {
                $output['field'] .= (is_array($this->coupon[$this->decoded_column_key])) ? wp_kses(implode(',', $this->coupon[$this->decoded_column_key]), Sanitizer::allowed_html_tags()) : wp_kses($this->coupon[$this->decoded_column_key], Sanitizer::allowed_html_tags());
            } else {
                $output['field'] .= ' ';
            }
        }

        $output['field'] .= '</td>';
        return $output;
    }

    private function get_id_column()
    {
        $output = '';
        $delete_type = 'trash';
        $delete_label = __('Delete coupon', 'ithemeland-woocommerce-bulk-coupons-editing-lite');
        $restore_button = '';
        $edit_button = '';

        if ($this->coupon['post_status'] == 'trash') {
            $delete_type = 'permanently';
            $delete_label = __('Delete permanently', 'ithemeland-woocommerce-bulk-coupons-editing-lite');
            $restore_button = '<button type="button" style="height: 28px;" class="wccbel-ml5 wccbel-button-flat wccbel-text-green wccbel-float-right wccbel-restore-item-btn" data-item-id="' . esc_attr($this->coupon['id']) . '" title="' . __('Restore', 'ithemeland-woocommerce-bulk-coupons-editing-lite') . '"><span class="wccbel-icon-rotate-cw"></span></button>';
        } else {
            $edit_button = '<a href="' . admin_url("post.php?post=" . esc_attr($this->coupon['id']) . "&action=edit") . '" target="_blank" style="height: 28px;" class="wccbel-ml5 wccbel-float-right" title="Edit Coupon"><span style="vertical-align: middle;" class="wccbel-icon-pencil"></span></a>';
        }

        if (Column::SHOW_ID_COLUMN === true) {
            $sticky_class = ($this->sticky_first_columns == 'yes') ? 'wccbel-td-sticky wccbel-td-sticky-id wccbel-gray-bg' : '';
            $output .= '<td data-item-id="' . esc_attr($this->coupon['id']) . '" data-item-title="#' . esc_attr($this->coupon['id']) . '" data-col-title="ID" class="' . esc_attr($sticky_class) . '">';
            $output .= '<label class="wccbel-td140">';
            $output .= '<input type="checkbox" class="wccbel-check-item" value="' . esc_attr($this->coupon['id']) . '" title="Select Coupon">';
            $output .= esc_attr($this->coupon['id']);
            $output .= $restore_button;
            $output .= '<button type="button" class="wccbel-ml5 wccbel-button-flat wccbel-text-red wccbel-float-right wccbel-delete-item-btn" data-delete-type="' . esc_attr($delete_type) . '" data-item-id="' . esc_attr($this->coupon['id']) . '" title="' . $delete_label . '"><span class="wccbel-icon-trash-2"></span></button>';
            $output .= $edit_button;
            $output .= "</label>";
            $output .= "</td>";
        }
        return $output;
    }

    private function get_static_columns()
    {
        $output = '';
        $output .= $this->get_id_column();
        if (!empty(Column::get_static_columns())) {
            foreach (Column::get_static_columns() as $static_column) {
                $sticky_class = ($this->sticky_first_columns == 'yes') ? 'wccbel-td-sticky wccbel-td-sticky-title wccbel-gray-bg' : '';
                $output .= '<td class="' . esc_attr($sticky_class) . '" data-item-id="' . esc_attr($this->coupon['id']) . '" data-item-title="' . esc_attr($this->coupon[$static_column['field']]) . '" data-col-title="' . esc_attr($static_column['title']) . '" data-field="' . esc_attr($static_column['field']) . '" data-name="' . esc_attr($static_column['field']) . '" data-update-type="' . esc_attr($static_column['update_type']) . '" data-field-type="" data-content-type="text" data-action="inline-editable">';
                $output .= '<span data-action="inline-editable" class="wccbel-td160">' . esc_attr($this->coupon[$static_column['field']]) . '</span>';
                $output .= '</td>';
            }
        }
        return $output;
    }

    private function set_coupon_field()
    {
        if (isset($this->column_data['field_type'])) {
            switch ($this->column_data['field_type']) {
                case 'custom_field':
                    $this->field_type = 'custom_field';
                    $this->coupon[$this->decoded_column_key] = (isset($this->coupon['custom_field'][$this->decoded_column_key])) ? $this->coupon['custom_field'][$this->decoded_column_key][0] : '';
                    break;
                default:
                    break;
            }
        }
    }

    private function get_column_colors_style()
    {
        $color['background'] = (!empty($this->column_data['background_color']) && $this->column_data['background_color'] != '#fff' && $this->column_data['background_color'] != '#ffffff') ? 'background:' . esc_attr($this->column_data['background_color']) . ';' : '';
        $color['text'] = (!empty($this->column_data['text_color'])) ? 'color:' . esc_attr($this->column_data['text_color']) . ';' : '';
        return $color;
    }

    private function generate_field()
    {
        if (isset($this->fields_method[$this->column_data['content_type']]) && method_exists($this, $this->fields_method[$this->column_data['content_type']])) {
            return $this->{$this->fields_method[$this->column_data['content_type']]}();
        } else {
            return (is_array($this->coupon[$this->decoded_column_key])) ? implode(',', $this->coupon[$this->decoded_column_key]) : $this->coupon[$this->decoded_column_key];
        }
    }

    private function get_fields_method()
    {
        return [
            'text' => 'text_field',
            'email' => 'text_field',
            'textarea' => 'textarea_field',
            'image' => 'image_field',
            'numeric' => 'numeric_with_calculator_field',
            'numeric_without_calculator' => 'numeric_field',
            'checkbox_dual_mode' => 'checkbox_dual_model_field',
            'checkbox' => 'checkbox_field',
            'radio' => 'radio_field',
            'file' => 'file_field',
            'select' => 'select_field',
            'date' => 'date_field',
            'date_picker' => 'date_field',
            'date_time_picker' => 'datetime_field',
            'time_picker' => 'time_field',
            'used_by' => 'used_by_field',
            'used_in' => 'used_in_field',
            'products' => 'products_field',
            'product_categories' => 'product_categories_field',
        ];
    }

    private function text_field()
    {
        $value = (is_array($this->coupon[$this->decoded_column_key])) ? implode(',', $this->coupon[$this->decoded_column_key]) : $this->coupon[$this->decoded_column_key];
        $output = "<span data-action='inline-editable' class='wccbel-td160'>" . wp_kses($value, Sanitizer::allowed_html_tags()) . "</span>";
        return $output;
    }

    private function textarea_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wccbel-modal-text-editor' class='wccbel-button wccbel-button-white wccbel-load-text-editor wccbel-td160' data-item-id='" . esc_attr($this->coupon['id']) . "' data-item-name='" . esc_attr($this->coupon['coupon_code']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'>Content</button>";
    }

    private function image_field()
    {
        if (isset($this->coupon[$this->decoded_column_key]['small'])) {
            $image_id = intval($this->coupon[$this->decoded_column_key]['id']);
            $image = wp_kses($this->coupon[$this->decoded_column_key]['small'], Sanitizer::allowed_html_tags());
            $full_size = wp_get_attachment_image_src($image_id, 'full');
        }
        if (isset($this->coupon[$this->decoded_column_key]) && is_numeric($this->coupon[$this->decoded_column_key])) {
            $image_id = intval($this->coupon[$this->decoded_column_key]);
            $image_url = wp_get_attachment_image_src($image_id, [40, 40]);
            $full_size = wp_get_attachment_image_src($image_id, 'full');
            $image = (!empty($image_url[0])) ? "<img src='" . esc_url($image_url[0]) . "' alt='' width='40' height='40' />" : null;
        }
        $image = (!empty($image)) ? $image : __('No Image', 'ithemeland-woocommerce-bulk-coupons-editing-lite');
        $full_size = (!empty($full_size[0])) ? $full_size[0] : esc_url(wp_upload_dir()['baseurl'] . "/woocommerce-placeholder.png");
        $image_id = (!empty($image_id)) ? $image_id : 0;

        return "<span data-toggle='modal' data-target='#wccbel-modal-image' data-id='wccbel-" . esc_attr($this->column_key) . "-" . esc_attr($this->coupon['id']) . "' class='wccbel-image-inline-edit' data-full-image-src='" . esc_url($full_size) . "' data-image-id='" . esc_attr($image_id) . "'>" . $image . "</span>";
    }

    private function numeric_with_calculator_field()
    {
        return "<span data-action='inline-editable' class='wccbel-numeric-content wccbel-td120'>" . esc_attr($this->coupon[$this->decoded_column_key]) . "</span><button type='button' data-toggle='modal' class='wccbel-calculator' data-field='" . esc_attr($this->column_key) . "' data-item-id='" . esc_attr($this->coupon['id']) . "' data-item-name='" . esc_attr($this->coupon['coupon_code']) . "' data-field-type='" . esc_attr($this->field_type) . "' data-target='#wccbel-modal-numeric-calculator'></button>";
    }

    private function numeric_field()
    {
        return "<span data-action='inline-editable' class='wccbel-numeric-content wccbel-td120'>" . esc_attr($this->coupon[$this->decoded_column_key]) . "</span>";
    }

    private function checkbox_dual_model_field()
    {
        $checked = ($this->coupon[$this->decoded_column_key] == 'yes' || $this->coupon[$this->decoded_column_key] == 1) ? 'checked="checked"' : "";
        return "<label><input type='checkbox' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->coupon['id']) . "' value='yes' class='wccbel-dual-mode-checkbox wccbel-inline-edit-action' " . wp_kses($checked, Sanitizer::allowed_html_tags()) . "><span>" . __('Yes', 'ithemeland-woocommerce-bulk-coupons-editing-lite') . "</span></label>";
    }

    private function file_field()
    {
        $file_id = (isset($this->coupon[$this->decoded_column_key])) ? intval($this->coupon[$this->decoded_column_key]) : null;
        $file_url = wp_get_attachment_url($file_id);
        $file_url = !empty($file_url) ? esc_url($file_url) : '';
        return "<button type='button' data-toggle='modal' data-target='#wccbel-modal-file' class='wccbel-button wccbel-button-white' data-item-id='" . esc_attr($this->coupon['id']) . "' data-item-name='" . esc_attr($this->coupon['coupon_code']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-file-id='" . $file_id . "' data-file-url='" . $file_url . "'>Select File</button>";
    }

    private function select_field()
    {
        $output = "<select class='wccbel-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-item-id='" . esc_attr($this->coupon['id']) . "' title='Select " . esc_attr($this->column_data['label']) . "' data-field-type='" . esc_attr($this->field_type) . "'>";
        if (!empty($this->column_data['options'])) {
            foreach ($this->column_data['options'] as $option_key => $option_value) {
                $selected = ($option_key == $this->coupon[$this->decoded_column_key]) ? 'selected' : '';
                $output .= "<option value='{$option_key}' $selected>{$option_value}</option>";
            }
        } else {
            if ($this->column_data['field_type'] == 'custom_field') {
                $meta_fields = get_option('wccbel_meta_fields', []);
                if (!empty($meta_fields[$this->column_data['name']]) && !empty($meta_fields[$this->column_data['name']]['key_value'])) {
                    $options = Meta_Field_Helper::key_value_field_to_array($meta_fields[$this->column_data['name']]['key_value']);
                    if (!empty($options) && is_array($options)) {
                        foreach ($options as $option_key => $option_value) {
                            $selected = isset($this->coupon[$this->decoded_column_key]) && $this->coupon[$this->decoded_column_key] == $option_key ? 'selected' : '';
                            $output .= "<option value='{$option_key}' $selected>{$option_value}</option>";
                        }
                    }
                }
            }
        }

        $output .= '</select>';
        return $output;
    }

    private function date_field()
    {
        $date = (!empty($this->coupon[$this->decoded_column_key])) ? gmdate('Y/m/d', strtotime($this->coupon[$this->decoded_column_key])) : '';
        $clear_button = ($this->decoded_column_key != 'date_created') ? "<button type='button' class='wccbel-clear-date-btn wccbel-inline-edit-clear-date' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->coupon['id']) . "' value=''><img src='" . esc_url(WCCBEL_IMAGES_URL . 'calendar_clear.svg') . "' alt='Clear' title='Clear Date'></button>" : '';
        return "<input type='text' class='wccbel-datepicker wccbel-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->coupon['id']) . "' title='Select " . esc_attr($this->column_data['label']) . "' value='" . esc_attr($date) . "'>" . wp_kses($clear_button, Sanitizer::allowed_html_tags());
    }

    private function datetime_field()
    {
        $date = (!empty($this->coupon[$this->decoded_column_key])) ? gmdate('Y/m/d H:i', strtotime($this->coupon[$this->decoded_column_key])) : '';
        $clear_button = ($this->decoded_column_key != 'date_created') ? "<button type='button' class='wccbel-clear-date-btn wccbel-inline-edit-clear-date' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->coupon['id']) . "' value=''><img src='" . esc_url(WCCBEL_IMAGES_URL . 'calendar_clear.svg') . "' alt='Clear' title='Clear Date'></button>" : '';
        return "<input type='text' class='wccbel-datetimepicker wccbel-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->coupon['id']) . "' title='Select " . esc_attr($this->column_data['label']) . "' value='" . esc_attr($date) . "'>" . wp_kses($clear_button, Sanitizer::allowed_html_tags());
    }

    private function time_field()
    {
        $date = (!empty($this->coupon[$this->decoded_column_key])) ? gmdate('H:i', strtotime($this->coupon[$this->decoded_column_key])) : '';
        $clear_button = "<button type='button' class='wccbel-clear-date-btn wccbel-inline-edit-clear-date' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->coupon['id']) . "' value=''><img src='" . esc_url(WCCBEL_IMAGES_URL . 'calendar_clear.svg') . "' alt='Clear' title='Clear Date'></button>";
        return "<input type='text' class='wccbel-timepicker wccbel-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->coupon['id']) . "' title='Select " . esc_attr($this->column_data['label']) . "' value='" . esc_attr($date) . "'>" . wp_kses($clear_button, Sanitizer::allowed_html_tags());
    }

    private function products_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wccbel-modal-products' class='wccbel-button wccbel-button-white wccbel-td160 wccbel-coupon-products-button' data-item-id='" . esc_attr($this->coupon['id']) . "' data-item-name='" . esc_attr($this->coupon['coupon_code']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'>" . __("Products", 'ithemeland-woocommerce-bulk-coupons-editing-lite') . "</button>";
    }

    private function product_categories_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wccbel-modal-categories' class='wccbel-button wccbel-button-white wccbel-td160 wccbel-coupon-categories-button' data-item-id='" . esc_attr($this->coupon['id']) . "' data-item-name='" . esc_attr($this->coupon['coupon_code']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'>" . __("Categories", 'ithemeland-woocommerce-bulk-coupons-editing-lite') . "</button>";
    }

    private function used_by_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wccbel-modal-used-by' class='wccbel-button wccbel-button-white wccbel-td160 wccbel-coupon-used-by-button' data-item-id='" . esc_attr($this->coupon['id']) . "' data-item-name='" . esc_attr($this->coupon['coupon_code']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'>" . __("Used by", 'ithemeland-woocommerce-bulk-coupons-editing-lite') . "</button>";
    }

    private function used_in_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wccbel-modal-used-in' class='wccbel-button wccbel-button-white wccbel-td160 wccbel-coupon-used-in-button' data-item-id='" . esc_attr($this->coupon['id']) . "' data-item-name='" . esc_attr($this->coupon['coupon_code']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'>" . __("Used in", 'ithemeland-woocommerce-bulk-coupons-editing-lite') . "</button>";
    }
}
