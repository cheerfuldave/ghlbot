<?php
namespace JuliusAI\ChatBot\Admin;

class Admin {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
        wp_enqueue_style('roboto-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'juliusAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    }

    public function create_admin_menu() {
        add_menu_page(
            'JuliusAI ChatBot',
            'JuliusAI ChatBot',
            'manage_options',
            'juliusai-chatbot',
            array($this, 'display_plugin_admin_page'),
            'dashicons-format-chat'
        );
    }

    public function display_plugin_admin_page() {
        include_once 'partials/settings-display.php';
    }
}