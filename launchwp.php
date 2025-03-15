<?php
/*
Plugin Name:  LaunchWP
Plugin URI:   https://launchwp.io
Description:  LaunchWP helper plugin for LaunchWP powered WordPress websites.
Author:       LaunchWP
Version:      1.0.1
Network:      True
Text Domain:  launchwp
Requires PHP: 7.1
Requires WP:  4.7
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'LAUNCHWP_HELPER_ACTIVE', true );

// Require the main plugin class
require_once plugin_dir_path(__FILE__) . 'inc/class.php';

// Initialize the plugin
add_action('plugins_loaded', function() {
    new LaunchWP\Main();
});