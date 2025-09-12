<?php
if (!defined('ABSPATH')) exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('dokmeplus_settings_save')) {
    update_option('dokmeplus_license', sanitize_text_field($_POST['license_key']));
    update_option('dokmeplus_language', sanitize_text_field($_POST['language'])); // ذخیره زبان انتخابی
    echo '<div class="updated"><p>Settings saved.</p></div>';
}

$license = get_option('dokmeplus_license', '');
$current_lang = get_option('dokmeplus_language', 'en'); // پیش‌فرض انگلیسی
?>

<div class="wrap">
    <h1><?php echo esc_html( dokmeplus_t('settings_title') ); ?></h1>
    <form method="post">
        <?php wp_nonce_field('dokmeplus_settings_save'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><label for="license_key"><?php echo esc_html( dokmeplus_t('license') ); ?></label></th>
                <td>
                    <input type="text" id="license_key" name="license_key" value="<?php echo esc_attr($license); ?>" class="regular-text" />
                    <p class="description"><?php echo esc_html( dokmeplus_t('license_buy') ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="language"><?php echo esc_html( dokmeplus_t('language') ); ?></label></th>
                <td>
                    <select id="language" name="language">
                        <option value="en" <?php selected($current_lang, 'en'); ?>>English</option>
                        <option value="fa" <?php selected($current_lang, 'fa'); ?>>فارسی</option>
                    </select>
                    <p class="description">Select the plugin language.</p>
                </td>
            </tr>
        </table>

        <?php submit_button( dokmeplus_t('save_changes') ); ?>
    </form>
</div>
