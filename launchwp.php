<?php
/*
Plugin Name:  LaunchWP
Plugin URI:   https://launchwp.io
Description:  LaunchWP helper plugin for LaunchWP powered WordPress websites.
Author:       LaunchWP
Version:      1.0.1
Network:      True
Text Domain:  launchwp
License:      GPLV3
Requires PHP: 7.1
Requires WP:  4.7
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'LAUNCHWP_HELPER_ACTIVE', true );
$plugin_version = get_file_data( __FILE__, array( 'Version' => 'Version' ), false )[ 'Version' ];
define( 'LAUNCHWP_HELPER_VERSION', $plugin_version );

// Require the main plugin class
require_once plugin_dir_path(__FILE__) . 'inc/class.php';

register_activation_hook( __FILE__, 'launchwp_after_active_actions_launchwp' );

/**
 * Perform actions on plugin activation.
 */
function launchwp_after_active_actions_launchwp() {
    $plugin_path   = untrailingslashit( dirname( __FILE__ ) );
    $wpmu_dir      = untrailingslashit( WPMU_PLUGIN_DIR );

    wp_mkdir_p( $wpmu_dir );
    @copy( $plugin_path . '/mu-plugins/launchwp-mu.php', $wpmu_dir . '/launchwp-mu.php' );
}

// Initialize the plugin
add_action('plugins_loaded', function() {
    new LaunchWP\Main();
});

if ( file_exists( ABSPATH . '/wp-content/mu-plugins/cron-control/cron-control.php' ) ) {
    require_once ( ABSPATH . '/wp-content/mu-plugins/cron-control/cron-control.php' );
}