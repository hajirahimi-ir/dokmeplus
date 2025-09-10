<?php
/**
 * Plugin Name: Dokme Plus
 * Plugin URI: https://hamtamehr.ir/shop/kf-j59n/lo55hg22
 * Description: Manage custom buttons with live preview and license system. Free version supports up to 3 buttons.
 * Version: 1.15.19
 * Author: Hajirahimi
 * Author URI: https://hajirahimi.ir
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dokmeplus
 * Domain Path: /languages
 */

if ( ! defined('ABSPATH') ) exit;

/* ---------------------------
  License API Settings
---------------------------- */
if ( ! defined('HAMTAMEHR_LICENSE_API') ) {
    define( 'HAMTAMEHR_LICENSE_API', 'https://hamtamehr.ir/wp-json/hamtamehr/v1/check-license' );
}

/* ---------------------------
 * Admin Menus
---------------------------- */
add_action('admin_menu', function() {
    add_menu_page(
        'Dokme Plus',
        'Dokme Plus',
        'manage_options',
        'dokmeplus',
        'dokmeplus_list_page',
        'dashicons-screenoptions',
        26
    );

    add_submenu_page(
        'dokmeplus',
        'Add Button',
        'Add Button',
        'manage_options',
        'dokmeplus_add',
        'dokmeplus_form_page'
    );

    add_submenu_page(
        'dokmeplus',
        'Settings',
        'Settings',
        'manage_options',
        'dokmeplus_settings',
        'dokmeplus_settings_page'
    );

    add_submenu_page(
        'dokmeplus',
        'About Developer',
        'About',
        'manage_options',
        'dokmeplus_about',
        'dokmeplus_about_page'
    );

    // Hidden submenu for live preview
    add_submenu_page(
        null,
        'Live Preview',
        'Live Preview',
        'manage_options',
        'dokmeplus_live',
        'dokmeplus_live_page'
    );
});

/* ---------------------------
 * License System
---------------------------- */
function dokmeplus_check_license_remote($license) {
    if (empty($license)) {
        return ['valid' => false, 'message' => 'Please enter a license key.'];
    }

    $domain = sanitize_text_field($_SERVER['SERVER_NAME']);
    $response = wp_remote_post(HAMTAMEHR_LICENSE_API, [
        'timeout' => 15,
        'body'    => ['license' => $license, 'domain' => $domain]
    ]);

    if (is_wp_error($response)) {
        return ['valid' => false, 'message' => 'License key is invalid.'];
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    return [
        'valid'   => (bool)($data['valid'] ?? false),
        'message' => sanitize_text_field($data['message'] ?? 'License key is invalid.')
    ];
}

function dokmeplus_is_license_valid() {
    $cached = get_transient('dokmeplus_license_check');
    if ($cached) {
        return (bool) $cached['valid'];
    }
    $license = get_option('dokmeplus_license', '');
    $check = dokmeplus_check_license_remote($license);
    set_transient('dokmeplus_license_check', $check, 12 * HOUR_IN_SECONDS);
    return (bool) $check['valid'];
}

/* ---------------------------
 * Live Preview Page
---------------------------- */
function dokmeplus_live_page() {
    include plugin_dir_path(__FILE__) . 'live.php';
}

/* ---------------------------
 * Button List Page
---------------------------- */
function dokmeplus_list_page() {
    include plugin_dir_path(__FILE__) . 'list.php';
}

/* ---------------------------
 * Add / Edit Button Page
---------------------------- */
function dokmeplus_form_page() {
    // Free version limit: 3 buttons
    $all = get_option('dokmeplus_buttons', []);
    if (!dokmeplus_is_license_valid() && count($all) >= 3 && !isset($_GET['edit_id'])) {
        wp_safe_redirect(admin_url('admin.php?page=dokmeplus&blocked=1'));
        exit;
    }

    include plugin_dir_path(__FILE__) . 'form.php';
}

/* ---------------------------
 * Settings Page
---------------------------- */
function dokmeplus_settings_page() {
    include plugin_dir_path(__FILE__) . 'settings.php';
}

/* ---------------------------
 * About Page
---------------------------- */
function dokmeplus_about_page() {
    echo '<div class="wrap"><h1>About Developer</h1>';
    echo '<p>This plugin is created with ❤ by <a href="https://hajirahimi.ir" target="_blank">Hajirahimi</a>.</p></div>';
}

/* ---------------------------
 * Shortcode for Displaying Buttons
---------------------------- */
add_shortcode('dokmeplus', function($atts) {
    $atts = shortcode_atts(['id' => ''], $atts);
    $all = get_option('dokmeplus_buttons', []);
    $id = sanitize_text_field($atts['id']);

    if (!$id || !isset($all[$id])) return '';

    $btn = $all[$id];
    $style = sprintf(
        'background:%s; font-size:%dpx; color:#fff; padding:10px 20px; border-radius:5px; text-decoration:none;',
        esc_attr($btn['color']),
        intval($btn['size'])
    );

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
        default:
            $href = !empty($btn['link']) ? esc_url($btn['link']) : '#';
            break;
    }

    return sprintf('<a href="%s" style="%s" target="_blank">%s</a>',
        esc_url($href),
        esc_attr($style),
        esc_html($btn['text'])
    );
});

