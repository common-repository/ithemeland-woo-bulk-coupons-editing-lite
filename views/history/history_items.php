<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!empty($histories)) :
    $i = 1;
    foreach ($histories as $history) :
        $user_data = get_userdata(intval($history->user_id));
?>
        <tr>
            <td><?php echo intval($i); ?></td>
            <td>
                <span class="wccbel-history-name wccbel-fw600">
                    <?php
                    switch ($history->operation_type) {
                        case 'inline':
                            $item = (wccbel\classes\repositories\History::get_instance())->get_history_items($history->id);
                            echo (!empty($item[0]->post_title)) ? esc_html($item[0]->post_title) : 'Inline Operation';
                            break;
                        case 'bulk':
                            echo 'Bulk Operation';
                            break;
                    }
                    ?>
                </span>
                <?php
                $fields = '';
                if (is_array(unserialize($history->fields)) && !empty(unserialize($history->fields))) {
                    foreach (unserialize($history->fields) as $field) {
                        if (is_array($field)) {
                            foreach ($field as $field_item) {
                                $field_arr = explode('_-_', $field_item);
                                if (!empty($field_arr[0]) && !empty($field_arr[1])) {
                                    $field_item = esc_html($field_arr[1]);
                                }

                                $fields .= "[" . esc_html($field_item) . "]";
                            }
                        } else {
                            $field_arr = explode('_-_', $field);
                            if (!empty($field_arr[0]) && !empty($field_arr[1])) {
                                $field = esc_html($field_arr[1]);
                            }

                            $fields .= "[" . esc_html($field) . "]";
                        }
                    }
                }
                ?>
                <span class="wccbel-history-text-sm"><?php echo esc_html($fields); ?></span>
            </td>
            <td class="wccbel-fw600"><?php echo (!empty($user_data)) ? esc_html($user_data->user_login) : ''; ?></td>
            <td class="wccbel-fw600"><?php echo esc_html(gmdate('Y / m / d', strtotime($history->operation_date))); ?></td>
            <td>
                <button type="button" disabled="disabled" class="wccbel-button wccbel-button-blue">
                    <i class="wccbel-icon-rotate-cw"></i>
                    <?php esc_html_e('Revert', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                </button>
                <button type="button" disabled="disabled" class="wccbel-button wccbel-button-red">
                    <i class="wccbel-icon-trash-2"></i>
                    <?php esc_html_e('Delete', 'ithemeland-woocommerce-bulk-coupons-editing-lite'); ?>
                </button>
            </td>
        </tr>
<?php
        $i++;
    endforeach;
endif;
