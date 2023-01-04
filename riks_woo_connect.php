<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.riksiot.com
 * @since             1.0.0
 * @package           Riks_woo_connect
 *
 * @wordpress-plugin
 * Plugin Name:       RiKS Woo Connect
 * Plugin URI:        https://www.riksiot.com/wooconnect
 * Description:       This plugins is designed to copy products accross WooCommerce websites via API
 * Version:           1.0.0
 * Author:            Ocean Vu
 * Author URI:        https://www.riksiot.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       riks_woo_connect
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
define( 'RIKS_WOO_CONNECT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-riks_woo_connect-activator.php
 */
function activate_riks_woo_connect() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-riks_woo_connect-activator.php';
	Riks_woo_connect_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-riks_woo_connect-deactivator.php
 */
function deactivate_riks_woo_connect() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-riks_woo_connect-deactivator.php';
	Riks_woo_connect_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_riks_woo_connect' );
register_deactivation_hook( __FILE__, 'deactivate_riks_woo_connect' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-riks_woo_connect.php';
// The core functions
require plugin_dir_path( __FILE__ ) . 'includes/functions-riks_woo_connect.php';

require plugin_dir_path( __FILE__ ) . 'includes/class-riks_wac-copy_to.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-riks_wac-copy_from.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-riks_wac_htmls.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_riks_woo_connect() {

	$plugin = new Riks_woo_connect();
	$plugin->run();

}
run_riks_woo_connect();
add_action( 'admin_menu', 'riks_woo_connect_menu' );   
add_action( 'admin_menu', 'riks_woo_connect_menu_copy_to' );  
add_action( 'admin_menu', 'riks_woo_connect_menu_copy_from' );
add_action( 'admin_menu', 'riks_woo_connect_setting_menu' );   
