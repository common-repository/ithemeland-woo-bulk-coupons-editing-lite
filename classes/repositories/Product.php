<?php

namespace wccbel\classes\repositories;

defined('ABSPATH') || exit(); // Exit if accessed directly

class Product
{
    public function get_products($args)
    {
        $posts = get_posts($args);
        return $posts;
    }

    public function get_categories_by_name($name)
    {
        return get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'name__like' => strtolower(sanitize_text_field($name))
        ]);
    }

    public function get_categories_by_id($category_ids)
    {
        $categories = [];
        if (!empty($category_ids) && is_array($category_ids)) {
            $categories = get_terms([
                'taxonomy' => 'product_cat',
                'include' => $category_ids,
                'hide_empty' => false,
                'fields' => 'id=>name'
            ]);
        }

        return $categories;
    }
}
