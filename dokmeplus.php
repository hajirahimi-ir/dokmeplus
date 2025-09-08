<?php
/**
 * Plugin Name: Dokme Plus
 * Plugin URI: https://hamtamehr.ir/shop/kf-j59n/lo55hg22
 * Description: Create custom buttons with actions like link, copy, share, call, and SMS. Free version allows up to 3 buttons.
 * Version: 1.11.9
 * Author: Hajirahimi
 * Author URI: https://hajirahimi.ir
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dokmeplus
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ---------------------------
  تنظیمات API لایسنس
---------------------------- */
if ( ! defined('HAMTAMEHR_LICENSE_API') ) {
    define( 'HAMTAMEHR_LICENSE_API', 'https://hamtamehr.ir/wp-json/hamtamehr/v1/check-license' );
}

/* ---------------------------
  ترجمه‌های ساده داخلی (فقط برای این افزونه)
---------------------------- */
function dokmeplus_t( $key ) {
    $lang = get_option('dokmeplus_language', 'fa');
    $trans = [
        'en' => [
            'menu_main'        => 'Button Plus',
            'menu_list'        => 'Buttons',
            'menu_add'         => 'Add Button',
            'menu_settings'    => 'Settings',
            'menu_about'       => 'About Developer',
            'list_title'       => 'Buttons',
            'add_button'       => 'Add Button',
            'edit_button'      => 'Edit Button',
            'shortcode'        => 'Shortcode',
            'actions'          => 'Actions',
            'edit'             => 'Edit',
            'delete'           => 'Delete',
            'confirm_delete'   => 'Delete this?',
            'no_buttons'       => 'No buttons found.',
            'label_title'      => 'Button Title',
            'label_text'       => 'Button Text',
            'label_color'      => 'Color',
            'label_size'       => 'Font Size (px)',
            'label_action'     => 'Action',
            'action_link'      => 'Link',
            'action_copy'      => 'Copy',
            'action_send'      => 'Share',
            'action_call'      => 'Call',
            'action_sms'       => 'SMS',
            'label_link'       => 'Link',
            'label_copy_text'  => 'Copy Text',
            'label_send_text'  => 'Share Text',
            'label_call_number'=> 'Phone Number',
            'label_sms_number' => 'Number',
            'label_sms_message'=> 'Message',
            'saved'            => 'Saved.',
            'settings_title'   => 'Plugin Settings',
            'language'         => 'Language',
            'license'          => 'License Key',
            'license_buy'      => 'Buy License Key',
            'save_changes'     => 'Save Changes',
            'license_missing'  => 'Please enter a license key in plugin Settings.',
            'license_invalid'  => 'License key is invalid.',
            'license_error'    => 'License check failed (connection error).',
            'license_ok'       => 'License is valid.',
            'add_blocked'      => 'You can create up to 3 buttons without a valid license. Enter a license to create more.',
            'about_text'       => 'This plugin is made with ❤ by Hajirahimi',
        ],
        'fa' => [
            'menu_main'        => 'دکمه پلاس',
            'menu_list'        => 'لیست دکمه‌ها',
            'menu_add'         => 'افزودن دکمه',
            'menu_settings'    => 'تنظیمات',
            'menu_about'       => 'درباره توسعه‌دهنده',
            'list_title'       => 'لیست دکمه‌ها',
            'add_button'       => 'افزودن دکمه',
            'edit_button'      => 'ویرایش دکمه',
            'shortcode'        => 'کد کوتاه',
            'actions'          => 'عملیات',
            'edit'             => 'ویرایش',
            'delete'           => 'حذف',
            'confirm_delete'   => 'حذف شود؟',
            'no_buttons'       => 'هیچ دکمه‌ای وجود ندارد.',
            'label_title'      => 'عنوان دکمه',
            'label_text'       => 'متن دکمه',
            'label_color'      => 'رنگ',
            'label_size'       => 'اندازه فونت (px)',
            'label_action'     => 'عملکرد',
            'action_link'      => 'لینک',
            'action_copy'      => 'کپی',
            'action_send'      => 'اشتراک‌گذاری',
            'action_call'      => 'تماس',
            'action_sms'       => 'پیامک',
            'label_link'       => 'لینک',
            'label_copy_text'  => 'متن کپی',
            'label_send_text'  => 'متن اشتراک‌گذاری',
            'label_call_number'=> 'شماره تماس',
            'label_sms_number' => 'شماره',
            'label_sms_message'=> 'متن پیامک',
            'saved'            => 'ذخیره شد.',
            'settings_title'   => 'تنظیمات افزونه',
            'language'         => 'زبان',
            'license'          => 'کلید لایسنس',
            'license_buy'      => 'خرید کلید لایسنس',
            'save_changes'     => 'ذخیره تغییرات',
            'license_missing'  => 'لطفاً کد لایسنس را در تنظیمات افزونه وارد کنید.',
            'license_invalid'  => 'کد لایسنس نادرست است.',
            'license_error'    => 'خطا در بررسی لایسنس (اتصال به سرور).',
            'license_ok'       => 'لایسنس معتبر است.',
            'add_blocked'      => 'شما بدون لایسنس معتبر فقط می‌توانید ۳ دکمه بسازید. برای ساخت بیشتر، لطفاً لایسنس وارد کنید.',
            'about_text'       => 'این افزونه با ❤ توسط Hajirahimi ساخته شده است',
        ],
    ];
    return $trans[$lang][$key] ?? $key;
}

