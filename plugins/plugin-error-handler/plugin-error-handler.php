<?php
/**
 * Plugin Name:  Plug Error Handler
 * Plugin URI:   https://wordpress.org/plugins/plug-error-handler
 * Description:  Automatically detects and deactivates plugins that cause PHP fatal or parse errors during activation or update to prevent site crashes.
 * Version:      1.0.2
 * Author:       Monzur
 * Author URI:   https://profiles.wordpress.org/monzur/
 * Text Domain:  plug-error-handler
 * License:      GPL v2 or later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.6
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'PLUG_ERROR_HANDLER_TRANSIENT_KEY', 'plug_error_handler_error_plugin' );

register_shutdown_function( 'plug_error_handler_check_for_error' );

function plug_error_handler_check_for_error() {
    $error = error_get_last();
    if ( $error && in_array( $error['type'], [ E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR ], true ) ) {
        $plugin_file = null;

        if ( 
            /* Adding a nonce check here isn't required because we just read the plugin activation context, which is already a core WordPress-admin action. */
            isset( $_GET['action'], $_GET['plugin'] )
            && in_array( wp_unslash( $_GET['action'] ), [ 'activate', 'update-plugin' ], true ) //
        ) {
            $plugin_file = sanitize_text_field( wp_unslash( $_GET['plugin'] ) );
        }

        if ( $plugin_file ) {
            deactivate_plugins( $plugin_file );
        }

        $error_type_map = [
            E_ERROR         => 'fatal error',
            E_PARSE         => 'parse error',
            E_COMPILE_ERROR => 'compile error',
            E_CORE_ERROR    => 'core error',
        ];

        $error_type = isset( $error_type_map[ $error['type'] ] ) ? $error_type_map[ $error['type'] ] : 'unknown error';

        set_transient( PLUG_ERROR_HANDLER_TRANSIENT_KEY, json_encode( [
            'plugin'     => $plugin_file ? $plugin_file : 'Unknown plugin',
            'error_type' => $error_type,
        ] ), 60 );
    }
}

add_action( 'admin_notices', 'plug_error_handler_show_admin_notice' );

function plug_error_handler_show_admin_notice() {
    $transient_data = get_transient( PLUG_ERROR_HANDLER_TRANSIENT_KEY );
    delete_transient( PLUG_ERROR_HANDLER_TRANSIENT_KEY );

    if ( $transient_data ) {
        $data        = json_decode( $transient_data, true );
        $plugin      = isset( $data['plugin'] ) ? esc_html( $data['plugin'] ) : 'Unknown plugin';
        $error_type  = isset( $data['error_type'] ) ? esc_html( $data['error_type'] ) : 'error';

        ?>
        <div class="notice notice-success is-dismissible" style="padding:14px; font-size: 16px;">
            <strong>
                <?php
                
                echo wp_kses_post(
                    sprintf(
                        /* translators: 1: Plugin name, 2: Error type (e.g., fatal error, parse error) */
                        __( 'The plugin "%1$s" was deactivated due to a %2$s during activation or update. Operation handled by Plug Error Handler plugin for the safety of your website.', 'plug-error-handler' ),
                        $plugin,
                        $error_type
                    )
                );
                ?>
            </strong>
        </div>
        <?php
    }
}
