<?php
if ( ! defined('ABSPATH') ) exit;

class DokmePlus_Elementor_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'dokmeplus_widget';
    }

    public function get_title() {
        return 'دکمه پلاس';
    }

    public function get_icon() {
        return 'eicon-button';
    }

    public function get_categories() {
        return [ 'dokmeplus-category' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'dokmeplus_section',
            [
                'label' => 'انتخاب دکمه'
            ]
        );

        // دریافت لیست دکمه‌های ایجاد شده
        $buttons = get_option('dokmeplus_buttons', []);
        $options = [];

        if ($buttons) {
            foreach ($buttons as $id => $btn) {
                $options[$id] = $btn['title'];
            }
        }

        $this->add_control(
            'button_id',
            [
                'label'   => 'انتخاب دکمه',
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => $options,
                'default' => array_key_first($options),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $id = $settings['button_id'];

        $all = get_option('dokmeplus_buttons', []);

        if (!isset($all[$id])) {
            echo '<p style="color:red;">دکمه یافت نشد.</p>';
            return;
        }

        $btn = $all[$id];

        echo '<a href="' . esc_url($btn['link']) . '" 
                style="
                    background:' . esc_attr($btn['color']) . ';
                    color:#fff;
                    padding:10px 20px;
                    border-radius:5px;
                    font-size:' . intval($btn['size']) . 'px;
                    text-decoration:none;
                ">' 
                . esc_html($btn['text']) . 
             '</a>';
    }
}
