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
    $atts = shortcode_atts(array(
        'name' => '',
    ), $atts, 'chatbot');

    if (empty($atts['name'])) {
        return '<p>Error: Chatbot name is required.</p>';
    }

    if (!is_user_logged_in()) {
        return '<p>Please log in to access the chatbot.</p>';
    }

    $chatbots = get_option('ayd_chatbots', array());
    $api_key = get_option('ayd_api_key', '');
    
    // Find the chatbot by name
    $found_chatbot = null;
    foreach ($chatbots as $chatbot) {
        if ($chatbot['name'] === $atts['name']) {
            $found_chatbot = $chatbot;
            break;
        }
    }

    if (!$found_chatbot) {
        return '<p>Error: Chatbot "' . esc_html($atts['name']) . '" not found.</p>';
    }

    $current_user = wp_get_current_user();
    
    // Generate session URL
    $session_url = ayd_generate_session_url(
        $api_key,
        $found_chatbot['id'],
        $current_user->display_name,
        $current_user->user_email
    );

    if (!$session_url) {
        return '<p>Error: Unable to generate chatbot session.</p>';
    }

    // Return the iframe with the session URL
    return sprintf(
        '<div class="ayd-chatbot-wrapper"><iframe src="%s" style="width: 100%%; height: 600px; border: none;" title="Chatbot"></iframe></div>',
        esc_url($session_url)
    );
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
