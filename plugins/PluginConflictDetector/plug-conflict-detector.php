<?php
/**
 * Plugin Name: Plug Conflict Detector
 * Plugin URI: https://wordpress.org/plugins/plug-conflict-detector
 * Description: Automatically detects plugin conflicts during activation or update, logs issues, and auto-disables conflicting plugins to prevent site crashes. Lightweight plugin that handles PHP error only but not JavaScript or CSS.  
 * Version: 1.0.1
 * Author: Monzur
 * Author URI: https://profiles.wordpress.org/monzur/
 * Text Domain: plug-conflict-detector
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.6
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'PCD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'PCD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Load required files.
require_once PCD_PLUGIN_PATH . 'includes/logger.php';
require_once PCD_PLUGIN_PATH . 'includes/admin-ui.php';
require_once PCD_PLUGIN_PATH . 'includes/detector-core.php';
require_once PCD_PLUGIN_PATH . 'includes/fatal-handler.php'; 


// Plugin activation hook.
register_activation_hook( __FILE__, 'pcd_activate_plugin' );
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

    // Attempt to remove stuck maintenance file in this stage

    $maintenance_file = ABSPATH . '.maintenance';
    if ( file_exists( $maintenance_file ) ) {
        wp_delete_file( $maintenance_file );
    }

 }

    // Plugin deactivation hook.
    register_deactivation_hook( __FILE__, 'pcd_deactivate_plugin' );
    function pcd_deactivate_plugin() {
        // Currently, nothing to clean up on deactivation.
    }
    
    // Register uninstallation system
    register_uninstall_hook( __FILE__, 'pcd_run_uninstall_script' );
    function pcd_run_uninstall_script() {
        // Load uninstall file if exists
        if ( file_exists( plugin_dir_path( __FILE__ ) . 'uninstall.php' ) ) {
            include plugin_dir_path( __FILE__ ) . 'uninstall.php';
        }
    }


    // Admin notice if .maintenance file was removed
    add_action( 'admin_notices', 'pcd_maintenance_notice' );
    function pcd_maintenance_notice() {
        if ( get_option( 'pcd_maintenance_removed' ) ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( '.maintenance file was removed during plugin activation to prevent site lock.', 'plug-conflict-detector' ) . '</p></div>';
            delete_option( 'pcd_maintenance_removed' );
        }
    }

    // Load plugin text domain.
    add_action( 'plugins_loaded', 'pcd_load_textdomain' );
    function pcd_load_textdomain() {
        load_plugin_textdomain( 'plug-conflict-detector', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
