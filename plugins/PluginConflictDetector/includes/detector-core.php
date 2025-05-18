<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __DIR__ ) . 'includes/logger.php';

/**
 * Capture PHP errors during shutdown to detect fatal errors
 */
function pcd_capture_errors() {
	$error = error_get_last();
	if ( $error && in_array( $error['type'], array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR ), true ) ) {
		$plugin_slug = get_option( 'pcd_last_action_plugin' );
		$context     = get_option( 'pcd_last_action_context' );

		if ( $plugin_slug && $context ) {
			pcd_log_conflict( $plugin_slug, $context, array( 'Fatal error detected in front-end output.' ) );
			delete_option( 'pcd_last_action_plugin' );
			delete_option( 'pcd_last_action_context' );

			// Auto-deactivate the conflicting plugin
			if ( is_plugin_active( $plugin_slug ) ) {
				deactivate_plugins( $plugin_slug );
			}
		}
	}
}
add_action( 'shutdown', 'pcd_capture_errors' );

/**
 * Save plugin slug and context when a plugin is activated
 */
function pcd_before_plugin_activation( $plugin ) {
	update_option( 'pcd_last_action_plugin', sanitize_text_field( $plugin ) );
	update_option( 'pcd_last_action_context', 'activation' );
}
add_action( 'activate_plugin', 'pcd_before_plugin_activation' );

/**
 * Save plugin slug and context before plugin update
 */
function pcd_before_plugin_update( $plugin ) {
	update_option( 'pcd_last_action_plugin', sanitize_text_field( $plugin ) );
	update_option( 'pcd_last_action_context', 'update' );
}
add_action( 'upgrader_pre_install', function( $return, $package, $upgrader ) {
	if ( isset( $upgrader->skin->plugin ) ) {
		pcd_before_plugin_update( $upgrader->skin->plugin );
	}
	return $return;
}, 10, 3 );

/**
 * WooCommerce-specific conflict test (placeholder for custom logic)
 */
function pcd_test_woocommerce_conflicts() {
	if ( class_exists( 'WooCommerce' ) ) {
		// Add additional WooCommerce-specific checks here
	}
}
add_action( 'plugins_loaded', 'pcd_test_woocommerce_conflicts' );

/**
 * Elementor-specific conflict test (placeholder for custom logic)
 */
function pcd_test_elementor_conflicts() {
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		// Add additional Elementor-specific checks here
	}
}
add_action( 'plugins_loaded', 'pcd_test_elementor_conflicts' );
