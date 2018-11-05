<?php
/**
 * Admin setup for the plugin
 *
 * @since 1.0
 * @function	pwapro_add_menu_links()			Add admin menu pages
 * @function	pwapro_register_settings		Register Settings
 * @function	pwapro_validater_and_sanitizer()	Validate And Sanitize User Input Before Its Saved To Database
 * @function	pwapro_get_settings()			Get settings from database
 * @function 	pwapro_enqueue_css_js()			Enqueue CSS and JS
 * @function	pwapro_after_save_settings_todo()	Todo list after saving admin options
 * @function	pwapro_footer_text()			Admin footer text
 * @function	pwapro_footer_version()			Admin footer version
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 
 
/**
 * Add admin menu pages
 *
 * @since 	1.0
 * @refer	https://developer.wordpress.org/plugins/administration-menus/
 */
function pwapro_add_menu_links() {
	
	// Main menu page
	add_menu_page( __( 'PWA Pro', 'pwa-pro' ), __( 'PWAPro', 'pwa-pro' ), 'manage_options', 'pwapro','pwapro_admin_interface_render', PWAPRO_PATH_SRC. 'admin/img/pwapro-menu-icon.png', 100 );
	
	// Settings page - Same as main menu page
	add_submenu_page( 'pwapro', __( 'PWA Pro', 'pwa-pro' ), __( 'Settings', 'pwa-pro' ), 'manage_options', 'pwapro', 'pwapro_admin_interface_render' );
	
	// Add-Ons page
	add_submenu_page( 'pwapro', __( 'PWA Pro', 'pwa-pro' ), __( 'Add-Ons', 'pwa-pro' ), 'manage_options', 'pwapro-addons', 'pwapro_addons_interface_render' );
}
add_action( 'admin_menu', 'pwapro_add_menu_links' );

/**
 * Register Settings
 *
 * @since 	1.0
 */
