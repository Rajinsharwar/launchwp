<?php
/*
Plugin Name: LaunchWP MU
Plugin URI: https://launchwp.io
Description: MU Plugin for LaunchWP-Powered sites.
Author: LaunchWP
Version: 1.0
Author URI: https://launchwp.io
*/

if ( ! defined( 'ABSPATH' ) || defined( 'LAUNCHWP_HELPER_ACTIVE' ) ) {
    exit;
}

/**
 * Check if the site is powered by LaunchWP
 *
 * @return bool
 */
$powered_by = $_SERVER['HTTP_X_POWERED_BY'] ?? '';

if ( ! empty( $powered_by ) && stripos( $powered_by, 'LaunchWP.io' ) !== false ) {
    add_action( 'admin_notices', function() {
        $class = 'notice notice-info';
        $message = sprintf(
            __('This site is powered by <a href="%1$s" target="_blank">LaunchWP</a>. The LaunchWP Helper plugin is designed to intelligently flush cache and ensure correct sync with LaunchWP backend. Please %2$sinstall and active the helper plugin%3$s.', 'launchwp'),
            'https://launchwp.io',
            '<a href="">',
            '</a>'
        );

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
    } );
}