<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$all = get_option('dokmeplus_buttons', []);
$edit_id = isset($_GET['edit_id']) ? sanitize_text_field($_GET['edit_id']) : '';
$edit = ($edit_id && isset($all[$edit_id])) ? $all[$edit_id] : [];

// پردازش ارسال فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && current_user_can('manage_options')) {

    if ( ! isset($_POST['_dokmeplus_nonce']) || ! wp_verify_nonce( wp_unslash($_POST['_dokmeplus_nonce']), 'dokmeplus_save' ) ) {
        wp_die('Invalid nonce');
    }

    $data = [
        'title'        => sanitize_text_field($_POST['title'] ?? ''),
        'text'         => sanitize_text_field($_POST['text'] ?? ''),
        'color'        => sanitize_text_field($_POST['color'] ?? '#0073aa'),
        'size'         => intval($_POST['size'] ?? 16),
        'action'       => sanitize_text_field($_POST['action'] ?? 'link'),
        'link'         => esc_url_raw($_POST['link'] ?? ''),
        'copy_text'    => sanitize_text_field($_POST['copy_text'] ?? ''),
        'send_text'    => sanitize_text_field($_POST['send_text'] ?? ''),
        'call_number'  => sanitize_text_field($_POST['call_number'] ?? ''),
        'sms_number'   => sanitize_text_field($_POST['sms_number'] ?? ''),
        'sms_message'  => sanitize_textarea_field($_POST['sms_message'] ?? ''),
    ];

    if ( ! empty($edit_id) ) {
        $id = $edit_id;
    } else {
        $id = function_exists('wp_generate_uuid4') ? wp_generate_uuid4() : uniqid('dok_', true);
    }

    $all[$id] = $data;
    update_option('dokmeplus_buttons', $all);

    $edit_id = $id;
    $edit = $all[$id];

    echo '<div class="updated"><p>' . esc_html( dokmeplus_t('saved') ?? 'Saved.' ) . '</p></div>';
}
?>

<div class="wrap">
    <h1><?php echo esc_html( $edit_id ? dokmeplus_t('edit_button') : dokmeplus_t('add_button') ); ?></h1>

    <form method="post" novalidate>
        <?php wp_nonce_field('dokmeplus_save', '_dokmeplus_nonce'); ?>

        <table class="form-table">
            <tr>
                <th><?php echo esc_html( dokmeplus_t('label_title') ); ?></th>
                <td><input name="title" value="<?php echo esc_attr($edit['title'] ?? ''); ?>" class="regular-text" required></td>
            </tr>

            <tr>
                <th><?php echo esc_html( dokmeplus_t('label_text') ); ?></th>
                <td><input name="text" value="<?php echo esc_attr($edit['text'] ?? ''); ?>" class="regular-text"></td>
            </tr>

            <tr>
                <th><?php echo esc_html( dokmeplus_t('label_color') ); ?></th>
                <td><input type="color" name="color" value="<?php echo esc_attr($edit['color'] ?? '#0073aa'); ?>"></td>
            </tr>

            <tr>
                <th><?php echo esc_html( dokmeplus_t('label_size') ); ?> (px)</th>
                <td><input type="number" name="size" value="<?php echo esc_attr($edit['size'] ?? 16); ?>"></td>
            </tr>

            <tr>
                <th><?php echo esc_html( dokmeplus_t('label_action') ); ?></th>
                <td>
                    <select name="action" id="dok_action" onchange="toggleFields(this.value)">
                        <option value="link" <?php selected($edit['action'] ?? '', 'link'); ?>><?php echo esc_html( dokmeplus_t('action_link') ); ?></option>
                        <option value="copy" <?php selected($edit['action'] ?? '', 'copy'); ?>><?php echo esc_html( dokmeplus_t('action_copy') ); ?></option>
                        <option value="send" <?php selected($edit['action'] ?? '', 'send'); ?>><?php echo esc_html( dokmeplus_t('action_send') ); ?></option>
                        <option value="call" <?php selected($edit['action'] ?? '', 'call'); ?>><?php echo esc_html( dokmeplus_t('action_call') ); ?></option>
                        <option value="sms" <?php selected($edit['action'] ?? '', 'sms'); ?>><?php echo esc_html( dokmeplus_t('action_sms') ); ?></option>
                    </select>
                </td>
            </tr>

            <tr id="row_link" style="display:none;">
                <th><?php echo esc_html( dokmeplus_t('label_link') ); ?></th>
                <td><input name="link" value="<?php echo esc_attr($edit['link'] ?? ''); ?>" class="regular-text"></td>
            </tr>

            <tr id="row_copy" style="display:none;">
                <th><?php echo esc_html( dokmeplus_t('label_copy_text') ); ?></th>
                <td><input name="copy_text" value="<?php echo esc_attr($edit['copy_text'] ?? ''); ?>" class="regular-text"></td>
            </tr>

            <tr id="row_send" style="display:none;">
                <th><?php echo esc_html( dokmeplus_t('label_send_text') ); ?></th>
                <td><input name="send_text" value="<?php echo esc_attr($edit['send_text'] ?? ''); ?>" class="regular-text"></td>
            </tr>

            <tr id="row_call" style="display:none;">
                <th><?php echo esc_html( dokmeplus_t('label_call_number') ); ?></th>
                <td><input name="call_number" value="<?php echo esc_attr($edit['call_number'] ?? ''); ?>" class="regular-text"></td>
            </tr>

            <tr id="row_sms" style="display:none;">
                <th><?php echo esc_html( dokmeplus_t('label_sms_number') ); ?></th>
                <td>
                    <input name="sms_number" value="<?php echo esc_attr($edit['sms_number'] ?? ''); ?>" class="regular-text"><br>
                    <textarea name="sms_message" rows="3" class="large-text"><?php echo esc_textarea($edit['sms_message'] ?? ''); ?></textarea>
                </td>
            </tr>
        </table>

        <div class="form-actions">
            <?php submit_button('ذخیره تغییرات'); ?>

            <?php if ( !empty($edit_id) ) : ?>
                <a href="<?php echo esc_url( plugin_dir_url(__FILE__) . 'live.php?id=' . urlencode($edit_id) ); ?>" 
                   class="button-secondary" target="_blank" style="margin-left:10px;">
                   پیش‌نمایش دکمه
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
(function(){
    window.toggleFields = function(action){
        var rows = ['row_link','row_copy','row_send','row_call','row_sms'];
        rows.forEach(function(r){ var el = document.getElementById(r); if(el) el.style.display = 'none'; });
        var target = document.getElementById('row_' + action);
        if (target) target.style.display = 'table-row';
    };

    document.addEventListener('DOMContentLoaded', function(){
        var currentActionEl = document.querySelector('[name=action]');
        var currentAction = currentActionEl ? currentActionEl.value : 'link';
        toggleFields(currentAction);
    });
})();
</script>