function pwapro_register_settings() {

	// Register Setting
	register_setting( 
		'pwapro_settings_group', 			// Group name
		'pwapro_settings', 				// Setting name = html form <input> name on settings form
		'pwapro_validater_and_sanitizer'	// Input sanitizer
	);
	
	// Basic Application Settings
    add_settings_section(
        'pwapro_basic_settings_section',					// ID
        __return_false(),									// Title
        '__return_false',									// Callback Function
        'pwapro_basic_settings_section'					// Page slug
    );
	
		// Application Name
		add_settings_field(
			'pwapro_app_name',									// ID
			__('Application Name', 'pwa-pro'),	// Title
			'pwapro_app_name_cb',									// CB
			'pwapro_basic_settings_section',						// Page slug
			'pwapro_basic_settings_section'						// Settings Section ID
		);
		
		// Application Short Name
		add_settings_field(
			'pwapro_app_short_name',								// ID
			__('Application Short Name', 'pwa-pro'),	// Title
			'pwapro_app_short_name_cb',							// CB
			'pwapro_basic_settings_section',						// Page slug
			'pwapro_basic_settings_section'						// Settings Section ID
		);
		
		// Description
		add_settings_field(
			'pwapro_description',									// ID
			__( 'Description', 'pwa-pro' ),		// Title
			'pwapro_description_cb',								// CB
			'pwapro_basic_settings_section',						// Page slug
			'pwapro_basic_settings_section'						// Settings Section ID
		);
		
		// Application Icon
		add_settings_field(
			'pwapro_icons',										// ID
			__('Application Icon', 'pwa-pro'),	// Title
			'pwapro_app_icon_cb',									// Callback function
			'pwapro_basic_settings_section',						// Page slug
			'pwapro_basic_settings_section'						// Settings Section ID
		);
		
		// Splash Screen Icon
		add_settings_field(
			'pwapro_splash_icon',									// ID
			__('Splash Screen Icon', 'pwa-pro'),	// Title
			'pwapro_splash_icon_cb',								// Callback function
			'pwapro_basic_settings_section',						// Page slug
			'pwapro_basic_settings_section'						// Settings Section ID
		);
		
		// Splash Screen Background Color
		add_settings_field(
			'pwapro_background_color',							// ID
			__('Background Color', 'pwa-pro'),	// Title
			'pwapro_background_color_cb',							// CB
			'pwapro_basic_settings_section',						// Page slug
			'pwapro_basic_settings_section'						// Settings Section ID
		);
		
		// Theme Color
		add_settings_field(
			'pwapro_theme_color',									// ID
			__('Theme Color', 'pwa-pro'),		// Title
			'pwapro_theme_color_cb',								// CB
			'pwapro_basic_settings_section',						// Page slug
			'pwapro_basic_settings_section'						// Settings Section ID
		);
		
		// Start URL
		add_settings_field(
			'pwapro_start_url',									// ID
			__('Start Page', 'pwa-pro'),			// Title
			'pwapro_start_url_cb',								// CB
			'pwapro_basic_settings_section',						// Page slug
			'pwapro_basic_settings_section'						// Settings Section ID
		);
		
		// Offline Page
		add_settings_field(
			'pwapro_offline_page',								// ID
			__('Offline Page', 'pwa-pro'),		// Title
			'pwapro_offline_page_cb',								// CB
			'pwapro_basic_settings_section',						// Page slug
			'pwapro_basic_settings_section'						// Settings Section ID
		);
		
		// Orientation
		add_settings_field(
			'pwapro_orientation',									// ID
			__('Orientation', 'pwa-pro'),		// Title
			'pwapro_orientation_cb',								// CB
			'pwapro_basic_settings_section',						// Page slug
			'pwapro_basic_settings_section'						// Settings Section ID
		);
		
	// PWA Status
    add_settings_section(
        'pwapro_pwa_status_section',					// ID
        __('Status', 'pwa-pro'),		// Title
        '__return_false',								// Callback Function
        'pwapro_pwa_status_section'					// Page slug
    );
	
		// Manifest status
		add_settings_field(
			'pwapro_manifest_status',								// ID
			__('Manifest', 'pwa-pro'),			// Title
			'pwapro_manifest_status_cb',							// CB
			'pwapro_pwa_status_section',							// Page slug
			'pwapro_pwa_status_section'							// Settings Section ID
		);
		
		// Service Worker status
		add_settings_field(
			'pwapro_sw_status',									// ID
			__('Service Worker', 'pwa-pro'),		// Title
			'pwapro_sw_status_cb',								// CB
			'pwapro_pwa_status_section',							// Page slug
			'pwapro_pwa_status_section'							// Settings Section ID
		);	
		
		// HTTPS status
		add_settings_field(
			'pwapro_https_status',								// ID
			__('HTTPS', 'pwa-pro'),				// Title
			'pwapro_https_status_cb',								// CB
			'pwapro_pwa_status_section',							// Page slug
			'pwapro_pwa_status_section'							// Settings Section ID
		);	
}
add_action( 'admin_init', 'pwapro_register_settings' );

/**
 * Validate and sanitize user input before its saved to database
 *
 * @since 1.0 
 * @since 1.3 Added splash_icon
 * @since 1.6 Added description
 */
function pwapro_validater_and_sanitizer( $settings ) {
	
	// Sanitize Application Name
	$settings['app_name'] = sanitize_text_field( $settings['app_name'] ) == '' ? get_bloginfo( 'name' ) : sanitize_text_field( $settings['app_name'] );
	
	// Sanitize Application Short Name
	$settings['app_short_name'] = sanitize_text_field( $settings['app_short_name'] ) == '' ? get_bloginfo( 'name' ) : sanitize_text_field( $settings['app_short_name'] );
	
	// Sanitize description
	$settings['description'] = sanitize_text_field( $settings['description'] );
	
	// Sanitize hex color input for background_color
	$settings['background_color'] = preg_match( '/#([a-f0-9]{3}){1,2}\b/i', $settings['background_color'] ) ? sanitize_text_field( $settings['background_color'] ) : '#D5E0EB';
	
	// Sanitize hex color input for theme_color
	$settings['theme_color'] = preg_match( '/#([a-f0-9]{3}){1,2}\b/i', $settings['theme_color'] ) ? sanitize_text_field( $settings['theme_color'] ) : '#D5E0EB';
	
	// Sanitize application icon
	$settings['icon'] = sanitize_text_field( $settings['icon'] ) == '' ? pwapro_httpsify( PWAPRO_PATH_SRC . 'public/images/logo.png' ) : sanitize_text_field( pwapro_httpsify( $settings['icon'] ) );
	
	// Sanitize splash screen icon
	$settings['splash_icon'] = sanitize_text_field( pwapro_httpsify( $settings['splash_icon'] ) );
	
	return $settings;
}
			
