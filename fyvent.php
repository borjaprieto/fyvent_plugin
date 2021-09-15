<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://fyvent.com
 * @since             1.0.0
 * @package           fyvent
 *
 * @wordpress-plugin
 * Plugin Name:       fyvent
 * Plugin URI:        https://fyvent.com
 * Description:       event organization solution.
 * Version:           1.0.0
 * Author:            fyvent
 * Author URI:        https://fyvent.com/
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       fyvent
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FYVENT_VERSION', '1.0.0' );


require_once plugin_dir_path( __FILE__ ) . '/vendor/cmb2/cmb2/init.php';

// Includes code for setup, shortcodes, general functions and CMB2 non-standard fields
require_once plugin_dir_path( __FILE__ ) . 'includes/setup.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/venue.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/lib/cmb2-attached-posts/cmb2-attached-posts-field.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/lib/cmb2-field-map/cmb-field-map.php';

// Hook for plugin set up on activation
register_activation_hook( __FILE__, 'fyv_initial_setup' );

/**
 * Gets code to show dashboard.
 *
 * @since 1.0.0
 */
function fyv_dashboard_page() {
	require_once plugin_dir_path( __FILE__ ) . 'admin/dashboard.php';
}

/**
 * Loads admin css files.
 *
 * @since 1.0.0
 */
function fyv_load_styles() {
	if ( is_admin() ) {
		wp_enqueue_style(
			'fyvent-plugin-admin',
			plugins_url( 'admin/css/fyvent-admin.css', __FILE__ ), [],
			FYVENT_VERSION,
			'screen'
		);
		wp_enqueue_style(
			'fyvent-plugin-admin2',
			plugins_url( 'admin/css/skeleton.css', __FILE__ ), [],
			FYVENT_VERSION,
			'screen'
		);
	}
}
add_action( 'admin_enqueue_scripts', 'fyv_load_styles' );

/**
 * Creates a menu in the WordPress Dashboard.
 *
 * @since 1.0.0
 */
function fyv_add_dashboard_menu() {
	add_menu_page( esc_html__('Fyvent', 'fyvent' ),
		esc_html__('Fyvent', 'fyvent' ),
		'manage_options', 'fyv_dashboard');
	add_submenu_page( 'main-options',
		esc_html__( 'Dashboard', 'fyvent' ),
		esc_html__( 'Dashboard', 'fyvent' ),
		'manage_options', 'fyv_dashboard', 'fyv_dashboard_page', 1
	);
}
add_action( 'admin_menu', 'fyv_add_dashboard_menu', 99 );


/**
 * Do not forget about translating your plugin, use esc_html__('english string', 'your_uniq_plugin_name') to retrieve translated string
 * and _e('english string', 'your_uniq_plugin_name') to echo it
 * in this example plugin your_uniq_plugin_name == cltd_example
 *
 * to create translation file, use poedit FileNew catalog...
 * Fill name of project, add "." to path (ENSURE that it was added - must be in list)
 * and on last tab add "__" and "_e"
 *
 * Name your file like this: [my_plugin]-[ru_RU].po
 *
 * http://codex.wordpress.org/Writing_a_Plugin#Internationalizing_Your_Plugin
 * http://codex.wordpress.org/I18n_for_WordPress_Developers
 */
function fyvent_languages() {     load_plugin_textdomain( 'fyvent', false, dirname( plugin_basename( __FILE__ ) ) );
}
add_action( 'init', 'fyvent_languages' );

