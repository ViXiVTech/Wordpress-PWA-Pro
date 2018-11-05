<?php
/**
 * UTM Tracking
 *
 * @since 1.7
 * 
 * @function	pwapro_utm_tracking_sub_menu()			Add sub-menu page for UTM Tracking
 * @function 	pwapro_utm_tracking_get_settings()		Get UTM Tracking settings
 * @function	pwapro_utm_tracking_for_start_url()		Add UTM Tracking to the start_url
 * @function 	pwapro_utm_tracking_save_settings_todo()	Todo list after saving UTM Tracking settings
 * @function 	pwapro_utm_tracking_deactivate_todo()		Deactivation Todo
 * @function 	pwapro_utm_tracking_register_settings()	Register UTM Tracking settings
 * @function	pwapro_utm_tracking_validater_sanitizer()	Validate and sanitize user input
 * @function 	pwapro_utm_tracking_section_cb()			Callback function for UTM Tracking section
 * @function 	pwapro_utm_tracking_start_url_cb()		Current Start URL
 * @function 	pwapro_utm_tracking_source_cb()			Campaign Source
 * @function 	pwapro_utm_tracking_medium_cb()			Campaign Medium
 * @function 	pwapro_utm_tracking_name_cb()				Campaign Name
 * @function 	pwapro_utm_tracking_term_cb()				Campaign Term
 * @function 	pwapro_utm_tracking_content_cb()			Campaign Content
 * @function	pwapro_utm_tracking_interface_render()	UTM Tracking UI renderer
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add sub-menu page for UTM Tracking
 * 
 * @since 1.7
 */
function pwapro_utm_tracking_sub_menu() {
	
	// UTM Tracking sub-menu
	add_submenu_page( 'pwapro', __( 'PWA Pro', 'pwa-pro' ), __( 'UTM Tracking', 'pwa-pro' ), 'manage_options', 'pwapro-utm-tracking', 'pwapro_utm_tracking_interface_render' );
}
add_action( 'admin_menu', 'pwapro_utm_tracking_sub_menu' );

/**
 * Get UTM Tracking settings
 *
 * @since 1.7
 */
function pwapro_utm_tracking_get_settings() {
	
	$defaults = array(
				'utm_source'		=> 'pwapro',
			);
	
	return get_option( 'pwapro_utm_tracking_settings', $defaults );
}

/**
 * Add UTM Tracking to the start_url
 * 
 * Hooks onto the pwapro_manifest_start_url filter to add the 
 * UTM tracking parameters to the start_url
 *
 * Example: https://pwapro.com/?utm_source=pwapro&utm_medium=medium&utm_campaign=name&utm_term=terms&utm_content=content
 * 
 * @param $start_url (string) the start_url for manifest from pwapro_get_start_url()
 * @return (string) Filtered start_url with UTM tracking added
 * 
 * @since 1.7
 */
function pwapro_utm_tracking_for_start_url( $start_url ) {
	
	// Get UTM Tracking settings
	$utm_params = pwapro_utm_tracking_get_settings();
	
	// Add the initial '/?'
	$start_url = trailingslashit( $start_url ) . '?';
	
	// Build the URL
	foreach ( $utm_params as $param => $value ) {
		
		if ( ! empty( $value ) ) {
			$start_url = $start_url . $param . '=' . rawurlencode( $value ) . '&';
		}
	}
	
	// Remove trailing '&'
	return rtrim( $start_url, '&' );
}
add_filter( 'pwapro_manifest_start_url', 'pwapro_utm_tracking_for_start_url' );

/**
 * Todo list after saving UTM Tracking settings
 *
 * Regenerate manifest when settings are saved. 
 * Also used when add-on is activated and deactivated.
 *
 * @since	1.7
 */
function pwapro_utm_tracking_save_settings_todo() {
	
	// Regenerate manifest
	pwapro_generate_manifest();
}
add_action( 'add_option_pwapro_utm_tracking_settings', 'pwapro_utm_tracking_save_settings_todo' );
add_action( 'update_option_pwapro_utm_tracking_settings', 'pwapro_utm_tracking_save_settings_todo' );
add_action( 'pwapro_addon_activated_utm_tracking', 'pwapro_utm_tracking_save_settings_todo' );

/**
 * Deactivation Todo
 * 
 * Unhook the filter and regenerate manifest
 * 
 * @since 1.7
 */
function pwapro_utm_tracking_deactivate_todo() {
	
	// Unhook the UTM tracking params filter
	remove_filter( 'pwapro_manifest_start_url', 'pwapro_utm_tracking_for_start_url' );
	
	// Regenerate manifest
	pwapro_generate_manifest();
}
add_action( 'pwapro_addon_deactivated_utm_tracking', 'pwapro_utm_tracking_deactivate_todo' );

