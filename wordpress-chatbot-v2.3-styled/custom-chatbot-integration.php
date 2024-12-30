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
    ?>
    <div class="wrap ayd-chatbot-wrap">
        <div class="ayd-header">
            <img src="https://storage.googleapis.com/msgsndr/me0EnocxsZH10tkDWF5K/media/67253ad0eded3ba14cf5b7e0.png" alt="Ask Your Database Logo" class="ayd-logo">
            <h1>Ask Your Database Custom Chatbot</h1>
        </div>
        
        <h2 class="nav-tab-wrapper">
            <a href="#settings" class="nav-tab nav-tab-active" id="settings-tab">Settings</a>
            <a href="#get-code" class="nav-tab" id="get-code-tab">Get Chat Code/API</a>
        </h2>

        <div id="settings-content" class="tab-content active">
            <div class="ayd-settings-container">
                <div class="ayd-settings-column">
                    <form method="post" action="options.php">
                        <?php settings_fields('ayd_chatbot_options'); ?>
                        <div class="ayd-settings-box">
                            <h3>Chatbot Configuration</h3>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row">API Key</th>
                                    <td><input type="text" name="ayd_chatbot_api_key" value="<?php echo esc_attr(get_option('ayd_chatbot_api_key')); ?>" class="regular-text" /></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">Chatbot Name</th>
                                    <td><input type="text" name="ayd_chatbot_name" value="<?php echo esc_attr(get_option('ayd_chatbot_name')); ?>" class="regular-text" /></td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">Custom Code</th>
                                    <td><textarea name="ayd_chatbot_code" class="large-text code" rows="5"><?php echo esc_textarea(get_option('ayd_chatbot_code')); ?></textarea></td>
                                </tr>
                            </table>
                            <?php submit_button(); ?>
                        </div>
                    </form>
                </div>
                
                <div class="ayd-settings-column">
                    <div class="ayd-shortcode-box">
                        <h3>Available Shortcodes</h3>
                        <div class="shortcode-item">
                            <code>[ayd_chatbot]</code>
                            <p>Basic chatbot implementation</p>
                        </div>
                        <div class="shortcode-item">
                            <code>[ayd_chatbot theme="dark"]</code>
                            <p>Dark theme variant</p>
                        </div>
                        <div class="shortcode-item">
                            <code>[ayd_chatbot position="left"]</code>
                            <p>Left-aligned chatbot</p>
                        </div>
                        <div class="shortcode-item">
                            <code>[ayd_chatbot floating="true"]</code>
                            <p>Floating chat button</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="get-code-content" class="tab-content" style="display: none;">
            <div class="ayd-api-container">
                <iframe src="https://www.askyourdatabase.com/dashboard/chatbot" style="width: 100%; height: 800px; border: none;"></iframe>
            </div>
        </div>
    </div>

    <style>
        .ayd-chatbot-wrap {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin: 20px 20px 20px 0;
        }
        .ayd-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .ayd-logo {
            height: 50px;
            margin-right: 20px;
        }
        .ayd-header h1 {
            margin: 0;
            color: #23282d;
        }
        .nav-tab-wrapper {
            margin-bottom: 20px;
        }
        .tab-content {
            padding: 20px 0;
        }
        .ayd-settings-container {
            display: flex;
            gap: 30px;
        }
        .ayd-settings-column {
            flex: 1;
        }
        .ayd-settings-box, .ayd-shortcode-box {
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .ayd-settings-box h3, .ayd-shortcode-box h3 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .shortcode-item {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .shortcode-item code {
            display: block;
            background: #f1f1f1;
            padding: 8px;
            margin-bottom: 8px;
            border-radius: 3px;
        }
        .shortcode-item p {
            margin: 0;
            color: #666;
        }
        .ayd-api-container {
            background: #fff;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        $('.nav-tab').click(function(e) {
            e.preventDefault();
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.tab-content').hide();
            $($(this).attr('href') + '-content').show();
        });
    });
    </script>
    <?php
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
