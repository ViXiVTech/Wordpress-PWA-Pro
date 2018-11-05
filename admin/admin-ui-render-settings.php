<?php
/**
 * Admin UI setup and render
 *
 * @since 1.0
 * 
 * @function	pwapro_app_name_cb()					Application Name
 * @function	pwapro_app_short_name_cb()			Application Short Name
 * @function	pwapro_description_cb()				Description
 * @function	pwapro_background_color_cb()			Splash Screen Background Color
 * @function	pwapro_theme_color_cb()				Theme Color
 * @function	pwapro_app_icon_cb()					Application Icon
 * @function	pwapro_app_icon_cb()					Splash Screen Icon
 * @function	pwapro_start_url_cb()					Start URL Dropdown
 * @function	pwapro_offline_page_cb()				Offline Page Dropdown
 * @function	pwapro_orientation_cb()				Default Orientation Dropdown
 * @function	pwapro_manifest_status_cb()			Manifest Status
 * @function	pwapro_sw_status_cb()					Service Worker Status
 * @function	pwapro_https_status_cb()				HTTPS Status
 * @function	pwapro_admin_interface_render()		Admin interface renderer
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Application Name
 *
 * @since 1.2
 */
function pwapro_app_name_cb() {

	// Get Settings
	$settings = pwapro_get_settings(); ?>
	
	<fieldset>
		
		<input type="text" name="pwapro_settings[app_name]" class="regular-text" value="<?php if ( isset( $settings['app_name'] ) && ( ! empty($settings['app_name']) ) ) echo esc_attr($settings['app_name']); ?>"/>
		
	</fieldset>

	<?php
}

/**
 * Application Short Name
 *
 * @since 1.2
 */
function pwapro_app_short_name_cb() {

	// Get Settings
	$settings = pwapro_get_settings(); ?>
	
	<fieldset>
		
		<input type="text" name="pwapro_settings[app_short_name]" class="regular-text" value="<?php if ( isset( $settings['app_short_name'] ) && ( ! empty($settings['app_short_name']) ) ) echo esc_attr($settings['app_short_name']); ?>"/>
		
		<p class="description">
			<?php _e('Used when there is insufficient space to display the full name of the application. <code>12</code> characters or less.', 'pwa-pro'); ?>
		</p>
		
	</fieldset>

	<?php
}

/**
 * Description
 *
 * @since 1.6
 */
function pwapro_description_cb() {

	// Get Settings
	$settings = pwapro_get_settings(); ?>
	
	<fieldset>
		
		<input type="text" name="pwapro_settings[description]" class="regular-text" value="<?php if ( isset( $settings['description'] ) && ( ! empty( $settings['description'] ) ) ) echo esc_attr( $settings['description'] ); ?>"/>
		
		<p class="description">
			<?php _e( 'A brief description of what your app is about.', 'pwa-pro' ); ?>
		</p>
		
	</fieldset>

	<?php
}

/**
 * Application Icon
 *
 * @since 1.0
 */
function pwapro_app_icon_cb() {

	// Get Settings
	$settings = pwapro_get_settings(); ?>
	
	<!-- Application Icon -->
	<input type="text" name="pwapro_settings[icon]" id="pwapro_settings[icon]" class="pwapro-icon regular-text" size="50" value="<?php echo isset( $settings['icon'] ) ? esc_attr( $settings['icon']) : ''; ?>">
	<button type="button" class="button pwapro-icon-upload" data-editor="content">
		<span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php _e( 'Choose Icon', 'pwa-pro' ); ?>
	</button>
	
	<p class="description">
		<?php _e('This will be the icon of your app when installed on the phone. Must be a <code>PNG</code> image exactly <code>192x192</code> in size.', 'pwa-pro'); ?>
	</p>

	<?php
}

/**
 * Splash Screen Icon
 *
 * @since 1.3
 */
