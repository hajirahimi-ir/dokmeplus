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

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once __DIR__ . '/elementor.php';

require_once plugin_dir_path(__FILE__) . 'language.php';

// AJAX handler to save settings and return translations
add_action( 'wp_ajax_dokmeplus_save_settings', 'dokmeplus_save_settings_ajax' );
function dokmeplus_save_settings_ajax() {
    // capability & nonce check
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'permission_denied' );
    }
    check_admin_referer( 'dokmeplus_settings_save' );

    $license  = isset( $_POST['license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : '';
    $language = isset( $_POST['language'] ) ? sanitize_text_field( wp_unslash( $_POST['language'] ) ) : 'en';

    update_option( 'dokmeplus_license', $license );
    update_option( 'dokmeplus_language', $language );

    // clear cached license check so it can re-run (optional but useful)
    if ( defined( 'DOKMEPLUS_TRANSIENT_LICENSE' ) ) {
        delete_transient( DOKMEPLUS_TRANSIENT_LICENSE );
    }

    // Return translation strings for the requested language
    if ( function_exists( 'dokmeplus_get_languages' ) ) {
        $all_langs = dokmeplus_get_languages();
        $payload = isset( $all_langs[ $language ] ) ? $all_langs[ $language ] : $all_langs['en'];
    } else {
        $payload = [];
    }

    wp_send_json_success( [ 'translations' => $payload ] );
}


/**
 * Backward-compatible translation helper (fallback)
 * If you prefer WP i18n, you can replace dokmeplus_t() with __()/esc_html__ etc.
 */
if ( ! function_exists( 'dokmeplus_t' ) ) {
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
        return $strings[ $key ] ?? $key;
    }
}

/* ---------------------------
 * Constants & Defaults
---------------------------- */
if ( ! defined( 'DOKMEPLUS_GITHUB_API' ) ) {
    define( 'DOKMEPLUS_GITHUB_API', 'https://api.github.com/repos/hajirahimi-ir/dokmeplus/releases/latest' );
}
if ( ! defined( 'DOKMEPLUS_LICENSE_API' ) ) {
    define( 'DOKMEPLUS_LICENSE_API', 'https://hamtamehr.ir/wp-json/hamtamehr/v1/check-license' );
}
if ( ! defined( 'DOKMEPLUS_TRANSIENT_LICENSE' ) ) {
    define( 'DOKMEPLUS_TRANSIENT_LICENSE', 'dokmeplus_license_check' );
}

