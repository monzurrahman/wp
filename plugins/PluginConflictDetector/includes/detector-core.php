<?php
/**
 * Core logic for detecting plugin conflicts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function pcd_check_for_conflicts() {
    if ( ! is_admin() ) {
        return;
    }

    global $wpdb;
    $active_plugins = get_option( 'active_plugins', array() );
    $conflicting_plugins = array();

    foreach ( $active_plugins as $plugin ) {
        $errors_before = pcd_capture_errors();

        do_action( 'plugins_loaded' ); // Try triggering common hooks

        $errors_after = pcd_capture_errors();

        if ( $errors_after !== $errors_before ) {
            $diff = array_diff( $errors_after, $errors_before );

            if ( ! empty( $diff ) ) {
                $conflicting_plugins[] = array(
                    'plugin'  => $plugin,
                    'context' => 'plugins_loaded',
                    'errors'  => implode( "\n", $diff ),
                );
            }
        }
    }

    if ( ! empty( $conflicting_plugins ) ) {
        foreach ( $conflicting_plugins as $conflict ) {
            pcd_log_conflict( $conflict['plugin'], $conflict['context'], $conflict['errors'] );
        }
    }
}

function pcd_capture_errors() {
    $errors = array();
    set_error_handler( function ( $errno, $errstr ) use ( &$errors ) {
        $errors[] = "[Error {$errno}] {$errstr}";
        return true;
    });

    do_action( 'init' ); // Test action

    restore_error_handler();
    return $errors;
}
