<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<?php
if (!is_array($flush_message) || !isset($flush_message['message'])) {
    return false;
}
?>

<div class="wccbel-flush-message <?php echo (isset($flush_message['type'])) ? 'wccbel-flush-message-' . esc_attr($flush_message['type']) : 'wccbel-flush-message-default' ?>">
    <span><?php echo esc_html($flush_message['message']); ?></span>
</div>