<?php
/**
 * Plugin Name: PWA Pro
 * Plugin URI: https://www.vixiv.net
 * Description: PWA Pro helps create Progressive Web Apps out of Responsive Wordpress sites instantly.
 * Author: SuperPWA, ViXiV Technologies
 * Author URI: https://superpwa.com/?utm_source=superpwa-plugin&utm_medium=author-uri
 * Author URI: https://www.ViXiV.net
 * Contributors: Arun Basil Lal, Jose Varghese, ViXiV Technologies
 * Version: 3.0
 * Text Domain: pwa-pro
 * Domain Path: /languages
 * License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * ~ Directory Structure ~
 *
 * Based on the WordPress starter plugin template
 * @link https://github.com/arunbasillal/WordPress-Starter-Plugin
 *
 * /3rd-party/					- Functions for compatibility with 3rd party plugins and hosts.
 * /addons/ 					- Bundled add-ons
 * /admin/						- Plugin backend.
 * /functions/					- Functions and utilites.
 * /includes/					- External third party classes and libraries.
 * /languages/					- Translation files go here. 
 * /public/ 					- Front end files go here.
 * index.php					- Dummy file.
 * license.txt					- GPL v2
 * loader.php					- Loads everything.
 * pwapro.php					- Main plugin file.
 * README.MD					- Readme for GitHub.
 * readme.txt					- Readme for WordPress plugin repository.
 * uninstall.php				- Fired when the plugin is uninstalled. 
 */

// Exit if accessed directly
if ( ! defined('ABSPATH') ) exit;

/**
 * Define constants
 *
 * @since 1.0
 * @since 1.6 Depreciated constants for multisite compatibility: SUPERPWA_MANIFEST_FILENAME, SUPERPWA_MANIFEST_ABS, SUPERPWA_MANIFEST_SRC
 * @since 1.6 Depreciated constants for multisite compatibility: SUPERPWA_SW_FILENAME, SUPERPWA_SW_ABS, SUPERPWA_SW_SRC
 */
if ( ! defined( 'PWAPRO_VERSION' ) )		define( 'PWA_VERSION'	, '3.0' ); // SuperPWA current version
if ( ! defined( 'PWAPRO_PATH_ABS' ) ) 	define( 'PWA_PATH_ABS'	, plugin_dir_path( __FILE__ ) ); // Absolute path to the plugin directory. eg - /var/www/html/wp-content/plugins/super-progressive-web-apps/
if ( ! defined( 'PWAPRO_PATH_SRC' ) ) 	define( 'PWA_PATH_SRC'	, plugin_dir_url( __FILE__ ) ); // Link to the plugin folder. eg - https://example.com/wp-content/plugins/super-progressive-web-apps/

// Load everything
require_once( SUPERPWA_PATH_ABS . 'loader.php' );
