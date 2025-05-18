<?php
/**
 * Uninstall script for Plug Conflict Detector
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

$table_name = $wpdb->prefix . 'pcd_conflict_logs';

// Delete the custom conflict logs table directly from the database
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching -- Table deletion must query DB directly, caching not applicable in this situation.
