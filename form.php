<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('dokmeplus_save', 'dokmeplus_nonce'); ?>

        <table class="form-table">
            <tr>
                <th><label for="title"><?php echo dokmeplus_t('button_title'); ?></label></th>
                <td><input type="text" name="title" id="title" class="regular-text"></td>
            </tr>

            <tr>
                <th><label for="text"><?php echo dokmeplus_t('button_text'); ?></label></th>
                <td><input type="text" name="text" id="text" class="regular-text"></td>
            </tr>

            <tr>
                <th><label for="color"><?php echo dokmeplus_t('color'); ?></label></th>
                <td><input type="color" name="color" id="color" value="#0073aa"></td>
            </tr>

            <tr>
                <th><label for="size"><?php echo dokmeplus_t('font_size'); ?></label></th>
                <td><input type="number" name="size" id="size" value="16"> px</td>
            </tr>

            <tr>
                <th><label for="action"><?php echo dokmeplus_t('action'); ?></label></th>
                <td>
                    <select name="action" id="action" onchange="toggleFields(this.value)">
                        <option value="link"><?php echo dokmeplus_t('link'); ?></option>
                        <option value="copy"><?php echo dokmeplus_t('copy'); ?></option>
                        <option value="send"><?php echo dokmeplus_t('send'); ?></option>
                        <option value="call"><?php echo dokmeplus_t('call'); ?></option>
                        <option value="sms"><?php echo dokmeplus_t('sms'); ?></option>
                    </select>
                </td>
            </tr>

            <tr class="field-link" style="display:none;">
                <th><label for="link"><?php echo dokmeplus_t('link'); ?></label></th>
                <td><input type="url" name="link" id="link" class="regular-text"></td>
            </tr>

            <tr class="field-copy" style="display:none;">
                <th><label for="copy_text"><?php echo dokmeplus_t('copy_text'); ?></label></th>
                <td><input type="text" name="copy_text" id="copy_text" class="regular-text"></td>
            </tr>

            <tr class="field-send" style="display:none;">
                <th><label for="send_text"><?php echo dokmeplus_t('send_text'); ?></label></th>
                <td><input type="text" name="send_text" id="send_text" class="regular-text"></td>
            </tr>

            <tr class="field-call" style="display:none;">
                <th><label for="call_number"><?php echo dokmeplus_t('call_number'); ?></label></th>
                <td><input type="text" name="call_number" id="call_number" class="regular-text"></td>
            </tr>

            <tr class="field-sms" style="display:none;">
                <th><label for="sms_number"><?php echo dokmeplus_t('sms_number'); ?></label></th>
                <td><input type="text" name="sms_number" id="sms_number" class="regular-text"></td>
            </tr>

            <tr class="field-sms" style="display:none;">
                <th><label for="sms_message"><?php echo dokmeplus_t('sms_message'); ?></label></th>
                <td><input type="text" name="sms_message" id="sms_message" class="regular-text"></td>
            </tr>
        </table>

        <!-- 🔹 پیش‌نمایش زنده -->
        <h2><?php echo dokmeplus_t('preview'); ?></h2>
        <div id="dokmeplus_preview_wrapper" style="padding:20px; background:#f9f9f9; border:1px solid #ddd; margin:15px 0;">
            <a id="dokmeplus_preview" href="#" target="_blank"
               style="display:inline-block; padding:10px 20px; border-radius:5px; text-decoration:none; background:#0073aa; color:#fff; font-size:16px;">
                <?php echo dokmeplus_t('button_text'); ?>
            </a>
        </div>

        <?php submit_button(dokmeplus_t('save_changes')); ?>
    </form>
</div>

<script>
function toggleFields(action) {
    document.querySelectorAll('.field-link,.field-copy,.field-send,.field-call,.field-sms')
        .forEach(el => el.style.display = 'none');
    document.querySelectorAll('.field-' + action).forEach(el => el.style.display = 'table-row');
}

function updatePreview() {
    var btn = document.getElementById('dokmeplus_preview');
    if (!btn) return;

    var text = document.querySelector('[name=text]').value;
    var color = document.querySelector('[name=color]').value;
    var size = document.querySelector('[name=size]').value + 'px';
    var action = document.querySelector('[name=action]').value;

    var link = document.querySelector('[name=link]').value;
    var copy = document.querySelector('[name=copy_text]').value;
    var send = document.querySelector('[name=send_text]').value;
    var call = document.querySelector('[name=call_number]').value;
    var sms_number = document.querySelector('[name=sms_number]').value;
    var sms_message = document.querySelector('[name=sms_message]').value;

    btn.textContent = text || '<?php echo dokmeplus_t('button_text'); ?>';
    btn.style.backgroundColor = color;
    btn.style.fontSize = size;

    if (action === 'link' && link) {
        btn.href = link;
    } else if (action === 'call' && call) {
        btn.href = 'tel:' + call;
    } else if (action === 'sms' && sms_number) {
        btn.href = 'sms:' + sms_number + (sms_message ? '?body=' + encodeURIComponent(sms_message) : '');
    } else if (action === 'copy' && copy) {
        btn.href = '#';
    } else {
        btn.href = '#';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleFields(document.querySelector('[name=action]').value);
    updatePreview();

    document.querySelectorAll('[name=text],[name=color],[name=size],[name=link],[name=copy_text],[name=send_text],[name=call_number],[name=sms_number],[name=sms_message],[name=action]')
        .forEach(function(el){
            el.addEventListener('input', updatePreview);
            el.addEventListener('change', updatePreview);
        });
});
</script>
