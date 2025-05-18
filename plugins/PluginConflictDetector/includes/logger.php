<?php
/**
 * Logger for Plug Conflict Detector
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Logs a conflict entry into the database
 *
 * @param string       $plugin_slug Plugin slug.
 * @param string       $context     Context of conflict (activation/update).
 * @param string|array $errors      Error message(s).
 */
function pcd_log_conflict( $plugin_slug, $context, $errors ) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'pcd_conflict_logs';

    // Sanitize inputs
    $plugin_slug = sanitize_text_field( $plugin_slug );
    $context     = sanitize_text_field( $context );
    $errors      = maybe_serialize( $errors );

    // Insert conflict log into DB
    $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Logging insert must hit DB, caching not suitable.
        $table_name,
        array(
            'plugin_slug' => $plugin_slug,
            'context'     => $context,
            'errors'      => $errors,
            'timestamp'   => current_time( 'mysql' ),
        ),
        array( '%s', '%s', '%s', '%s' )
    );
}