function pwapro_splash_icon_cb() {

	// Get Settings
	$settings = pwapro_get_settings(); ?>
	
	<!-- Splash Screen Icon -->
	<input type="text" name="pwapro_settings[splash_icon]" id="pwapro_settings[splash_icon]" class="pwapro-splash-icon regular-text" size="50" value="<?php echo isset( $settings['splash_icon'] ) ? esc_attr( $settings['splash_icon']) : ''; ?>">
	<button type="button" class="button pwapro-splash-icon-upload" data-editor="content">
		<span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php _e( 'Choose Icon', 'pwa-pro' ); ?>
	</button>
	
	<p class="description">
		<?php _e('This icon will be displayed on the splash screen of your app on supported devices. Must be a <code>PNG</code> image exactly <code>512x512</code> in size.', 'pwa-pro'); ?>
	</p>

	<?php
}

/**
 * Splash Screen Background Color
 *
 * @since 1.0
 */
function pwapro_background_color_cb() {

	// Get Settings
	$settings = pwapro_get_settings(); ?>
	
	<!-- Background Color -->
	<input type="text" name="pwapro_settings[background_color]" id="pwapro_settings[background_color]" class="pwapro-colorpicker" value="<?php echo isset( $settings['background_color'] ) ? esc_attr( $settings['background_color']) : '#D5E0EB'; ?>" data-default-color="#D5E0EB">
	
	<p class="description">
		<?php _e('Background color of the splash screen.', 'pwa-pro'); ?>
	</p>

	<?php
}

/**
 * Theme Color
 *
 * @since 1.4
 */
function pwapro_theme_color_cb() {

	// Get Settings
	$settings = pwapro_get_settings(); ?>
	
	<!-- Theme Color -->
	<input type="text" name="pwapro_settings[theme_color]" id="pwapro_settings[theme_color]" class="pwapro-colorpicker" value="<?php echo isset( $settings['theme_color'] ) ? esc_attr( $settings['theme_color']) : '#D5E0EB'; ?>" data-default-color="#D5E0EB">
	
	<p class="description">
		<?php _e('Theme color is used on supported devices to tint the UI elements of the browser and app switcher. When in doubt, use the same color as <code>Background Color</code>.', 'pwa-pro'); ?>
	</p>

	<?php
}

/**
 * Start URL Dropdown
 *
 * @since 1.2
 */
function pwapro_start_url_cb() {

	// Get Settings
	$settings = pwapro_get_settings(); ?>
	
	<fieldset>
	
		<!-- WordPress Pages Dropdown -->
		<label for="pwapro_settings[start_url]">
		<?php echo wp_dropdown_pages( array( 
				'name' => 'pwapro_settings[start_url]', 
				'echo' => 0, 
				'show_option_none' => __( '&mdash; Homepage &mdash;' ), 
				'option_none_value' => '0', 
				'selected' =>  isset($settings['start_url']) ? $settings['start_url'] : '',
			)); ?>
		</label>
		
		<p class="description">
			<?php printf( __( 'Specify the page to load when the application is launched from a device. Current start page is <code>%s</code>', 'pwa-pro' ), pwapro_get_start_url() ); ?>
		</p>
		
		<?php if ( pwapro_is_amp() ) { ?>
		
			<!--  AMP Page As Start Page -->
			<br><input type="checkbox" name="pwapro_settings[start_url_amp]" id="pwapro_settings[start_url_amp]" value="1" 
				<?php if ( isset( $settings['start_url_amp'] ) ) { checked( '1', $settings['start_url_amp'] ); } ?>>
				<label for="pwapro_settings[start_url_amp]"><?php _e('Use AMP version of the start page.', 'pwa-pro') ?></label>
				<br>
			
			<!-- AMP for WordPress 0.6.2 doesn't support homepage, the blog index, and archive pages. -->
			<?php if ( is_plugin_active( 'amp/amp.php' ) ) { ?>
				<p class="description">
					<?php _e( 'Do not check this if your start page is the homepage, the blog index, or the archives page. AMP for WordPress does not create AMP versions for these pages.', 'pwa-pro' ); ?>
				</p>
			<?php } ?>
			
			<!-- tagDiv AMP 1.2 doesn't enable AMP for pages by default and needs to be enabled manually in settings -->			
			<?php if ( is_plugin_active( 'td-amp/td-amp.php' ) && method_exists( 'td_util', 'get_option' ) ) { 
				
				// Read option value from db
				$td_amp_page_post_type = td_util::get_option( 'tds_amp_post_type_page' );

				// Show notice if option to enable AMP for pages is disabled.
				if ( empty( $td_amp_page_post_type ) ) { ?>
					<p class="description">
						<?php printf( __( 'Please enable AMP support for Page in <a href="%s">Theme Settings > Theme Panel</a> > AMP > Post Type Support.', 'pwa-pro' ), admin_url( 'admin.php?page=td_theme_panel' ) ); ?>
					</p>
				<?php }
			} ?>
				
		<?php } ?>
	
	</fieldset>

	<?php
}

