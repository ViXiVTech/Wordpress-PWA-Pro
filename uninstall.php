<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Everything in uninstall.php will be executed when user decides to delete the plugin. 
 * 
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// If uninstall not called from WordPress, then die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) die;

/**
 * Delete database settings
 *
 * @since 1.0
 * @since 1.7 Added clean-up for pwapro_active_addons and pwapro_utm_tracking_settings
 */ 
delete_option( 'pwapro_settings' );
delete_option( 'pwapro_active_addons' );
delete_option( 'pwapro_utm_tracking_settings' );
delete_option( 'pwapro_version' );

/**
 * Clean up for Multisites
 *
 * @since 1.6
 * @since 1.7 Added clean-up for pwapro_active_addons and pwapro_utm_tracking_settings
 */
if ( is_multisite() ) {
	
	// Retrieve the list of blog ids where PWA Pro is active. (saved with blog_id as $key and activation_status as $value)
	$pwapro_sites = get_site_option( 'pwapro_active_sites' );
	
	// Loop through each active site.
	foreach( $pwapro_sites as $blog_id => $actviation_status ) {
		
		// Switch to each blog
		switch_to_blog( $blog_id );
		
		// Delete database settings for each site.
		delete_option( 'pwapro_settings' );
		delete_option( 'pwapro_active_addons' );
		delete_option( 'pwapro_utm_tracking_settings' );
		delete_option( 'pwapro_version' );
		
		// Return to main site
		restore_current_blog();
	}
	
	// Delete the list of websites where PWA Pro was activated.
	delete_site_option( 'pwapro_active_sites' );
}
