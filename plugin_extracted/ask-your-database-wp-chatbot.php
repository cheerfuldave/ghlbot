<?php
/*
Plugin Name: Ask Your Database WP Chatbot
Plugin URI: https://askyourdatabase.com
Description: A modern chatbot solution for querying your WordPress database with natural language.
Version: 2.0.0
Author: Ask Your Database
Author URI: https://askyourdatabase.com
License: GPL v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: ask-your-database-wp-chatbot
*/

if (!defined('ABSPATH')) {
    exit;
}

// Basic functionality
function ask_your_database_init() {
    // Plugin initialization code
}
add_action('init', 'ask_your_database_init');
