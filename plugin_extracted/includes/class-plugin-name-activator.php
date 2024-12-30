<?php
/**
 * Fired during plugin activation
 */

class Plugin_Name_Activator {
    public static function activate() {
        // Activation logic here
        global $wpdb;
        
        // Create any necessary database tables
        $charset_collate = $wpdb->get_charset_collate();
        
        // Example table creation
        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ayd_chatbot_logs` (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT CURRENT_TIMESTAMP,
            message text NOT NULL,
            response text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Add default options
        add_option('ayd_api_key', '');
        add_option('ayd_chatbot_settings', array(
            'theme' => 'light',
            'position' => 'bottom-right'
        ));
    }
}
