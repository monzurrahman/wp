<?php
/**
 * Uninstall procedure
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Clean up database table
global $wpdb;
$table_name = $wpdb->prefix . 'pcd_conflict_logs';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

// Optionally clean options, if used
// delete_option( 'pcd_some_option' );
