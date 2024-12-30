<?php
/*
Plugin Name: Ask Your Database WP Chatbot
Description: Enhanced chatbot integration with modern UI
Version: 2.0
Author: Ask Your Database
Author URI: https://askyourdatabase.com
*/

if (!defined('WPINC')) {
    die;
}

// Enqueue required assets
function ayd_enqueue_admin_assets($hook) {
    if ('toplevel_page_ayd-chatbot-settings' !== $hook) {
        return;
    }
    
    // Enqueue Roboto font
    wp_enqueue_style(
        'google-roboto',
        'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap'
    );
    
    // Enqueue jQuery UI
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_style('wp-jquery-ui-dialog');
    
    // Plugin styles
    wp_add_inline_style('admin-bar', "
        .ayd-settings-wrap {
            font-family: 'Roboto', sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .ayd-tabs {
            margin-top: 20px;
        }
        
        .ayd-tab-content {
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-top: none;
        }
        
        .ayd-preview-button {
            position: fixed;
            right: 20px;
            bottom: 20px;
            background: #2271b1;
            color: #fff;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .ayd-preview-button:hover {
            background: #135e96;
        }
        
        .ayd-preview-window {
            display: none;
            position: fixed;
            right: 0;
            top: 32px;
            bottom: 0;
            width: 400px;
            background: #fff;
            box-shadow: -2px 0 4px rgba(0,0,0,0.1);
            z-index: 99999;
        }
    ");
}
add_action('admin_enqueue_scripts', 'ayd_enqueue_admin_assets');

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

// Settings page with tabs and modern UI
function ayd_chatbot_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    if (isset($_POST['save_settings'])) {
        // Save settings logic here
        update_option('ayd_api_key', sanitize_text_field($_POST['api_key']));
        update_option('ayd_chatbot_theme', sanitize_text_field($_POST['theme']));
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }
    
    $api_key = get_option('ayd_api_key', '');
    $theme = get_option('ayd_chatbot_theme', 'light');
    
    ?>
    <div class="wrap ayd-settings-wrap">
        <h1>Ask Your Database Chatbot Settings</h1>
        
        <div class="ayd-tabs" id="ayd-settings-tabs">
            <ul>
                <li><a href="#general">General Settings</a></li>
                <li><a href="#appearance">Appearance</a></li>
                <li><a href="#preview">Preview</a></li>
            </ul>
            
            <div id="general" class="ayd-tab-content">
                <form method="post" action="">
                    <table class="form-table">
                        <tr>
                            <th scope="row">API Key</th>
                            <td>
                                <input type="text" name="api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text">
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Save Settings', 'primary', 'save_settings'); ?>
                </form>
            </div>
            
            <div id="appearance" class="ayd-tab-content">
                <form method="post" action="">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Theme</th>
                            <td>
                                <select name="theme">
                                    <option value="light" <?php selected($theme, 'light'); ?>>Light</option>
                                    <option value="dark" <?php selected($theme, 'dark'); ?>>Dark</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Save Appearance', 'primary', 'save_settings'); ?>
                </form>
            </div>
            
            <div id="preview" class="ayd-tab-content">
                <p>Click the preview button to see how your chatbot will appear on your site.</p>
            </div>
        </div>
        
        <div class="ayd-preview-button" onclick="togglePreview()">
            <span class="dashicons dashicons-visibility"></span> Preview Chatbot
        </div>
        
        <div class="ayd-preview-window" id="previewWindow">
            <iframe src="about:blank" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#ayd-settings-tabs').tabs();
        
        // Initialize preview window
        var previewWindow = $('#previewWindow');
        var isPreviewOpen = false;
        
        window.togglePreview = function() {
            if (isPreviewOpen) {
                previewWindow.hide('slide', {direction: 'right'}, 300);
            } else {
                previewWindow.show('slide', {direction: 'right'}, 300);
            }
            isPreviewOpen = !isPreviewOpen;
        };
    });
    </script>
    <?php
}

// Shortcode for embedding the chatbot
function ayd_chatbot_shortcode($atts) {
    $api_key = get_option('ayd_api_key', '');
    $theme = get_option('ayd_chatbot_theme', 'light');
    
    if (empty($api_key)) {
        return '<p>Please configure the chatbot API key in the settings.</p>';
    }
    
    ob_start();
    ?>
    <div class="ayd-chatbot-container" data-theme="<?php echo esc_attr($theme); ?>">
        <!-- Chatbot iframe will be loaded here -->
        <iframe src="about:blank" style="width: 100%; height: 500px; border: none;"></iframe>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('ask_your_database_chatbot', 'ayd_chatbot_shortcode');