/**
 * Register UTM Tracking settings
 *
 * @since 	1.7
 */
function pwapro_utm_tracking_register_settings() {

	// Register Setting
	register_setting( 
		'pwapro_utm_tracking_settings_group',		 // Group name
		'pwapro_utm_tracking_settings', 			// Setting name = html form <input> name on settings form
		'pwapro_utm_tracking_validater_sanitizer'	// Input validator and sanitizer
	);
		
	// UTM Tracking
    add_settings_section(
        'pwapro_utm_tracking_section',				// ID
        __return_false(),								// Title
        'pwapro_utm_tracking_section_cb',				// Callback Function
        'pwapro_utm_tracking_section'					// Page slug
    );
	
		// Current Start URL
		add_settings_field(
			'pwapro_utm_tracking_start_url',						// ID
			__('Current Start URL', 'pwa-pro'),	// Title
			'pwapro_utm_tracking_start_url_cb',					// CB
			'pwapro_utm_tracking_section',						// Page slug
			'pwapro_utm_tracking_section'							// Settings Section ID
		);
		
		// Campaign Source
		add_settings_field(
			'pwapro_utm_tracking_source',							// ID
			__('Campaign Source', 'pwa-pro'),	// Title
			'pwapro_utm_tracking_source_cb',						// CB
			'pwapro_utm_tracking_section',						// Page slug
			'pwapro_utm_tracking_section'							// Settings Section ID
		);
		
		// Campaign Medium
		add_settings_field(
			'pwapro_utm_tracking_medium',							// ID
			__('Campaign Medium', 'pwa-pro'),	// Title
			'pwapro_utm_tracking_medium_cb',						// CB
			'pwapro_utm_tracking_section',						// Page slug
			'pwapro_utm_tracking_section'							// Settings Section ID
		);	
		
		// Campaign Name
		add_settings_field(
			'pwapro_utm_tracking_name',							// ID
			__('Campaign Name', 'pwa-pro'),		// Title
			'pwapro_utm_tracking_name_cb',						// CB
			'pwapro_utm_tracking_section',						// Page slug
			'pwapro_utm_tracking_section'							// Settings Section ID
		);
		
		// Campaign Term
		add_settings_field(
			'pwapro_utm_tracking_term',							// ID
			__('Campaign Term', 'pwa-pro'),		// Title
			'pwapro_utm_tracking_term_cb',						// CB
			'pwapro_utm_tracking_section',						// Page slug
			'pwapro_utm_tracking_section'							// Settings Section ID
		);	
		
		// Campaign Content
		add_settings_field(
			'pwapro_utm_tracking_content',						// ID
			__('Campaign Content', 'pwa-pro'),	// Title
			'pwapro_utm_tracking_content_cb',						// CB
			'pwapro_utm_tracking_section',						// Page slug
			'pwapro_utm_tracking_section'							// Settings Section ID
		);	
}
add_action( 'admin_init', 'pwapro_utm_tracking_register_settings' );

/**
 * Validate and sanitize user input
 *
 * @since 1.7
 */
function pwapro_utm_tracking_validater_sanitizer( $settings ) {
	
	// Sanitize and validate campaign source. Campaign source cannot be empty.
	$settings['utm_source'] = sanitize_text_field( $settings['utm_source'] ) == '' ? 'pwapro' : sanitize_text_field( $settings['utm_source'] );
	
	// Sanitize campaign medium
	$settings['utm_medium'] = sanitize_text_field( $settings['utm_medium'] );
	
	// Sanitize campaign name
	$settings['utm_campaign'] = sanitize_text_field( $settings['utm_campaign'] );
	
	// Sanitize campaign term
	$settings['utm_term'] = sanitize_text_field( $settings['utm_term'] );
	
	// Sanitize campaign medium
	$settings['utm_content'] = sanitize_text_field( $settings['utm_content'] );
	
	return $settings;
}

/**
 * Callback function for UTM Tracking section
 *
 * @since 1.7
 */
function pwapro_utm_tracking_section_cb() {
	
	// Get add-on info
	$addon_utm_tracking = pwapro_get_addons( 'utm_tracking' );
	
	printf( '<p>' . __( 'This add-on automatically adds UTM campaign parameters to the <code>Start Page</code> URL in your <a href="%s" target="_blank">manifest</a>. This will help you identify visitors coming specifically from your app. <a href="%s" target="_blank">Read more</a> about UTM Tracking.', 'pwa-pro' ) . '</p>', pwapro_manifest( 'src' ), $addon_utm_tracking['link'] . '?utm_source=pwapro-plugin&utm_medium=utm-tracking-settings' );
}