/**
 * Offline Page Dropdown
 *
 * @since 1.1
 */
function pwapro_offline_page_cb() {

	// Get Settings
	$settings = pwapro_get_settings(); ?>
	
	<!-- WordPress Pages Dropdown -->
	<label for="pwapro_settings[offline_page]">
	<?php echo wp_dropdown_pages( array( 
			'name' => 'pwapro_settings[offline_page]', 
			'echo' => 0, 
			'show_option_none' => __( '&mdash; Default &mdash;' ), 
			'option_none_value' => '0', 
			'selected' =>  isset($settings['offline_page']) ? $settings['offline_page'] : '',
		)); ?>
	</label>
	
	<p class="description">
		<?php printf( __( 'Offline page is displayed when the device is offline and the requested page is not already cached. Current offline page is <code>%s</code>', 'pwa-pro' ), get_permalink($settings['offline_page']) ? get_permalink( $settings['offline_page'] ) : get_bloginfo( 'wpurl' ) ); ?>
	</p>

	<?php
}

/**
 * Default Orientation Dropdown
 *
 * @since 1.4
 */
function pwapro_orientation_cb() {

	// Get Settings
	$settings = pwapro_get_settings(); ?>
	
	<!-- Orientation Dropdown -->
	<label for="pwapro_settings[orientation]">
		<select name="pwapro_settings[orientation]" id="pwapro_settings[orientation]">
			<option value="0" <?php if ( isset( $settings['orientation'] ) ) { selected( $settings['orientation'], 0 ); } ?>>
				<?php _e( 'Follow Device Orientation', 'pwa-pro' ); ?>
			</option>
			<option value="1" <?php if ( isset( $settings['orientation'] ) ) { selected( $settings['orientation'], 1 ); } ?>>
				<?php _e( 'Portrait', 'pwa-pro' ); ?>
			</option>
			<option value="2" <?php if ( isset( $settings['orientation'] ) ) { selected( $settings['orientation'], 2 ); } ?>>
				<?php _e( 'Landscape', 'pwa-pro' ); ?>
			</option>
		</select>
	</label>
	
	<p class="description">
		<?php _e( 'Set the orientation of your app on devices. When set to <code>Follow Device Orientation</code> your app will rotate as the device is rotated.', 'pwa-pro' ); ?>
	</p>

	<?php
}

/**
 * Manifest Status
 *
 * @since 1.2
 * @since 1.8 Attempt to generate manifest again if the manifest doesn't exist.
 */
