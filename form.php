<div class="wrap">
    <h1><?php echo isset($_GET['edit_id']) ? dokmeplus_t('edit') : dokmeplus_t('add_button'); ?></h1>
    <?php
    $all = get_option('dokmeplus_buttons', []);
    $edit_id = $_GET['edit_id'] ?? null;
    $edit = $edit_id && isset($all[$edit_id]) ? $all[$edit_id] : [];
    if ($_POST) {
        $data = [
            'title' => sanitize_text_field($_POST['title']),
            'text' => sanitize_text_field($_POST['text']),
            'color' => sanitize_text_field($_POST['color']),
            'size' => intval($_POST['size']),
            'action' => sanitize_text_field($_POST['action']),
            'link' => esc_url_raw($_POST['link'] ?? ''),
            'copy_text' => sanitize_text_field($_POST['copy_text'] ?? ''),
            'send_text' => sanitize_text_field($_POST['send_text'] ?? ''),
            'call_number' => sanitize_text_field($_POST['call_number'] ?? ''),
            'sms_number' => sanitize_text_field($_POST['sms_number'] ?? ''),
            'sms_message' => sanitize_textarea_field($_POST['sms_message'] ?? ''),
        ];
        $id = $edit_id ?: time();
        $all[$id] = $data;
        update_option('dokmeplus_buttons', $all);
        echo '<div class="updated"><p>' . dokmeplus_t('save') . '.</p></div>';
        $edit = $data;
    }
    ?>
    <form method="post">
        <table class="form-table">
            <tr><th><?php echo dokmeplus_t('button_title'); ?></th><td><input name="title" value="<?php echo esc_attr($edit['title'] ?? '') ?>" required></td></tr>
            <tr><th><?php echo dokmeplus_t('button_text'); ?></th><td><input name="text" value="<?php echo esc_attr($edit['text'] ?? '') ?>"></td></tr>
            <tr><th><?php echo dokmeplus_t('color'); ?></th><td><input type="color" name="color" value="<?php echo esc_attr($edit['color'] ?? '#0073aa') ?>"></td></tr>
            <tr><th><?php echo dokmeplus_t('font_size'); ?></th><td><input type="number" name="size" value="<?php echo esc_attr($edit['size'] ?? 16) ?>"></td></tr>
            <tr><th><?php echo dokmeplus_t('action'); ?></th><td>
                <select name="action" onchange="toggleFields(this.value)">
                    <option value="link" <?php selected($edit['action'] ?? '', 'link') ?>><?php echo dokmeplus_t('link'); ?></option>
                    <option value="copy" <?php selected($edit['action'] ?? '', 'copy') ?>><?php echo dokmeplus_t('copy'); ?></option>
                    <option value="send" <?php selected($edit['action'] ?? '', 'send') ?>><?php echo dokmeplus_t('send'); ?></option>
                    <option value="call" <?php selected($edit['action'] ?? '', 'call') ?>><?php echo dokmeplus_t('call'); ?></option>
                    <option value="sms" <?php selected($edit['action'] ?? '', 'sms') ?>><?php echo dokmeplus_t('sms'); ?></option>
                </select></td></tr>
            <tr id="row_link"><th><?php echo dokmeplus_t('link_field'); ?></th><td><input name="link" value="<?php echo esc_attr($edit['link'] ?? '') ?>"></td></tr>
            <tr id="row_copy"><th><?php echo dokmeplus_t('copy_text_field'); ?></th><td><input name="copy_text" value="<?php echo esc_attr($edit['copy_text'] ?? '') ?>"></td></tr>
            <tr id="row_send"><th><?php echo dokmeplus_t('send_text_field'); ?></th><td><input name="send_text" value="<?php echo esc_attr($edit['send_text'] ?? '') ?>"></td></tr>
            <tr id="row_call"><th><?php echo dokmeplus_t('call_number_field'); ?></th><td><input name="call_number" value="<?php echo esc_attr($edit['call_number'] ?? '') ?>"></td></tr>
            <tr id="row_sms">
                <th><?php echo dokmeplus_t('sms_number_field'); ?></th>
                <td>
                    <input name="sms_number" placeholder="<?php echo dokmeplus_t('sms_number_field'); ?>" value="<?php echo esc_attr($edit['sms_number'] ?? '') ?>"><br>
                    <textarea name="sms_message" placeholder="<?php echo dokmeplus_t('sms_message_field'); ?>"><?php echo esc_textarea($edit['sms_message'] ?? '') ?></textarea>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>

<script>
function toggleFields(val) {
    document.getElementById('row_link').style.display = val === 'link' ? '' : 'none';
    document.getElementById('row_copy').style.display = val === 'copy' ? '' : 'none';
    document.getElementById('row_send').style.display = val === 'send' ? '' : 'none';
    document.getElementById('row_call').style.display = val === 'call' ? '' : 'none';
    document.getElementById('row_sms').style.display = val === 'sms' ? '' : 'none';
}
document.addEventListener('DOMContentLoaded', function() {
    toggleFields(document.querySelector('[name=action]').value);
});
</script>
