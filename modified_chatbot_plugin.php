<?php
/*
Plugin Name: WordPress Ask Your Database Custom Chatbot
Description: Easily integrate customizable chatbots into your WordPress site using the Ask Your Database API.
Version: 2.3
Author: Ask Your Database
Author URI: https://askyourdatabase.com
*/

// Add admin styles
function chatbot_admin_styles() {
    if (isset($_GET["page"]) && $_GET["page"] === "chatbot-settings") {
        ?>
        <style>
            .chatbot-settings-container {
                max-width: 800px;
                margin: 20px auto;
                padding: 20px;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .chatbot-header {
                border-bottom: 2px solid #eee;
                padding-bottom: 15px;
                margin-bottom: 20px;
            }
            .chatbot-section {
                margin-bottom: 30px;
            }
            .chatbot-config {
                background: #f9f9f9;
                padding: 15px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .chatbot-instructions {
                background: #f5f5f5;
                padding: 15px;
                border-left: 4px solid #0073aa;
                margin-bottom: 20px;
            }
        </style>
        <?php
    }
}
add_action("admin_head", "chatbot_admin_styles");

// Frontend styles for the chatbot iframe
function chatbot_frontend_styles() {
    ?>
    <style>
        .chatbot-wrapper {
            width: 100%;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .chatbot-wrapper iframe {
            border: none;
            width: 100%;
            height: 500px;
        }
    </style>
    <?php
}
add_action("wp_head", "chatbot_frontend_styles");

// Rest of the original functionality remains unchanged