/* ---------------------------
  بررسی لایسنس (remote) و کش
---------------------------- */
function dokmeplus_check_license_remote( $license ) {
    if ( empty( $license ) ) {
        return ['valid' => false, 'message' => dokmeplus_t('license_missing')];
    }

    $domain = isset($_SERVER['SERVER_NAME']) ? sanitize_text_field($_SERVER['SERVER_NAME']) : sanitize_text_field(parse_url(home_url(), PHP_URL_HOST));

    $response = wp_remote_post( HAMTAMEHR_LICENSE_API, array(
        'timeout' => 15,
        'body'    => array(
            'license' => $license,
            'domain'  => $domain,
        )
    ) );

    if ( is_wp_error( $response ) ) {
        return ['valid' => false, 'message' => dokmeplus_t('license_error') ];
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( ! is_array($data) || ! array_key_exists('valid', $data) ) {
        return ['valid' => false, 'message' => dokmeplus_t('license_error')];
    }

    $valid = (bool) $data['valid'];
    $message = isset($data['message']) ? sanitize_text_field($data['message']) : ( $valid ? dokmeplus_t('license_ok') : dokmeplus_t('license_invalid') );

    return ['valid' => $valid, 'message' => $message];
}

function dokmeplus_get_cached_license_check() {
    return get_transient('dokmeplus_license_check_result');
}
function dokmeplus_set_cached_license_check($data) {
    set_transient('dokmeplus_license_check_result', $data, 12 * HOUR_IN_SECONDS);
}

/**
 * returns bool
 * uses transient if present, otherwise checks remote once and caches
 */
function dokmeplus_is_license_valid() {
    $cached = dokmeplus_get_cached_license_check();
    if ( is_array($cached) && array_key_exists('valid', $cached) ) {
        return (bool) $cached['valid'];
    }
    $license = get_option('dokmeplus_license', '');
    $check = dokmeplus_check_license_remote( $license );
    dokmeplus_set_cached_license_check($check);
    return (bool) ($check['valid'] ?? false);
}

/* ---------------------------
  منوها و زیرمنوها
---------------------------- */
add_action('admin_menu', function() {
    add_menu_page( dokmeplus_t('menu_main'), dokmeplus_t('menu_main'), 'manage_options', 'dokmeplus', 'dokmeplus_list_page', 'dashicons-button' );

    add_submenu_page('dokmeplus', dokmeplus_t('menu_list'), dokmeplus_t('menu_list'), 'manage_options', 'dokmeplus', 'dokmeplus_list_page' );

    add_submenu_page('dokmeplus', dokmeplus_t('menu_add'), dokmeplus_t('menu_add'), 'manage_options', 'dokmeplus_add', 'dokmeplus_form_page' );

    add_submenu_page('dokmeplus', dokmeplus_t('menu_settings'), dokmeplus_t('menu_settings'), 'manage_options', 'dokmeplus_settings', 'dokmeplus_settings_page' );

    add_submenu_page('dokmeplus', dokmeplus_t('menu_about'), '<span style="color:red;">' . dokmeplus_t('menu_about') . '</span>', 'manage_options', 'dokmeplus_about', 'dokmeplus_about_page' );
});

/* ---------------------------
  admin notice: show license status on plugin pages
---------------------------- */
add_action('admin_notices', function() {
    $page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
    $relevant = in_array($page, ['dokmeplus','dokmeplus_add','dokmeplus_settings','dokmeplus_about'], true);
    if ( ! $relevant ) return;

    $license = get_option('dokmeplus_license', '');
    $cached = dokmeplus_get_cached_license_check();

    // لینک خرید (با اتصال صحیح رشته‌ها)
    $buy_link_html = '<a href="https://hamtamehr.ir/shop/kf-j59n/lo55hg22" target="_blank" style="color:#2271b1;font-weight:bold;">'
        . esc_html( dokmeplus_t('license_buy') )
        . '</a>';

    if ( empty($license) ) {
        echo '<div class="notice notice-warning"><p>'
            . esc_html( dokmeplus_t('license_missing') )
            . ' <a href="' . esc_url(admin_url('admin.php?page=dokmeplus_settings')) . '">'
            . esc_html(dokmeplus_t('menu_settings')) . '</a> | '
            . $buy_link_html
            . '</p></div>';
        return;
    }

    if ( is_array($cached) ) {
        if ( ! $cached['valid'] ) {
            echo '<div class="notice notice-error"><p>'
                . esc_html( sanitize_text_field($cached['message']) )
                . ' | ' . $buy_link_html
                . '</p></div>';
        }
        return;
    }

    // no cached info: just remind to check settings
    echo '<div class="notice notice-warning"><p>'
        . esc_html( dokmeplus_t('license_missing') )
        . ' <a href="' . esc_url(admin_url('admin.php?page=dokmeplus_settings')) . '">'
        . esc_html(dokmeplus_t('menu_settings')) . '</a> | '
        . $buy_link_html
        . '</p></div>';
});

/* ---------------------------
  حذف دکمه (via GET delete_id) — امن‌شده با nonce و sanitize
---------------------------- */
add_action('admin_init', function() {
    if ( isset($_GET['delete_id']) && current_user_can('manage_options') ) {
        $id_raw = $_GET['delete_id'];
        $id = is_numeric($id_raw) ? absint($id_raw) : sanitize_text_field($id_raw);

        // nonce verification: نام action هم با تولید لینک همخوان باشد
        if ( ! isset($_GET['_wpnonce']) || ! wp_verify_nonce( wp_unslash($_GET['_wpnonce']), 'dokmeplus_delete_' . $id ) ) {
            // invalid nonce: do nothing or optionally show error
            return;
        }

        $all = get_option('dokmeplus_buttons', []);
        if ( isset($all[$id]) ) {
            unset($all[$id]);
            update_option('dokmeplus_buttons', $all);
        }
        wp_safe_redirect(admin_url('admin.php?page=dokmeplus'));
        exit;
    }
});

/* ---------------------------
  شورتکد نمایش دکمه
---------------------------- */
add_shortcode('dokmeplus', function($atts) {
    $atts = shortcode_atts(['id' => ''], $atts);
    $buttons = get_option('dokmeplus_buttons', []);
    $id = $atts['id'];
    if ( ! isset($buttons[$id]) ) return '';
    $b = $buttons[$id];

    // آماده‌سازی امن خروجی‌ها
    $title = esc_html($b['title'] ?? '');
    $text = esc_html($b['text'] ?? '');
    $color = esc_attr($b['color'] ?? '#0073aa');
    $size = intval($b['size'] ?? 16);

    $style = 'background-color:' . $color . '; font-size:' . $size . 'px; padding:10px 20px; color:white; border:none; border-radius:5px; cursor:pointer;';

    switch ( $b['action'] ?? 'link' ) {
        case 'link':
            return '<a href="' . esc_url($b['link'] ?? '') . '" target="_blank" rel="noopener noreferrer"><button style="' . $style . '">' . $text . '</button></a>';
        case 'copy':
            // escape for JS
            $copy_text_js = esc_js($b['copy_text'] ?? '');
            $saved_js = esc_js(dokmeplus_t('saved'));
            return '<button style="' . $style . '" onclick="navigator.clipboard.writeText(\'' . $copy_text_js . '\'); alert(\'' . $saved_js . '\');">' . $text . '</button>';
        case 'send':
            $send_text_js = esc_js($b['send_text'] ?? '');
            $err_js = esc_js(dokmeplus_t('license_error'));
            return '<button style="' . $style . '" onclick="if(navigator.share){navigator.share({text: \'' . $send_text_js . '\'});}else{alert(\'' . $err_js . '\');}">' . $text . '</button>';
        case 'call':
            return '<a href="tel:' . esc_attr($b['call_number'] ?? '') . '"><button style="' . $style . '">' . $text . '</button></a>';
        case 'sms':
            $sms_number = esc_attr($b['sms_number'] ?? '');
            $sms_message = isset($b['sms_message']) ? rawurlencode($b['sms_message']) : '';
            return '<a href="sms:' . $sms_number . '?body=' . $sms_message . '"><button style="' . $style . '">' . $text . '</button></a>';
        default:
            return '<button style="' . $style . '">' . $text . '</button>';
    }
});

/* ---------------------------
  صفحه لیست دکمه‌ها
---------------------------- */
function dokmeplus_list_page() {
    $all = get_option('dokmeplus_buttons', []);
    if ( isset($_GET['blocked']) && $_GET['blocked'] == '1' ) {
        echo '<div class="notice notice-error"><p>' . esc_html(dokmeplus_t('add_blocked')) . ' <a href="' . esc_url(admin_url('admin.php?page=dokmeplus_settings')) . '">' . esc_html(dokmeplus_t('menu_settings')) . '</a></p></div>';
    }
    if ( isset($_GET['updated']) ) {
        echo '<div class="notice notice-success"><p>' . esc_html(dokmeplus_t('saved')) . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( dokmeplus_t('list_title') ); ?></h1>
        <a href="<?php echo esc_url(admin_url('admin.php?page=dokmeplus_add')); ?>" class="button-primary"><?php echo esc_html(dokmeplus_t('add_button')); ?></a>
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php echo esc_html(dokmeplus_t('label_title')); ?></th>
                    <th><?php echo esc_html(dokmeplus_t('shortcode')); ?></th>
                    <th><?php echo esc_html(dokmeplus_t('actions')); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ( $all ) {
                foreach ( $all as $id => $btn ) {
                    $id_attr = esc_attr($id);
                    $edit_url = esc_url( admin_url('admin.php?page=dokmeplus_add&edit_id=' . rawurlencode($id)) );
                    // لینک حذف با nonce امن ساخته می‌شود (action: dokmeplus_delete_$id)
                    $delete_url = wp_nonce_url( admin_url('admin.php?page=dokmeplus&delete_id=' . rawurlencode($id)), 'dokmeplus_delete_' . $id );
                    echo '<tr>';
                    echo '<td>' . esc_html( $btn['title'] ) . '</td>';
                    echo '<td>[dokmeplus id="' . $id_attr . '"]</td>';
                    echo '<td><a href="' . $edit_url . '">' . esc_html(dokmeplus_t('edit')) . '</a> | <a href="' . $delete_url . '" onclick="return confirm(\'' . esc_js(dokmeplus_t('confirm_delete')) . '\')">' . esc_html(dokmeplus_t('delete')) . '</a></td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="3">' . esc_html(dokmeplus_t('no_buttons')) . '</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
}

/* ---------------------------
  صفحه افزودن / ویرایش دکمه
  — تابع نمایش فرم (فقط نمایش). ذخیره در بخش admin_post انجام می‌شود.
---------------------------- */
function dokmeplus_form_page() {
    if ( ! current_user_can('manage_options') ) wp_die('Access denied');

    $all = get_option('dokmeplus_buttons', []);
    $edit_id = isset($_GET['edit_id']) ? sanitize_text_field($_GET['edit_id']) : null;
    $edit = ($edit_id && isset($all[$edit_id])) ? $all[$edit_id] : [];

    // محدودیت ساخت بدون لایسنس
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $is_valid = dokmeplus_is_license_valid();
        $count = is_array($all) ? count($all) : 0;
        if ( ! $is_valid && $count >= 3 && ! $edit_id ) {
            wp_safe_redirect(admin_url('admin.php?page=dokmeplus&blocked=1'));
            exit;
        }
    }

    // نمایش فرم (تصویر/چاپ فرم)
    ?>
    <div class="wrap">
        <h1><?php echo $edit_id ? esc_html(dokmeplus_t('edit_button')) : esc_html(dokmeplus_t('add_button')); ?></h1>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php
            // nonce و فیلدهای مورد نیاز برای پردازش ایمن
            wp_nonce_field('dokmeplus_save', '_dokmeplus_nonce');
            ?>
            <input type="hidden" name="action" value="dokmeplus_save_button">
            <input type="hidden" name="edit_id" value="<?php echo esc_attr($edit_id ?? ''); ?>">
            <table class="form-table">
                <tr><th><?php echo esc_html(dokmeplus_t('label_title')); ?></th><td><input name="title" value="<?php echo esc_attr($edit['title'] ?? '') ?>" required></td></tr>
                <tr><th><?php echo esc_html(dokmeplus_t('label_text')); ?></th><td><input name="text" value="<?php echo esc_attr($edit['text'] ?? '') ?>"></td></tr>
                <tr><th><?php echo esc_html(dokmeplus_t('label_color')); ?></th><td><input type="color" name="color" value="<?php echo esc_attr($edit['color'] ?? '#0073aa') ?>"></td></tr>
                <tr><th><?php echo esc_html(dokmeplus_t('label_size')); ?></th><td><input type="number" name="size" value="<?php echo esc_attr($edit['size'] ?? 16) ?>"></td></tr>
                <tr><th><?php echo esc_html(dokmeplus_t('label_action')); ?></th><td>
                    <select name="action" onchange="toggleFields(this.value)">
                        <option value="link" <?php selected($edit['action'] ?? '', 'link'); ?>><?php echo esc_html(dokmeplus_t('action_link')); ?></option>
                        <option value="copy" <?php selected($edit['action'] ?? '', 'copy'); ?>><?php echo esc_html(dokmeplus_t('action_copy')); ?></option>
                        <option value="send" <?php selected($edit['action'] ?? '', 'send'); ?>><?php echo esc_html(dokmeplus_t('action_send')); ?></option>
                        <option value="call" <?php selected($edit['action'] ?? '', 'call'); ?>><?php echo esc_html(dokmeplus_t('action_call')); ?></option>
                        <option value="sms" <?php selected($edit['action'] ?? '', 'sms'); ?>><?php echo esc_html(dokmeplus_t('action_sms')); ?></option>
                    </select>
                </td></tr>

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
    <?php
}

/* ---------------------------
  پردازش ذخیره فرم (امن) — از طریق admin_post
---------------------------- */
add_action('admin_post_dokmeplus_save_button', function() {
    if ( ! current_user_can('manage_options') ) wp_die('Access denied');

    // nonce و ارزیابی
    if ( ! isset($_POST['_dokmeplus_nonce']) || ! wp_verify_nonce( wp_unslash($_POST['_dokmeplus_nonce']), 'dokmeplus_save' ) ) {
        wp_die('Invalid nonce');
    }

    $all = get_option('dokmeplus_buttons', []);
    $edit_id = isset($_POST['edit_id']) ? sanitize_text_field($_POST['edit_id']) : '';

    // محدودیت ساخت بدون لایسنس
    $is_valid = dokmeplus_is_license_valid();
    $count = is_array($all) ? count($all) : 0;
    $is_new = empty($edit_id);
    if ( $is_new && ! $is_valid && $count >= 3 ) {
        wp_safe_redirect(admin_url('admin.php?page=dokmeplus&blocked=1'));
        exit;
    }

    // داده‌ها را sanitize کن و ذخیره کن
    $data = [
        'title' => sanitize_text_field($_POST['title'] ?? ''),
        'text'  => sanitize_text_field($_POST['text'] ?? ''),
        'color' => sanitize_text_field($_POST['color'] ?? '#0073aa'),
        'size'  => intval($_POST['size'] ?? 16),
        'action'=> sanitize_text_field($_POST['action'] ?? 'link'),
        'link'  => esc_url_raw($_POST['link'] ?? ''),
        'copy_text' => sanitize_text_field($_POST['copy_text'] ?? ''),
        'send_text' => sanitize_text_field($_POST['send_text'] ?? ''),
        'call_number'=> sanitize_text_field($_POST['call_number'] ?? ''),
        'sms_number' => sanitize_text_field($_POST['sms_number'] ?? ''),
        'sms_message'=> sanitize_textarea_field($_POST['sms_message'] ?? ''),
    ];

    // تولید ID امن: اگر edit_id موجود باشد از آن استفاده کن، وگرنه یک uuid/uniqid جدید بساز
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

    wp_redirect(admin_url('admin.php?page=dokmeplus&updated=1'));
    exit;
});

/* ---------------------------
  صفحه تنظیمات: زبان + لایسنس (با لینک خرید)
---------------------------- */
function dokmeplus_settings_page() {
    if ( ! current_user_can('manage_options') ) wp_die('Access denied');

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        if ( ! isset($_POST['dokmeplus_settings_nonce']) || ! wp_verify_nonce($_POST['dokmeplus_settings_nonce'], 'dokmeplus_settings_save') ) {
            wp_die('Invalid nonce');
        }

        $lang = in_array($_POST['dokmeplus_language'] ?? 'en', ['en','fa']) ? $_POST['dokmeplus_language'] : 'en';
        update_option('dokmeplus_language', $lang);

        $license = sanitize_text_field($_POST['dokmeplus_license'] ?? '');
        update_option('dokmeplus_license', $license);

        $check = dokmeplus_check_license_remote( $license );
        dokmeplus_set_cached_license_check($check);

        wp_redirect(admin_url('admin.php?page=dokmeplus_settings&lic=' . ( $check['valid'] ? 'ok' : 'no' )));
        exit;
    }

    $current_lang = get_option('dokmeplus_language', 'fa');
    $current_license = get_option('dokmeplus_license', '');

    if ( isset($_GET['lic']) ) {
        $cached = dokmeplus_get_cached_license_check();
        if ( is_array($cached) ) {
            if ( $cached['valid'] ) {
                echo '<div class="notice notice-success"><p>' . esc_html($cached['message']) . '</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>' . esc_html($cached['message']) . '</p></div>';
            }
        }
    }

    // لینک خرید
    $buy_link_html = '<a href="https://hamtamehr.ir/shop/kf-j59n/lo55hg22" target="_blank" style="color:#2271b1;font-weight:bold;">'
        . esc_html( dokmeplus_t('license_buy') )
        . '</a>';
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(dokmeplus_t('settings_title')); ?></h1>
        <form method="post">
            <?php wp_nonce_field('dokmeplus_settings_save', 'dokmeplus_settings_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><?php echo esc_html(dokmeplus_t('language')); ?></th>
                    <td>
                        <select name="dokmeplus_language">
                            <option value="en" <?php selected($current_lang, 'en'); ?>>English</option>
                            <option value="fa" <?php selected($current_lang, 'fa'); ?>>فارسی</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php echo esc_html(dokmeplus_t('license')); ?></th>
                    <td>
                        <input type="text" name="dokmeplus_license" value="<?php echo esc_attr($current_license); ?>" placeholder="<?php echo esc_attr(dokmeplus_t('license')); ?>" style="width:380px;">
                        <p class="description"><?php echo esc_html__('Enter the license key provided by the vendor.','dokmeplus'); ?></p>
                        <p><?php echo $buy_link_html; ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button( dokmeplus_t('save_changes') ); ?>
        </form>
    </div>
    <?php
}

/* ---------------------------
  صفحه درباره توسعه‌دهنده
---------------------------- */
function dokmeplus_about_page() {
    echo '<div class="wrap"><h1>' . esc_html(dokmeplus_t('menu_about')) . '</h1>';
    echo '<p>' . esc_html(dokmeplus_t('about_text')) . ' <a href="https://hajirahimi.ir" target="_blank">hajirahimi.ir</a></p></div>';
}

/* ============================================================
   سیستم بروزرسانی خودکار از GitHub (hajirahimi-ir/dokmeplus)
============================================================ */
add_filter('pre_set_site_transient_update_plugins', 'dokmeplus_check_github_update');

function dokmeplus_check_github_update($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $repo_owner = 'hajirahimi-ir';
    $repo_name  = 'dokmeplus';
    $plugin_file = plugin_basename(__FILE__); // مثلا: dokmeplus/dokmeplus.php

    // دریافت آخرین ریلیز از GitHub
    $response = wp_remote_get("https://api.github.com/repos/$repo_owner/$repo_name/releases/latest", [
        'headers' => [
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
        ],
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        return $transient;
    }

    $release = json_decode(wp_remote_retrieve_body($response));

    if (empty($release) || empty($release->tag_name)) {
        return $transient;
    }

    if (!function_exists('get_plugin_data')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $plugin_data     = get_plugin_data(__FILE__, false, false);
    $current_version = isset($plugin_data['Version']) ? $plugin_data['Version'] : '0.0.0';
    $latest_version  = ltrim($release->tag_name, 'vV'); // پشتیبانی از تگ‌های v1.9

    if (version_compare($current_version, $latest_version, '<')) {
        $obj = new stdClass();
        $obj->slug        = dirname($plugin_file); // dokmeplus
        $obj->plugin      = $plugin_file;          // dokmeplus/dokmeplus.php
        $obj->new_version = $latest_version;
        $obj->url         = "https://github.com/$repo_owner/$repo_name";
        $obj->package     = isset($release->zipball_url) ? $release->zipball_url : "https://github.com/$repo_owner/$repo_name/archive/refs/tags/{$release->tag_name}.zip";

        $transient->response[$plugin_file] = $obj;
    }

    return $transient;
}

// اطلاعات جزئیات افزونه در پنجره پاپ‌آپ «نمایش جزئیات»
add_filter('plugins_api', 'dokmeplus_plugins_api', 10, 3);
function dokmeplus_plugins_api($result, $action, $args) {
    if ($action !== 'plugin_information') return $result;

    $this_slug = dirname(plugin_basename(__FILE__)); // dokmeplus
    if (!isset($args->slug) || $args->slug !== $this_slug) return $result;

    if (!function_exists('get_plugin_data')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $plugin_data = get_plugin_data(__FILE__, false, false);

    $info = new stdClass();
    $info->name        = $plugin_data['Name'];
    $info->slug        = $this_slug;
    $info->version     = $plugin_data['Version'];
    $info->author      = '<a href="https://hajirahimi.ir" target="_blank">Hajirahimi</a>';
    $info->homepage    = 'https://github.com/hajirahimi-ir/dokmeplus';
    $info->requires    = '5.0';
    $info->tested      = get_bloginfo('version');
    $info->sections    = [
        'description' => 'Create custom buttons with actions like link, copy, share, call, and SMS.',
        'changelog'   => 'برای مشاهده تغییرات به <a href="https://github.com/hajirahimi-ir/dokmeplus/releases" target="_blank">GitHub Releases</a> مراجعه کنید.',
    ];

    return $info;
}


