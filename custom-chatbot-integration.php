<?php
/*
Plugin Name: WordPress Ask Your Database Custom Chatbot
Description: Easily integrate customizable chatbots into your WordPress site using the Ask Your Database API.
Version: 2.3
Author: Ask Your Database
Author URI: https://askyourdatabase.com
*/

// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

// Add necessary CSS for the admin interface
function ayd_admin_styles() {
    if (isset($_GET['page']) && $_GET['page'] === 'ayd-chatbot-settings') {
        ?>
        <style>
            .ayd-container {
                max-width: 1200px;
                margin: 20px;
                padding: 20px;
                background: #fff;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            .ayd-header {
                display: flex;
                align-items: center;
                margin-bottom: 30px;
            }
            .ayd-header img {
                max-width: 200px;
                margin-right: 20px;
            }
            .ayd-section {
                margin-bottom: 30px;
                padding: 20px;
                background: #f9f9f9;
                border-radius: 5px;
            }
            .ayd-chatbot-config {
                margin-bottom: 20px;
                padding: 15px;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            .ayd-field {
                margin-bottom: 15px;
            }
            .ayd-field label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }
            .ayd-field input[type="text"],
            .ayd-field select {
                width: 100%;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            .ayd-instructions {
                background: #f0f9ff;
                padding: 15px;
                border-left: 4px solid #0073aa;
                margin-bottom: 20px;
            }
            .ayd-shortcode-example {
                background: #f5f5f5;
                padding: 10px;
                border-radius: 4px;
                font-family: monospace;
            }
        </style>
        <?php
    }
}
add_action('admin_head', 'ayd_admin_styles');

// Register plugin settings
add_action('admin_menu', 'ayd_chatbot_integration_menu');
function ayd_chatbot_integration_menu() {
    add_menu_page(
        'Chatbot Settings',
        'Chatbot Settings',
        'manage_options',
        'ayd-chatbot-settings',
        'ayd_chatbot_settings_page',
        'dashicons-format-chat'
    );
}

// Settings page implementation
function ayd_chatbot_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save settings if form is submitted
    if (isset($_POST['save_settings'])) {
        check_admin_referer('ayd_save_settings');
        
        $api_key = sanitize_text_field($_POST['api_key']);
        update_option('ayd_api_key', $api_key);

        $chatbots = [];
        if (isset($_POST['chatbots'])) {
            foreach ($_POST['chatbots'] as $index => $chatbot) {
                if (!empty($chatbot['name']) && !empty($chatbot['id'])) {
                    $chatbots[] = [
                        'name' => sanitize_text_field($chatbot['name']),
                        'id' => sanitize_text_field($chatbot['id'])
                    ];
                }
            }
        }
        update_option('ayd_chatbots', $chatbots);

        $user_permissions = isset($_POST['user_permissions']) ? array_map('absint', $_POST['user_permissions']) : [];
        update_option('ayd_user_permissions', $user_permissions);

        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }

    $api_key = get_option('ayd_api_key', '');
    $chatbots = get_option('ayd_chatbots', []);
    $user_permissions = get_option('ayd_user_permissions', []);

    ?>
    <div class="ayd-container">
        <div class="ayd-header">
            <img src="https://storage.googleapis.com/msgsndr/me0EnocxsZH10tkDWF5K/media/67253ad0eded3ba14cf5b7e0.png" alt="Ask Your Database Logo">
            <h1>Ask Your Database Chatbot Settings</h1>
        </div>

        <div class="ayd-instructions">
            <h2>Quick Start Guide</h2>
            <ol>
                <li>Enter your API key from Ask Your Database</li>
                <li>Configure up to 5 chatbots with unique names and IDs</li>
                <li>Select which WordPress users can access each chatbot</li>
                <li>Use the shortcode [chatbot name="Your Chatbot Name"] to embed the chatbot in your content</li>
            </ol>
            <h3>Iframe Integration</h3>
            <p>The chatbot will be embedded as an iframe with the following specifications:</p>
            <ul>
                <li>Base URL: https://www.askyourdatabase.com/dashboard/chatbot</li>
                <li>Parameters automatically included: chatbotid, user_name, user_email, api_key</li>
                <li>Default iframe size: 100% width, 500px height</li>
            </ul>
        </div>

        <form method="post" action="">
            <?php wp_nonce_field('ayd_save_settings'); ?>
            
            <div class="ayd-section">
                <h2>API Configuration</h2>
                <div class="ayd-field">
                    <label for="api_key">API Key:</label>
                    <input type="text" id="api_key" name="api_key" value="<?php echo esc_attr($api_key); ?>" required>
                </div>
            </div>

            <div class="ayd-section">
                <h2>Chatbot Configurations</h2>
                <?php for ($i = 0; $i < 5; $i++) : ?>
                    <div class="ayd-chatbot-config">
                        <h3>Chatbot <?php echo $i + 1; ?></h3>
                        <div class="ayd-field">
                            <label for="chatbots[<?php echo $i; ?>][name]">Name:</label>
                            <input type="text" 
                                   id="chatbots[<?php echo $i; ?>][name]" 
                                   name="chatbots[<?php echo $i; ?>][name]" 
                                   value="<?php echo isset($chatbots[$i]) ? esc_attr($chatbots[$i]['name']) : ''; ?>">
                        </div>
                        <div class="ayd-field">
                            <label for="chatbots[<?php echo $i; ?>][id]">ID:</label>
                            <input type="text" 
                                   id="chatbots[<?php echo $i; ?>][id]" 
                                   name="chatbots[<?php echo $i; ?>][id]" 
                                   value="<?php echo isset($chatbots[$i]) ? esc_attr($chatbots[$i]['id']) : ''; ?>">
                        </div>
                        <?php if (isset($chatbots[$i]) && !empty($chatbots[$i]['name'])) : ?>
                        <div class="ayd-shortcode-display">
                            <label>Shortcode:</label>
                            <code class="ayd-shortcode">[chatbot name="<?php echo esc_attr($chatbots[$i]['name']); ?>"]</code>
                            <button type="button" class="button button-secondary copy-shortcode" 
                                    data-shortcode='[chatbot name="<?php echo esc_attr($chatbots[$i]['name']); ?>"]'>
                                Copy Shortcode
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="ayd-section">
                <h2>User Permissions</h2>
                <div class="ayd-field">
                    <label for="user_permissions">Select Users:</label>
                    <select name="user_permissions[]" id="user_permissions" multiple style="height: 200px;">
                        <?php
                        $users = get_users(['fields' => ['ID', 'display_name']]);
                        foreach ($users as $user) {
                            $selected = in_array($user->ID, $user_permissions) ? 'selected' : '';
                            echo sprintf(
                                '<option value="%s" %s>%s</option>',
                                esc_attr($user->ID),
                                $selected,
                                esc_html($user->display_name)
                            );
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="ayd-section">
                <h2>Shortcode Usage</h2>
                <div class="ayd-shortcode-example">
                    [chatbot name="Your Chatbot Name"]
                </div>
                <p>Replace "Your Chatbot Name" with the name you configured above.</p>
            </div>

            <input type="submit" name="save_settings" class="button button-primary" value="Save Settings">
        </form>
<script>
jQuery(document).ready(function($) {
    $('.copy-shortcode').click(function() {
        var shortcode = $(this).data('shortcode');
        navigator.clipboard.writeText(shortcode).then(function() {
            var $button = $(this);
            $button.text('Copied!');
            setTimeout(function() {
                $button.text('Copy Shortcode');
            }, 2000);
        }.bind(this));
    });
});
</script>

    </div>
    <?php
}

// Shortcode implementation





function ayd_chatbot_shortcode($atts) {
    $atts = shortcode_atts([
        'name' => ''
    ], $atts, 'chatbot');
    
    if (empty($atts['name'])) {
        return '<p>Error: Chatbot name is required.</p>';
    }

    $chatbots = get_option('ayd_chatbots', []);
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
    
    if (!is_user_logged_in()) {
        return '<p>Please log in to access the chatbot.</p>';
    }
    
    $current_user = wp_get_current_user();
    $user_permissions = get_option('ayd_user_permissions', []);
    
    if (!in_array($current_user->ID, $user_permissions)) {
        return '<p>You do not have permission to access this chatbot.</p>';
    }

    if (empty($atts['name'])) {
        return '<p>Error: Chatbot name is required.</p>';
    }

    // Check if user is logged in
    if (!is_user_logged_in()) {
        return '<p>Please log in to access the chatbot.</p>';
    }

    $current_user = wp_get_current_user();
    $user_permissions = get_option('ayd_user_permissions', []);
    
    // Check if user has permission
    if (!in_array($current_user->ID, $user_permissions)) {
        return '<p>You do not have permission to access this chatbot.</p>';
    }

    $chatbots = get_option('ayd_chatbots', []);
    $api_key = get_option('ayd_api_key', '');

    // Find the chatbot by name
    $chatbot = null;
    foreach ($chatbots as $bot) {
        if ($bot['name'] === $atts['name']) {
            $chatbot = $bot;
            break;
        }
    }

    if (!$chatbot) {
        return '<p>Error: Chatbot not found.</p>';
    }

    // Generate iframe URL with all necessary parameters
    $iframe_url = add_query_arg([
        'chatbotid' => urlencode($chatbot['id']),
        'user_name' => urlencode($current_user->display_name),
        'user_email' => urlencode($current_user->user_email),
        'api_key' => urlencode($api_key)
    ], 'https://www.askyourdatabase.com/dashboard/chatbot');

    // Return the iframe with proper styling and error handling
    return sprintf(
        '<div class="ayd-chatbot-wrapper">
            <iframe src="%s" 
                    style="width: 100%%; height: 500px; border: none; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" 
                    title="%s"
                    loading="lazy"
                    allowfullscreen></iframe>
        </div>',
        esc_url($iframe_url),
        esc_attr("Ask Your Database Chatbot - {$chatbot['name']}")
    );
}

// Add frontend styles for the chatbot iframe
add_action('wp_head', 'ayd_frontend_styles');
function ayd_frontend_styles() {
    ?>
    <style>
        .ayd-chatbot-wrapper {
            margin: 20px 0;
            padding: 10px;
            background: #fff;
            border-radius: 4px;
        }
    
            .ayd-shortcode-display {
                margin-top: 15px;
                padding: 10px;
                background: #f5f5f5;
                border-radius: 4px;
            }
            .ayd-shortcode {
                display: inline-block;
                padding: 5px 10px;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 3px;
                margin-right: 10px;
                font-family: monospace;
            }
            .copy-shortcode {
                vertical-align: middle;
            }
</style>
    <?php
}
?>
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

// Get and update chatbot permissions
function ayd_get_chatbot_permissions($chatbot_id) {
    $permissions = get_option('ayd_chatbot_permissions_' . $chatbot_id, array());
    return $permissions;
}

function ayd_update_chatbot_permissions($chatbot_id, $permissions) {
    update_option('ayd_chatbot_permissions_' . $chatbot_id, $permissions);
}

// Check if user has permission for specific chatbot
function ayd_user_has_chatbot_permission($chatbot_id) {
    if (current_user_can('manage_options')) {
        return true;
    }
    $permissions = ayd_get_chatbot_permissions($chatbot_id);
    $current_user_id = get_current_user_id();
    return in_array($current_user_id, $permissions);
}

// Register plugin settings
function ayd_register_settings() {
    register_setting('ayd_chatbot_settings', 'ayd_api_key');
    register_setting('ayd_chatbot_settings', 'ayd_chatbots');
    register_setting('ayd_chatbot_settings', 'ayd_user_permissions');
}
add_action('admin_init', 'ayd_register_settings');

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

    // Check chatbot-specific permissions
    if (!ayd_user_has_chatbot_permission($found_chatbot['id'])) {
        return '<p>You do not have permission to access this chatbot.</p>';
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

add_shortcode('chatbot', 'ayd_chatbot_shortcode');
