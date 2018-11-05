<?php
/**
 * Manifest related functions of SuperPWA
 *
 * @since 1.0
 * 
 * @function	pwapro_manifest()						Manifest filename, absolute path and link
 * @function	pwapro_generate_manifest()			Generate and write manifest
 * @function	pwapro_add_manifest_to_wp_head()		Add manifest to header (wp_head)
 * @function	pwapro_register_service_worker()		Register service worker in the footer (wp_footer)
 * @function	pwapro_delete_manifest()				Delete manifest
 * @function 	pwapro_get_pwa_icons()				Get PWA Icons
 * @function	pwapro_get_scope()					Get navigation scope of PWA
 * @function	pwapro_get_orientation()				Get orientation of PWA
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Manifest filename, absolute path and link
 *
 * For Multisite compatibility. Used to be constants defined in pwapro.php
 * On a multisite, each sub-site needs a different manifest file.
 *
 * @param $arg 	filename for manifest filename (replaces PWAPRO_MANIFEST_FILENAME)
 *				abs for absolute path to manifest (replaces PWAPRO_MANIFEST_ABS)
 *				src for link to manifest (replaces PWAPRO_MANIFEST_SRC). Default value
 *
 * @return String filename, absolute path or link to manifest. 
 *  
 * @since 1.6
 */
function pwapro_manifest( $arg = 'src' ) {
	
	$manifest_filename = 'pwapro-manifest' . pwapro_multisite_filename_postfix() . '.json';
	
	switch( $arg ) {
		
		// Name of Manifest file
		case 'filename': 
			return $manifest_filename;
			break;
		
		// Absolute path to manifest		
		case 'abs':
			return trailingslashit( ABSPATH ) . $manifest_filename;
			break;
		
		// Link to manifest
		case 'src':
		default:
			return trailingslashit( network_site_url() ) . $manifest_filename;
			break;
	}
}

/**
 * Generate and write manifest into WordPress root folder
 *
 * @return (boolean) true on success, false on failure.
 * 
 * @since 1.0
 * @since 1.3 Added support for 512x512 icon.
 * @since 1.4 Added orientation and scope.
 * @since 1.5 Added gcm_sender_id
 * @since 1.6 Added description
 * @since 1.8 Removed gcm_sender_id and introduced filter pwapro_manifest. gcm_sender_id is added in /3rd-party/onesignal.php
 */
function pwapro_generate_manifest() {
	
	// Get Settings
	$settings = pwapro_get_settings();
	
	$manifest 						= array();
	$manifest['name']				= $settings['app_name'];
	$manifest['short_name']			= $settings['app_short_name'];
	
	// Description
	if ( isset( $settings['description'] ) && ! empty( $settings['description'] ) ) {
		$manifest['description'] 	= $settings['description'];
	}
	
	$manifest['icons']				= pwapro_get_pwa_icons();
	$manifest['background_color']	= $settings['background_color'];
	$manifest['theme_color']		= $settings['theme_color'];
	$manifest['display']			= 'standalone';
	$manifest['orientation']		= pwapro_get_orientation();
	$manifest['start_url']			= pwapro_get_start_url( true );
	$manifest['scope']				= pwapro_get_scope();
	
	// Filter the manifest.
	$manifest = apply_filters( 'pwapro_manifest', $manifest );
	
	// Delete manifest if it exists.
	pwapro_delete_manifest();
	
	// Write the manfiest to disk.
	if ( ! pwapro_put_contents( pwapro_manifest( 'abs' ), json_encode( $manifest ) ) ) {
		return false;
	}
	
	return true;
}

/**
 * Add manifest to header (wp_head)
 *
 * @since 1.0
 * @since 1.8 Introduced filter pwapro_wp_head_tags
 * @since 1.9 Introduced filter pwapro_add_theme_color
 */
function pwapro_add_manifest_to_wp_head() {
	
	$tags  = '<!-- Manifest added by SuperPWA - Progressive Web Apps Plugin For WordPress -->' . PHP_EOL; 
	$tags .= '<link rel="manifest" href="'. parse_url( pwapro_manifest( 'src' ), PHP_URL_PATH ) . '">' . PHP_EOL;
	
	// theme-color meta tag 
	if ( apply_filters( 'pwapro_add_theme_color', true ) ) {
		
		// Get Settings
		$settings = pwapro_get_settings();
		$tags .= '<meta name="theme-color" content="'. $settings['theme_color'] .'">' . PHP_EOL;
	}
	
	$tags  = apply_filters( 'pwapro_wp_head_tags', $tags );
	
	$tags .= '<!-- / SuperPWA.com -->' . PHP_EOL; 
	
	echo $tags;
}
add_action( 'wp_head', 'pwapro_add_manifest_to_wp_head', 0 );

/**
 * Delete manifest
 *
 * @return (boolean) true on success, false on failure
 * 
 * @since 1.0
 */
function pwapro_delete_manifest() {
	return pwapro_delete( pwapro_manifest( 'abs' ) );
}

/**
 * Get PWA Icons
 *
 * @return	array	An array of icons to be used as the application icons and splash screen icons
 * @since	1.3
 */
function pwapro_get_pwa_icons() {
	
	// Get settings
	$settings = pwapro_get_settings();
	
	// Application icon
	$icons_array[] = array(
							'src' 	=> $settings['icon'],
							'sizes'	=> '192x192', // must be 192x192. Todo: use getimagesize($settings['icon'])[0].'x'.getimagesize($settings['icon'])[1] in the future
							'type'	=> 'image/png', // must be image/png. Todo: use getimagesize($settings['icon'])['mime']
						);
	
	// Splash screen icon - Added since 1.3
	if ( @$settings['splash_icon'] != '' ) {
		
		$icons_array[] = array(
							'src' 	=> $settings['splash_icon'],
							'sizes'	=> '512x512', // must be 512x512.
							'type'	=> 'image/png', // must be image/png
						);
	}
	
	return $icons_array;
}

/**
 * Get navigation scope of PWA
 *
 * @return	string	Relative path to the folder where WordPress is installed. Same folder as manifest and wp-config.php
 * @since	1.4
 */
function pwapro_get_scope() {
	return parse_url( trailingslashit( get_bloginfo( 'wpurl' ) ), PHP_URL_PATH );
}

/**
 * Get orientation of PWA
 *
 * @return	string	Orientation of PWA as set in the plugin settings. 
 * @since	1.4
 */
function pwapro_get_orientation() {
	
	// Get Settings
	$settings = pwapro_get_settings();
	
	$orientation = isset( $settings['orientation'] ) ? $settings['orientation'] : 0;
	
	switch ( $orientation ) {
		
		case 0:
			return 'any';
			break;
			
		case 1:
			return 'portrait';
			break;
			
		case 2:
			return 'landscape';
			break;
			
		default: 
			return 'any';
	}
}
