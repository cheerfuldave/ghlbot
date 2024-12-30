<?php
/*
Plugin Name: Ask Your Database WP Chatbot Enhanced
Description: Fully functional chatbot integration with complete settings, shortcodes, and instructions
Version: 2.2
Author: Ask Your Database
Author URI: https://askyourdatabase.com
*/

if (!defined('WPINC')) {
    die;
}

// Register and save plugin settings
function ayd_register_settings() {
    register_setting('ayd_chatbot_settings', 'ayd_api_key');
    register_setting('ayd_chatbot_settings', 'ayd_bot_id');
    register_setting('ayd_chatbot_settings', 'ayd_position');
    register_setting('ayd_chatbot_settings', 'ayd_theme');
    register_setting('ayd_chatbot_settings', 'ayd_custom_css');
    register_setting('ayd_chatbot_settings', 'ayd_greeting');
    register_setting('ayd_chatbot_settings', 'ayd_window_size');
    register_setting('ayd_chatbot_settings', 'ayd_chatbot_name');
}
add_action('admin_init', 'ayd_register_settings');

// Add menu item
function ayd_add_menu_item() {
    add_menu_page(
        'Chatbot Settings',
        'Chatbot Settings',
        'manage_options',
        'ayd-chatbot-settings',
        'ayd_settings_page',
        'dashicons-format-chat'
    );
}
add_action('admin_menu', 'ayd_add_menu_item');

