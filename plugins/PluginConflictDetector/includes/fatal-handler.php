<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __DIR__ ) . 'includes/logger.php';

/**
 * Fallback method to detect fatal errors via debug.log
 */
function pcd_scan_debug_log_for_fatal_errors() {
	$debug_log = WP_CONTENT_DIR . '/debug.log';

	if ( ! file_exists( $debug_log ) ) {
		return;
	}

	$contents = file_get_contents( $debug_log );

	if ( strpos( $contents, 'PHP Fatal error' ) !== false ) {
		$plugin_slug = get_option( 'pcd_last_action_plugin' );
		$context     = get_option( 'pcd_last_action_context' );

		if ( $plugin_slug && $context ) {
			pcd_log_conflict( $plugin_slug, $context, array( 'Fatal error found in debug.log' ) );

			if ( is_plugin_active( $plugin_slug ) ) {
				deactivate_plugins( $plugin_slug );
			}

			delete_option( 'pcd_last_action_plugin' );
			delete_option( 'pcd_last_action_context' );
		}
	}
}
add_action( 'admin_init', 'pcd_scan_debug_log_for_fatal_errors' );
