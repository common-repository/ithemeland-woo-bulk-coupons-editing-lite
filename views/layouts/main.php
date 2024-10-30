<?php

use wccbel\classes\helpers\Sanitizer;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<?php include WCCBEL_VIEWS_DIR . "layouts/header.php"; ?>

<div id="wccbel-body">
    <div class="wccbel-tabs wccbel-tabs-main">
        <div class="wccbel-tabs-navigation">
            <nav class="wccbel-tabs-navbar">
                <ul class="wccbel-tabs-list" data-type="url" data-content-id="wccbel-main-tabs-contents">
                    <?php echo wp_kses(apply_filters('wccbel_top_navigation_buttons', ''), Sanitizer::allowed_html_tags()); ?>
                </ul>
            </nav>

            <div class="wccbel-top-nav-filters-per-page">
                <select id="wccbel-quick-per-page" title="The number of products per page">
                    <?php
                    if (!empty($count_per_page_items)) :
                        foreach ($count_per_page_items as $count_per_page_item) :
                    ?>
                            <option value="<?php echo intval($count_per_page_item); ?>" <?php if (isset($current_settings['count_per_page']) && $current_settings['count_per_page'] == intval($count_per_page_item)) : ?> selected <?php endif; ?>>
                                <?php echo esc_html($count_per_page_item); ?>
                            </option>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="wccbel-items-pagination"></div>
        </div>

        <div class="wccbel-tabs-contents" id="wccbel-main-tabs-contents">
            <div class="wccbel-wrap">
                <div class="wccbel-tab-middle-content">
                    <div class="wccbel-table" id="wccbel-items-table">
                        <p style="width: 100%; text-align: center; padding: 10px 0;"><img src="<?php echo esc_url(WCCBEL_IMAGES_URL . 'loading.gif'); ?>" width="30" height="30"></p>
                    </div>
                    <div class="wccbel-items-count"></div>
                </div>
            </div>
        </div>

        <div class="wccbel-created-by">
            <a href="https://ithemelandco.com" target="_blank">Created by iThemelandCo</a>
        </div>
    </div>
</div>

<?php include_once  WCCBEL_VIEWS_DIR . "layouts/footer.php"; ?>