// Settings page function
function ayd_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Get saved values
    $api_key = get_option('ayd_api_key', '');
    $bot_id = get_option('ayd_bot_id', '');
    $position = get_option('ayd_position', 'bottom-right');
    $theme = get_option('ayd_theme', 'light');
    $custom_css = get_option('ayd_custom_css', '');
    $greeting = get_option('ayd_greeting', 'Hello! How can I help you?');
    $window_size = get_option('ayd_window_size', 'medium');
    $chatbot_name = get_option('ayd_chatbot_name', 'Chatbot');

    // Save settings
    if (isset($_POST['submit'])) {
        if (check_admin_referer('ayd_save_settings', 'ayd_nonce')) {
            update_option('ayd_api_key', sanitize_text_field($_POST['api_key']));
            update_option('ayd_bot_id', sanitize_text_field($_POST['bot_id']));
            update_option('ayd_position', sanitize_text_field($_POST['position']));
            update_option('ayd_theme', sanitize_text_field($_POST['theme']));
            update_option('ayd_custom_css', sanitize_textarea_field($_POST['custom_css']));
            update_option('ayd_greeting', sanitize_text_field($_POST['greeting']));
            update_option('ayd_window_size', sanitize_text_field($_POST['window_size']));
            update_option('ayd_chatbot_name', sanitize_text_field($_POST['chatbot_name']));
            
            echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
        }
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('ayd_save_settings', 'ayd_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">API Key (Required)</th>
                    <td>
                        <input type="text" name="api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text" required>
                        <p class="description">Enter your Ask Your Database API key</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Bot ID (Required)</th>
                    <td>
                        <input type="text" name="bot_id" value="<?php echo esc_attr($bot_id); ?>" class="regular-text" required>
                        <p class="description">Enter your chatbot ID</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Chatbot Name</th>
                    <td>
                        <input type="text" name="chatbot_name" value="<?php echo esc_attr($chatbot_name); ?>" class="regular-text">
                        <p class="description">Enter a name for your chatbot</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Position</th>
                    <td>
                        <select name="position">
                            <option value="bottom-right" <?php selected($position, 'bottom-right'); ?>>Bottom Right</option>
                            <option value="bottom-left" <?php selected($position, 'bottom-left'); ?>>Bottom Left</option>
                            <option value="top-right" <?php selected($position, 'top-right'); ?>>Top Right</option>
                            <option value="top-left" <?php selected($position, 'top-left'); ?>>Top Left</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Theme</th>
                    <td>
                        <select name="theme">
                            <option value="light" <?php selected($theme, 'light'); ?>>Light</option>
                            <option value="dark" <?php selected($theme, 'dark'); ?>>Dark</option>
                            <option value="custom" <?php selected($theme, 'custom'); ?>>Custom</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Window Size</th>
                    <td>
                        <select name="window_size">
                            <option value="small" <?php selected($window_size, 'small'); ?>>Small</option>
                            <option value="medium" <?php selected($window_size, 'medium'); ?>>Medium</option>
                            <option value="large" <?php selected($window_size, 'large'); ?>>Large</option>
                            <option value="full" <?php selected($window_size, 'full'); ?>>Full Screen</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Greeting Message</th>
                    <td>
                        <input type="text" name="greeting" value="<?php echo esc_attr($greeting); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Custom CSS</th>
                    <td>
                        <textarea name="custom_css" rows="5" cols="50" class="large-text code"><?php echo esc_textarea($custom_css); ?></textarea>
                        <p class="description">Add custom CSS to style your chatbot</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Save Settings'); ?>
        </form>
        
        <hr>
        
        <h2>How to Use</h2>
        <div class="ayd-instructions">
            <h3>Basic Usage</h3>
            <p>Use this shortcode to add the chatbot to any page or post:</p>
            <code>[ask_your_database_chatbot]</code>
            
            <h3>Advanced Usage</h3>
            <p>Add this PHP code to your template:</p>
            <code><?php echo esc_html('<?php echo do_shortcode("[ask_your_database_chatbot]"); ?>'); ?></code>
            
            <h3>Available Shortcode Parameters</h3>
            <ul>
                <li><code>[ask_your_database_chatbot theme="dark"]</code> - Override theme setting</li>
                <li><code>[ask_your_database_chatbot position="top-right"]</code> - Override position setting</li>
                <li><code>[ask_your_database_chatbot greeting="Welcome!"]</code> - Override greeting message</li>
            </ul>
        </div>
    </div>
    <?php
}

// Add shortcode function
function ayd_chatbot_shortcode($atts) {
    // Get saved settings
    $api_key = get_option('ayd_api_key', '');
    $bot_id = get_option('ayd_bot_id', '');
    $position = get_option('ayd_position', 'bottom-right');
    $theme = get_option('ayd_theme', 'light');
    $greeting = get_option('ayd_greeting', 'Hello! How can I help you?');
    $window_size = get_option('ayd_window_size', 'medium');
    $chatbot_name = get_option('ayd_chatbot_name', 'Chatbot');
    
    // Allow shortcode attributes to override settings
    $args = shortcode_atts(array(
        'position' => $position,
        'theme' => $theme,
        'greeting' => $greeting,
        'size' => $window_size,
        'name' => $chatbot_name
    ), $atts);
    
    // Check for required settings
    if (empty($api_key) || empty($bot_id)) {
        return '<p>Please configure the chatbot API key and Bot ID in the settings.</p>';
    }
    
    // Size configurations
    $size_configs = array(
        'small' => array('width' => '300px', 'height' => '400px'),
        'medium' => array('width' => '350px', 'height' => '500px'),
        'large' => array('width' => '400px', 'height' => '600px'),
        'full' => array('width' => '100%', 'height' => '100%')
    );
    
    $size = $size_configs[$args['size']];
    
    // Position styles
    $position_styles = array(
        'bottom-right' => 'bottom: 20px; right: 20px;',
        'bottom-left' => 'bottom: 20px; left: 20px;',
        'top-right' => 'top: 20px; right: 20px;',
        'top-left' => 'top: 20px; left: 20px;'
    );
    
    // Generate unique ID for this instance
    $instance_id = 'ayd-chatbot-' . uniqid();
    
    $output = sprintf(
        '<div id="%s" class="ayd-chatbot-container" style="position: fixed; %s z-index: 9999;">
            <div class="ayd-chatbot-frame" style="width: %s; height: %s;">
                <iframe 
                    src="https://askyourdatabase.com/embed/%s?theme=%s&greeting=%s&name=%s" 
                    style="width: 100%%; height: 100%%; border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"
                    allow="microphone"
                ></iframe>
            </div>
            <style>
                #%s {
                    transition: all 0.3s ease;
                }
                %s
            </style>
        </div>',
        $instance_id,
        $position_styles[$args['position']],
        $size['width'],
        $size['height'],
        esc_attr($bot_id),
        esc_attr($args['theme']),
        urlencode($args['greeting']),
        urlencode($args['name']),
        $instance_id,
        get_option('ayd_custom_css', '')
    );
    
    return $output;
}
add_shortcode('ask_your_database_chatbot', 'ayd_chatbot_shortcode');