/**
 * Get settings from database
 *
 * @since 	1.0
 * @return	Array	A merged array of default and settings saved in database. 
 */
function pwapro_get_settings() {

	$defaults = array(
				'app_name'			=> get_bloginfo( 'name' ),
				'app_short_name'	=> get_bloginfo( 'name' ),
				'description'		=> get_bloginfo( 'description' ),
				'icon'				=> PWAPRO_PATH_SRC . 'public/images/logo.png',
				'splash_icon'		=> PWAPRO_PATH_SRC . 'public/images/logo-512x512.png',
				'background_color' 	=> '#D5E0EB',
				'theme_color' 		=> '#D5E0EB',
				'start_url' 		=> 0,
				'start_url_amp'		=> 0,
				'offline_page' 		=> 0,
				'orientation'		=> 1,
			);

	$settings = get_option( 'pwapro_settings', $defaults );
	
	return $settings;
}

/**
 * Enqueue CSS and JS
 *
 * @since	1.0
 */
function pwapro_enqueue_css_js( $hook ) {
	
    // Load only on PWAPro plugin pages
	if ( strpos( $hook, 'pwapro' ) === false ) {
		return;
	}
	
	// Color picker CSS
	// @refer https://make.wordpress.org/core/2012/11/30/new-color-picker-in-wp-3-5/
    wp_enqueue_style( 'wp-color-picker' );
	
	// Everything needed for media upload
	wp_enqueue_media();
	
	// Main JS
    wp_enqueue_script( 'pwapro-main-js', PWAPRO_PATH_SRC . 'admin/js/main.js', array( 'wp-color-picker' ), PWAPRO_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'pwapro_enqueue_css_js' );

/**
 * Todo list after saving admin options
 *
 * Regenerate manifest
 * Regenerate service worker
 *
 * @since	1.0
 */
function pwapro_after_save_settings_todo() {
	
	// Regenerate manifest
	pwapro_generate_manifest();
	
	// Regenerate service worker
	pwapro_generate_sw();
}
add_action( 'add_option_pwapro_settings', 'pwapro_after_save_settings_todo' );
add_action( 'update_option_pwapro_settings', 'pwapro_after_save_settings_todo' );

/**
 * Admin footer text
 *
 * A function to add footer text to the settings page of the plugin.
 * @since	1.2
 * @refer	https://codex.wordpress.org/Function_Reference/get_current_screen
 */
function pwapro_footer_text( $default ) {
    
	// Retun default on non-plugin pages
	$screen = get_current_screen();
	if ( strpos( $screen->id, 'pwapro' ) === false ) {
		return $default;
	}
	
    $pwapro_footer_text = sprintf( __( 'If you like PWAPro, please <a href="%s" target="_blank">make a donation</a> or leave a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating to support continued development. Thanks a bunch!', 'pwa-pro' ), 
	'https://millionclues.com/donate/',
	'https://wordpress.org/support/plugin/pwa-pro/reviews/?rate=5#new-post'
	);
	
	return $pwapro_footer_text;
}
add_filter( 'admin_footer_text', 'pwapro_footer_text' );

/**
 * Admin footer version
 *
 * @since	1.0
 */
function pwapro_footer_version( $default ) {
	
	// Retun default on non-plugin pages
	$screen = get_current_screen();
	if ( strpos( $screen->id, 'pwapro' ) === false ) {
		return $default;
	}
	
	return 'PWAPro ' . PWAPRO_VERSION;
}
add_filter( 'update_footer', 'pwapro_footer_version', 11 );
