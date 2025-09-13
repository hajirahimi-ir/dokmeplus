<?php
if (!defined('ABSPATH')) exit;

// server-side fallback (in case JS غیرفعال باشد)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('dokmeplus_settings_save')) {
    update_option('dokmeplus_license', sanitize_text_field($_POST['license_key']));
    update_option('dokmeplus_language', sanitize_text_field($_POST['language']));
    echo '<div class="updated"><p>' . esc_html( dokmeplus_t('saved') ) . '</p></div>';
}

$license = get_option('dokmeplus_license', '');
$current_lang = get_option('dokmeplus_language', 'en');
?>

<div class="wrap">
    <!-- data-dokmeplus-key attributes allow JS to update these text nodes -->
    <h1 data-dokmeplus-key="settings_title"><?php echo esc_html( dokmeplus_t('settings_title') ); ?></h1>

    <form id="dokmeplus_settings_form" method="post">
        <?php wp_nonce_field('dokmeplus_settings_save'); ?>

        <table class="form-table">
            <tr>
                <th scope="row"><label for="license_key" data-dokmeplus-key="license"><?php echo esc_html( dokmeplus_t('license') ); ?></label></th>
                <td>
                    <input type="text" id="license_key" name="license_key" value="<?php echo esc_attr($license); ?>" class="regular-text" />
                    <p class="description">
                    <a href="https://hamtamehr.ir/shop/kf-j59n/lo55hg22" target="_blank" data-dokmeplus-key="license_buy_link">
                    <?php echo esc_html( dokmeplus_t('license_buy_link') ); ?>
    </a>
</p>

                </td>
            </tr>

            <tr>
                <th scope="row"><label for="language" data-dokmeplus-key="language"><?php echo esc_html( dokmeplus_t('language') ); ?></label></th>
                <td>
                    <select id="language" name="language">
                        <option value="en" <?php selected($current_lang, 'en'); ?>>English</option>
                        <option value="fa" <?php selected($current_lang, 'fa'); ?>>فارسی</option>
                    </select>
                    <p class="description" data-dokmeplus-key="language_desc">
    <?php echo esc_html( dokmeplus_t('language_desc') ); ?>
                    </p>

                </td>
            </tr>
        </table>

        <?php submit_button( dokmeplus_t('save_changes') ); ?>
    </form>
</div>

<script>
jQuery(function($){
    $('#dokmeplus_settings_form').on('submit', function(e){
        e.preventDefault();

        var $form = $(this);
        var formData = $form.serializeArray();
        formData.push({ name: 'action', value: 'dokmeplus_save_settings' });

        $.post(ajaxurl, $.param(formData), function(resp){
            if ( resp && resp.success && resp.data && resp.data.translations ) {
                var t = resp.data.translations;

                // update all elements on the page that have data-dokmeplus-key
                $('[data-dokmeplus-key]').each(function(){
                    var key = $(this).data('dokmeplus-key');
                    if ( typeof key !== 'undefined' && typeof t[key] !== 'undefined' ) {
                        if ( $(this).is('input,textarea,select') ) {
                            $(this).val(t[key]);
                        } else {
                            $(this).text(t[key]);
                        }
                    }
                });

                // update the submit button text
                var saveText = t['save_changes'] || 'Save Changes';
                $form.find('button[type="submit"], input[type="submit"]').each(function(){
                    if ($(this).is('input')) $(this).val(saveText); else $(this).text(saveText);
                });

                // update DokmePlus menu texts (top-level + submenu)
                var topSelector = $('#toplevel_page_dokmeplus .wp-menu-name');
                if ( topSelector.length && t['menu_main'] ) topSelector.text(t['menu_main']);

                $('#toplevel_page_dokmeplus .wp-submenu a').each(function(){
                    var href = $(this).attr('href') || '';
                    if ( href.indexOf('page=dokmeplus_add') !== -1 && t['menu_add'] ) $(this).text(t['menu_add']);
                    else if ( href.indexOf('page=dokmeplus_settings') !== -1 && t['menu_settings'] ) $(this).text(t['menu_settings']);
                    else if ( href.indexOf('page=dokmeplus_about') !== -1 && t['menu_about'] ) $(this).text(t['menu_about']);
                    else {
                        // fallback: the list page (main listing)
                        if ( href.indexOf('page=dokmeplus') !== -1 && t['menu_list'] ) {
                            // avoid overwriting settings/add/about already handled
                            var p = new URL(href, window.location.origin);
                            if ( p.searchParams.get('page') === 'dokmeplus' ) $(this).text(t['menu_list']);
                        }
                    }
                });

                // success notice (temporary)
                $('.notice.settings-dokmeplus').remove();
                $('<div class="notice notice-success settings-dokmeplus"><p>' + (t['saved'] || 'Settings saved.') + '</p></div>').insertBefore('.wrap').delay(2500).fadeOut(400);

            } else {
                var msg = (resp && resp.data) ? resp.data : 'Error saving settings';
                $('.notice.settings-dokmeplus').remove();
                $('<div class="notice notice-error settings-dokmeplus"><p>' + msg + '</p></div>').insertBefore('.wrap').delay(4000).fadeOut(400);
            }
        }, 'json').fail(function(){
            $('.notice.settings-dokmeplus').remove();
            $('<div class="notice notice-error settings-dokmeplus"><p>AJAX error</p></div>').insertBefore('.wrap').delay(4000).fadeOut(400);
        });
    });
});
</script>
