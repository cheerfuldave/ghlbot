<?php
/*
Plugin Name: Ask Your Database Custom Chatbot
Plugin URI: https://www.askyourdatabase.com
Description: A custom chatbot integration for WordPress websites with support for multiple chatbots
Version: 2.4
Author: Ask Your Database
Author URI: https://www.askyourdatabase.com
License: GPL v2 or later
Text Domain: ask-your-database
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('ASK_YOUR_DATABASE_VERSION', '2.4');
define('ASK_YOUR_DATABASE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ASK_YOUR_DATABASE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Activation Hook
register_activation_hook(__FILE__, 'ask_your_database_activate');
function ask_your_database_activate() {
    $default_options = array(
        'universal_api_key' => '',
        'chatbots' => array(
            array(
                'name' => 'Default Chatbot',
                'code' => '',
                'enabled' => true
            )
        )
    );
    add_option('ask_your_database_options', $default_options);
}

// Deactivation Hook
register_deactivation_hook(__FILE__, 'ask_your_database_deactivate');
function ask_your_database_deactivate() {
    // Cleanup if needed
}

// Register Settings
add_action('admin_init', 'ask_your_database_register_settings');
function ask_your_database_register_settings() {
    register_setting(
        'ask_your_database_options',
        'ask_your_database_options',
        'ask_your_database_validate_options'
    );
}

// Validate options
function ask_your_database_validate_options($input) {
    $valid = array();
    $valid['universal_api_key'] = sanitize_text_field($input['universal_api_key']);
    $valid['chatbots'] = array();
    
    if (isset($input['chatbots']) && is_array($input['chatbots'])) {
        foreach ($input['chatbots'] as $chatbot) {
            $valid['chatbots'][] = array(
                'name' => sanitize_text_field($chatbot['name']),
                'code' => sanitize_text_field($chatbot['code']),
                'enabled' => isset($chatbot['enabled']) ? true : false
            );
        }
    }
    
    return $valid;
}

// Add menu item
add_action('admin_menu', 'ask_your_database_add_menu');
function ask_your_database_add_menu() {
    add_menu_page(
        'Ask Your Database Chatbot',
        'AYD Chatbot',
        'manage_options',
        'ask-your-database',
        'ask_your_database_settings_page',
        'dashicons-format-chat'
    );
}

// Settings page
function ask_your_database_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    $options = get_option('ask_your_database_options');
    ?>
    <div class="wrap">
        <div class="ayd-header">
            <img src="https://storage.googleapis.com/msgsndr/me0EnocxsZH10tkDWF5K/media/67253ad0eded3ba14cf5b7e0.png" alt="Ask Your Database Logo">
            <h1>Ask Your Database Custom Chatbot</h1>
        </div>

        <form method="post" action="options.php">
            <?php settings_fields('ask_your_database_options'); ?>
            
            <div class="ayd-universal-api">
                <h2>Universal API Key</h2>
                <input type="text" name="ask_your_database_options[universal_api_key]" 
                    value="<?php echo esc_attr($options['universal_api_key']); ?>" class="regular-text">
            </div>

            <div class="ayd-chatbots">
                <h2>Chatbot Configuration</h2>
                <div class="chatbot-list">
                    <?php
                    if (!empty($options['chatbots'])) {
                        foreach ($options['chatbots'] as $index => $chatbot) {
                            ?>
                            <div class="chatbot-item">
                                <h3>Chatbot <?php echo $index + 1; ?></h3>
                                <table class="form-table">
                                    <tr>
                                        <th>Chatbot Name</th>
                                        <td>
                                            <input type="text" 
                                                name="ask_your_database_options[chatbots][<?php echo $index; ?>][name]" 
                                                value="<?php echo esc_attr($chatbot['name']); ?>" 
                                                class="regular-text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Chatbot Code</th>
                                        <td>
                                            <input type="text" 
                                                name="ask_your_database_options[chatbots][<?php echo $index; ?>][code]" 
                                                value="<?php echo esc_attr($chatbot['code']); ?>" 
                                                class="regular-text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Enable Chatbot</th>
                                        <td>
                                            <input type="checkbox" 
                                                name="ask_your_database_options[chatbots][<?php echo $index; ?>][enabled]" 
                                                <?php checked($chatbot['enabled'], true); ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Shortcode</th>
                                        <td>
                                            <div class="shortcode-box">
                                                [ask_your_database_chatbot id="<?php echo $index; ?>"]
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <button type="button" class="button add-chatbot">Add New Chatbot</button>
            </div>
            
            <?php submit_button(); ?>
        </form>
    </div>

    <style>
        .ayd-header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            margin-bottom: 20px;
        }
        .ayd-header img {
            max-height: 50px;
            vertical-align: middle;
            margin-right: 15px;
        }
        .ayd-header h1 {
            display: inline-block;
            vertical-align: middle;
            margin: 0;
            color: white;
        }
        .ayd-universal-api {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .chatbot-item {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .shortcode-box {
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            font-family: monospace;
        }
        .add-chatbot {
            margin: 20px 0;
        }
        .form-table th {
            width: 200px;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        $('.add-chatbot').click(function() {
            var chatbotCount = $('.chatbot-item').length;
            var template = `
                <div class="chatbot-item">
                    <h3>Chatbot ${chatbotCount + 1}</h3>
                    <table class="form-table">
                        <tr>
                            <th>Chatbot Name</th>
                            <td>
                                <input type="text" 
                                    name="ask_your_database_options[chatbots][${chatbotCount}][name]" 
                                    class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th>Chatbot Code</th>
                            <td>
                                <input type="text" 
                                    name="ask_your_database_options[chatbots][${chatbotCount}][code]" 
                                    class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th>Enable Chatbot</th>
                            <td>
                                <input type="checkbox" 
                                    name="ask_your_database_options[chatbots][${chatbotCount}][enabled]" 
                                    checked>
                            </td>
                        </tr>
                        <tr>
                            <th>Shortcode</th>
                            <td>
                                <div class="shortcode-box">
                                    [ask_your_database_chatbot id="${chatbotCount}"]
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            `;
            $('.chatbot-list').append(template);
        });
    });
    </script>
    <?php
}

// Add shortcode
add_shortcode('ask_your_database_chatbot', 'ask_your_database_shortcode');
function ask_your_database_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0
    ), $atts);
    
    $options = get_option('ask_your_database_options');
    $chatbot_id = intval($atts['id']);
    
    if (!isset($options['chatbots'][$chatbot_id]) || !$options['chatbots'][$chatbot_id]['enabled']) {
        return '';
    }
    
    $chatbot = $options['chatbots'][$chatbot_id];
    $api_key = esc_attr($options['universal_api_key']);
    
    ob_start();
    ?>
    <div class="ask-your-database-chatbot" 
        data-api-key="<?php echo $api_key; ?>"
        data-chatbot-code="<?php echo esc_attr($chatbot['code']); ?>"
        data-chatbot-name="<?php echo esc_attr($chatbot['name']); ?>">
    </div>
    <?php
    return ob_get_clean();
}

// Frontend scripts
add_action('wp_enqueue_scripts', 'ask_your_database_frontend_scripts');
function ask_your_database_frontend_scripts() {
    if (has_shortcode(get_post()->post_content, 'ask_your_database_chatbot')) {
        wp_enqueue_script(
            'ask-your-database-frontend',
            plugins_url('js/frontend.js', __FILE__),
            array('jquery'),
            ASK_YOUR_DATABASE_VERSION,
            true
        );
    }
}
