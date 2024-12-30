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

// Register settings
add_action('admin_init', 'ayd_chatbot_register_settings');
function ayd_chatbot_register_settings() {
    register_setting('ayd_chatbot_options', 'ayd_chatbot_api_key');
    register_setting('ayd_chatbot_options', 'ayd_chatbot_name');
    register_setting('ayd_chatbot_options', 'ayd_chatbot_code');
}

// Settings page
function ayd_chatbot_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap" style="max-width: 1200px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <img src="https://storage.googleapis.com/msgsndr/me0EnocxsZH10tkDWF5K/media/67253ad0eded3ba14cf5b7e0.png" 
                 alt="Ask Your Database Logo" 
                 style="height: 50px; margin-right: 15px;">
            <h1 style="margin: 0;">Ask Your Database Custom Chatbot</h1>
        </div>

        <div class="nav-tab-wrapper" style="margin-bottom: 20px;">
            <a href="#settings" class="nav-tab nav-tab-active" onclick="showTab('settings', this)">Settings</a>
            <a href="#get-code" class="nav-tab" onclick="showTab('get-code', this)">Get Chat Code/API</a>
        </div>

        <div id="settings-tab" class="tab-content" style="display: block;">
            <form method="post" action="options.php">
                <?php settings_fields('ayd_chatbot_options'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">API Key</th>
                        <td>
                            <input type="text" name="ayd_chatbot_api_key" 
                                   value="<?php echo esc_attr(get_option('ayd_chatbot_api_key')); ?>" 
                                   class="regular-text" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Chatbot Name</th>
                        <td>
                            <input type="text" name="ayd_chatbot_name" 
                                   value="<?php echo esc_attr(get_option('ayd_chatbot_name')); ?>" 
                                   class="regular-text" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Custom Code</th>
                        <td>
                            <textarea name="ayd_chatbot_code" class="large-text code" rows="5"><?php echo esc_textarea(get_option('ayd_chatbot_code')); ?></textarea>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>

        <div id="get-code-tab" class="tab-content" style="display: none;">
            <iframe src="https://www.askyourdatabase.com/dashboard/chatbot" 
                    style="width: 100%; height: 800px; border: none;"></iframe>
        </div>
    </div>

    <style>
        .nav-tab-wrapper { border-bottom: 1px solid #ccc; }
        .nav-tab { 
            background: white; 
            border: 1px solid #ccc; 
            padding: 10px 20px;
            margin-left: 0.5em;
            font-size: 14px;
            cursor: pointer;
        }
        .nav-tab-active { 
            border-bottom: 1px solid white; 
            margin-bottom: -1px;
            background: white;
        }
        .form-table th { width: 200px; }
        .regular-text { width: 100%; max-width: 400px; }
    </style>

    <script>
    function showTab(tabId, element) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(function(tab) {
            tab.style.display = 'none';
        });
        
        // Remove active class from all nav tabs
        document.querySelectorAll('.nav-tab').forEach(function(tab) {
            tab.classList.remove('nav-tab-active');
        });
        
        // Show selected tab
        document.getElementById(tabId + '-tab').style.display = 'block';
        
        // Add active class to clicked tab
        element.classList.add('nav-tab-active');
    }
    </script>
    <?php
}

// Add shortcode
add_shortcode('ayd_chatbot', 'ayd_chatbot_shortcode');
function ayd_chatbot_shortcode($atts) {
    $api_key = get_option('ayd_chatbot_api_key');
    $chatbot_name = get_option('ayd_chatbot_name');
    $custom_code = get_option('ayd_chatbot_code');
    
    if (empty($api_key)) {
        return 'Please configure your API key in the Chatbot Settings.';
    }
    
    // Process custom code if available
    if (!empty($custom_code)) {
        return $custom_code;
    }
    
    // Default implementation
    $atts = shortcode_atts(array(
        'theme' => 'light',
        'position' => 'right',
        'floating' => 'false'
    ), $atts);
    
    // Build chatbot code here based on attributes
    $output = "<!-- AYD Chatbot Code -->";
    $output .= "<script>";
    $output .= "// Chatbot implementation";
    $output .= "</script>";
    
    return $output;
}
