<?php
/**
 * Plugin Name: Plugin Conflict Detector
 * Plugin URI: https://wordpress.org/plugins/plugin-conflict-detector/
 * Description: Automatically detects and logs plugin conflicts that may cause fatal errors or site malfunctions.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://github.com/monzurrahman/wp/tree/main/plugins/PluginConflictDetector
 * Text Domain: plugin-conflict-detector
 * Domain Path: /languages
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

/**
 * Define plugin constants
 */
define( 'PCD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PCD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Include plugin core files
 * Order is important: define dependencies before using them.
 */
require_once PCD_PLUGIN_DIR . 'includes/logger.php';
require_once PCD_PLUGIN_DIR . 'includes/detector-core.php'; // Contains pcd_check_for_conflicts()
require_once PCD_PLUGIN_DIR . 'includes/admin-ui.php';

/**
 * Run conflict check on init
 * This must be added only after detector-core.php is loaded
 */
add_action( 'init', 'pcd_check_for_conflicts' );

/**
 * Plugin activation: create custom DB table
 */
function pcd_activate_plugin() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'pcd_conflict_logs';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        plugin_slug varchar(255) NOT NULL,
        context text NOT NULL,
        errors longtext NOT NULL,
        timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'pcd_activate_plugin' );
