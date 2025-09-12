<?php
if (!defined('ABSPATH')) exit;

/**
 * مدیریت زبان‌ها برای Dokme Plus
 */

// آرایه رشته‌ها
function dokmeplus_get_languages() {
    return [
        'fa' => [
            'menu_main'        => 'داکمه پلاس',
            'menu_list'        => 'دکمه‌ها',
            'menu_add'         => 'افزودن دکمه',
            'menu_settings'    => 'تنظیمات',
            'menu_about'       => 'درباره توسعه‌دهنده',
            'list_title'       => 'لیست دکمه‌ها',
            'add_button'       => 'افزودن دکمه',
            'edit_button'      => 'ویرایش دکمه',
            'shortcode'        => 'شورت‌کد',
            'actions'          => 'عملیات',
            'edit'             => 'ویرایش',
            'delete'           => 'حذف',
            'confirm_delete'   => 'آیا از حذف این دکمه مطمئن هستید؟',
            'no_buttons'       => 'هیچ دکمه‌ای یافت نشد.',
            'label_title'      => 'عنوان دکمه',
            'label_text'       => 'متن دکمه',
            'label_color'      => 'رنگ',
            'label_size'       => 'اندازه فونت (px)',
            'label_action'     => 'نوع عملکرد',
            'action_link'      => 'لینک',
            'action_copy'      => 'کپی',
            'action_send'      => 'اشتراک گذاری',
            'action_call'      => 'تماس',
            'action_sms'       => 'پیامک',
            'label_link'       => 'آدرس لینک',
            'label_copy_text'  => 'متن کپی',
            'label_send_text'  => 'متن اشتراک گذاری',
            'label_call_number'=> 'شماره تماس',
            'label_sms_number' => 'شماره پیامک',
            'label_sms_message'=> 'متن پیامک',
            'saved'            => 'ذخیره شد.',
            'settings_title'   => 'تنظیمات افزونه',
            'language'         => 'زبان',
            'license'          => 'کلید لایسنس',
            'license_buy'      => 'خرید لایسنس',
            'save_changes'     => 'ذخیره تغییرات',
            'license_missing'  => 'لطفاً کلید لایسنس را در تنظیمات وارد کنید.',
            'license_invalid'  => 'کلید لایسنس معتبر نیست.',
            'license_error'    => 'خطا در بررسی لایسنس (مشکل ارتباط).',
            'license_ok'       => 'لایسنس معتبر است.',
            'add_blocked'      => 'شما فقط می‌توانید تا ۳ دکمه بدون لایسنس ایجاد کنید. برای ایجاد دکمه‌های بیشتر، لایسنس وارد کنید.',
            'about_text'       => 'این افزونه با ❤ توسط حاجی‌رحیمی ساخته شده است',
        ],
        'en' => [
            'menu_main'        => 'Dokme Plus',
            'menu_list'        => 'Buttons',
            'menu_add'         => 'Add Button',
            'menu_settings'    => 'Settings',
            'menu_about'       => 'About Developer',
            'list_title'       => 'Buttons List',
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
        ]
    ];
}

// زبان پیش‌فرض: انگلیسی
function dokmeplus_get_current_language() {
    $lang = get_option('dokmeplus_language', 'en'); // پیش‌فرض انگلیسی
    return $lang;
}

// گرفتن متن ترجمه‌شده
function dokmeplus_t($key) {
    $languages = dokmeplus_get_languages();
    $current_lang = dokmeplus_get_current_language();

    return $languages[$current_lang][$key] ?? $key;
}
