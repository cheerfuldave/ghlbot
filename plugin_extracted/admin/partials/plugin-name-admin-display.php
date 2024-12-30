
<?php
if (!defined('ABSPATH')) exit;

// Check user capabilities
if (!current_user_can('manage_options')) {
    return;
}

// Get the active tab from the $_GET param
$default_tab = null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

?>
<div class="wrap settings-wrapper">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <nav class="nav-tab-wrapper">
        <a href="?page=plugin-settings" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>" data-tab="general">
            <?php esc_html_e('General Settings', 'ask-your-database-wp-chatbot'); ?>
        </a>
        <a href="?page=plugin-settings&tab=advanced" class="nav-tab <?php if($tab==='advanced'):?>nav-tab-active<?php endif; ?>" data-tab="advanced">
            <?php esc_html_e('Advanced Settings', 'ask-your-database-wp-chatbot'); ?>
        </a>
        <a href="?page=plugin-settings&tab=about" class="nav-tab <?php if($tab==='about'):?>nav-tab-active<?php endif; ?>" data-tab="about">
            <?php esc_html_e('About', 'ask-your-database-wp-chatbot'); ?>
        </a>
    </nav>

    <div class="tab-content" id="general" <?php if($tab!==null):?>style="display:none;"<?php endif; ?>>
        <form method="post" action="options.php" id="settings-form">
            <?php
                settings_fields('ask_your_database_options');
                do_settings_sections('ask_your_database_options');
            ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="api_key"><?php esc_html_e('API Key', 'ask-your-database-wp-chatbot'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="api_key" name="ask_your_database_options[api_key]" 
                               value="<?php echo esc_attr(get_option('ask_your_database_api_key')); ?>" 
                               class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="chat_position"><?php esc_html_e('Chat Position', 'ask-your-database-wp-chatbot'); ?></label>
                    </th>
                    <td>
                        <select id="chat_position" name="ask_your_database_options[chat_position]">
                            <option value="bottom-right" <?php selected(get_option('ask_your_database_chat_position'), 'bottom-right'); ?>>
                                <?php esc_html_e('Bottom Right', 'ask-your-database-wp-chatbot'); ?>
                            </option>
                            <option value="bottom-left" <?php selected(get_option('ask_your_database_chat_position'), 'bottom-left'); ?>>
                                <?php esc_html_e('Bottom Left', 'ask-your-database-wp-chatbot'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>

    <div class="tab-content" id="advanced" <?php if($tab!=='advanced'):?>style="display:none;"<?php endif; ?>>
        <form method="post" action="options.php">
            <?php
                settings_fields('ask_your_database_advanced_options');
                do_settings_sections('ask_your_database_advanced_options');
            ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="custom_css"><?php esc_html_e('Custom CSS', 'ask-your-database-wp-chatbot'); ?></label>
                    </th>
                    <td>
                        <textarea id="custom_css" name="ask_your_database_advanced_options[custom_css]" 
                                  rows="10" class="large-text code"><?php echo esc_textarea(get_option('ask_your_database_custom_css')); ?></textarea>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>

    <div class="tab-content" id="about" <?php if($tab!=='about'):?>style="display:none;"<?php endif; ?>>
        <h2><?php esc_html_e('About Ask Your Database WP Chatbot', 'ask-your-database-wp-chatbot'); ?></h2>
        <p><?php esc_html_e('Version: 1.0.0', 'ask-your-database-wp-chatbot'); ?></p>
        <p><?php esc_html_e('A powerful chatbot solution for WordPress that allows your visitors to query your database in natural language.', 'ask-your-database-wp-chatbot'); ?></p>
    </div>

    <div class="preview-window">
        <h3><?php esc_html_e('Live Preview', 'ask-your-database-wp-chatbot'); ?></h3>
        <div class="preview-content">
            <!-- Preview content will be loaded here via AJAX -->
        </div>
    </div>
</div>
