<?php
/*
Plugin Name: Dokme Plus
Plugin URI: https://github.com/hajirahimi-ir/dokmeplus
Description: افزونه ساخت دکمه‌های سفارشی با قابلیت لینک، کپی متن، ارسال متن، تماس و پیامک.
Version: 1.9
Author: Hajirahimi
Author URI: https://hajirahimi.ir
License: GPL2
Text Domain: dokmeplus
*/

// جلوگیری از دسترسی مستقیم
if (!defined('ABSPATH')) {
    exit;
}

/**
 * بارگذاری فایل‌های ترجمه
 */
function dokmeplus_load_textdomain() {
    load_plugin_textdomain('dokmeplus', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'dokmeplus_load_textdomain');

/**
 * تابع ترجمه سریع
 */
function dokmeplus_t($key) {
    $translations = [
        'plugin_name'        => 'Dokme Plus',
        'add_button'         => 'افزودن دکمه جدید',
        'button_title'       => 'عنوان دکمه',
        'button_text'        => 'متن دکمه',
        'color'              => 'رنگ دکمه',
        'font_size'          => 'اندازه فونت',
        'action'             => 'عملکرد',
        'link'               => 'لینک',
        'copy'               => 'کپی',
        'send'               => 'ارسال',
        'call'               => 'تماس',
        'sms'                => 'پیامک',
        'link_field'         => 'آدرس لینک',
        'copy_text_field'    => 'متن برای کپی',
        'send_text_field'    => 'متن برای ارسال',
        'call_number_field'  => 'شماره تماس',
        'sms_number_field'   => 'شماره پیامک',
        'sms_message_field'  => 'متن پیامک',
        'save'               => 'ذخیره شد',
        'edit'               => 'ویرایش',
        'delete'             => 'حذف',
        'confirm_delete'     => 'آیا از حذف این دکمه مطمئن هستید؟',
        'no_buttons'         => 'هیچ دکمه‌ای هنوز اضافه نشده است.',
        'title'              => 'عنوان',
        'shortcode'          => 'کد کوتاه',
        'actions'            => 'عملیات',
    ];

    return $translations[$key] ?? $key;
}

/**
 * افزودن منو در پیشخوان وردپرس
 */
function dokmeplus_admin_menu() {
    add_menu_page(
        dokmeplus_t('plugin_name'),
        dokmeplus_t('plugin_name'),
        'manage_options',
        'dokmeplus',
        'dokmeplus_admin_list_page',
        'dashicons-button',
        30
    );

    add_submenu_page(
        'dokmeplus',
        dokmeplus_t('add_button'),
        dokmeplus_t('add_button'),
        'manage_options',
        'dokmeplus_add',
        'dokmeplus_admin_form_page'
    );
}
add_action('admin_menu', 'dokmeplus_admin_menu');

/**
 * نمایش لیست دکمه‌ها
 */
function dokmeplus_admin_list_page() {
    // حذف دکمه
    if (isset($_GET['delete_id'])) {
        $all = get_option('dokmeplus_buttons', []);
        unset($all[$_GET['delete_id']]);
        update_option('dokmeplus_buttons', $all);
        echo '<div class="updated"><p>' . dokmeplus_t('delete') . '.</p></div>';
    }

    include plugin_dir_path(__FILE__) . 'list.php';
}

/**
 * نمایش فرم افزودن یا ویرایش دکمه
 */
function dokmeplus_admin_form_page() {
    include plugin_dir_path(__FILE__) . 'form.php';
}

/**
 * شورتکد برای نمایش دکمه
 */
function dokmeplus_shortcode($atts) {
    $atts = shortcode_atts(['id' => ''], $atts, 'dokmeplus');
    $all = get_option('dokmeplus_buttons', []);
    $id = $atts['id'];

    if (!isset($all[$id])) {
        return '';
    }

    $btn = $all[$id];
    $style = 'background-color:' . esc_attr($btn['color']) . ';font-size:' . intval($btn['size']) . 'px;padding:10px 20px;color:#fff;border:none;border-radius:5px;cursor:pointer;';

    $output = '';

    switch ($btn['action']) {
        case 'link':
            $output = '<a href="' . esc_url($btn['link']) . '" style="' . $style . '" target="_blank">' . esc_html($btn['text']) . '</a>';
            break;
        case 'copy':
            $output = '<button style="' . $style . '" onclick="navigator.clipboard.writeText(\'' . esc_js($btn['copy_text']) . '\')">' . esc_html($btn['text']) . '</button>';
            break;
        case 'send':
            $output = '<button style="' . $style . '" onclick="alert(\'' . esc_js($btn['send_text']) . '\')">' . esc_html($btn['text']) . '</button>';
            break;
        case 'call':
            $output = '<a href="tel:' . esc_attr($btn['call_number']) . '" style="' . $style . '">' . esc_html($btn['text']) . '</a>';
            break;
        case 'sms':
            $output = '<a href="sms:' . esc_attr($btn['sms_number']) . '?body=' . urlencode($btn['sms_message']) . '" style="' . $style . '">' . esc_html($btn['text']) . '</a>';
            break;
    }

    return $output;
}
add_shortcode('dokmeplus', 'dokmeplus_shortcode');

/* ============================================
 * سیستم بروزرسانی خودکار از GitHub
 * GitHub Repo: hajirahimi-ir/dokmeplus
 * ============================================ */
add_filter('pre_set_site_transient_update_plugins', 'dokmeplus_check_github_update');

function dokmeplus_check_github_update($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $repo_owner = 'hajirahimi-ir';
    $repo_name  = 'dokmeplus';
    $plugin_slug = plugin_basename(__FILE__);

    // دریافت اطلاعات آخرین نسخه
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

    if (!isset($release->tag_name)) {
        return $transient;
    }

    if (!function_exists('get_plugin_data')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $plugin_data = get_plugin_data(__FILE__);
    $current_version = $plugin_data['Version'];

    // بررسی نسخه
    if (version_compare($current_version, $release->tag_name, '<')) {
        $plugin_info = new stdClass();
        $plugin_info->slug = dirname($plugin_slug);
        $plugin_info->plugin = $plugin_slug;
        $plugin_info->new_version = $release->tag_name;
        $plugin_info->url = "https://github.com/$repo_owner/$repo_name";
        $plugin_info->package = $release->zipball_url;

        $transient->response[$plugin_slug] = $plugin_info;
    }

    return $transient;
}

/**
 * نمایش اطلاعات پلاگین در صفحه جزئیات
 */
add_filter('plugins_api', 'dokmeplus_plugins_api', 10, 3);

function dokmeplus_plugins_api($result, $action, $args) {
    if ($action !== 'plugin_information') {
        return $result;
    }

    if ($args->slug !== dirname(plugin_basename(__FILE__))) {
        return $result;
    }

    if (!function_exists('get_plugin_data')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $plugin_data = get_plugin_data(__FILE__);

    return (object)[
        'name' => $plugin_data['Name'],
        'slug' => dirname(plugin_basename(__FILE__)),
        'version' => $plugin_data['Version'],
        'author' => '<a href="https://hajirahimi.ir">Hajirahimi</a>',
        'homepage' => 'https://github.com/hajirahimi-ir/dokmeplus',
        'sections' => [
            'description' => 'ساخت دکمه‌های سفارشی با عملکردهای مختلف مانند لینک، کپی، تماس و پیامک.',
            'changelog'   => 'تغییرات نسخه‌ها را در <a href="https://github.com/hajirahimi-ir/dokmeplus/releases" target="_blank">GitHub Releases</a> مشاهده کنید.',
        ],
    ];
}

