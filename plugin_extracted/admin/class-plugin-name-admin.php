<?php
/**
 * The admin-specific functionality of the plugin
 */

class Plugin_Name_Admin {

    public function add_plugin_admin_menu() {
        add_menu_page(
            'Plugin Settings',
            'Plugin Settings',
            'manage_options',
            'plugin-settings',
            array($this, 'display_plugin_settings_page'),
            'dashicons-admin-generic',
            26
        );
    }

    public function display_plugin_settings_page() {
        include plugin_dir_path(__FILE__) . 'settings-page.html';
    }

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/admin.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            'AYD Chatbot Settings',
            'AYD Chatbot',
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_setup_page'),
            'dashicons-format-chat'
        );
    }

    public function display_plugin_setup_page() {
        include_once('partials/plugin-name-admin-display.php');
    }
}
