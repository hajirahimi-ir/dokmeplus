<div class="wrap">
    <h1><?php echo isset($_GET['edit_id']) ? esc_html(dokmeplus_t('edit_button')) : esc_html(dokmeplus_t('add_button')); ?></h1>
    <?php
    $all = get_option('dokmeplus_buttons', []);
    $edit_id = isset($_GET['edit_id']) ? sanitize_text_field($_GET['edit_id']) : null;
    $edit = $edit_id && isset($all[$edit_id]) ? $all[$edit_id] : [];

    // نمایش پیام ذخیره (در صورتی که از redirect استفاده نشده باشد)
    if ( isset($_GET['updated']) ) {
        echo '<div class="notice notice-success"><p>' . esc_html(dokmeplus_t('saved')) . '</p></div>';
    }
    ?>
    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
        <?php wp_nonce_field('dokmeplus_save', '_dokmeplus_nonce'); ?>
        <input type="hidden" name="action" value="dokmeplus_save_button">
        <input type="hidden" name="edit_id" value="<?php echo esc_attr($edit_id ?? ''); ?>">
        <table class="form-table">
            <tr><th><?php echo esc_html(dokmeplus_t('label_title')); ?></th><td><input name="title" value="<?php echo esc_attr($edit['title'] ?? '') ?>" required></td></tr>
            <tr><th><?php echo esc_html(dokmeplus_t('label_text')); ?></th><td><input name="text" value="<?php echo esc_attr($edit['text'] ?? '') ?>"></td></tr>
            <tr><th><?php echo esc_html(dokmeplus_t('label_color')); ?></th><td><input type="color" name="color" value="<?php echo esc_attr($edit['color'] ?? '#0073aa') ?>"></td></tr>
            <tr><th><?php echo esc_html(dokmeplus_t('label_size')); ?></th><td><input type="number" name="size" value="<?php echo esc_attr($edit['size'] ?? 16) ?>"></td></tr>
            <tr><th><?php echo esc_html(dokmeplus_t('label_action')); ?></th><td>
                <select name="action" onchange="toggleFields(this.value)">
                    <option value="link" <?php selected($edit['action'] ?? '', 'link') ?>><?php echo esc_html(dokmeplus_t('action_link')); ?></option>
                    <option value="copy" <?php selected($edit['action'] ?? '', 'copy') ?>><?php echo esc_html(dokmeplus_t('action_copy')); ?></option>
                    <option value="send" <?php selected($edit['action'] ?? '', 'send') ?>><?php echo esc_html(dokmeplus_t('action_send')); ?></option>
                    <option value="call" <?php selected($edit['action'] ?? '', 'call') ?>><?php echo esc_html(dokmeplus_t('action_call')); ?></option>
                    <option value="sms" <?php selected($edit['action'] ?? '', 'sms') ?>><?php echo esc_html(dokmeplus_t('action_sms')); ?></option>
                </select></td></tr>
            <tr id="row_link"><th><?php echo esc_html(dokmeplus_t('label_link')); ?></th><td><input name="link" value="<?php echo esc_attr($edit['link'] ?? '') ?>"></td></tr>
            <tr id="row_copy"><th><?php echo esc_html(dokmeplus_t('label_copy_text')); ?></th><td><input name="copy_text" value="<?php echo esc_attr($edit['copy_text'] ?? '') ?>"></td></tr>
            <tr id="row_send"><th><?php echo esc_html(dokmeplus_t('label_send_text')); ?></th><td><input name="send_text" value="<?php echo esc_attr($edit['send_text'] ?? '') ?>"></td></tr>
            <tr id="row_call"><th><?php echo esc_html(dokmeplus_t('label_call_number')); ?></th><td><input name="call_number" value="<?php echo esc_attr($edit['call_number'] ?? '') ?>"></td></tr>
            <tr id="row_sms">
                <th><?php echo esc_html(dokmeplus_t('label_sms_number')); ?></th>
                <td>
                    <input name="sms_number" placeholder="<?php echo esc_attr(dokmeplus_t('label_sms_number')); ?>" value="<?php echo esc_attr($edit['sms_number'] ?? '') ?>"><br>
                    <textarea name="sms_message" placeholder="<?php echo esc_attr(dokmeplus_t('label_sms_message')); ?>"><?php echo esc_textarea($edit['sms_message'] ?? '') ?></textarea>
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
    var sel = document.querySelector('[name=action]');
    if (sel) toggleFields(sel.value);
});
</script>
