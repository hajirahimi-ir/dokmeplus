<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// این فایل مستقل است و فرم افزودن/ویرایش + پیش‌نمایش زنده را نمایش می‌دهد.
// فرض شده نام فیلدها مطابق کد اصلی شماست: title,text,color,size,action,link,copy_text,send_text,call_number,sms_number,sms_message

// بارگذاری داده‌ها
$all = get_option('dokmeplus_buttons', []);
$edit_id = isset($_GET['edit_id']) ? sanitize_text_field($_GET['edit_id']) : '';
$edit = ($edit_id && isset($all[$edit_id])) ? $all[$edit_id] : [];

// پردازش ارسال فرم (اگر ارسال شد)
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

    // تولید شناسه امن (حفظ رفتار قبلی با بهبود)
    if ( ! empty($edit_id) ) {
        $id = $edit_id;
    } else {
        if ( function_exists('wp_generate_uuid4') ) {
            $id = wp_generate_uuid4();
        } else {
            $id = uniqid('dok_', true);
        }
    }

    $all[$id] = $data;
    update_option('dokmeplus_buttons', $all);

    // مقدار ویرایشی را بروزرسانی کن تا فرم پس از ذخیره مقدار جدید را نشان دهد
    $edit_id = $id;
    $edit = $all[$id];

    // نمایش اعلامیه موفقیت (یا می‌توان wp_redirect کرد)
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

        <!-- ===== پیش‌نمایش زنده ===== -->
        <h2><?php echo esc_html__('Preview', 'dokmeplus'); ?></h2>
        <div id="dokmeplus_preview_wrapper" style="padding:20px; background:#f9f9f9; border:1px solid #ddd; margin:15px 0;">
            <a id="dokmeplus_preview" href="#" target="_blank"
               style="display:inline-block; padding:10px 20px; border-radius:5px; text-decoration:none; background:<?php echo esc_attr($edit['color'] ?? '#0073aa'); ?>; color:#fff; font-size:<?php echo intval($edit['size'] ?? 16); ?>px;">
                <?php echo esc_html( $edit['text'] ?? dokmeplus_t('label_text') ); ?>
            </a>
        </div>

        <?php submit_button(); ?>
    </form>
</div>

<script>
(function(){
    // helper to safely get element value
    function val(selector){
        var el = document.querySelector(selector);
        return el ? el.value : '';
    }

    window.toggleFields = function(action){
        var rows = ['row_link','row_copy','row_send','row_call','row_sms'];
        rows.forEach(function(r){ var el = document.getElementById(r); if(el) el.style.display = 'none'; });
        var target = document.getElementById('row_' + action);
        if (target) target.style.display = 'table-row';
    };

    function updatePreview(){
        var btn = document.getElementById('dokmeplus_preview');
        if (!btn) return;

        var text = val('[name=text]');
        var color = val('[name=color]') || '#0073aa';
        var size = val('[name=size]') || '16';
        var action = val('[name=action]') || 'link';

        var link = val('[name=link]');
        var copy = val('[name=copy_text]');
        var send = val('[name=send_text]');
        var call = val('[name=call_number]');
        var sms_number = val('[name=sms_number]');
        var sms_message = val('[name=sms_message]');

        btn.textContent = text || '<?php echo esc_js( dokmeplus_t('label_text') ); ?>';
        btn.style.backgroundColor = color;
        btn.style.fontSize = parseInt(size,10) + 'px';

        if (action === 'link' && link) {
            btn.setAttribute('href', link);
            btn.removeAttribute('onclick');
            btn.setAttribute('target','_blank');
        } else if (action === 'call' && call) {
            btn.setAttribute('href', 'tel:' + call);
            btn.removeAttribute('onclick');
            btn.removeAttribute('target');
        } else if (action === 'sms' && sms_number) {
            var href = 'sms:' + sms_number;
            if (sms_message) href += '?body=' + encodeURIComponent(sms_message);
            btn.setAttribute('href', href);
            btn.removeAttribute('onclick');
            btn.removeAttribute('target');
        } else if (action === 'copy' && copy) {
            btn.setAttribute('href', '#');
            btn.removeAttribute('target');
            // اضافه کردن رفتار copy در پیش‌نمایش
            btn.onclick = function(e){ e.preventDefault(); try{ navigator.clipboard.writeText(copy); alert('<?php echo esc_js( dokmeplus_t('saved') ); ?>'); }catch(err){ alert('<?php echo esc_js( dokmeplus_t('license_error') ); ?>'); } };
        } else if (action === 'send' && send) {
            btn.setAttribute('href', '#');
            btn.removeAttribute('target');
            btn.onclick = function(e){ e.preventDefault(); if(navigator.share){ navigator.share({text: send}); } else { alert('<?php echo esc_js( dokmeplus_t('license_error') ); ?>'); } };
        } else {
            btn.setAttribute('href', '#');
            btn.removeAttribute('onclick');
            btn.setAttribute('target','_blank');
        }
    }

    document.addEventListener('DOMContentLoaded', function(){
        // set initial visibility for action-dependent fields
        var currentActionEl = document.querySelector('[name=action]');
        var currentAction = currentActionEl ? currentActionEl.value : 'link';
        toggleFields(currentAction);

        // attach listeners
        var selectors = '[name=text],[name=color],[name=size],[name=link],[name=copy_text],[name=send_text],[name=call_number],[name=sms_number],[name=sms_message],[name=action]';
        document.querySelectorAll(selectors).forEach(function(el){
            el.addEventListener('input', updatePreview);
            el.addEventListener('change', updatePreview);
        });

        // initial preview update
        updatePreview();
    });
})();
</script>
