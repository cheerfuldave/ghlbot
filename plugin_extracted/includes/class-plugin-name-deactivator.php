<?php
/**
 * Fired during plugin deactivation
 */

class Plugin_Name_Deactivator {
    public static function deactivate() {
        // Deactivation logic here
        // Don't delete tables/data by default - let user decide via settings
    }
}
