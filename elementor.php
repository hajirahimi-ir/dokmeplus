<?php
if ( ! defined('ABSPATH') ) exit;

/**
 * Elementor Integration for DokmePlus
 */

add_action('elementor/init', 'dokmeplus_register_category');
function dokmeplus_register_category() {
    \Elementor\Plugin::instance()->elements_manager->add_category(
        'dokmeplus-category',
        [
            'title' => 'دکمه پلاس',
            'icon'  => 'fa fa-plug'
        ],
        1
    );
}

add_action('elementor/widgets/register', 'dokmeplus_register_widget');
function dokmeplus_register_widget( $widgets_manager ) {
    require_once __DIR__ . '/elementor-widget-dokmeplus.php';
    $widgets_manager->register( new \DokmePlus_Elementor_Widget() );
}
