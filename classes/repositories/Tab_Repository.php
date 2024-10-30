<?php

namespace wccbel\classes\repositories;

defined('ABSPATH') || exit(); // Exit if accessed directly

use wccbel\classes\helpers\Generator;
use wccbel\classes\helpers\Operator;
use wccbel\classes\helpers\Meta_Field as Meta_Field_Helper;

class Tab_Repository
{
    private $field_titles;
    private $coupon_statuses;
    private $discount_types;
    private $meta_fields;
    private $meta_field_repository;

    public function __construct()
    {
        $column_repository = new Column();
        $this->meta_field_repository = new Meta_Field();

        $coupon_repository = Coupon::get_instance();

        $this->field_titles = $column_repository->get_columns_title();
        $this->coupon_statuses = $coupon_repository->get_coupon_statuses();
        $this->discount_types = wc_get_coupon_types();
        $this->meta_fields = $this->meta_field_repository->get();
    }

    public function get_bulk_edit_form_tabs_title()
    {
        return [
            'general' => __("General", 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'usage_restriction' => __("Usage Restriction", 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'usage_limits' => __("Usage Limits", 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'custom_fields' => __("Custom Fields", 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
        ];
    }

    private function get_text_variable()
    {
        return [
            '' => __('Variable', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'coupon_code' => __('Coupon Code', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'id' => __('ID', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
        ];
    }

    public function get_bulk_edit_form_tabs_content()
    {
        $custom_fields = $this->get_bulk_edit_custom_fields();

        return [
            'general' => [
                'wrapper_start' => Generator::div_field_start([
                    'class' => 'selected wccbel-tab-content-item',
                    'data-content' => 'general'
                ]),
                'wrapper_end' => Generator::div_field_end(),
                'fields' => [
                    'coupon_code' => [
                        'wrap_attributes' => 'data-name="coupon_code" data-type="woocommerce_field"',
                        'html' => [
                            Generator::label_field(['for' => 'wccbel-bulk-edit-form-coupon-title'], esc_html__('Coupon Code', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'id' => 'wccbel-bulk-edit-form-coupon-title-operator',
                                'data-field' => 'operator',
                                'title' => esc_html__('Select Operator', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], Operator::edit_text()),
                            Generator::input_field([
                                'type' => 'text',
                                'id' => 'wccbel-bulk-edit-form-coupon-title',
                                'data-field' => 'value',
                                'placeholder' => esc_html__('Coupon Code ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ])
                        ]
                    ],
                    'date_created' => [
                        'wrap_attributes' => 'data-name="date_created" data-type="woocommerce_field"',
                        'html' => [
                            Generator::label_field(['for' => 'wccbel-bulk-edit-form-coupon-date'], esc_html__('Date', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::input_field([
                                'class' => 'wccbel-datetimepicker',
                                'type' => 'text',
                                'id' => 'wccbel-bulk-edit-form-coupon-date',
                                'data-field' => 'value',
                                'placeholder' => esc_html__('Date ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ]),
                        ]
                    ],
                    'description' => [
                        'wrap_attributes' => 'data-name="description" data-type="woocommerce_field"',
                        'html' => [
                            Generator::label_field(['for' => 'wccbel-bulk-edit-form-coupon-description'], esc_html__('Description', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'id' => 'wccbel-bulk-edit-form-coupon-description-operator',
                                'data-field' => 'operator',
                                'title' => esc_html__('Select Operator', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], Operator::edit_text()),
                            Generator::textarea_field([
                                'id' => 'wccbel-bulk-edit-form-coupon-description',
                                'data-field' => 'value',
                                'placeholder' => esc_html__('Description ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ]),
                        ]
                    ],
                    'post_status' => [
                        'wrap_attributes' => 'data-name="post_status" data-type="wp_posts_field"',
                        'html' => [
                            Generator::label_field(['for' => 'wccbel-bulk-edit-form-coupon-status'], esc_html__('Status', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'class' => 'wccbel-input-md',
                                'id' => 'wccbel-bulk-edit-form-coupon-status',
                                'data-field' => 'value',
                                'title' => esc_html__('Select Status ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], $this->coupon_statuses, true),
                        ]
                    ],
                    'discount_type' => [
                        'wrap_attributes' => 'data-name="discount_type" data-type="woocommerce_field"',
                        'html' => [
                            Generator::label_field(['for' => 'wccbel-bulk-edit-form-coupon-discount-type'], esc_html__('Discount Type', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'class' => 'wccbel-input-md',
                                'id' => 'wccbel-bulk-edit-form-coupon-discount-type',
                                'data-field' => 'value',
                                'title' => esc_html__('Select Discount Type ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], $this->discount_types, true),
                        ]
                    ],
                    'coupon_amount' => [
                        'wrap_attributes' => 'data-name="coupon_amount" data-type="woocommerce_field"',
                        'html' => [
                            Generator::label_field(['for' => 'wccbel-bulk-edit-form-coupon-amount'], esc_html__('Coupon Amount', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'id' => 'wccbel-bulk-edit-form-coupon-amount-operator',
                                'data-field' => 'operator',
                                'title' => esc_html__('Select Operator', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], Operator::edit_number()),
                            Generator::input_field([
                                'type' => 'number',
                                'id' => 'wccbel-bulk-edit-form-coupon-amount',
                                'data-field' => 'value',
                                'placeholder' => esc_html__('Coupon Amount ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ]),
                            Generator::help_icon(isset($this->field_titles['coupon_amount']) ? $this->field_titles['coupon_amount'] : '')
                        ]
                    ],
                    'free_shipping' => [
                        'wrap_attributes' => 'data-name="free_shipping" data-type="woocommerce_field"',
                        'html' => [
                            Generator::label_field(['for' => 'wccbel-bulk-edit-form-coupon-free-shipping'], esc_html__('Allow free shipping', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'class' => 'wccbel-input-md',
                                'id' => 'wccbel-bulk-edit-form-coupon-free-shipping',
                                'data-field' => 'value',
                            ], [
                                'yes' => esc_html__('Yes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                                'no' => esc_html__('No', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                            ], true),
                        ]
                    ],
                    'date_expires' => [
                        'wrap_attributes' => 'data-name="date_expires" data-type="woocommerce_field"',
                        'html' => [
                            Generator::label_field(['for' => 'wccbel-bulk-edit-form-coupon-expire-date'], esc_html__('Coupon expiry date', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::input_field([
                                'class' => 'wccbel-datepicker',
                                'type' => 'text',
                                'id' => 'wccbel-bulk-edit-form-coupon-expire-date',
                                'data-field' => 'value',
                                'placeholder' => esc_html__('Coupon expiry date ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ]),
                            Generator::help_icon(isset($this->field_titles['date_expires']) ? $this->field_titles['date_expires'] : '')
                        ]
                    ],
                ]
            ],
            'usage_restriction' => [
                'wrapper_start' => Generator::div_field_start([
                    'class' => 'wccbel-tab-content-item',
                    'data-content' => 'usage_restriction'
                ]),
                'wrapper_end' => Generator::div_field_end(),
                'fields' => [
                    'minimum_amount' => [
                        'wrap_attributes' => 'data-name="minimum_amount" data-type="woocommerce_field"',
                        'html' => [
                            Generator::label_field(['for' => 'wccbel-bulk-edit-form-coupon-minimum-amount'], esc_html__('Minimum spend', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'id' => 'wccbel-bulk-edit-form-coupon-minimum-amount-operator',
                                'data-field' => 'operator',
                                'title' => esc_html__('Select Operator', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], Operator::edit_number()),
                            Generator::input_field([
                                'type' => 'number',
                                'id' => 'wccbel-bulk-edit-form-coupon-minimum-amount',
                                'data-field' => 'value',
                                'placeholder' => esc_html__('Minimum spend ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ]),
                            Generator::help_icon(isset($this->field_titles['minimum_amount']) ? $this->field_titles['minimum_amount'] : '')
                        ]
                    ],
                    'maximum_amount' => [
                        'wrap_attributes' => 'data-name="maximum_amount" data-type="woocommerce_field"',
                        'html' => [
                            Generator::label_field(['for' => 'wccbel-bulk-edit-form-coupon-maximum-amount'], esc_html__('Maximum spend', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'id' => 'wccbel-bulk-edit-form-coupon-maximum-amount-operator',
                                'data-field' => 'operator',
                                'title' => esc_html__('Select Operator', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], Operator::edit_number()),
                            Generator::input_field([
                                'type' => 'number',
                                'id' => 'wccbel-bulk-edit-form-coupon-maximum-amount',
                                'data-field' => 'value',
                                'placeholder' => esc_html__('Maximum spend ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ]),
                            Generator::help_icon(isset($this->field_titles['maximum_amount']) ? $this->field_titles['maximum_amount'] : '')
                        ]
                    ],
                    'individual_use' => [
                        'wrap_attributes' => '',
                        'html' => [
                            Generator::label_field([], esc_html__('Individual use only', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'class' => 'wccbel-input-md',
                                'disabled' => 'disabled',
                            ], [], true),
                            Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                                'class' => 'wccbel-short-description'
                            ])
                        ]
                    ],
                    'exclude_sale_items' => [
                        'wrap_attributes' => '',
                        'html' => [
                            Generator::label_field([], esc_html__('Exclude sale items', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'class' => 'wccbel-input-md',
                                'disabled' => 'disabled',
                            ], [], true),
                            Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                                'class' => 'wccbel-short-description'
                            ])
                        ]
                    ],
                    'product_ids' => [
                        'wrap_attributes' => '',
                        'html' => [
                            Generator::label_field([], esc_html__('Include products', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'disabled' => 'disabled',
                            ], Operator::edit_taxonomy()),
                            Generator::select_field([
                                'class' => 'wccbel-select2-products',
                                'multiple' => 'multiple',
                                'disabled' => 'disabled',
                                'data-placeholder' => esc_html__('Include products ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], []),
                            Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                                'class' => 'wccbel-short-description'
                            ])
                        ]
                    ],
                    'exclude_product_ids' => [
                        'wrap_attributes' => '',
                        'html' => [
                            Generator::label_field([], esc_html__('Exclude products', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'disabled' => 'disabled',
                            ], Operator::edit_taxonomy()),
                            Generator::select_field([
                                'class' => 'wccbel-select2-products',
                                'multiple' => 'multiple',
                                'disabled' => 'disabled',
                                'data-placeholder' => esc_html__('Exclude products ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], []),
                            Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                                'class' => 'wccbel-short-description'
                            ])
                        ]
                    ],
                    'product_categories' => [
                        'wrap_attributes' => '',
                        'html' => [
                            Generator::label_field([], esc_html__('Include categories', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'disabled' => 'disabled',
                            ], Operator::edit_taxonomy()),
                            Generator::select_field([
                                'class' => 'wccbel-select2-categories',
                                'multiple' => 'multiple',
                                'disabled' => 'disabled',
                                'data-placeholder' => esc_html__('Include categories ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], []),
                            Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                                'class' => 'wccbel-short-description'
                            ])
                        ]
                    ],
                    'exclude_product_categories' => [
                        'wrap_attributes' => '',
                        'html' => [
                            Generator::label_field([], esc_html__('Exclude categories', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'disabled' => 'disabled',
                            ], Operator::edit_taxonomy()),
                            Generator::select_field([
                                'class' => 'wccbel-select2-categories',
                                'multiple' => 'multiple',
                                'disabled' => 'disabled',
                                'data-placeholder' => esc_html__('Exclude categories ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], []),
                            Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                                'class' => 'wccbel-short-description'
                            ])
                        ]
                    ],
                    'customer_email' => [
                        'wrap_attributes' => '',
                        'html' => [
                            Generator::label_field([], esc_html__('Allowed Emails', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::input_field([
                                'type' => 'text',
                                'disabled' => 'disabled',
                                'placeholder' => esc_html__('Allowed Emails ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ]),
                            Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                                'class' => 'wccbel-short-description'
                            ])
                        ]
                    ],
                ]
            ],
            'usage_limits' => [
                'wrapper_start' => Generator::div_field_start([
                    'class' => 'wccbel-tab-content-item',
                    'data-content' => 'usage_limits'
                ]),
                'wrapper_end' => Generator::div_field_end(),
                'fields' => [
                    'usage_limit' => [
                        'wrap_attributes' => 'data-name="usage_limit" data-type="woocommerce_field"',
                        'html' => [
                            Generator::label_field(['for' => 'wccbel-bulk-edit-form-coupon-usage-limit'], esc_html__('Usage limit per coupon', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'id' => 'wccbel-bulk-edit-form-coupon-usage-limit-operator',
                                'data-field' => 'operator',
                                'title' => esc_html__('Select Operator', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], Operator::edit_number()),
                            Generator::input_field([
                                'type' => 'number',
                                'id' => 'wccbel-bulk-edit-form-coupon-usage-limit',
                                'data-field' => 'value',
                                'placeholder' => esc_html__('Usage limit per coupon ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ]),
                            Generator::help_icon(isset($this->field_titles['usage_limit']) ? $this->field_titles['usage_limit'] : '')
                        ]
                    ],
                    'limit_usage_to_x_items' => [
                        'wrap_attributes' => 'data-name="limit_usage_to_x_items" data-type="woocommerce_field"',
                        'html' => [
                            Generator::label_field(['for' => 'wccbel-bulk-edit-form-coupon-limit-usage-to-x-items'], esc_html__('Limit usage to x items', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'id' => 'wccbel-bulk-edit-form-coupon-limit-usage-to-x-items-operator',
                                'data-field' => 'operator',
                                'title' => esc_html__('Select Operator', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], Operator::edit_number()),
                            Generator::input_field([
                                'type' => 'number',
                                'id' => 'wccbel-bulk-edit-form-coupon-limit-usage-to-x-items',
                                'data-field' => 'value',
                                'placeholder' => esc_html__('Limit usage to x items ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ]),
                        ]
                    ],
                    'usage_limit_per_user' => [
                        'wrap_attributes' => 'data-name="usage_limit_per_user" data-type="woocommerce_field"',
                        'html' => [
                            Generator::label_field(['for' => 'wccbel-bulk-edit-form-coupon-usage-limit-per-user'], esc_html__('Usage limit per user', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                            Generator::select_field([
                                'id' => 'wccbel-bulk-edit-form-coupon-usage-limit-per-user-operator',
                                'data-field' => 'operator',
                                'title' => esc_html__('Select Operator', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ], Operator::edit_number()),
                            Generator::input_field([
                                'type' => 'number',
                                'id' => 'wccbel-bulk-edit-form-coupon-usage-limit-per-user',
                                'data-field' => 'value',
                                'placeholder' => esc_html__('Usage limit per user ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                            ]),
                            Generator::help_icon(isset($this->field_titles['usage_limit_per_user']) ? $this->field_titles['usage_limit_per_user'] : '')
                        ]
                    ],
                ]
            ],
            'custom_fields' => [
                'wrapper_start' => Generator::div_field_start([
                    'class' => 'wccbel-tab-content-item',
                    'data-content' => 'custom_fields'
                ]),
                'fields_top' => $custom_fields['top_alert'],
                'wrapper_end' => Generator::div_field_end(),
                'fields' => $custom_fields['fields']
            ],
        ];
    }

    public function get_filter_form_tabs_title()
    {
        return [
            'filter_general' => __("General", 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'filter_usage_restriction' => __("Usage Restriction", 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'filter_usage_limits' => __("Usage Limits", 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
            'filter_custom_fields' => __("Custom Fields", 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
        ];
    }

    public function get_filter_form_tabs_content()
    {
        $custom_fields = $this->get_filter_form_custom_fields();

        return [
            'filter_general' => [
                'wrapper_start' => Generator::div_field_start([
                    'class' => 'selected wccbel-tab-content-item',
                    'data-content' => 'filter_general'
                ]),
                'wrapper_end' => Generator::div_field_end(),
                'fields' => [
                    'coupon_ids' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-ids'], esc_html__('Coupon ID(s)', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::select_field([
                            'id' => 'wccbel-filter-form-coupon-ids-operator',
                            'data-field' => 'operator',
                            'title' => esc_html__('Select Operator', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ], [
                            'exact' => esc_html__('Exact', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::input_field([
                            'type' => 'text',
                            'id' => 'wccbel-filter-form-coupon-ids',
                            'data-field' => 'value',
                            'placeholder' => esc_html__('for example: 1,2,3 or 1-10 or 1,2,3|10-20', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                    ],
                    'coupon_code' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-title'], esc_html__('Coupon Code', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::select_field([
                            'id' => 'wccbel-filter-form-coupon-title-operator',
                            'data-field' => 'operator',
                            'title' => esc_html__('Select Operator', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ], Operator::filter_text()),
                        Generator::input_field([
                            'type' => 'text',
                            'id' => 'wccbel-filter-form-coupon-title',
                            'data-field' => 'value',
                            'placeholder' => esc_html__('Title ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                    ],
                    'post_excerpt' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-description'], esc_html__('Description', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::select_field([
                            'id' => 'wccbel-filter-form-coupon-description-operator',
                            'data-field' => 'operator',
                            'title' => esc_html__('Select Operator', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ], Operator::filter_text()),
                        Generator::textarea_field([
                            'id' => 'wccbel-filter-form-coupon-description',
                            'data-field' => 'value',
                            'placeholder' => esc_html__('Description ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                    ],
                    'post_date' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-date-from'], esc_html__('Date', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::input_field([
                            'class' => 'wccbel-datetimepicker wccbel-input-ft wccbel-date-from',
                            'data-to-id' => 'wccbel-filter-form-coupon-date-to',
                            'type' => 'text',
                            'id' => 'wccbel-filter-form-coupon-date-from',
                            'data-field' => 'from',
                            'placeholder' => esc_html__('Date From ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::input_field([
                            'class' => 'wccbel-datetimepicker wccbel-input-ft',
                            'type' => 'text',
                            'id' => 'wccbel-filter-form-coupon-date-to',
                            'data-field' => 'to',
                            'placeholder' => esc_html__('Date To ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                    ],
                    'post_modified' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-modified-date-from'], esc_html__('Modified Date', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::input_field([
                            'class' => 'wccbel-datetimepicker wccbel-input-ft wccbel-date-from',
                            'data-to-id' => 'wccbel-filter-form-coupon-modified-date-to',
                            'type' => 'text',
                            'id' => 'wccbel-filter-form-coupon-modified-date-from',
                            'data-field' => 'from',
                            'placeholder' => esc_html__('Modified Date From ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::input_field([
                            'class' => 'wccbel-datetimepicker wccbel-input-ft',
                            'type' => 'text',
                            'id' => 'wccbel-filter-form-coupon-modified-date-to',
                            'data-field' => 'to',
                            'placeholder' => esc_html__('Modified Date To ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                    ],
                    'post_status' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-status'], esc_html__('Status', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::select_field([
                            'multiple' => 'true',
                            'class' => 'wccbel-input-md wccbel-select2',
                            'id' => 'wccbel-filter-form-coupon-status',
                            'data-field' => 'value',
                            'title' => esc_html__('Select Status ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ], $this->coupon_statuses, false)
                    ],
                    'discount_type' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-discount-type'], esc_html__('Discount type', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::select_field([
                            'multiple' => 'true',
                            'class' => 'wccbel-input-md wccbel-select2',
                            'id' => 'wccbel-filter-form-coupon-discount-type',
                            'data-field' => 'value',
                            'title' => esc_html__('Discount type ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ], $this->discount_types, false)
                    ],
                    'coupon_amount' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-amount-from'], esc_html__('Coupon Amount', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::input_field([
                            'class' => 'wccbel-input-ft',
                            'data-to-id' => 'wccbel-filter-form-coupon-amount-to',
                            'type' => 'number',
                            'id' => 'wccbel-filter-form-coupon-amount-from',
                            'data-field' => 'from',
                            'placeholder' => esc_html__('Amount From ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::input_field([
                            'class' => 'wccbel-input-ft',
                            'type' => 'number',
                            'id' => 'wccbel-filter-form-coupon-amount-to',
                            'data-field' => 'to',
                            'placeholder' => esc_html__('Amount To ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::help_icon(isset($this->field_titles['coupon_amount']) ? $this->field_titles['coupon_amount'] : '')
                    ],
                    'free_shipping' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-free-shipping'], esc_html__('Allow free shipping', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::select_field([
                            'class' => 'wccbel-input-md',
                            'id' => 'wccbel-filter-form-coupon-free-shipping',
                            'data-field' => 'value',
                        ], [
                            'yes' => esc_html__('Yes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                            'no' => esc_html__('No', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                        ], true),
                    ],
                    'date_expires' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-expiry-date-from'], esc_html__('Expiry date', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::input_field([
                            'class' => 'wccbel-datepicker wccbel-input-ft wccbel-date-from',
                            'data-to-id' => 'wccbel-filter-form-coupon-expiry-date-to',
                            'type' => 'text',
                            'id' => 'wccbel-filter-form-coupon-expiry-date-from',
                            'data-field' => 'from',
                            'placeholder' => esc_html__('Date From ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::input_field([
                            'class' => 'wccbel-datepicker wccbel-input-ft',
                            'type' => 'text',
                            'id' => 'wccbel-filter-form-coupon-expiry-date-to',
                            'data-field' => 'to',
                            'placeholder' => esc_html__('Date To ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::help_icon(isset($this->field_titles['date_expires']) ? $this->field_titles['date_expires'] : '')
                    ],
                ]
            ],
            'filter_usage_restriction' => [
                'wrapper_start' => Generator::div_field_start([
                    'class' => 'wccbel-tab-content-item',
                    'data-content' => 'filter_usage_restriction'
                ]),
                'wrapper_end' => Generator::div_field_end(),
                'fields' => [
                    'minimum_amount' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-minimum-amount'], esc_html__('Minimum spend', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::input_field([
                            'class' => 'wccbel-input-ft',
                            'data-to-id' => 'wccbel-filter-form-coupon-minimum-amount-to',
                            'type' => 'number',
                            'id' => 'wccbel-filter-form-coupon-minimum-amount-from',
                            'data-field' => 'from',
                            'placeholder' => esc_html__('Amount From ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::input_field([
                            'class' => 'wccbel-input-ft',
                            'type' => 'number',
                            'id' => 'wccbel-filter-form-coupon-minimum-amount-to',
                            'data-field' => 'to',
                            'placeholder' => esc_html__('Amount To ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::help_icon(isset($this->field_titles['minimum_amount']) ? $this->field_titles['minimum_amount'] : '')
                    ],
                    'maximum_amount' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-maximum-amount'], esc_html__('Maximum spend', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::input_field([
                            'class' => 'wccbel-input-ft',
                            'data-to-id' => 'wccbel-filter-form-coupon-maximum-amount-to',
                            'type' => 'number',
                            'id' => 'wccbel-filter-form-coupon-maximum-amount-from',
                            'data-field' => 'from',
                            'placeholder' => esc_html__('Amount From ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::input_field([
                            'class' => 'wccbel-input-ft',
                            'type' => 'number',
                            'id' => 'wccbel-filter-form-coupon-maximum-amount-to',
                            'data-field' => 'to',
                            'placeholder' => esc_html__('Amount To ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::help_icon(isset($this->field_titles['maximum_amount']) ? $this->field_titles['maximum_amount'] : '')
                    ],
                    'individual_use' => [
                        Generator::label_field([], esc_html__('Individual use only', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::select_field([
                            'class' => 'wccbel-input-md',
                            'disabled' => 'disabled',
                        ], [], true),
                        Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                            'class' => 'wccbel-short-description'
                        ])
                    ],
                    'exclude_sale_items' => [
                        Generator::label_field([], esc_html__('Exclude sale items', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::select_field([
                            'class' => 'wccbel-input-md',
                            'disabled' => 'disabled',
                        ], [
                            'yes' => esc_html__('Yes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                            'no' => esc_html__('No', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                        ], true),
                        Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                            'class' => 'wccbel-short-description'
                        ])
                    ],
                    'product_ids' => [
                        Generator::label_field([], esc_html__('Include products', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::select_field([
                            'disabled' => 'disabled',
                        ], Operator::filter_multi_select()),
                        Generator::select_field([
                            'class' => 'wccbel-select2-products',
                            'multiple' => 'multiple',
                            'disabled' => 'disabled',
                            'data-placeholder' => esc_html__('Include products ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ], []),
                        Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                            'class' => 'wccbel-short-description'
                        ])
                    ],
                    'exclude_product_ids' => [
                        Generator::label_field([], esc_html__('Exclude products', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::select_field([
                            'disabled' => 'disabled',
                        ], Operator::filter_multi_select()),
                        Generator::select_field([
                            'class' => 'wccbel-select2-products',
                            'multiple' => 'multiple',
                            'disabled' => 'disabled',
                            'data-placeholder' => esc_html__('Exclude products ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ], []),
                        Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                            'class' => 'wccbel-short-description'
                        ])
                    ],
                    'product_categories' => [
                        Generator::label_field([], esc_html__('Include categories', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::select_field([
                            'disabled' => 'disabled',
                        ], Operator::filter_multi_select()),
                        Generator::select_field([
                            'class' => 'wccbel-select2-categories',
                            'multiple' => 'multiple',
                            'disabled' => 'disabled',
                            'data-placeholder' => esc_html__('Include categories ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ], []),
                        Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                            'class' => 'wccbel-short-description'
                        ])
                    ],
                    'exclude_product_categories' => [
                        Generator::label_field([], esc_html__('Exclude categories', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::select_field([
                            'disabled' => 'disabled',
                        ], Operator::filter_multi_select()),
                        Generator::select_field([
                            'class' => 'wccbel-select2-categories',
                            'multiple' => 'multiple',
                            'disabled' => 'disabled',
                            'data-placeholder' => esc_html__('Exclude categories ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ], []),
                        Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                            'class' => 'wccbel-short-description'
                        ])
                    ],
                    'customer_email' => [
                        Generator::label_field([], esc_html__('Allowed Emails', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::select_field([
                            'disabled' => 'disabled',
                        ], Operator::filter_text()),
                        Generator::input_field([
                            'type' => 'text',
                            'disabled' => 'disabled',
                            'placeholder' => esc_html__('Allowed Emails ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::span_field(esc_html__("Upgrade to pro version", 'ithemeland-woocommerce-bulk-coupons-editing-lite'), [
                            'class' => 'wccbel-short-description'
                        ])
                    ],
                ]
            ],
            'filter_usage_limits' => [
                'wrapper_start' => Generator::div_field_start([
                    'class' => 'wccbel-tab-content-item',
                    'data-content' => 'filter_usage_limits'
                ]),
                'wrapper_end' => Generator::div_field_end(),
                'fields' => [
                    'usage_limit' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-usage-limit-from'], esc_html__('Usage limit per coupon', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::input_field([
                            'class' => 'wccbel-input-ft',
                            'data-to-id' => 'wccbel-filter-form-coupon-usage-limit-to',
                            'type' => 'number',
                            'id' => 'wccbel-filter-form-coupon-usage-limit-from',
                            'data-field' => 'from',
                            'placeholder' => esc_html__('From ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::input_field([
                            'class' => 'wccbel-input-ft',
                            'type' => 'number',
                            'id' => 'wccbel-filter-form-coupon-usage-limit-to',
                            'data-field' => 'to',
                            'placeholder' => esc_html__('To ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::help_icon(isset($this->field_titles['usage_limit']) ? $this->field_titles['usage_limit'] : '')
                    ],
                    'limit_usage_to_x_items' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-limit-usage-to-x-items-from'], esc_html__('Limit usage to x items', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::input_field([
                            'class' => 'wccbel-input-ft',
                            'data-to-id' => 'wccbel-filter-form-coupon-limit-usage-to-x-items-to',
                            'type' => 'number',
                            'id' => 'wccbel-filter-form-coupon-limit-usage-to-x-items-from',
                            'data-field' => 'from',
                            'placeholder' => esc_html__('From ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::input_field([
                            'class' => 'wccbel-input-ft',
                            'type' => 'number',
                            'id' => 'wccbel-filter-form-coupon-limit-usage-to-x-items-to',
                            'data-field' => 'to',
                            'placeholder' => esc_html__('To ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                    ],
                    'usage_limit_per_user' => [
                        Generator::label_field(['for' => 'wccbel-filter-form-coupon-usage-limit-per-user-from'], esc_html__('Usage limit per user', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                        Generator::input_field([
                            'class' => 'wccbel-input-ft',
                            'data-to-id' => 'wccbel-filter-form-coupon-usage-limit-per-user-to',
                            'type' => 'number',
                            'id' => 'wccbel-filter-form-coupon-usage-limit-per-user-from',
                            'data-field' => 'from',
                            'placeholder' => esc_html__('From ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::input_field([
                            'class' => 'wccbel-input-ft',
                            'type' => 'number',
                            'id' => 'wccbel-filter-form-coupon-usage-limit-per-user-to',
                            'data-field' => 'to',
                            'placeholder' => esc_html__('To ...', 'ithemeland-woocommerce-bulk-coupons-editing-lite')
                        ]),
                        Generator::help_icon(isset($this->field_titles['usage_limit_per_user']) ? $this->field_titles['usage_limit_per_user'] : '')
                    ],
                ]
            ],
            'filter_custom_fields' => [
                'wrapper_start' => Generator::div_field_start([
                    'class' => 'wccbel-tab-content-item',
                    'data-content' => 'filter_custom_fields'
                ]),
                'fields_top' => $custom_fields['top_alert'],
                'wrapper_end' => Generator::div_field_end(),
                'fields' => $custom_fields['fields']
            ],
        ];
    }

    private function get_bulk_edit_custom_fields()
    {
        $output['top_alert'] = [];
        $output['fields'] = [];

        if (!empty($this->meta_fields) && is_array($this->meta_fields)) {
            foreach ($this->meta_fields as $meta_field) {
                $field_id = 'wccbel-bulk-edit-form-coupon-' . $meta_field['key'];
                $output['fields'][$meta_field['key']]['wrap_attributes'] = "data-name='{$meta_field['key']}' data-type='meta_field'";;
                $output['fields'][$meta_field['key']]['html'][] = Generator::label_field(['for' => $field_id], $meta_field['title']);
                if (in_array($meta_field['main_type'], $this->meta_field_repository::get_fields_name_have_operator()) || ($meta_field['main_type'] == $this->meta_field_repository::TEXTINPUT && $meta_field['sub_type'] == $this->meta_field_repository::STRING_TYPE)) {
                    $class = ($meta_field['main_type'] == $this->meta_field_repository::CALENDAR) ? 'wccbel-datepicker' : '';
                    $output['fields'][$meta_field['key']]['html'][] = Generator::select_field([
                        'data-field' => 'operator',
                        'id' => $field_id . '-operator'
                    ], Operator::edit_text());
                    $output['fields'][$meta_field['key']]['html'][] = Generator::input_field([
                        'type' => 'text',
                        'data-field' => 'value',
                        'id' => $field_id,
                        'placeholder' => $meta_field['title'] . ' ...',
                        'class' => $class
                    ]);
                } elseif ($meta_field['main_type'] == $this->meta_field_repository::TEXTINPUT && $meta_field['sub_type'] == $this->meta_field_repository::NUMBER) {
                    $output['fields'][$meta_field['key']]['html'][] = Generator::select_field([
                        'data-field' => 'operator',
                        'for' => $field_id
                    ], Operator::edit_number());
                    $output['fields'][$meta_field['key']]['html'][] = Generator::input_field([
                        'type' => 'number',
                        'data-field' => 'value',
                        'id' => $field_id,
                        'placeholder' => $meta_field['title'] . ' ...',
                    ]);
                } elseif ($meta_field['main_type'] == $this->meta_field_repository::CHECKBOX) {
                    $output['fields'][$meta_field['key']]['html'][] = Generator::select_field([
                        'id' => $field_id,
                        'data-field' => 'value',
                    ], [
                        'yes' => esc_html__('Yes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                        'no' => esc_html__('No', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                    ], true);
                } elseif (in_array($meta_field['main_type'], [$this->meta_field_repository::SELECT, $this->meta_field_repository::ARRAY_TYPE]) && !empty($meta_field['key_value'])) {
                    $options = Meta_Field_Helper::key_value_field_to_array($meta_field['key_value']);
                    $output['fields'][$meta_field['key']]['html'][] = Generator::select_field([
                        'id' => $field_id,
                        'class' => 'wccbel-input-md',
                        'data-field' => 'value',
                    ], $options, true);
                } elseif (in_array($meta_field['main_type'], [$this->meta_field_repository::CALENDAR, $this->meta_field_repository::DATE])) {
                    $output['fields'][$meta_field['key']]['html'][] = Generator::input_field([
                        'type' => 'text',
                        'class' => 'wccbel-input-md wccbel-datepicker',
                        'data-field' => 'value',
                        'data-field-type' => 'date',
                        'id' => $field_id,
                        'placeholder' => $meta_field['title'] . ' ...',
                    ]);
                } elseif ($meta_field['main_type'] == $this->meta_field_repository::DATE_TIME) {
                    $output['fields'][$meta_field['key']]['html'][] = Generator::input_field([
                        'type' => 'text',
                        'class' => 'wccbel-input-md wccbel-datetimepicker',
                        'data-field' => 'value',
                        'data-field-type' => 'date',
                        'id' => $field_id,
                        'placeholder' => $meta_field['title'] . ' ...',
                    ]);
                } elseif ($meta_field['main_type'] == $this->meta_field_repository::TIME) {
                    $output['fields'][$meta_field['key']]['html'][] = Generator::input_field([
                        'type' => 'text',
                        'class' => 'wccbel-input-md wccbel-timepicker',
                        'data-field' => 'value',
                        'data-field-type' => 'date',
                        'id' => $field_id,
                        'placeholder' => $meta_field['title'] . ' ...',
                    ]);
                }
            }
        } else {
            $output['top_alert'] = [
                Generator::div_field_start([
                    'class' => 'wccbel-alert wccbel-alert-warning',
                ]),
                Generator::span_field(__('There is not any added Meta Fields, You can add new Meta Fields trough "Meta Fields" tab.', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                Generator::div_field_end()
            ];
        }

        return $output;
    }

    private function get_filter_form_custom_fields()
    {
        $output['top_alert'] = [];
        $output['fields'] = [];

        if (!empty($this->meta_fields) && is_array($this->meta_fields)) {
            foreach ($this->meta_fields as $meta_field) {
                $field_id = 'wccbel-bulk-edit-form-coupon-' . $meta_field['key'];
                $output['fields'][$meta_field['key']][] = Generator::label_field(['for' => $field_id], $meta_field['title']);
                if (in_array($meta_field['main_type'], $this->meta_field_repository::get_fields_name_have_operator()) || ($meta_field['main_type'] == $this->meta_field_repository::TEXTINPUT && $meta_field['sub_type'] == $this->meta_field_repository::STRING_TYPE)) {
                    $class = ($meta_field['main_type'] == $this->meta_field_repository::CALENDAR) ? 'wccbel-datepicker' : '';
                    $output['fields'][$meta_field['key']][] = Generator::select_field([
                        'data-field' => 'operator',
                        'id' => $field_id . '-operator'
                    ], Operator::filter_text());
                    $output['fields'][$meta_field['key']][] = Generator::input_field([
                        'type' => 'text',
                        'data-field' => 'value',
                        'id' => $field_id,
                        'placeholder' => $meta_field['title'] . ' ...',
                        'class' => $class
                    ]);
                } elseif ($meta_field['main_type'] == $this->meta_field_repository::TEXTINPUT && $meta_field['sub_type'] == $this->meta_field_repository::NUMBER) {
                    $output['fields'][$meta_field['key']][] = Generator::input_field([
                        'type' => 'number',
                        'class' => 'wccbel-input-md',
                        'data-field' => 'from',
                        'data-field-type' => 'number',
                        'id' => $field_id . '-from',
                        'placeholder' => $meta_field['title'] . ' From ...',
                    ]);
                    $output['fields'][$meta_field['key']][] = Generator::input_field([
                        'type' => 'number',
                        'class' => 'wccbel-input-md',
                        'data-field' => 'to',
                        'data-field-type' => 'number',
                        'id' => $field_id . '-to',
                        'placeholder' => $meta_field['title'] . ' To ...',
                    ]);
                } elseif ($meta_field['main_type'] == $this->meta_field_repository::CHECKBOX) {
                    $output['fields'][$meta_field['key']][] = Generator::select_field([
                        'id' => $field_id,
                        'data-field' => 'value',
                    ], [
                        'yes' => esc_html__('Yes', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                        'no' => esc_html__('No', 'ithemeland-woocommerce-bulk-coupons-editing-lite'),
                    ], true);
                } elseif (in_array($meta_field['main_type'], [$this->meta_field_repository::SELECT, $this->meta_field_repository::ARRAY_TYPE]) && !empty($meta_field['key_value'])) {
                    $options = Meta_Field_Helper::key_value_field_to_array($meta_field['key_value']);
                    $output['fields'][$meta_field['key']][] = Generator::select_field([
                        'id' => $field_id,
                        'class' => 'wccbel-input-md',
                        'data-field' => 'value',
                    ], $options, true);
                } elseif (in_array($meta_field['main_type'], [$this->meta_field_repository::CALENDAR, $this->meta_field_repository::DATE])) {
                    $output['fields'][$meta_field['key']][] = Generator::input_field([
                        'type' => 'text',
                        'class' => 'wccbel-input-md wccbel-datepicker',
                        'data-field' => 'from',
                        'data-field-type' => 'date',
                        'id' => $field_id . '-from',
                        'data-to-id' => $field_id . '-to',
                        'placeholder' => $meta_field['title'] . ' From ...',
                    ]);
                    $output['fields'][$meta_field['key']][] = Generator::input_field([
                        'type' => 'text',
                        'class' => 'wccbel-input-md wccbel-datepicker',
                        'data-field' => 'to',
                        'data-field-type' => 'date',
                        'id' => $field_id . '-to',
                        'placeholder' => $meta_field['title'] . ' To ...',
                    ]);
                } elseif ($meta_field['main_type'] == $this->meta_field_repository::DATE_TIME) {
                    $output['fields'][$meta_field['key']][] = Generator::input_field([
                        'type' => 'text',
                        'class' => 'wccbel-input-md wccbel-datetimepicker',
                        'data-field' => 'from',
                        'data-field-type' => 'date',
                        'id' => $field_id . '-from',
                        'data-to-id' => $field_id . '-to',
                        'placeholder' => $meta_field['title'] . ' From ...',
                    ]);
                    $output['fields'][$meta_field['key']][] = Generator::input_field([
                        'type' => 'text',
                        'class' => 'wccbel-input-md wccbel-datetimepicker',
                        'data-field' => 'to',
                        'data-field-type' => 'date',
                        'id' => $field_id . '-to',
                        'placeholder' => $meta_field['title'] . ' To ...',
                    ]);
                } elseif ($meta_field['main_type'] == $this->meta_field_repository::TIME) {
                    $output['fields'][$meta_field['key']][] = Generator::input_field([
                        'type' => 'text',
                        'class' => 'wccbel-input-md wccbel-timepicker',
                        'data-field' => 'from',
                        'data-field-type' => 'time',
                        'id' => $field_id . '-from',
                        'data-to-id' => $field_id . '-to',
                        'placeholder' => $meta_field['title'] . ' From ...',
                    ]);
                    $output['fields'][$meta_field['key']][] = Generator::input_field([
                        'type' => 'text',
                        'class' => 'wccbel-input-md wccbel-timepicker',
                        'data-field' => 'to',
                        'data-field-type' => 'time',
                        'id' => $field_id . '-to',
                        'placeholder' => $meta_field['title'] . ' To ...',
                    ]);
                }
            }
        } else {
            $output['top_alert'] = [
                Generator::div_field_start([
                    'class' => 'wccbel-alert wccbel-alert-warning',
                ]),
                Generator::span_field(__('There is not any added Meta Fields, You can add new Meta Fields trough "Meta Fields" tab.', 'ithemeland-woocommerce-bulk-coupons-editing-lite')),
                Generator::div_field_end()
            ];
        }

        return $output;
    }
}
