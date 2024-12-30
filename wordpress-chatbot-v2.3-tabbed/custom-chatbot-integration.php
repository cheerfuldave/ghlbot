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
        update_option('ayd_chatbot_settings', $chatbots);
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }
    
    $chatbots = get_option('ayd_chatbot_settings', array());
    ?>
    <div class="wrap">
        <h2>Ask Your Database Chatbot Settings</h2>
        
        <h2 class="nav-tab-wrapper">
            <a href="#settings" class="nav-tab nav-tab-active" id="settings-tab">Settings</a>
            <a href="#get-code" class="nav-tab" id="get-code-tab">Get Chat Code/API</a>
        </h2>

        <div id="settings-content" class="tab-content active">
            <form method="post">
                <table class="form-table">
                    <?php foreach ($chatbots as $index => $chatbot): ?>
                    <tr>
                        <th scope="row">Chatbot <?php echo $index + 1; ?></th>
                        <td>
                            <input type="text" name="chatbots[<?php echo $index; ?>][name]" 
                                   value="<?php echo esc_attr($chatbot['name']); ?>" placeholder="Name" />
                            <input type="text" name="chatbots[<?php echo $index; ?>][id]" 
                                   value="<?php echo esc_attr($chatbot['id']); ?>" placeholder="ID" />
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th scope="row">Add New Chatbot</th>
                        <td>
                            <input type="text" name="chatbots[<?php echo count($chatbots); ?>][name]" placeholder="Name" />
                            <input type="text" name="chatbots[<?php echo count($chatbots); ?>][id]" placeholder="ID" />
                        </td>
                    </tr>
                </table>
                <input type="submit" name="save_settings" class="button button-primary" value="Save Settings" />
            </form>
        </div>

        <div id="get-code-content" class="tab-content" style="display: none;">
            <iframe src="https://www.askyourdatabase.com/dashboard/chatbot" style="width: 100%; height: 600px; border: none;"></iframe>
        </div>
    </div>

    <style>
        .tab-content { padding: 20px 0; }
        .nav-tab-wrapper { margin-bottom: 20px; }
        .form-table input[type="text"] { width: 200px; margin-right: 10px; }
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
