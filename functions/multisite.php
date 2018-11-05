<?php
/**
 * Functions for compatibility with WordPress multisites
 *
 * @since 1.6
 * 
 * @function	pwapro_multisite_filename_postfix()		Filename postfix for multisites
 * @function	pwapro_multisite_activation_status()		Save activation status for current blog id
 * @function	pwapro_multisite_network_deactivator()	Handle multisite network deactivation
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Filename postfix for multisites
 * 
 * @return (string) Returns the current blog ID on a multisite. An empty string otherwise
 * 
 * @since 1.6
 */
function pwapro_multisite_filename_postfix() {
	
	// Return empty string if not a multisite
	if ( ! is_multisite() ) {
		return '';
	}
	
	return '-' . get_current_blog_id();
}

/**
 * Save activation status for current blog id
 *
 * For clean multisite uninstall. 
 * Manifest and service worker are deleted during deactivation. 
 * Database settings are cleaned during uninstall
 *
 * @param (bool) $status True when plugin is activated, false when deactivated.
 * 
 * @since 1.6
 */
function pwapro_multisite_activation_status( $status ) {
	
	// Only for multisites
	if ( ! is_multisite() || ! isset( $status ) ) {
		return;
	}
	
	// Get current list of sites where SuperPWA is activated.
	$pwapro_sites = get_site_option( 'pwapro_active_sites', array() );
	
	// Set the status for the current blog.
	$pwapro_sites[ get_current_blog_id() ] = $status;
	
	// Save it back to the database.
	update_site_option( 'pwapro_active_sites', $pwapro_sites );
}

/**
 * Handle multisite network deactivation
 * 
 * Deletes manifest and service worker of all sub-sites.
 * Sets the deactivation status for each site.
 *
 * Not used when wp_is_large_network() is true. Deleting that many files and db options will most likely time out. 
 * This also this gives the user an option to decide if SuperPWA should handle this by changing the defenition of wp_is_large_network.
 * @link https://developer.wordpress.org/reference/functions/wp_is_large_network/
 */
function pwapro_multisite_network_deactivator() {
	
	// Do not run on large networks
	if ( wp_is_large_network() ) {
		return;
	}
	
	// Retrieve the list of blog ids where SuperPWA is active. (saved with blog_id as $key and activation_status as $value)
	$pwapro_sites = get_site_option( 'pwapro_active_sites' );
	
	// Loop through each active site.
	foreach( $pwapro_sites as $blog_id => $actviation_status ) {
		
		// Switch to each blog
		switch_to_blog( $blog_id );
		
		// Delete manifest
		pwapro_delete_manifest();
	
		// Delete service worker
		pwapro_delete_sw();
		
		/**
		 * Delete SuperPWA version info for current blog.
		 * 
		 * This is required so that pwapro_upgrader() will run and create the manifest and service worker on next activation.
		 * Known edge case: Database upgrade that relies on the version number will fail if user deactivates and later activates after SuperPWA is updated.
		 */
		delete_option( 'pwapro_version' );
	
		// Save the de-activation status of current blog.
		pwapro_multisite_activation_status( false );
		
		// Return to main site
		restore_current_blog();
	}
}
