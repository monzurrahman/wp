<?php
/**
 * Admin UI for Plugin Conflict Detector
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add a submenu under Tools for Conflict Logs
 */
function pcd_add_admin_menu() {
    add_submenu_page(
        'tools.php',
        __( 'Plugin Conflict Logs', 'plugin-conflict-detector' ),
        __( 'Plugin Conflict Logs', 'plugin-conflict-detector' ), // This text will be displayed in menu. i.e. Plugin Conflict Logs
        'manage_options',
        'plugin-conflict-logs',
        'pcd_render_admin_page'
    );
}
add_action( 'admin_menu', 'pcd_add_admin_menu' );

/**
 * Enqueue styles and scripts only on plugin admin page
 */
function pcd_enqueue_admin_assets( $hook ) {
    if ( $hook !== 'tools_page_plugin-conflict-logs' ) {
        return;
    }

    wp_enqueue_style(
        'pcd-admin-style',
        plugin_dir_url( __DIR__ ) . 'assets/css/admin-style.css',
        [],
        '1.0.0'
    );

    wp_enqueue_script(
        'pcd-admin-script',
        plugin_dir_url( __DIR__ ) . 'assets/js/admin-script.js',
        [],
        '1.0.0',
        true
    );
}
add_action( 'admin_enqueue_scripts', 'pcd_enqueue_admin_assets' );

/**
 * Show notice if .maintenance file was removed.
 */
function pcd_admin_notices() {
    if ( get_transient( 'pcd_maintenance_removed_notice' ) ) {
        echo '<div class="notice notice-success is-dismissible"><p><strong>Plugin Conflict Detector:</strong> A stuck <code>.maintenance</code> file was found and removed during activation.</p></div>';
        delete_transient( 'pcd_maintenance_removed_notice' );
    }

    if ( get_transient( 'pcd_maintenance_removal_failed' ) ) {
        echo '<div class="notice notice-error is-dismissible"><p><strong>Plugin Conflict Detector:</strong> A <code>.maintenance</code> file was found, but could not be removed. Please delete it manually from the root folder.</p></div>';
        delete_transient( 'pcd_maintenance_removal_failed' );
    }
}
add_action( 'admin_notices', 'pcd_admin_notices' );

/**
 * Render the Conflict Logs Admin Page
 */
function pcd_render_admin_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Plugin Conflict Logs', 'plugin-conflict-detector' ); ?></h1>

        <?php
        $conflict_logs = get_option( 'pcd_conflict_logs', [] );

        // Unserialize if it's a stored string
        if ( is_string( $conflict_logs ) ) {
            $conflict_logs = maybe_unserialize( $conflict_logs );
        }

        if ( ! empty( $conflict_logs ) && is_array( $conflict_logs ) ) :
            echo '<div class="pcd-log-list">';
            foreach ( $conflict_logs as $log ) {
                echo '<div class="pcd-log-item">';
                echo '<pre>' . esc_html( $log ) . '</pre>';
                echo '</div>';
            }
            echo '</div>';
        else :
            echo '<p>' . esc_html__( 'No conflict logs found.', 'plugin-conflict-detector' ) . '</p>';
        endif;
        ?>
    </div>
    <?php
}
