<?php
/**
 * Uninstall file of plugin
 * Runs automatically when plugin is being uninstalled.
 *
 * @package WordPress Contributors
 */

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete contributors data from postmeta table.
global $wpdb;

$wpdb->delete(
	$wpdb->prefix . 'postmeta',
	array( 'meta_key' => 'wpci-contributors' )
);
