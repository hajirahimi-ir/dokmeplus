<?php
/**
 * Plugin Name: Dokme Plus
 * Description: افزونه مدیریت دکمه‌های سفارشی با پیش‌نمایش زنده.
 * Version: 1.0.0
 * Author: شما
 */

if ( ! defined('ABSPATH') ) {
    exit; // جلوگیری از دسترسی مستقیم
}

/* ------------------------------
 * بارگذاری منوهای مدیریتی افزونه
 * ------------------------------ */
add_action('admin_menu', 'dokmeplus_register_admin_menu');
function dokmeplus_register_admin_menu() {

    // منوی اصلی
    add_menu_page(
        'دکمه پلاس',          // عنوان صفحه
        'دکمه پلاس',          // عنوان منو
        'manage_options',     // سطح دسترسی
        'dokmeplus',          // slug
        'dokmeplus_list_page',// کال‌بک
        'dashicons-screenoptions', // آیکون
        26                     // موقعیت منو
    );

    // زیرمنو - افزودن دکمه
    add_submenu_page(
        'dokmeplus',
        'افزودن دکمه',
        'افزودن دکمه',
        'manage_options',
        'dokmeplus_add',
        'dokmeplus_add_page'
    );

    // زیرمنو مخفی برای پیش‌نمایش دکمه
    add_submenu_page(
        null,
        'پیش‌نمایش دکمه',
        'پیش‌نمایش دکمه',
        'manage_options',
        'dokmeplus_live',
        'dokmeplus_live_page'
    );
}

/* ------------------------------
 * صفحه لیست دکمه‌ها
 * ------------------------------ */
function dokmeplus_list_page() {
    // حذف دکمه (در صورت ارسال پارامتر delete_id)
    if (isset($_GET['delete_id'])) {
        $delete_id = sanitize_text_field($_GET['delete_id']);
        check_admin_referer('dokmeplus_delete_' . $delete_id);

        $all = get_option('dokmeplus_buttons', []);
        if (isset($all[$delete_id])) {
            unset($all[$delete_id]);
            update_option('dokmeplus_buttons', $all);

            echo '<div class="updated"><p>دکمه با موفقیت حذف شد.</p></div>';
        }
    }

    include plugin_dir_path(__FILE__) . 'list.php';
}

/* ------------------------------
 * صفحه افزودن یا ویرایش دکمه
 * ------------------------------ */
function dokmeplus_add_page() {
    include plugin_dir_path(__FILE__) . 'form.php';
}

/* ------------------------------
 * صفحه پیش‌نمایش دکمه
 * ------------------------------ */
function dokmeplus_live_page() {
    include plugin_dir_path(__FILE__) . 'live.php';
}

/* ------------------------------
 * شورت‌کد برای نمایش دکمه در سایت
 * [dokmeplus id="شناسه"]
 * ------------------------------ */
add_shortcode('dokmeplus', 'dokmeplus_render_button');
function dokmeplus_render_button($atts) {
    $atts = shortcode_atts([
        'id' => ''
    ], $atts);

    $all = get_option('dokmeplus_buttons', []);
    $id = sanitize_text_field($atts['id']);

    if (!$id || !isset($all[$id])) {
        return '';
    }

    $btn = $all[$id];
    $style = sprintf(
        'background:%s; font-size:%dpx; color:#fff; padding:10px 20px; border-radius:5px; text-decoration:none;',
        esc_attr($btn['color']),
        intval($btn['size'])
    );

    // تعیین نوع عملکرد دکمه
    switch ($btn['action']) {
        case 'call':
            $href = 'tel:' . esc_attr($btn['call_number']);
            break;
        case 'sms':
            $href = 'sms:' . esc_attr($btn['sms_number']);
            if (!empty($btn['sms_message'])) {
                $href .= '?body=' . urlencode($btn['sms_message']);
            }
            break;
        case 'link':
        default:
            $href = !empty($btn['link']) ? esc_url($btn['link']) : '#';
            break;
    }

    return sprintf(
        '<a href="%s" style="%s" target="_blank">%s</a>',
        esc_url($href),
        esc_attr($style),
        esc_html($btn['text'])
    );
}

/* ------------------------------
 * توابع کمکی
 * ------------------------------ */
function dokmeplus_t($key) {
    // اینجا می‌توانید سیستم ترجمه را اضافه کنید
    $translations = [
        'list_title'      => 'لیست دکمه‌ها',
        'add_button'      => 'افزودن دکمه',
        'edit_button'     => 'ویرایش دکمه',
        'label_title'     => 'عنوان دکمه',
        'label_text'      => 'متن دکمه',
        'label_color'     => 'رنگ',
        'label_size'      => 'اندازه فونت',
        'label_action'    => 'عملکرد',
        'label_link'      => 'لینک',
        'label_copy_text' => 'متن برای کپی',
        'label_send_text' => 'متن برای ارسال',
        'label_call_number' => 'شماره تماس',
        'label_sms_number'  => 'شماره پیامک',
        'shortcode'       => 'شورت‌کد',
        'actions'         => 'عملیات',
        'edit'            => 'ویرایش',
        'delete'          => 'حذف',
        'confirm_delete'  => 'آیا مطمئن هستید؟',
        'no_buttons'      => 'هیچ دکمه‌ای یافت نشد.',
        'saved'           => 'ذخیره شد',
        'action_link'     => 'لینک',
        'action_copy'     => 'کپی متن',
        'action_send'     => 'ارسال به اشتراک',
        'action_call'     => 'تماس',
        'action_sms'      => 'ارسال پیامک',
    ];

    return $translations[$key] ?? $key;
}
