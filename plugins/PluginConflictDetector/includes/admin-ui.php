<?php
/**
 * Admin UI for Plug Conflict Detector
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Enqueue CSS and JS assets
function pcd_enqueue_admin_assets( $hook ) {
    if ( 'tools_page_pcd-conflict-logs' !== $hook ) {
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

// Add submenu page under Tools
function pcd_add_admin_menu() {
    add_menu_page(
        //'tools.php',
        esc_html__( 'Conflict Logs', 'plug-conflict-detector' ),
        esc_html__( 'Conflict Logs', 'plug-conflict-detector' ), // menu title
        'manage_options',
        'pcd-conflict-logs',
        'pcd_render_conflict_logs_page',
        'dashicons-warning',
        80
    );
}
add_action( 'admin_menu', 'pcd_add_admin_menu' );

// Render the plugin admin page
function pcd_render_conflict_logs_page() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'pcd_conflict_logs';

    // Prepare the query properly
    $query = $wpdb->prepare(
        "SELECT * FROM %i ORDER BY timestamp DESC LIMIT %d",
        $table_name,
        100
    );

    // $logs = $wpdb->get_results( $query );
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $logs = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM `{$wpdb->prefix}pcd_conflict_logs` ORDER BY timestamp DESC LIMIT %d",
                100
            )
        );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__( 'Plugin Conflict Logs', 'plug-conflict-detector' ); ?></h1>

        <?php if ( ! empty( $logs ) ) : ?>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__( 'Plugin Slug', 'plug-conflict-detector' ); ?></th>
                        <th><?php echo esc_html__( 'Context', 'plug-conflict-detector' ); ?></th>
                        <th><?php echo esc_html__( 'Errors', 'plug-conflict-detector' ); ?></th>
                        <th><?php echo esc_html__( 'Timestamp', 'plug-conflict-detector' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $logs as $log ) : ?>
                        <tr>
                            <td><?php echo esc_html( $log->plugin_slug ); ?></td>
                            <td><?php echo esc_html( $log->context ); ?></td>
                            <td>
                                <?php
                                $decoded = maybe_unserialize( $log->errors );
                                if ( is_array( $decoded ) ) {
                                    echo '<ul>';
                                    foreach ( $decoded as $error ) {
                                        echo '<li>' . esc_html( $error ) . '</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo esc_html( $decoded );
                                }
                                ?>
                            </td>
                            <td><?php echo esc_html( $log->timestamp ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php echo esc_html__( 'No conflict logs found.', 'plug-conflict-detector' ); ?></p>
        <?php endif; ?>
    </div>
    <?php
}