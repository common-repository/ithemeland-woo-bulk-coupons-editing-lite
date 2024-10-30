<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<input type="hidden" id="wccbel-last-modal-opened" value="">

<div class='wccbel-thumbnail-hover-box'></div>
</div>

<?php
include_once WCCBEL_VIEWS_DIR . "bulk_edit/bulk_edit_form.php";
include_once WCCBEL_VIEWS_DIR . "bulk_edit/filter_form.php";
include_once WCCBEL_VIEWS_DIR . "bulk_edit/columns_modals/products.php";
include_once WCCBEL_VIEWS_DIR . "bulk_edit/columns_modals/categories.php";
include_once WCCBEL_VIEWS_DIR . "bulk_edit/columns_modals/used_in.php";
include_once WCCBEL_VIEWS_DIR . "bulk_edit/columns_modals/used_by.php";
include_once WCCBEL_VIEWS_DIR . "column_manager/main.php";
include_once WCCBEL_VIEWS_DIR . "column_manager/edit_preset.php";
include_once WCCBEL_VIEWS_DIR . "import_export/main.php";
include_once WCCBEL_VIEWS_DIR . "meta_field/main.php";
include_once WCCBEL_VIEWS_DIR . "settings/main.php";
include_once WCCBEL_VIEWS_DIR . "modals/text_editor.php";
include_once WCCBEL_VIEWS_DIR . "modals/numeric_calculator.php";
include_once WCCBEL_VIEWS_DIR . "modals/duplicate_item.php";
include_once WCCBEL_VIEWS_DIR . "modals/new_item.php";
include_once WCCBEL_VIEWS_DIR . "history/main.php";
include_once WCCBEL_VIEWS_DIR . "modals/filter_profiles.php";
include_once WCCBEL_VIEWS_DIR . "modals/column_profiles.php";
