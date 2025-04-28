<?php
/*
Plugin Name: LaunchWP MU
Plugin URI: https://launchwp.io
Description: MU Plugin for LaunchWP-Powered sites.
Author: LaunchWP
Version: 1.0
Author URI: https://launchwp.io
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'is_plugin_active' ) ) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( is_plugin_active( 'launchwp/launchwp.php' ) ) {
    return;
}

/**
 * Check if the site is powered by LaunchWP
 *
 * @return bool
 */
$powered_by = isset($_SERVER['HTTP_X_POWERED_BY']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_X_POWERED_BY'])) : '';

if ( ! empty( $powered_by ) && stripos( $powered_by, 'LaunchWP.io' ) !== false ) {
    add_action( 'admin_notices', function() {
        $class = 'notice notice-info';

        $message = sprintf(
			/* translators: 1: LaunchWP website URL, 2: Opening link tag for helper plugin, 3: Closing link tag */
            __('This site is powered by <a href="%1$s" target="_blank">LaunchWP</a>. The LaunchWP Helper plugin is designed to intelligently flush cache and ensure correct sync with LaunchWP backend. Please %2$sinstall and active the helper plugin%3$s.', 'launchwp'),
            'https://launchwp.io',
            '<a href="https://github.com/Rajinsharwar/launchwp" target="_blank">',
            '</a>'
        );

        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message ) );
    } );
}

if ( file_exists( ABSPATH . '/wp-content/mu-plugins/cron-control/cron-control.php' ) ) {
    require_once ( ABSPATH . '/wp-content/mu-plugins/cron-control/cron-control.php' );
}