<?php
/*
Plugin Name: WordPress Ask Your Database Custom Chatbot
Description: Easily integrate customizable chatbots into your WordPress site using the Ask Your Database API.
Version: 2.3
Author: Ask Your Database
Author URI: https://askyourdatabase.com
*/

// Add admin menu
function chatbot_admin_menu() {
    add_menu_page(
        'Chatbot Settings',
        'Chatbot Settings',
        'manage_options',
        'chatbot-settings',
        'chatbot_settings_page',
        'dashicons-format-chat'
    );
}
add_action('admin_menu', 'chatbot_admin_menu');

// Register plugin settings
function chatbot_register_settings() {
    register_setting('chatbot-settings-group', 'chatbot_api_key');
    for ($i = 1; $i <= 5; $i++) {
        register_setting('chatbot-settings-group', 'chatbot_name_' . $i);
        register_setting('chatbot-settings-group', 'chatbot_id_' . $i);
        register_setting('chatbot-settings-group', 'chatbot_users_' . $i);
    }
}
add_action('admin_init', 'chatbot_register_settings');

// Admin styles
function chatbot_admin_styles() {
    if (isset($_GET["page"]) && $_GET["page"] === "chatbot-settings") {
        ?>
        <style>
            .chatbot-settings-container {
                max-width: 800px;
                margin: 20px auto;
                padding: 20px;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .chatbot-header {
                border-bottom: 2px solid #eee;
                padding-bottom: 15px;
                margin-bottom: 20px;
            }
            .chatbot-section {
                margin-bottom: 30px;
            }
            .chatbot-config {
                background: #f9f9f9;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .chatbot-instructions {
                background: #f5f5f5;
                padding: 15px;
                border-left: 4px solid #0073aa;
                margin-bottom: 20px;
            }
            input[type="text"] {
                width: 100%;
                padding: 8px;
                margin: 5px 0;
            }
            .submit-button {
                margin-top: 20px;
            }
        </style>
        <?php
    }
}
add_action('admin_head', 'chatbot_admin_styles');

// Settings page
function chatbot_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
    ?>
    <div class="chatbot-settings-container">
        <div class="chatbot-header">
            <h2>Chatbot Settings</h2>
        </div>
        
        <form method="post" action="options.php">
            <?php
            settings_fields('chatbot-settings-group');
            do_settings_sections('chatbot-settings-group');
            ?>
            
            <div class="chatbot-section">
                <h3>API Configuration</h3>
                <div class="chatbot-config">
                    <label for="chatbot_api_key">API Key:</label>
                    <input type="text" id="chatbot_api_key" name="chatbot_api_key" 
                           value="<?php echo esc_attr(get_option('chatbot_api_key')); ?>" />
                </div>
            </div>

            <div class="chatbot-section">
                <h3>Chatbot Configurations</h3>
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    ?>
                    <div class="chatbot-config">
                        <h4>Chatbot <?php echo $i; ?></h4>
                        <label for="chatbot_name_<?php echo $i; ?>">Name:</label>
                        <input type="text" id="chatbot_name_<?php echo $i; ?>" 
                               name="chatbot_name_<?php echo $i; ?>" 
                               value="<?php echo esc_attr(get_option('chatbot_name_' . $i)); ?>" />
                        
                        <label for="chatbot_id_<?php echo $i; ?>">ID:</label>
                        <input type="text" id="chatbot_id_<?php echo $i; ?>" 
                               name="chatbot_id_<?php echo $i; ?>" 
                               value="<?php echo esc_attr(get_option('chatbot_id_' . $i)); ?>" />
                        
                        <label for="chatbot_users_<?php echo $i; ?>">Allowed Users (comma-separated):</label>
                        <input type="text" id="chatbot_users_<?php echo $i; ?>" 
                               name="chatbot_users_<?php echo $i; ?>" 
                               value="<?php echo esc_attr(get_option('chatbot_users_' . $i)); ?>" />
                    </div>
                    <?php
                }
                ?>
            </div>

            <div class="chatbot-instructions">
                <p>Use shortcode [chatbot name="Your Chatbot Name"] to display the chatbot in your content.</p>
            </div>

            <?php submit_button('Save Settings', 'primary submit-button'); ?>
        </form>
    </div>
    <?php
}

// Frontend styles
function chatbot_frontend_styles() {
    ?>
    <style>
        .chatbot-wrapper {
            width: 100%;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .chatbot-wrapper iframe {
            border: none;
            width: 100%;
            height: 500px;
        }
    </style>
    <?php
}
add_action('wp_head', 'chatbot_frontend_styles');

// Shortcode functionality
function chatbot_shortcode($atts) {
    $atts = shortcode_atts(array(
        'name' => '',
    ), $atts);

    if (empty($atts['name'])) {
        return 'Error: Chatbot name is required.';
    }

    // Check if user is logged in
    if (!is_user_logged_in()) {
        return 'Please log in to use the chatbot.';
    }

    $current_user = wp_get_current_user();
    $chatbot_id = '';
    $allowed_users = '';

    // Find matching chatbot configuration
    for ($i = 1; $i <= 5; $i++) {
        if (get_option('chatbot_name_' . $i) === $atts['name']) {
            $chatbot_id = get_option('chatbot_id_' . $i);
            $allowed_users = get_option('chatbot_users_' . $i);
            break;
        }
    }

    if (empty($chatbot_id)) {
        return 'Error: Chatbot not found.';
    }

    // Check user permissions
    if (!empty($allowed_users)) {
        $allowed_users_array = array_map('trim', explode(',', $allowed_users));
        if (!in_array($current_user->user_login, $allowed_users_array)) {
            return 'You do not have permission to use this chatbot.';
        }
    }

    $api_key = get_option('chatbot_api_key');
    if (empty($api_key)) {
        return 'Error: API key not set.';
    }

    // Generate iframe URL with parameters
    $iframe_url = add_query_arg(array(
        'chatbotid' => urlencode($chatbot_id),
        'user_name' => urlencode($current_user->display_name),
        'user_email' => urlencode($current_user->user_email),
        'api_key' => urlencode($api_key)
    ), 'https://www.askyourdatabase.com/dashboard/chatbot');

    // Return iframe wrapped in styled div
    return '<div class="chatbot-wrapper"><iframe src="' . esc_url($iframe_url) . '"></iframe></div>';
}
add_shortcode('chatbot', 'chatbot_shortcode');