function pwapro_manifest_status_cb() {
	
	/** 
	 * Check to see if the manifest exists, If not attempts to generate a new one.
	 * 
	 * Users who had permissions issue in the beginning will check the status after changing file system permissions. 
	 * At this point we try to generate the manifest and service worker to see if its possible with the new permissions. 
	 */
	if ( pwapro_get_contents( pwapro_manifest( 'abs' ) ) || pwapro_generate_manifest() ) {
		
		printf( '<p><span class="dashicons dashicons-yes" style="color: #46b450;"></span> ' . __( 'Manifest generated successfully. You can <a href="%s" target="_blank">see it here &rarr;</a>', 'pwa-pro' ) . '</p>', pwapro_manifest( 'src' ) );
	} else {
		
		printf( '<p><span class="dashicons dashicons-no-alt" style="color: #dc3232;"></span> ' . __( 'Manifest generation failed. Check if WordPress can write to your root folder (the same folder with wp-config.php). <a href="%s" target="_blank">Read more &rarr;</a>', 'pwa-pro' ) . '</p>', 'https://pwapro.com/doc/fixing-manifest-service-worker-generation-failed-error/?utm_source=pwapro-plugin&utm_medium=settings-status-no-manifest' );
	}
}

/**
 * Service Worker Status
 *
 * @since 1.2
 * @since 1.8 Attempt to generate service worker again if it doesn't exist.
 */
function pwapro_sw_status_cb() {

	// See pwapro_manifest_status_cb() for documentation.
	if ( pwapro_get_contents( pwapro_sw( 'abs' ) ) || pwapro_generate_sw() ) {
		
		printf( '<p><span class="dashicons dashicons-yes" style="color: #46b450;"></span> ' . __( 'Service worker generated successfully.', 'pwa-pro' ) . '</p>' );
	} else {
		
		printf( '<p><span class="dashicons dashicons-no-alt" style="color: #dc3232;"></span> ' . __( 'Service worker generation failed. Check if WordPress can write to your root folder (the same folder with wp-config.php). <a href="%s" target="_blank">Read more &rarr;</a>', 'pwa-pro' ) . '</p>', 'https://pwapro.com/doc/fixing-manifest-service-worker-generation-failed-error/?utm_source=pwapro-plugin&utm_medium=settings-status-no-sw' );
	}
}

/**
 * HTTPS Status
 *
 * @since 1.2
 */
function pwapro_https_status_cb() {

	if ( is_ssl() ) {
		
		printf( '<p><span class="dashicons dashicons-yes" style="color: #46b450;"></span> ' . __( 'Your website is served over HTTPS.', 'pwa-pro' ) . '</p>' );
	} else {
		
		printf( '<p><span class="dashicons dashicons-no-alt" style="color: #dc3232;"></span> ' . __( 'Progressive Web Apps require that your website is served over HTTPS. Please contact your host to add a SSL certificate to your domain.', 'pwa-pro' ) . '</p>' );
	}
}
 
/**
 * Admin interface renderer
 *
 * @since 1.0
 * @since 1.7 Handling of settings saved messages since UI is its own menu item in the admin menu.
 */ 
function pwapro_admin_interface_render() {
	
	// Authentication
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	// Handing save settings
	if ( isset( $_GET['settings-updated'] ) ) {
		
		// Add settings saved message with the class of "updated"
		add_settings_error( 'pwapro_settings_group', 'pwapro_settings_saved_message', __( 'Settings saved.', 'pwa-pro' ), 'updated' );
		
		// Show Settings Saved Message
		settings_errors( 'pwapro_settings_group' );
	}
	
	?>
	
	<div class="wrap">	
		<h1>Super Progressive Web Apps <sup><?php echo SUPERPWA_VERSION; ?></sup></h1>
		
		<form action="options.php" method="post" enctype="multipart/form-data">		
			<?php
			// Output nonce, action, and option_page fields for a settings page.
			settings_fields( 'pwapro_settings_group' );
			
			// Basic Application Settings
			do_settings_sections( 'pwapro_basic_settings_section' );	// Page slug
			
			// Status
			do_settings_sections( 'pwapro_pwa_status_section' );	// Page slug
			
			// Output save settings button
			submit_button( __('Save Settings', 'pwa-pro') );
			?>
		</form>
	</div>
	<?php
}
