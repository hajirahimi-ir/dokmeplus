<?php
/**
 * Plugin Name: Dokme Plus
 * Plugin URI: https://hamtamehr.ir/shop/kf-j59n/lo55hg22
 * Description: Manage custom buttons with live preview and license system. Free version supports up to 3 buttons.
 * Version: 1.12
 * Author: Hajirahimi
 * Author URI: https://hajirahimi.ir
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dokmeplus
 * Domain Path: /languages
 */


if ( ! defined('ABSPATH') ) exit;

// Fallback translation function (ensures older include files that use dokmeplus_t() won't fatal)
if ( ! function_exists('dokmeplus_t') ) {
    function dokmeplus_t( $key ) {
        $strings = [
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
        ];
        return $strings[$key] ?? $key;
    }
}


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
 * GitHub Auto Update System
---------------------------- */
add_action('init', function() {
    if (is_admin()) {
        new DokmePlus_GitHub_Updater();
    }
});

class DokmePlus_GitHub_Updater {
    private $plugin_file;
    private $github_api;
    private $plugin_slug;

    public function __construct() {
        $this->plugin_file = plugin_basename(__FILE__);
        $this->plugin_slug = dirname($this->plugin_file);
        $this->github_api  = 'https://api.github.com/repos/YOUR_GITHUB_USERNAME/YOUR_REPOSITORY/releases/latest';

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
    }

    // Check for update
    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $remote = wp_remote_get($this->github_api, [
            'headers' => ['User-Agent' => 'WordPress']
        ]);

        if (!is_wp_error($remote) && isset($remote['response']['code']) && $remote['response']['code'] == 200) {
            $data = json_decode(wp_remote_retrieve_body($remote));
            if (isset($data->tag_name)) {
                $plugin_data = get_plugin_data(__FILE__);
                $current_version = $plugin_data['Version'];
                $latest_version = ltrim($data->tag_name, 'v');

                if (version_compare($current_version, $latest_version, '<')) {
                    $plugin = [
                        'slug'        => $this->plugin_slug,
                        'plugin'      => $this->plugin_file,
                        'new_version' => $latest_version,
                        'url'         => $data->html_url,
                        'package'     => $data->zipball_url
                    ];
                    $transient->response[$this->plugin_file] = (object) $plugin;
                }
            }
        }

        return $transient;
    }

    // Display plugin info on update details screen
    public function plugin_info($res, $action, $args) {
        if ($action !== 'plugin_information' || $args->slug !== $this->plugin_slug) {
            return $res;
        }

        $remote = wp_remote_get($this->github_api, [
            'headers' => ['User-Agent' => 'WordPress']
        ]);

        if (!is_wp_error($remote) && isset($remote['response']['code']) && $remote['response']['code'] == 200) {
            $data = json_decode(wp_remote_retrieve_body($remote));
            $res = (object) [
                'name'          => 'Dokme Plus',
                'slug'          => $this->plugin_slug,
                'version'       => ltrim($data->tag_name, 'v'),
                'author'        => '<a href="https://hajirahimi.ir">Hajirahimi</a>',
                'homepage'      => $data->html_url,
                'sections'      => [
                    'description' => $data->body
                ],
                'download_link' => $data->zipball_url,
            ];
        }

        return $res;
    }
}


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

