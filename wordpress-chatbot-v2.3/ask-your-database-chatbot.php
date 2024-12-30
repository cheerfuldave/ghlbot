<?php
/**
 * Plugin Name: WordPress Ask Your Database Custom Chatbot
 * Description: Easily integrate customizable chatbots into your WordPress site using the Ask Your Database API
 * Version: 2.3
 * Author: Ask Your Database
 * Author URI: https://askyourdatabase.com
 * Text Domain: ask-your-database-chatbot
 */

if (!defined('WPINC')) {
    die;
}

register_activation_hook(__FILE__, 'ayd_chatbot_activate');
register_deactivation_hook(__FILE__, 'ayd_chatbot_deactivate');

function ayd_chatbot_activate() {
    add_option('ayd_chatbot_api_key', '');
    add_option('ayd_chatbot_name', '');
    add_option('ayd_chatbot_code', '');
}

function ayd_chatbot_deactivate() {
    delete_option('ayd_chatbot_api_key');
    delete_option('ayd_chatbot_name');
    delete_option('ayd_chatbot_code');
}

add_action('admin_menu', 'ayd_chatbot_add_admin_menu');
function ayd_chatbot_add_admin_menu() {
    add_menu_page(
        'Chatbot Settings',
        'Chatbot',
        'manage_options',
        'ayd-chatbot-settings',
        'ayd_chatbot_settings_page',
        'dashicons-format-chat',
        90
    );
}

function ayd_chatbot_settings_page() {
    ?>
    <div class="wrap">
        <h1>Chatbot Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ayd_chatbot_options');
            do_settings_sections('ayd_chatbot_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">API Key</th>
                    <td><input type="text" name="ayd_chatbot_api_key" value="<?php echo esc_attr(get_option('ayd_chatbot_api_key')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Chatbot Name</th>
                    <td><input type="text" name="ayd_chatbot_name" value="<?php echo esc_attr(get_option('ayd_chatbot_name')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Chatbot Code</th>
                    <td><textarea name="ayd_chatbot_code" rows="5" cols="50"><?php echo esc_textarea(get_option('ayd_chatbot_code')); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', 'ayd_chatbot_settings_init');
function ayd_chatbot_settings_init() {
    register_setting('ayd_chatbot_options', 'ayd_chatbot_api_key');
    register_setting('ayd_chatbot_options', 'ayd_chatbot_name');
    register_setting('ayd_chatbot_options', 'ayd_chatbot_code');
}

register_activation_hook(__FILE__, 'ayd_chatbot_activate');
register_deactivation_hook(__FILE__, 'ayd_chatbot_deactivate');

function ayd_chatbot_activate() {
    add_option('ayd_chatbot_api_key', '');
    add_option('ayd_chatbot_name', '');
    add_option('ayd_chatbot_code', '');
}

function ayd_chatbot_deactivate() {
    // Cleanup tasks if needed
}

add_action('admin_menu', 'ayd_chatbot_add_admin_menu');
function ayd_chatbot_add_admin_menu() {
    add_menu_page(
        'Chatbot Settings',
        'Chatbot',
        'manage_options',
        'ayd-chatbot-settings',
        'ayd_chatbot_settings_page',
        'dashicons-admin-generic',
        90
    );
}

function ayd_chatbot_settings_page() {
    ?>
    <div class="wrap">
        <h1>Chatbot Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ayd_chatbot_options');
            do_settings_sections('ayd_chatbot_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', 'ayd_chatbot_settings_init');
function ayd_chatbot_settings_init() {
    register_setting('ayd_chatbot_options', 'ayd_chatbot_api_key');
    register_setting('ayd_chatbot_options', 'ayd_chatbot_name');
    register_setting('ayd_chatbot_options', 'ayd_chatbot_code');

    add_settings_section(
        'ayd_chatbot_general_section',
        'General Settings',
        'ayd_chatbot_general_section_callback',
        'ayd_chatbot_settings'
    );

    add_settings_field(
        'ayd_chatbot_api_key',
        'API Key',
        'ayd_chatbot_api_key_render',
        'ayd_chatbot_settings',
        'ayd_chatbot_general_section'
    );

    add_settings_field(
        'ayd_chatbot_name',
        'Chatbot Name',
        'ayd_chatbot_name_render',
        'ayd_chatbot_settings',
        'ayd_chatbot_general_section'
    );

    add_settings_field(
        'ayd_chatbot_code',
        'Chatbot Code',
        'ayd_chatbot_code_render',
        'ayd_chatbot_settings',
        'ayd_chatbot_general_section'
    );
}

function ayd_chatbot_general_section_callback() {
    echo '<p>Configure your chatbot settings below:</p>';
}

function ayd_chatbot_api_key_render() {
    $api_key = get_option('ayd_chatbot_api_key');
    ?>
    <input type="text" name="ayd_chatbot_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text">
    <p class="description">Enter your API key from the Ask Your Database dashboard</p>
    <?php
}

function ayd_chatbot_name_render() {
    $name = get_option('ayd_chatbot_name');
    ?>
    <input type="text" name="ayd_chatbot_name" value="<?php echo esc_attr($name); ?>" class="regular-text">
    <p class="description">Give your chatbot a custom name (optional)</p>
    <?php
}

function ayd_chatbot_code_render() {
    $code = get_option('ayd_chatbot_code');
    ?>
    <textarea name="ayd_chatbot_code" class="large-text code" rows="10"><?php echo esc_textarea($code); ?></textarea>
    <p class="description">Add any custom code for your chatbot (optional)</p>
    <?php
}

function ayd_chatbot_shortcode($atts) {
    $defaults = array(
        'theme' => 'light',
        'position' => 'right'
    );
    $atts = shortcode_atts($defaults, $atts);

    $api_key = get_option('ayd_chatbot_api_key');
    $name = get_option('ayd_chatbot_name');
    $custom_code = get_option('ayd_chatbot_code');

    $url = 'https://www.askyourdatabase.com/chat/' . esc_attr($api_key);
    if ($atts['theme'] === 'dark') {
        $url .= '?theme=dark';
    }

    $position_class = 'ayd-position-' . esc_attr($atts['position']);

    return '<div class="ayd-chatbot ' . $position_class . '">
        ' . ($name ? '<div class="ayd-chatbot-name">' . esc_html($name) . '</div>' : '') . '
        <iframe src="' . esc_url($url) . '" style="width: 100%; height: 600px; border: none;"></iframe>
        ' . ($custom_code ? $custom_code : '') . '
    </div>';
}
add_shortcode('ayd_chatbot', 'ayd_chatbot_shortcode');

// Add custom CSS for positioning
add_action('wp_head', 'ayd_chatbot_custom_css');
function ayd_chatbot_custom_css() {
    ?>
    <style>
        .ayd-chatbot {
            margin: 20px 0;
        }
        .ayd-position-left {
            float: left;
            margin-right: 20px;
        }
        .ayd-position-right {
            float: right;
            margin-left: 20px;
        }
        .ayd-chatbot-name {
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
    <?php
}
