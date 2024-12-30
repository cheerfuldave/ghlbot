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


// Add custom styling for the chatbot
function ayd_chatbot_custom_styles() {
    $theme = get_option('ayd_chatbot_theme', 'light');
    $position = get_option('ayd_chatbot_position', 'bottom-right');
    $custom_css = get_option('ayd_chatbot_custom_css', '');
    
    $styles = "<style>
        .ayd-chatbot-container {
            position: fixed;
            z-index: 9999;
            max-width: 400px;
            background: " . ($theme === 'dark' ? '#2d2d2d' : '#ffffff') . ";
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            " . ayd_get_position_styles($position) . "
        }
        
        .ayd-chatbot-header {
            padding: 15px;
            background: " . ($theme === 'dark' ? '#1a1a1a' : '#f5f5f5') . ";
            border-radius: 10px 10px 0 0;
            color: " . ($theme === 'dark' ? '#ffffff' : '#333333') . ";
        }
        
        .ayd-chatbot-messages {
            padding: 15px;
            max-height: 400px;
            overflow-y: auto;
            color: " . ($theme === 'dark' ? '#ffffff' : '#333333') . ";
        }
        
        .ayd-chatbot-input {
            padding: 15px;
            border-top: 1px solid " . ($theme === 'dark' ? '#3d3d3d' : '#eeeeee') . ";
        }
        
        .ayd-chatbot-input input {
            width: 100%;
            padding: 8px;
            border: 1px solid " . ($theme === 'dark' ? '#3d3d3d' : '#dddddd') . ";
            border-radius: 5px;
            background: " . ($theme === 'dark' ? '#1a1a1a' : '#ffffff') . ";
            color: " . ($theme === 'dark' ? '#ffffff' : '#333333') . ";
        }
        
        .ayd-chatbot-toggle {
            position: fixed;
            " . ayd_get_position_styles($position, true) . "
            z-index: 9998;
            padding: 15px;
            border-radius: 50%;
            background: #007bff;
            color: #ffffff;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        $custom_css
    </style>";
    
    echo $styles;
}

function ayd_get_position_styles($position, $is_toggle = false) {
    $styles = array(
        'bottom-right' => array(
            'main' => 'bottom: 100px; right: 20px;',
            'toggle' => 'bottom: 20px; right: 20px;'
        ),
        'bottom-left' => array(
            'main' => 'bottom: 100px; left: 20px;',
            'toggle' => 'bottom: 20px; left: 20px;'
        )
    );
    
    return $styles[$position][$is_toggle ? 'toggle' : 'main'];
}

// Add settings for appearance customization
add_action('admin_init', 'ayd_chatbot_appearance_settings');
function ayd_chatbot_appearance_settings() {
    register_setting('ayd_chatbot_settings', 'ayd_chatbot_theme');
    register_setting('ayd_chatbot_settings', 'ayd_chatbot_position');
    register_setting('ayd_chatbot_settings', 'ayd_chatbot_custom_css');
    
    add_settings_section(
        'ayd_chatbot_appearance',
        'Chatbot Appearance',
        'ayd_chatbot_appearance_section_callback',
        'ayd-chatbot-settings'
    );
    
    add_settings_field(
        'ayd_chatbot_theme',
        'Theme',
        'ayd_chatbot_theme_field',
        'ayd-chatbot-settings',
        'ayd_chatbot_appearance'
    );
    
    add_settings_field(
        'ayd_chatbot_position',
        'Position',
        'ayd_chatbot_position_field',
        'ayd-chatbot-settings',
        'ayd_chatbot_appearance'
    );
    
    add_settings_field(
        'ayd_chatbot_custom_css',
        'Custom CSS',
        'ayd_chatbot_custom_css_field',
        'ayd-chatbot-settings',
        'ayd_chatbot_appearance'
    );
}

function ayd_chatbot_appearance_section_callback() {
    echo '<p>Customize the appearance of your chatbot</p>';
}

function ayd_chatbot_theme_field() {
    $theme = get_option('ayd_chatbot_theme', 'light');
    ?>
    <select name="ayd_chatbot_theme">
        <option value="light" <?php selected($theme, 'light'); ?>>Light Theme</option>
        <option value="dark" <?php selected($theme, 'dark'); ?>>Dark Theme</option>
    </select>
    <?php
}

function ayd_chatbot_position_field() {
    $position = get_option('ayd_chatbot_position', 'bottom-right');
    ?>
    <select name="ayd_chatbot_position">
        <option value="bottom-right" <?php selected($position, 'bottom-right'); ?>>Bottom Right</option>
        <option value="bottom-left" <?php selected($position, 'bottom-left'); ?>>Bottom Left</option>
    </select>
    <?php
}

function ayd_chatbot_custom_css_field() {
    $custom_css = get_option('ayd_chatbot_custom_css', '');
    ?>
    <textarea name="ayd_chatbot_custom_css" rows="5" cols="50" class="large-text code"><?php echo esc_textarea($custom_css); ?></textarea>
    <p class="description">Add custom CSS to further customize the chatbot appearance</p>
    <?php
}

// Add the chatbot container and toggle button to the footer
add_action('wp_footer', 'ayd_chatbot_frontend_display');
function ayd_chatbot_frontend_display() {
    ayd_chatbot_custom_styles();
    ?>
    <div class="ayd-chatbot-toggle" onclick="toggleChatbot()">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2Z" fill="currentColor"/>
        </svg>
    </div>
    <div class="ayd-chatbot-container" style="display: none;">
        <div class="ayd-chatbot-header">
            <h3 style="margin: 0;">Ask Your Database</h3>
        </div>
        <div class="ayd-chatbot-messages" id="ayd-messages">
            <!-- Messages will be inserted here -->
        </div>
        <div class="ayd-chatbot-input">
            <input type="text" placeholder="Type your question..." onkeypress="handleChatInput(event)">
        </div>
    </div>
    <script>
    function toggleChatbot() {
        const container = document.querySelector('.ayd-chatbot-container');
        container.style.display = container.style.display === 'none' ? 'block' : 'none';
    }
    
    function handleChatInput(event) {
        if (event.key === 'Enter') {
            const input = event.target;
            const message = input.value.trim();
            if (message) {
                addMessage('user', message);
                input.value = '';
                // Process the message and get response
                processMessage(message);
            }
        }
    }
    
    function addMessage(type, content) {
        const messagesDiv = document.getElementById('ayd-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `ayd-message ${type}-message`;
        messageDiv.textContent = content;
        messagesDiv.appendChild(messageDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
    
    function processMessage(message) {
        // Add loading message
        addMessage('system', 'Processing your request...');
        
        // Make API call to process message
        fetch('/wp-json/ayd-chatbot/v1/query', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            // Remove loading message
            const messagesDiv = document.getElementById('ayd-messages');
            messagesDiv.removeChild(messagesDiv.lastChild);
            
            // Add response
            addMessage('bot', data.response);
        })
        .catch(error => {
            console.error('Error:', error);
            // Remove loading message
            const messagesDiv = document.getElementById('ayd-messages');
            messagesDiv.removeChild(messagesDiv.lastChild);
            
            // Add error message
            addMessage('system', 'Sorry, there was an error processing your request.');
        });
    }
    </script>
    <?php
}

// Register REST API endpoint for processing messages
add_action('rest_api_init', function () {
    register_rest_route('ayd-chatbot/v1', '/query', array(
        'methods' => 'POST',
        'callback' => 'ayd_process_chatbot_query',
        'permission_callback' => function () {
            return true;
        }
    ));
});

function ayd_process_chatbot_query($request) {
    $parameters = $request->get_json_params();
    $message = sanitize_text_field($parameters['message']);
    
    // Process the message using the Ask Your Database API
    $api_key = get_option('ayd_api_key');
    $response = wp_remote_post('https://api.askyourdatabase.com/query', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode(array(
            'query' => $message
        ))
    ));
    
    if (is_wp_error($response)) {
        return new WP_Error('api_error', 'Failed to process query', array('status' => 500));
    }
    
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    return array(
        'response' => $body['response']
    );
}
