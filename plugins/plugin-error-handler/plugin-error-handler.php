<?php
/**
 * Plugin Name:  Plugin Error Handler
 * Plugin URI: https://wordpress.org/plugins/plugin-error-handler
 * Description: A Lightweight plugin that auto Detects and deactivates plugins that cause fatal errors on activation to prevent site crashes. Handles PHP error only but not JavaScript or CSS.  
 * Version: 1.0.1
 * Author: Monzur
 * Author URI: https://profiles.wordpress.org/monzur/
 * Text Domain: peh_defender
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.6
 * Requires PHP: 7.4
 */
// name should be - Plugin-Error-Handler 
if ( ! defined( 'ABSPATH' ) ) exit;  

define( 'PEH_DEFENDER_TRANSIENT_KEY', 'peh_defender_fatal_error_plugin' );

register_shutdown_function( 'peh_defender_check_for_fatal_error' );

function peh_defender_check_for_fatal_error() {
    $error = error_get_last();
    if ( $error && in_array( $error['type'], [ E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR ] ) ) {
        /*isset() does not need wp_unslash() as It doesn’t access the value deeply enough to require unslashing. ignore warning here*/
        if ( isset( $_GET['action'], $_GET['plugin'] ) && wp_unslash( $_GET['action'] ) === 'activate') {
            $plugin = sanitize_text_field( wp_unslash( $_GET['plugin'] ) );
            // We can now safely use $plugin
            deactivate_plugins( $plugin_file );
            // set_transient( PEH_DEFENDER_TRANSIENT_KEY, $plugin_file, 60 );
            $error_type_map = [
                                E_ERROR         => 'fatal error',
                                E_PARSE         => 'parse error',
                                E_COMPILE_ERROR => 'compile error',
                                E_CORE_ERROR    => 'core error',
                              ];

$error_type = isset( $error_type_map[ $error['type'] ] ) ? $error_type_map[ $error['type'] ] : 'unknown error';

set_transient( PEH_DEFENDER_TRANSIENT_KEY, json_encode([
    'plugin' => $plugin_file,
    'error_type' => $error_type,
]), 60 );

        }
    }
}

add_action( 'admin_notices', 'peh_defender_show_admin_notice' );

function peh_defender_show_admin_notice() {
    $transient_data = get_transient( PEH_DEFENDER_TRANSIENT_KEY );
    delete_transient( PEH_DEFENDER_TRANSIENT_KEY );

    if ( $transient_data ) {
        $data = json_decode( $transient_data, true );
        $plugin = isset( $data['plugin'] ) ? $data['plugin'] : 'Unknown plugin';
        $error_type = isset( $data['error_type'] ) ? $data['error_type'] : 'error';
        ?>
        <div class="notice notice-error is-dismissible" style="padding:12px">
            <strong>
                <?php 

                // translators: 1: Plugin name, 2: Error type (e.g., fatal error, parse error)
                        echo wp_kses_post( 
                        sprintf( 
                                /* translators: 1: Plugin name. 2: Error type (e.g., fatal or parse).*/
                            __( '<strong>The plugin "%1$s" was deactivated due to a %2$s . Operation handled by Plug Conflict Detector plugin for the safety of your website</strong>', 
                            'plug_conflict_detector' ), 
                            esc_html( $plugin ), 
                            esc_html( $error_type ) 
                                ) 
                    );
                ?>
                
            </strong>
        </div>
    <?php
}

}
