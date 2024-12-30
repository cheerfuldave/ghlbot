<?php
/**
 * Plugin Name:       Ask Your Database WP Chatbot
 * Plugin URI:        https://askyourdatabase.com
 * Description:       A modern chatbot solution for querying your WordPress database with natural language.
 * Version:           2.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Ask Your Database
 * Author URI:        https://askyourdatabase.com
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ask-your-database-wp-chatbot
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('ASK_YOUR_DATABASE_VERSION', '2.0.0');
define('ASK_YOUR_DATABASE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ASK_YOUR_DATABASE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Composer autoloader
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

// Activation and deactivation hooks
register_activation_hook(__FILE__, array('AskYourDatabase\Activator', 'activate'));
register_deactivation_hook(__FILE__, array('AskYourDatabase\Deactivator', 'deactivate'));

// Initialize the plugin
function run_ask_your_database() {
    $plugin = new AskYourDatabase\Plugin();
    $plugin->run();
}
run_ask_your_database();
