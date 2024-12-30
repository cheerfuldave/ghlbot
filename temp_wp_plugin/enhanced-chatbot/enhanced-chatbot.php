<?php
/**
 * Plugin Name: Enhanced Chatbot
 * Plugin URI: https://julius.ai
 * Description: A modern chatbot plugin with enhanced features.
 * Version: 1.0.0
 * Author: Julius AI
 * Author URI: https://julius.ai
 * License: GPL2
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Include the admin class
require_once plugin_dir_path(__FILE__) . 'admin/class-admin.php';

use JuliusAI\ChatBot\Admin\Admin;

// Initialize the plugin
function run_enhanced_chatbot() {
    $plugin = new Admin('enhanced-chatbot', '1.0.0');
    add_action('admin_enqueue_scripts', array($plugin, 'enqueue_styles'));
    add_action('admin_enqueue_scripts', array($plugin, 'enqueue_scripts'));
    add_action('admin_menu', array($plugin, 'create_admin_menu'));
}

run_enhanced_chatbot();
