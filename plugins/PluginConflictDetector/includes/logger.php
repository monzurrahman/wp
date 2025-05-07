<?php
/**
 * Logger for Plugin Conflict Detector
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pcd_log_conflict( $plugin_slug, $context, $errors ) {
    global $wpdb;
    $table = $wpdb->prefix . 'pcd_conflict_logs';

    $wpdb->insert(
        $table,
        array(
            'plugin_slug' => sanitize_text_field( $plugin_slug ),
            'context'     => sanitize_text_field( $context ),
            'errors'      => wp_kses_post( $errors ),
            'timestamp'   => current_time( 'mysql' ),
        ),
        array( '%s', '%s', '%s', '%s' )
    );
}
