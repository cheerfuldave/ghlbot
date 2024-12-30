<?php
/**
 * Plugin Name: WordPress Ask Your Database Custom Chatbot
 * Description: Easily integrate customizable chatbots into your WordPress site using the Ask Your Database API.
 * Version: 2.3
 * Author: Ask Your Database
 * Author URI: https://askyourdatabase.com
 * Text Domain: ayd-chatbot
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('AYD_CHATBOT_VERSION', '2.3');
define('AYD_CHATBOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AYD_CHATBOT_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once AYD_CHATBOT_PLUGIN_DIR . 'includes/class-ayd-chatbot.php';
require_once AYD_CHATBOT_PLUGIN_DIR . 'admin/class-ayd-chatbot-admin.php';

function run_ayd_chatbot() {
    $plugin = new AYD_Chatbot();
    $plugin->run();
}

run_ayd_chatbot();
