<?php
global $wpdb;

$table_name = $wpdb->prefix . 'dokmeplus';

$id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$button = null;
if ($id) {
    $button = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = array(
        'title'   => sanitize_text_field($_POST['title']),
        'text'    => sanitize_text_field($_POST['text']),
        'color'   => sanitize_hex_color($_POST['color']),
        'size'    => intval($_POST['size']),
        'action'  => sanitize_text_field($_POST['action']),
        'link'    => esc_url_raw($_POST['link']),
        'phone'   => sanitize_text_field($_POST['phone']),
        'sms'     => sanitize_textarea_field($_POST['sms']),
    );

    if ($id) {
        $wpdb->update($table_name, $data, array('id' => $id));
    } else {
        $wpdb->insert($table_name, $data);
    }

    echo '<div class="updated"><p>' . dokmeplus_t('saved_successfully') . '</p></div>';
}
?>

<div class="wrap">
    <h1><?php echo $id ? dokmeplus_t('edit_button') : dokmeplus_t('add_button'); ?></h1>
    <form method="post">
        <table class="form-table">
            <tr>
                <th><label for="title"><?php echo dokmeplus_t('title'); ?></label></th>
                <td><input name="title" type="text" value="<?php echo esc_attr($button->title ?? ''); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="text"><?php echo dokmeplus_t('button_text'); ?></label></th>
                <td><input name="text" type="text" value="<?php echo esc_attr($button->text ?? ''); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="color"><?php echo dokmeplus_t('color'); ?></label></th>
                <td><input name="color" type="color" value="<?php echo esc_attr($button->color ?? '#000000'); ?>"></td>
            </tr>
            <tr>
                <th><label for="size"><?php echo dokmeplus_t('font_size'); ?> (px)</label></th>
                <td><input name="size" type="number" value="<?php echo esc_attr($button->size ?? 16); ?>"></td>
            </tr>
            <tr>
                <th><label for="action"><?php echo dokmeplus_t('action'); ?></label></th>
                <td>
                    <select name="action">
                        <option value="link" <?php selected($button->action ?? '', 'link'); ?>><?php echo dokmeplus_t('link'); ?></option>
                        <option value="call" <?php selected($button->action ?? '', 'call'); ?>><?php echo dokmeplus_t('call'); ?></option>
                        <option value="sms" <?php selected($button->action ?? '', 'sms'); ?>><?php echo dokmeplus_t('sms'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="link"><?php echo dokmeplus_t('link'); ?></label></th>
                <td><input name="link" type="text" value="<?php echo esc_attr($button->link ?? ''); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="phone"><?php echo dokmeplus_t('phone'); ?></label></th>
                <td><input name="phone" type="text" value="<?php echo esc_attr($button->phone ?? ''); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="sms"><?php echo dokmeplus_t('sms_text'); ?></label></th>
                <td><textarea name="sms" class="large-text" rows="3"><?php echo esc_textarea($button->sms ?? ''); ?></textarea></td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary"><?php echo dokmeplus_t('save'); ?></button>
        </p>
    </form>

    <!-- پیش‌نمایش زنده دکمه -->
    <h2><?php echo dokmeplus_t('preview'); ?></h2>
    <div id="dokmeplus_preview_wrapper" style="padding:20px; background:#f9f9f9; border:1px solid #ddd; margin-top:15px;">
        <a id="dokmeplus_preview" href="#" target="_blank" 
           style="display:inline-block; padding:10px 20px; border-radius:5px; text-decoration:none; background:#000; color:#fff;">
            <?php echo dokmeplus_t('button_text'); ?>
        </a>
    </div>
</div>

<script>
function updatePreview() {
    var btn = document.getElementById('dokmeplus_preview');
    var text = document.querySelector('[name=text]').value;
    var color = document.querySelector('[name=color]').value;
    var size = document.querySelector('[name=size]').value + 'px';
    var link = document.querySelector('[name=link]').value;

    btn.textContent = text || '<?php echo dokmeplus_t('button_text'); ?>';
    btn.style.backgroundColor = color;
    btn.style.fontSize = size;
    btn.href = link ? link : '#';
}

// وقتی ورودی‌ها تغییر کنن، پیش‌نمایش آپدیت بشه
document.querySelectorAll('[name=text],[name=color],[name=size],[name=link]')
    .forEach(function(el){
        el.addEventListener('input', updatePreview);
    });

// بارگذاری اولیه
document.addEventListener('DOMContentLoaded', updatePreview);
</script>
