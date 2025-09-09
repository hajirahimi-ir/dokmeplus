<?php
// live.php
if ( ! defined( 'ABSPATH' ) ) {
    // لود کردن وردپرس برای دسترسی به توابع
    require_once( dirname(__FILE__) . '/../../../wp-load.php' );
}

// بررسی دسترسی کاربر
if ( ! current_user_can('manage_options') ) {
    wp_die('Access Denied');
}

// دریافت شناسه دکمه
$id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : '';
if ( empty($id) ) {
    wp_die('Button ID not provided.');
}

// گرفتن اطلاعات دکمه از دیتابیس
$all = get_option('dokmeplus_buttons', []);
$button = isset($all[$id]) ? $all[$id] : null;

if ( ! $button ) {
    wp_die('Button not found.');
}

// تعیین لینک و رفتار دکمه بر اساس نوع اکشن
$href = '#';
$onclick = '';
$target = '_self';

switch ($button['action']) {
    case 'link':
        $href = esc_url($button['link']);
        $target = '_blank';
        break;

    case 'call':
        $href = 'tel:' . esc_attr($button['call_number']);
        break;

    case 'sms':
        $href = 'sms:' . esc_attr($button['sms_number']);
        if (!empty($button['sms_message'])) {
            $href .= '?body=' . rawurlencode($button['sms_message']);
        }
        break;

    case 'copy':
        $onclick = "navigator.clipboard.writeText('" . esc_js($button['copy_text']) . "').then(() => alert('متن کپی شد')); return false;";
        break;

    case 'send':
        $onclick = "if(navigator.share){navigator.share({text:'" . esc_js($button['send_text']) . "'});} else {alert('مرورگر شما از اشتراک‌گذاری پشتیبانی نمی‌کند.');} return false;";
        break;
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>پیش‌نمایش دکمه</title>
    <style>
        body {
            background: #f9f9f9;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
        .dokmeplus-preview-btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            font-size: <?php echo intval($button['size']); ?>px;
            background: <?php echo esc_attr($button['color']); ?>;
        }
    </style>
</head>
<body>
    <h2>پیش‌نمایش دکمه: <?php echo esc_html($button['title']); ?></h2>
    <div style="margin-top:30px;">
        <a href="<?php echo $href; ?>" class="dokmeplus-preview-btn" target="<?php echo esc_attr($target); ?>" onclick="<?php echo esc_attr($onclick); ?>">
            <?php echo esc_html($button['text']); ?>
        </a>
    </div>
</body>
</html>
