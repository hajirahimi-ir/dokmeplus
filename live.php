<?php
if (!defined('ABSPATH')) exit;

$all = get_option('dokmeplus_buttons', []);
$id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : '';

if (!$id || !isset($all[$id])) {
    wp_die('دکمه مورد نظر یافت نشد.');
}

$btn = $all[$id];
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>پیش نمایش دکمه</title>
    <style>
        body {
            font-family: tahoma, sans-serif;
            background: #f9f9f9;
            padding: 40px;
            text-align: center;
        }
        a.button-preview {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            font-size: <?php echo intval($btn['size']); ?>px;
            background: <?php echo esc_attr($btn['color']); ?>;
        }
    </style>
</head>
<body>

    <h2><?php echo esc_html($btn['title']); ?></h2>
    <a class="button-preview" 
       href="<?php echo esc_url($btn['link'] ?: '#'); ?>" 
       target="_blank">
        <?php echo esc_html($btn['text']); ?>
    </a>

</body>
</html>
