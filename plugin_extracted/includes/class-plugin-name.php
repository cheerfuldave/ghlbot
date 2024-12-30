<?php
/*
Plugin Name: Ask Your Database Custom Chatbot
Description: Easily integrate customizable chatbots into your WordPress site using the Ask Your Database API.
Version: 3.1
Author: Ask Your Database
Author URI: https://www.askyourdatabase.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Register plugin settings
add_action('admin_menu', 'ayd_chatbot_integration_menu');
function ayd_chatbot_integration_menu() {
    add_menu_page(
        'Chatbot Settings',
        'Chatbot Settings',
        'manage_options',
        'ayd-chatbot-settings',
        'ayd_chatbot_settings_page'
    );
}

// Create database table on activation
register_activation_hook(__FILE__, 'ayd_create_database_table');
function ayd_create_database_table() {
    global $wpdb;
    $table_name = esc_sql($wpdb->prefix . 'ayd_chatbots');

    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        chatbot_id VARCHAR(255) NOT NULL,
        assigned_users TEXT NOT NULL
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Settings page with logo, instructions, and shortcodes display
function ayd_chatbot_settings_page() {
    global $wpdb;
    $table_name = esc_sql($wpdb->prefix . 'ayd_chatbots');

    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        check_admin_referer('ayd_save_settings', 'ayd_nonce');

        $chatbots = isset($_POST['chatbots']) ? array_map(function ($chatbot) {
            return [
                'name' => sanitize_text_field(wp_unslash($chatbot['name'])),
                'chatbot_id' => sanitize_text_field(wp_unslash($chatbot['chatbot_id'])),
                'assigned_users' => isset($chatbot['assigned_users']) ? implode(',', array_map('intval', wp_unslash($chatbot['assigned_users']))) : ''
            ];
        }, wp_unslash($_POST['chatbots'])) : [];

        $wpdb->query($wpdb->prepare("TRUNCATE TABLE %s", $table_name));
        foreach ($chatbots as $chatbot) {
            $wpdb->insert($table_name, $chatbot);
        }

        echo '<div class="updated"><p>Settings saved successfully.</p></div>';
    }

    $cache_key = 'ayd_chatbots_all';
    $chatbots = wp_cache_get($cache_key);

    if (false === $chatbots) {
        $chatbots = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
        wp_cache_set($cache_key, $chatbots);
    }

    $users = get_users();

    echo '<img src="' . esc_url(plugins_url('assets/logo.png', __FILE__)) . '" alt="Ask Your Database Logo" style="width: 200px; margin-bottom: 20px;">';
    echo '<h1>Ask Your Database</h1>';
    echo '<p>Follow the steps below to configure your chatbots:</p>';
    echo '<ol>
            <li>Enter a unique name and chatbot ID for each chatbot.</li>
            <li>Assign users who can access each chatbot.</li>
            <li>Save the settings and use the shortcodes displayed below to embed the chatbot.</li>
          </ol>';

    echo '<form method="post">';
    wp_nonce_field('ayd_save_settings', 'ayd_nonce');

    foreach ($chatbots as $index => $chatbot) {
        $assigned_users = explode(',', $chatbot['assigned_users']);

        echo '<div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;">';
        echo '<h2>Chatbot ' . esc_html($index + 1) . '</h2>';
        echo '<p>Chatbot Name: <input type="text" name="chatbots[' . esc_attr($index) . '][name]" value="' . esc_attr($chatbot['name']) . '" style="width: 300px;" /></p>';
        echo '<p>Chatbot ID: <input type="text" name="chatbots[' . esc_attr($index) . '][chatbot_id]" value="' . esc_attr($chatbot['chatbot_id']) . '" style="width: 300px;" /></p>';
        echo '<p>Shortcode: <code>[ayd_chatbot id="' . esc_attr(sanitize_title($chatbot['name'])) . '"]</code></p>';

        echo '<label>Assign Users:</label><br>';
        echo '<select name="chatbots[' . esc_attr($index) . '][assigned_users][]" multiple style="width: 300px; height: 100px;">';
        foreach ($users as $user) {
            $selected = in_array($user->ID, $assigned_users) ? 'selected' : '';
            echo '<option value="' . esc_attr($user->ID) . '" ' . esc_attr($selected) . '>' . esc_html($user->display_name) . '</option>';
        }
        echo '</select><br>';
        echo '</div>';
    }

    echo '<input type="submit" value="Save Settings" class="button button-primary">';
    echo '</form>';
    echo '</div>';
}

// Shortcode to render chatbot
add_shortcode('ayd_chatbot', 'ayd_chatbot_shortcode');
function ayd_chatbot_shortcode($atts) {
    $atts = shortcode_atts(['id' => ''], $atts);
    $chatbot_name = $atts['id'];
    global $wpdb;
    $table_name = esc_sql($wpdb->prefix . 'ayd_chatbots');

    $chatbot = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE name = %s", $chatbot_name), ARRAY_A);

    if (!$chatbot) {
        return '<div class="ayd-error"><p>Chatbot not found.</p></div>';
    }

    // Header on the rendered page
    $output = '<div style="text-align: center; margin-bottom: 20px;">';
    $output .= '<h1>Ask Your Database - ' . esc_html($chatbot['name']) . '</h1>';
    $output .= '</div>';

    $output .= '<iframe src="https://www.askyourdatabase.com/chatbot/' . esc_attr($chatbot['chatbot_id']) . '" width="100%" height="900" style="border: none;"></iframe>';

    return $output;
}
