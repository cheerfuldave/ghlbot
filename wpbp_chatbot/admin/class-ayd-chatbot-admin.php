<?php
class AYD_Chatbot_Admin {
    private $plugin_name;
    private $version;

    public function __construct() {
        $this->plugin_name = 'ayd-chatbot';
        $this->version = AYD_CHATBOT_VERSION;
        add_action('admin_menu', array($this, 'create_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function create_admin_menu() {
        add_menu_page(
            'Chatbot Settings',
            'Chatbot Settings',
            'manage_options',
            'ayd-chatbot-settings',
            array($this, 'display_plugin_admin_page'),
            'dashicons-format-chat'
        );
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
        wp_enqueue_style('roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'aydAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }

    public function display_plugin_admin_page() {
        include_once 'partials/admin-display.php';
    }
}
