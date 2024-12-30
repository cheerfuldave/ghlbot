<?php
// Register shortcode for chatbot integration
add_shortcode('ayd_chatbot', 'ayd_chatbot_shortcode');

/**
 * Shortcode handler for chatbot integration
 * Usage: [ayd_chatbot name="chatbot_name"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output for chatbot
 */
function ayd_chatbot_shortcode($atts) {
    // Sanitize and validate attributes
    $atts = shortcode_atts([
        'name' => '',
        'height' => '500px',
        'width' => '100%'
    ], $atts, 'ayd_chatbot');

    // Validate required parameters
    if (empty($atts['name'])) {
        return '<div class="ayd-error">Error: Chatbot name is required.</div>';
    }

    // Get current user information
    global $current_user;
    wp_get_current_user();

    // Get plugin settings
    $chatbots = get_option('ayd_chatbots', []);
    $api_key = get_option('ayd_api_key', '');

    // Validate API key
    if (empty($api_key)) {
        return '<div class="ayd-error">Error: API key not configured.</div>';
    }

    // Find chatbot configuration
    $chatbot = null;
    foreach ($chatbots as $bot) {
        if ($bot['name'] === $atts['name']) {
            $chatbot = $bot;
            break;
        }
    }

    if (!$chatbot) {
        return '<div class="ayd-error">Error: Chatbot not found.</div>';
    }

    // Generate secure session URL
    $session_url = ayd_generate_session_url(
        $api_key,
        $chatbot['id'],
        $current_user->display_name,
        $current_user->user_email
    );

    if (!$session_url) {
        return '<div class="ayd-error">Error: Unable to generate chat session.</div>';
    }

    // Return responsive iframe with proper styling and error handling
    return sprintf(
        '<div class="ayd-chatbot-wrapper" style="width: %2$s;">
            <iframe src="%1$s" 
                    class="ayd-chatbot-frame"
                    style="width: 100%%; height: %3$s; border: none; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" 
                    title="%4$s"
                    loading="lazy"
                    allowfullscreen></iframe>
        </div>',
        esc_url($session_url),
        esc_attr($atts['width']),
        esc_attr($atts['height']),
        esc_attr("Ask Your Database Chatbot - {$chatbot['name']}")
    );
}

/**
 * Generate secure session URL for chatbot
 *
 * @param string $api_key API key
 * @param string $chatbot_id Chatbot ID
 * @param string $name User name
 * @param string $email User email
 * @return string|false Session URL or false on failure
 */
function ayd_generate_session_url($api_key, $chatbot_id, $name, $email) {
    $response = wp_remote_post('https://www.askyourdatabase.com/api/chatbot/v2/session', [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ],
        'body' => json_encode([
            'chatbotid' => $chatbot_id,
            'name' => $name,
            'email' => $email,
        ]),
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        error_log('AYD Chatbot Error: ' . $response->get_error_message());
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!isset($data['session_url'])) {
        error_log('AYD Chatbot Error: Invalid session response');
        return false;
    }

    return $data['session_url'];
}

// Add required styles for chatbot
add_action('wp_head', 'ayd_chatbot_styles');
function ayd_chatbot_styles() {
    ?>
    <style>
        .ayd-chatbot-wrapper {
            margin: 20px 0;
            padding: 10px;
            background: #fff;
            border-radius: 4px;
            max-width: 100%;
        }
        .ayd-error {
            padding: 10px;
            margin: 10px 0;
            background-color: #fee;
            border-left: 4px solid #c00;
            color: #333;
        }
        .ayd-chatbot-frame {
            transition: height 0.3s ease;
        }
        @media (max-width: 768px) {
            .ayd-chatbot-wrapper {
                padding: 5px;
            }
            .ayd-chatbot-frame {
                height: 400px !important;
            }
        }
    </style>
    <?php
}
