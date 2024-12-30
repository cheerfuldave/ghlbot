<?php
/*
Plugin Name: WordPress Ask Your Database Custom Chatbot
Description: Easily integrate customizable chatbots into your WordPress site using the Ask Your Database API.
Version: 2.0
Author: Ask Your Database
Author URI: https://askyourdatabase.com
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

// Settings page with enhanced instructions and full user list for selection
function ayd_chatbot_settings_page() {
    if (isset($_POST['save_settings'])) {
        $chatbots = isset($_POST['chatbots']) ? array_map(function($chatbot) {
            return array(
                'name' => sanitize_text_field($chatbot['name']),
                'id' => sanitize_text_field($chatbot['id']),
            );
        }, $_POST['chatbots']) : array();

        $selected_users = isset($_POST['selected_users']) ? array_map('intval', $_POST['selected_users']) : array();

        update_option('ayd_chatbots', $chatbots);
        update_option('ayd_api_key', sanitize_text_field($_POST['api_key']));
        update_option('ayd_selected_users', $selected_users);

        echo '<div class="updated"><p>Settings saved successfully.</p></div>';
    }

    $api_key = get_option('ayd_api_key', '');
    $chatbots = get_option('ayd_chatbots', array());
    $selected_users = get_option('ayd_selected_users', array());

    if (!is_array($chatbots)) {
        $chatbots = array();
    }

    echo '
    <div class="wrap">
        <h1>Ask Your Database Chatbot Integration</h1>
        <p>Follow the steps below to configure your chatbots:</p>
        <ol>
            <li>
                Enter your <strong>API Key</strong>, which can be found on your 
                <a href="https://www.askyourdatabase.com/dashboard/api-key" target="_blank">Ask Your Database API Keys page</a>.
            </li>
            <li>
                Go to the <a href="https://www.askyourdatabase.com/dashboard/" target="_blank">Chatbot Dashboard</a>, click 
                <strong>Integrate</strong> on the selected chatbot, and retrieve the <strong>Chatbot ID</strong>.
            </li>
            <li>
                Provide a unique name and Chatbot ID for each chatbot you want to configure.
            </li>
            <li>
                Select the users who can access the configured chatbots.
            </li>
            <li>
                Save the settings and use the shortcodes displayed below to embed the chatbot into your WordPress site.
            </li>
        </ol>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th><label for="api_key">API Key</label></th>
                    <td><input type="text" id="api_key" name="api_key" value="' . esc_attr($api_key) . '" class="regular-text"></td>
                </tr>
                <tr>
                    <th colspan="2"><h3>Chatbot Configurations</h3></th>
                </tr>';

    for ($i = 0; $i < 5; $i++) {
        $chatbot_label = "Chatbot " . ($i + 1);
        $name = isset($chatbots[$i]['name']) ? $chatbots[$i]['name'] : '';
        $id = isset($chatbots[$i]['id']) ? $chatbots[$i]['id'] : '';
        echo '
                <tr>
                    <th colspan="2"><h4>' . $chatbot_label . '</h4></th>
                </tr>
                <tr>
                    <th><label for="chatbots[' . $i . '][name]">Chatbot Name</label></th>
                    <td><input type="text" name="chatbots[' . $i . '][name]" value="' . esc_attr($name) . '" class="regular-text"></td>
                </tr>
                <tr>
                    <th><label for="chatbots[' . $i . '][id]">Chatbot ID</label></th>
                    <td><input type="text" name="chatbots[' . $i . '][id]" value="' . esc_attr($id) . '" class="regular-text"></td>
                </tr>';
    }

    // Full user selection list
    echo '
                <tr>
                    <th><label for="selected_users">Select Users</label></th>
                    <td>
                        <select id="selected_users" name="selected_users[]" multiple style="width: 100%;">';

    $users = get_users(array('fields' => array('ID', 'display_name', 'user_email')));
    foreach ($users as $user) {
        $selected = in_array($user->ID, $selected_users) ? 'selected' : '';
        echo '<option value="' . esc_attr($user->ID) . '" ' . $selected . '>' . esc_html($user->display_name) . ' (' . esc_html($user->user_email) . ')</option>';
    }

    echo '
                        </select>
                        <p class="description">Hold down the Ctrl (Windows) or Command (Mac) key to select multiple users.</p>
                    </td>
                </tr>
            </table>
            <p><input type="submit" name="save_settings" value="Save Settings" class="button-primary"></p>
        </form>';

    // Display shortcodes for configured chatbots
    echo '<h2>Available Chatbot Shortcodes</h2>';
    echo '<p>Use the following shortcodes to display your configured chatbots:</p>';
    echo '<ul>';
    foreach ($chatbots as $chatbot) {
        if (!empty($chatbot['name'])) {
            echo '<li><code>[chatbot chatbot="' . esc_html($chatbot['name']) . '"]</code></li>';
        }
    }
    echo '</ul>';

    echo '</div>';
}

// Shortcode to display chatbot only for selected users
function ayd_chatbot_shortcode($atts) {
    $atts = shortcode_atts(
        array('chatbot' => ''),
        $atts,
        'chatbot'
    );

    $chatbot_name = sanitize_text_field($atts['chatbot']);
    $chatbots = get_option('ayd_chatbots', array());
    $selected_users = get_option('ayd_selected_users', array());

    $chatbot_config = array_filter($chatbots, function ($chatbot) use ($chatbot_name) {
        return isset($chatbot['name']) && $chatbot['name'] === $chatbot_name;
    });

    $user = wp_get_current_user();
    if (empty($chatbots) || empty($chatbot_config) || !$user->exists() || !in_array($user->ID, $selected_users)) {
        return '<p>You do not have access to this chatbot.</p>';
    }

    $api_key = get_option('ayd_api_key', '');
    if (!$api_key) {
        return '<p>API Key is missing. Please configure it in the settings.</p>';
    }

    $chatbot_id = reset($chatbot_config)['id'];

    $session_url = ayd_generate_session_url($api_key, $chatbot_id, $user->display_name, $user->user_email);

    if (!$session_url) {
        return '<p>Unable to generate chatbot session. Please try again later.</p>';
    }

    return '
    <div class="chatbot-container">
        <h3 style="text-align: center; font-size: 20px; margin-bottom: 10px;">Ask Your Database - ' . esc_html($chatbot_name) . '</h3>
        <iframe src="' . esc_url($session_url) . '" style="width: 100%; height: 600px; border: none;"></iframe>
    </div>';
}
add_shortcode('chatbot', 'ayd_chatbot_shortcode');

// Generate session URL
function ayd_generate_session_url($api_key, $chatbot_id, $name, $email) {
    $response = wp_remote_post('https://www.askyourdatabase.com/api/chatbot/v2/session', array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
        'body' => json_encode(array(
            'chatbotid' => $chatbot_id,
            'name' => $name,
            'email' => $email,
        )),
    ));

    if (is_wp_error($response)) {
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body['url'] ?? false;
}

// Enqueue basic styles
add_action('wp_head', function () {
    echo '
    <style>
    .chatbot-container {
        max-width: 800px;
        margin: 20px auto;
        border: 1px solid #ccc;
        border-radius: 10px;
        overflow: hidden;
    }
    </style>';
});
?>


// Add chatbot appearance settings
add_action('admin_init', 'ayd_chatbot_appearance_settings');
function ayd_chatbot_appearance_settings() {
    register_setting('ayd_chatbot_settings', 'ayd_chatbot_theme');
    register_setting('ayd_chatbot_settings', 'ayd_chatbot_position');

    add_settings_section(
        'ayd_chatbot_appearance_section',
        'Chatbot Appearance',
        'ayd_chatbot_appearance_section_callback',
        'ayd-chatbot-settings'
    );

    add_settings_field(
        'ayd_chatbot_theme',
        'Chatbot Theme',
        'ayd_chatbot_theme_callback',
        'ayd-chatbot-settings',
        'ayd_chatbot_appearance_section'
    );

    add_settings_field(
        'ayd_chatbot_position',
        'Chatbot Position',
        'ayd_chatbot_position_callback',
        'ayd-chatbot-settings',
        'ayd_chatbot_appearance_section'
    );
}

function ayd_chatbot_appearance_section_callback() {
    echo '<p>Customize the appearance of your chatbot.</p>';
}

function ayd_chatbot_theme_callback() {
    $theme = get_option('ayd_chatbot_theme', 'light');
    echo '<select name="ayd_chatbot_theme">
            <option value="light" ' . selected($theme, 'light', false) . '>Light</option>
            <option value="dark" ' . selected($theme, 'dark', false) . '>Dark</option>
          </select>';
}

function ayd_chatbot_position_callback() {
    $position = get_option('ayd_chatbot_position', 'bottom-right');
    echo '<select name="ayd_chatbot_position">
            <option value="bottom-right" ' . selected($position, 'bottom-right', false) . '>Bottom Right</option>
            <option value="bottom-left" ' . selected($position, 'bottom-left', false) . '>Bottom Left</option>
          </select>';
}
