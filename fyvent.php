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
 * Plugin Name:       Fyvent
 * Plugin URI:        https://fyvent.com
 * Description:       Events organization solution.
 * Version:           1.0.0
 * Author:            Fyvent
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
require_once plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';

require_once plugin_dir_path( __FILE__ ) . 'includes/venue.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/room.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/session.php';

require_once plugin_dir_path( __FILE__ ) . 'includes/attendant.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/speaker.php';

require_once plugin_dir_path( __FILE__ ) . 'admin/admin_options.php';

require_once plugin_dir_path( __FILE__ ) . 'includes/lib/cmb2-attached-posts/cmb2-attached-posts-field.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/lib/cmb2-field-map/cmb-field-map.php';

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
function fyv_add_menu() {

	$icon = 'data:image/svg+xml;base64,' . base64_encode('<svg width="20" height="20" viewBox="0 0 1720 1729" transform="scale (1, -1)" xmlns="http://www.w3.org/2000/svg"><path fill="black" d="M795 1740 c-566 -89 -890 -712 -634 -1224 51 -103 93 -158 183 -243
171 -163 392 -243 631 -230 218 13 417 104 568 261 219 227 292 561 191 865
-42 126 -99 216 -204 322 -124 125 -237 191 -405 234 -75 19 -250 27 -330 15z
m431 -159 c90 -41 99 -120 17 -145 -28 -9 -57 6 -106 53 -34 32 -44 36 -92 36
-65 0 -99 -22 -135 -86 -23 -41 -25 -56 -28 -196 l-4 -153 125 0 c101 0 129
-3 141 -16 21 -20 20 -30 -4 -54 -18 -18 -33 -20 -140 -20 l-120 0 0 -367 c0
-203 -5 -386 -10 -408 -12 -55 -40 -75 -103 -75 -92 0 -87 -26 -87 442 l0 408
-66 0 c-76 0 -104 12 -104 43 0 38 19 47 96 47 l71 0 7 71 c21 228 118 371
289 424 65 21 204 18 253 -4z"/></svg>');

	add_menu_page( esc_html__('Fyvent', 'fyvent' ),
		esc_html__('Fyvent', 'fyvent' ),
		'manage_options', 'fyv_menu', '',
		$icon
	);
	add_submenu_page( 'fyv_menu',
		esc_html__( 'Options', 'fyvent' ),
		esc_html__( 'Options', 'fyvent' ),
		'manage_options', 'fyv_options', 'fyv_options_page', 1
	);
	remove_submenu_page('fyv_menu','fyv_menu');
}
add_action( 'admin_menu', 'fyv_add_menu', 99 );


// Creates settings page
add_action( 'admin_init', 'fyv_settings_init' );
// Creates messages page
add_action( 'admin_init', 'fyv_messages_init' );
// Removes the date filter in admin tables for custom types
add_action('admin_head', 'fyv_remove_date_filter' );
// Removes admin color scheme options
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
// Removes the leftover 'Visual Editor', 'Keyboard Shortcuts' and 'Toolbar' options.
add_action( 'admin_head', function () {
	ob_start( function( $subject ) {
		$subject = preg_replace( '#<h[0-9]>'.__("Personal Options").'</h[0-9]>.+?/table>#s', '', $subject, 1 );
		return $subject;
	});
});
add_action( 'admin_footer', function(){ ob_end_flush(); });


/**
 * Removes access to the WordPress Dashboard for non-admin users
 *
 * @since 1.0.0
 */
function fyv_block_dashboard() {
	if ( is_admin() && ! current_user_can( 'administrator' ) &&
		!( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			wp_redirect( home_url() );
		exit;
	}
}
add_action( 'init', 'fyv_block_dashboard' );


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
function fyvent_languages() {
	load_plugin_textdomain( 'fyvent', false, dirname( plugin_basename( __FILE__ ) ) );
}
add_action( 'init', 'fyvent_languages' );

