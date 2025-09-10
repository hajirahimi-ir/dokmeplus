<?php
if (!defined('ABSPATH')) exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('dokmeplus_settings_save')) {
    update_option('dokmeplus_license', sanitize_text_field($_POST['license_key']));
    echo '<div class="updated"><p>Settings saved.</p></div>';
}

$license = get_option('dokmeplus_license', '');
?>

<div class="wrap">
    <h1>Dokme Plus Settings</h1>
    <form method="post">
        <?php wp_nonce_field('dokmeplus_settings_save'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><label for="license_key">License Key</label></th>
                <td>
                    <input type="text" id="license_key" name="license_key" value="<?php echo esc_attr($license); ?>" class="regular-text" />
                    <p class="description">Enter your valid license key to unlock full features.</p>
                    <p class="description">
                        Need a license? 
                        <a href="https://hamtamehr.ir/shop/kf-j59n/lo55hg22" target="_blank">
                            Purchase a license here
                        </a>.
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button('Save Settings'); ?>
    </form>
</div>