/* ---------------------------
 * Load textdomain (i18n)
---------------------------- */
add_action( 'plugins_loaded', function() {
    load_plugin_textdomain( 'dokmeplus', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
});

/* ---------------------------
 * Handle delete & copy actions (admin_init)
 *
 * We keep these actions here to ensure they run before any output is sent.
 * They redirect back to the list page with ?msg=...
---------------------------- */
add_action( 'admin_init', 'dokmeplus_handle_admin_actions' );
function dokmeplus_handle_admin_actions() {

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // only run when we're on the plugin page - optional but safer
    $is_plugin_page = ( isset( $_GET['page'] ) && in_array( $_GET['page'], [ 'dokmeplus', 'dokmeplus_add', 'dokmeplus_settings', 'dokmeplus_about' ], true ) );
    // allow when page parameter missing (direct links) as well
    // if you prefer stricter check, uncomment next line:
    // if ( ! $is_plugin_page ) return;

    $all = get_option( 'dokmeplus_buttons', [] );
    $redirect = admin_url( 'admin.php?page=dokmeplus' );

    // ----- DELETE -----
    if ( isset( $_GET['delete_id'] ) ) {
        $delete_id = sanitize_text_field( wp_unslash( $_GET['delete_id'] ) );
        $nonce = isset( $_GET['_wpnonce'] ) ? wp_unslash( $_GET['_wpnonce'] ) : '';

        if ( ! wp_verify_nonce( $nonce, 'dokmeplus_delete_' . $delete_id ) ) {
            wp_safe_redirect( add_query_arg( 'msg', 'invalid', $redirect ) );
            exit;
        }

        if ( isset( $all[ $delete_id ] ) ) {
            unset( $all[ $delete_id ] );
            update_option( 'dokmeplus_buttons', $all );
            wp_safe_redirect( add_query_arg( 'msg', 'deleted', $redirect ) );
            exit;
        }

        wp_safe_redirect( add_query_arg( 'msg', 'notfound', $redirect ) );
        exit;
    }

    // ----- COPY -----
    if ( isset( $_GET['copy_id'] ) ) {

        $copy_id = sanitize_text_field( wp_unslash( $_GET['copy_id'] ) );
        $nonce = isset( $_GET['_wpnonce'] ) ? wp_unslash( $_GET['_wpnonce'] ) : '';

        if ( ! wp_verify_nonce( $nonce, 'dokmeplus_copy_' . $copy_id ) ) {
            wp_safe_redirect( add_query_arg( 'msg', 'invalid', $redirect ) );
            exit;
        }

        // Free version limit (3 items) unless license valid
        if ( ! dokmeplus_is_license_valid() && count( $all ) >= 3 ) {
            wp_safe_redirect( add_query_arg( 'msg', 'limit', $redirect ) );
            exit;
        }

        if ( isset( $all[ $copy_id ] ) ) {
            $original = $all[ $copy_id ];

            // generate id (use wp_generate_uuid4 if available)
            if ( function_exists( 'wp_generate_uuid4' ) ) {
                $new_id = wp_generate_uuid4();
            } else {
                $new_id = uniqid( 'dok_', true );
            }

            $new = $original;
            $new['title'] = ( $new['title'] ?? 'بدون عنوان' ) . ' (کپی)';

            $all[ $new_id ] = $new;
            update_option( 'dokmeplus_buttons', $all );

            wp_safe_redirect( add_query_arg( 'msg', 'copied', $redirect ) );
            exit;
        }

        wp_safe_redirect( add_query_arg( 'msg', 'notfound', $redirect ) );
        exit;
    }
}

/* ---------------------------
 * Admin Menus
---------------------------- */
add_action( 'admin_menu', function() {
    add_menu_page(
        dokmeplus_t('menu_main'),
        dokmeplus_t('menu_main'),
        'manage_options',
        'dokmeplus',
        'dokmeplus_list_page',
        'dashicons-screenoptions',
        26
    );

    add_submenu_page(
        'dokmeplus',
        dokmeplus_t('menu_add'),
        dokmeplus_t('menu_add'),
        'manage_options',
        'dokmeplus_add',
        'dokmeplus_form_page'
    );

    add_submenu_page(
        'dokmeplus',
        dokmeplus_t('menu_settings'),
        dokmeplus_t('menu_settings'),
        'manage_options',
        'dokmeplus_settings',
        'dokmeplus_settings_page'
    );
    

    add_submenu_page(
        'dokmeplus',
        dokmeplus_t('menu_about'), // عنوان صفحه
        dokmeplus_t('menu_about'), // متن منو هم داینامیک شد
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
add_action( 'init', function() {
    if ( is_admin() ) {
        // instantiate updater
        new DokmePlus_GitHub_Updater();
    }
});

class DokmePlus_GitHub_Updater {
    private $plugin_file;
    private $plugin_basename;
    private $github_api;

    public function __construct() {
        $this->plugin_file     = __FILE__;
        $this->plugin_basename = plugin_basename( $this->plugin_file ); // e.g. dokmeplus/dokmeplus.php
        $this->github_api      = DOKMEPLUS_GITHUB_API;

        add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_for_update' ] );
        add_filter( 'plugins_api', [ $this, 'plugin_info' ], 10, 3 );
    }

    public function check_for_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        // attempt remote request
        $headers = [
            'User-Agent' => 'DokmePlusUpdater/1.0; ' . get_bloginfo( 'url' ),
        ];

        // Optional: use personal access token stored in option (admin can set it in settings)
        $token = get_option( 'dokmeplus_github_token', '' );
        if ( ! empty( $token ) ) {
            $headers['Authorization'] = 'token ' . trim( $token );
        }

        $remote = wp_remote_get( $this->github_api, [
            'headers' => $headers,
            'timeout' => 15,
        ] );

        if ( is_wp_error( $remote ) ) {
            error_log( '[DokmePlus] GitHub updater request failed: ' . $remote->get_error_message() );
            return $transient;
        }

        $code = wp_remote_retrieve_response_code( $remote );
        if ( $code !== 200 ) {
            // possible rate limit or not found
            error_log( "[DokmePlus] GitHub updater returned HTTP {$code}" );
            return $transient;
        }

        $body = wp_remote_retrieve_body( $remote );
        $data = json_decode( $body );
        if ( empty( $data ) || empty( $data->tag_name ) ) {
            error_log( '[DokmePlus] GitHub updater: invalid response body' );
            return $transient;
        }

        $latest_version = ltrim( $data->tag_name, 'v' );

        // read plugin header version
        if ( ! function_exists( 'get_plugin_data' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugin_data = get_plugin_data( $this->plugin_file );
        $current_version = $plugin_data['Version'] ?? '0';

        if ( version_compare( $current_version, $latest_version, '<' ) ) {
            $package = $data->zipball_url ?? ( $data->tarball_url ?? '' );
            if ( empty( $package ) ) {
                error_log( '[DokmePlus] GitHub updater: no package URL' );
                return $transient;
            }

            $plugin = (object) [
                'slug'        => dirname( $this->plugin_basename ),
                'plugin'      => $this->plugin_basename,
                'new_version' => $latest_version,
                'url'         => $data->html_url ?? '',
                'package'     => $package,
            ];

            $transient->response[ $this->plugin_basename ] = $plugin;
        }

        return $transient;
    }

    public function plugin_info( $res, $action, $args ) {
        if ( 'plugin_information' !== $action || empty( $args->slug ) ) {
            return $res;
        }

        // compare slugs: WordPress passes the plugin file path (folder/plugin.php)
        if ( $args->slug !== $this->plugin_basename && $args->slug !== dirname( $this->plugin_basename ) ) {
            // not our plugin
            return $res;
        }

        $headers = [
            'User-Agent' => 'DokmePlusUpdater/1.0; ' . get_bloginfo( 'url' ),
        ];
        $token = get_option( 'dokmeplus_github_token', '' );
        if ( ! empty( $token ) ) {
            $headers['Authorization'] = 'token ' . trim( $token );
        }

        $remote = wp_remote_get( $this->github_api, [
            'headers' => $headers,
            'timeout' => 15,
        ] );

        if ( is_wp_error( $remote ) ) {
            return $res;
        }

        $code = wp_remote_retrieve_response_code( $remote );
        if ( $code !== 200 ) {
            return $res;
        }

        $data = json_decode( wp_remote_retrieve_body( $remote ) );
        if ( empty( $data ) ) {
            return $res;
        }

        // Build plugin info object for WP updater modal
        $res = (object) [
            'name'          => 'Dokme Plus',
            'slug'          => dirname( $this->plugin_basename ),
            'version'       => ltrim( $data->tag_name, 'v' ),
            'author'        => '<a href="https://hajirahimi.ir">Hajirahimi</a>',
            'homepage'      => $data->html_url ?? '',
            'sections'      => [
                'description' => $data->body ?? '',
            ],
            'download_link' => $data->zipball_url ?? '',
        ];

        return $res;
    }
}

/* ---------------------------
 * License System (improved error handling)
---------------------------- */
function dokmeplus_check_license_remote( $license ) {
    $license = trim( (string) $license );
    if ( $license === '' ) {
        return [ 'valid' => false, 'message' => dokmeplus_t( 'license_missing' ) ];
    }

    $domain = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : get_site_url();
    $response = wp_remote_post( DOKMEPLUS_LICENSE_API, [
        'timeout' => 15,
        'body'    => [ 'license' => $license, 'domain' => $domain ],
    ] );

    if ( is_wp_error( $response ) ) {
        error_log( '[DokmePlus] License API error: ' . $response->get_error_message() );
        return [ 'valid' => false, 'message' => dokmeplus_t( 'license_error' ) ];
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );
    if ( empty( $data ) || ! isset( $data['valid'] ) ) {
        return [ 'valid' => false, 'message' => dokmeplus_t( 'license_invalid' ) ];
    }

    return [
        'valid'   => (bool) $data['valid'],
        'message' => sanitize_text_field( $data['message'] ?? dokmeplus_t( 'license_invalid' ) ),
    ];
}

function dokmeplus_is_license_valid() {
    $cached = get_transient( DOKMEPLUS_TRANSIENT_LICENSE );
    if ( is_array( $cached ) && isset( $cached['valid'] ) ) {
        return (bool) $cached['valid'];
    }

    $license = get_option( 'dokmeplus_license', '' );
    $check = dokmeplus_check_license_remote( $license );
    set_transient( DOKMEPLUS_TRANSIENT_LICENSE, $check, 12 * HOUR_IN_SECONDS );
    return (bool) ( $check['valid'] ?? false );
}

/* ---------------------------
 * Keep plugin active after update (robust)
---------------------------- */
// store activation state on activation/deactivation
function dokmeplus_track_activation( $network_wide = false ) {
    // For single-site, update_option is fine; for multisite we still store option per-site.
    update_option( 'dokmeplus_should_be_active', true );
    update_option( 'dokmeplus_should_be_network_active', is_multisite() ? (bool) $network_wide : false );
}
function dokmeplus_track_deactivation( $network_wide = false ) {
    update_option( 'dokmeplus_should_be_active', false );
    update_option( 'dokmeplus_should_be_network_active', false );
}
register_activation_hook( __FILE__, 'dokmeplus_track_activation' );
register_deactivation_hook( __FILE__, 'dokmeplus_track_deactivation' );

// reactivate after core/plugin upgrader finishes
add_action( 'upgrader_process_complete', 'dokmeplus_reactivate_after_update', 10, 2 );
function dokmeplus_reactivate_after_update( $upgrader_object, $options ) {
    // only care about plugin updates
    if ( empty( $options['action'] ) || empty( $options['type'] ) ) {
        return;
    }
    if ( 'update' !== $options['action'] || 'plugin' !== $options['type'] ) {
        return;
    }
    if ( empty( $options['plugins'] ) || ! is_array( $options['plugins'] ) ) {
        return;
    }

    $plugin_basename = plugin_basename( __FILE__ );
    if ( ! in_array( $plugin_basename, $options['plugins'], true ) ) {
        return;
    }

    $should_be_active  = get_option( 'dokmeplus_should_be_active', false );
    $should_be_network = get_option( 'dokmeplus_should_be_network_active', false );

    if ( ! $should_be_active ) {
        return;
    }

    if ( ! function_exists( 'activate_plugin' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    // Reactivate network-wide if needed
    if ( is_multisite() && $should_be_network ) {
        if ( ! is_plugin_active_for_network( $plugin_basename ) ) {
            activate_plugin( $plugin_basename, '', true );
        }
    } else {
        if ( ! is_plugin_active( $plugin_basename ) ) {
            activate_plugin( $plugin_basename );
        }
    }

    // clean up stored flags (optional)
    delete_option( 'dokmeplus_should_be_active' );
    delete_option( 'dokmeplus_should_be_network_active' );
    update_option( 'dokmeplus_last_auto_reactivated', current_time( 'mysql' ) );
}

/* ---------------------------
 * Admin Pages (includes with safety)
---------------------------- */
function dokmeplus_list_page() {
    $path = plugin_dir_path( __FILE__ ) . 'list.php';
    if ( file_exists( $path ) ) {
        include $path;
    } else {
        echo '<div class="notice notice-warning"><p>List page template not found.</p></div>';
    }
}

function dokmeplus_form_page() {
    // Free version limit: 3 buttons
    $all = get_option( 'dokmeplus_buttons', [] );
    $edit_id = isset( $_GET['edit_id'] ) ? sanitize_text_field( wp_unslash( $_GET['edit_id'] ) ) : '';

    // If license is not valid and the user already has 3 or more buttons and is trying to add a new one
    if ( ! dokmeplus_is_license_valid() && count( $all ) >= 3 && '' === $edit_id ) {

        // Display warning instead of redirect
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>' . esc_html( dokmeplus_t('license_limit_title') ) . '</strong></p>';
        echo '<p>' . esc_html( dokmeplus_t('license_limit_desc') ) . '</p>';
        echo '<p><a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=dokmeplus_settings' ) ) . '">'
    . esc_html( dokmeplus_t('license_limit_button') ) . '</a></p>';
        echo '</div>';


        return; // Stop further execution
    }

    // Load the button form
    $path = plugin_dir_path( __FILE__ ) . 'form.php';
    if ( file_exists( $path ) ) {
        include $path;
    } else {
        echo '<div class="notice notice-warning"><p>Form page template not found.</p></div>';
    }
}


function dokmeplus_settings_page() {
    $path = plugin_dir_path( __FILE__ ) . 'settings.php';
    if ( file_exists( $path ) ) {
        include $path;
    } else {
        echo '<div class="wrap"><h1>' . esc_html( dokmeplus_t( 'settings_title' ) ) . '</h1>';
        echo '<p>Settings page template not found.</p></div>';
    }
}

function dokmeplus_about_page() {
    echo '<div class="wrap">';
    
    // عنوان صفحه درباره
    echo '<h1>' . esc_html(dokmeplus_t('menu_about')) . '</h1>';
    
    // متن درباره با لینک
    echo '<p>' . esc_html(dokmeplus_t('about_text')) . ' <a href="https://hajirahimi.ir" target="_blank">Hajirahimi</a>.</p>';
    
    echo '</div>';
}


function dokmeplus_live_page() {
    $path = plugin_dir_path( __FILE__ ) . 'live.php';
    if ( file_exists( $path ) ) {
        include $path;
    } else {
        echo '<div class="notice notice-warning"><p>Live preview template not found.</p></div>';
    }
}

/* ---------------------------
 * Shortcode for Displaying Buttons (secure)
---------------------------- */
add_shortcode( 'dokmeplus', function( $atts ) {
    $atts = shortcode_atts( [ 'id' => '' ], $atts, 'dokmeplus' );
    $all  = get_option( 'dokmeplus_buttons', [] );
    $id   = sanitize_text_field( $atts['id'] );

    if ( ! $id || ! isset( $all[ $id ] ) ) {
        return '';
    }

    $btn = $all[ $id ];

    // sanitize values and apply limits
    $color = isset( $btn['color'] ) ? sanitize_hex_color( $btn['color'] ) : '#0073aa';
    if ( ! $color ) {
        $color = '#0073aa';
    }
    $size = isset( $btn['size'] ) ? intval( $btn['size'] ) : 16;
    $size = max( 10, min( 48, $size ) ); // limit font size to reasonable range

    $style = sprintf(
        'background:%s; font-size:%dpx; color:#fff; padding:10px 20px; border-radius:5px; text-decoration:none;',
        esc_attr( $color ),
        intval( $size )
    );

    switch ( $btn['action'] ?? 'link' ) {
        case 'call':
            $href = 'tel:' . rawurlencode( sanitize_text_field( $btn['call_number'] ?? '' ) );
            break;
        case 'sms':
            $number = rawurlencode( sanitize_text_field( $btn['sms_number'] ?? '' ) );
            $href = 'sms:' . $number;
            if ( ! empty( $btn['sms_message'] ) ) {
                $href .= '?body=' . rawurlencode( $btn['sms_message'] );
            }
            break;
        default:
            $href = ! empty( $btn['link'] ) ? esc_url( $btn['link'] ) : '#';
            break;
    }

    $text = esc_html( $btn['text'] ?? $btn['title'] ?? '' );

    return sprintf( '<a href="%s" style="%s" target="_blank" rel="noopener noreferrer">%s</a>',
        esc_url( $href ),
        esc_attr( $style ),
        $text
    );
} );