/**
 * Current Start URL
 *
 * @since 1.7
 */
function pwapro_utm_tracking_start_url_cb() {
	
	echo '<code style="word-break: break-all;">' . pwapro_get_start_url( true ) . '</code>';
}

/**
 * Campaign Source
 *
 * @since 1.7
 */
function pwapro_utm_tracking_source_cb() {

	// Get Settings
	$settings = pwapro_utm_tracking_get_settings(); ?>
	
	<fieldset>
		
		<input type="text" name="pwapro_utm_tracking_settings[utm_source]" class="regular-text" value="<?php if ( isset( $settings['utm_source'] ) && ( ! empty($settings['utm_source']) ) ) echo esc_attr( $settings['utm_source'] ); ?>"/>
		
	</fieldset>
	
	<p class="description">
		<?php _e( 'Campaign Source is mandatory and defaults to <code>pwapro</code>. The remaining fields are optional.', 'pwa-pro' ); ?>
	</p>

	<?php
}

/**
 * Campaign Medium
 *
 * @since 1.7
 */
function pwapro_utm_tracking_medium_cb() {

	// Get Settings
	$settings = pwapro_utm_tracking_get_settings(); ?>
	
	<fieldset>
		
		<input type="text" name="pwapro_utm_tracking_settings[utm_medium]" placeholder="Optional" class="regular-text" value="<?php if ( isset( $settings['utm_medium'] ) && ( ! empty($settings['utm_medium']) ) ) echo esc_attr( $settings['utm_medium'] ); ?>"/>
		
	</fieldset>

	<?php
}

/**
 * Campaign Name
 *
 * @since 1.7
 */
function pwapro_utm_tracking_name_cb() {

	// Get Settings
	$settings = pwapro_utm_tracking_get_settings(); ?>
	
	<fieldset>
		
		<input type="text" name="pwapro_utm_tracking_settings[utm_campaign]" placeholder="Optional" class="regular-text" value="<?php if ( isset( $settings['utm_campaign'] ) && ( ! empty($settings['utm_campaign']) ) ) echo esc_attr( $settings['utm_campaign'] ); ?>"/>
		
	</fieldset>

	<?php
}

/**
 * Campaign Term
 *
 * @since 1.7
 */
function pwapro_utm_tracking_term_cb() {

	// Get Settings
	$settings = pwapro_utm_tracking_get_settings(); ?>
	
	<fieldset>
		
		<input type="text" name="pwapro_utm_tracking_settings[utm_term]" placeholder="Optional" class="regular-text" value="<?php if ( isset( $settings['utm_term'] ) && ( ! empty($settings['utm_term']) ) ) echo esc_attr( $settings['utm_term'] ); ?>"/>
		
	</fieldset>

	<?php
}

/**
 * Campaign Content
 *
 * @since 1.7
 */
function pwapro_utm_tracking_content_cb() {

	// Get Settings
	$settings = pwapro_utm_tracking_get_settings(); ?>
	
	<fieldset>
		
		<input type="text" name="pwapro_utm_tracking_settings[utm_content]" placeholder="Optional" class="regular-text" value="<?php if ( isset( $settings['utm_content'] ) && ( ! empty($settings['utm_content']) ) ) echo esc_attr( $settings['utm_content'] ); ?>"/>
		
	</fieldset>

	<?php
}

/**
 * UTM Tracking UI renderer
 *
 * @since 1.7
 */ 
function pwapro_utm_tracking_interface_render() {
	
	// Authentication
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	// Handing save settings
	if ( isset( $_GET['settings-updated'] ) ) {
		
		// Add settings saved message with the class of "updated"
		add_settings_error( 'pwapro_settings_group', 'pwapro_utm_tracking_settings_saved_message', __( 'Settings saved.', 'pwa-pro' ), 'updated' );
		
		// Show Settings Saved Message
		settings_errors( 'pwapro_settings_group' );
	}
	
	?>
	
	<div class="wrap">	
		<h1><?php _e( 'UTM Tracking for', 'pwa-pro' ); ?> PWA Pro <sup><?php echo PWAPRO_VERSION; ?></sup></h1>
		
		<form action="options.php" method="post" enctype="multipart/form-data">		
			<?php
			// Output nonce, action, and option_page fields for a settings page.
			settings_fields( 'pwapro_utm_tracking_settings_group' );
			
			// Status
			do_settings_sections( 'pwapro_utm_tracking_section' );	// Page slug
			
			// Output save settings button
			submit_button( __('Save Settings', 'pwa-pro') );
			?>
		</form>
	</div>
	<?php
}
